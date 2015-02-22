<?php
    /**
	 * \defgroup Forms
	 *
	 * The Form class helps in data validation.
	 */
	
	/**
	 * Set this to true if you want to get some debug information every time
	 * a validation error occurs
	 */
	define( "FORM_VALIDATOR_DEBUG", false );
	
	/**
	 * \ingroup Forms
	 *
	 * This the class used for form validation. It works helped by the Validator classes to perform data validation,
	 * as well as in conjunction with the Action and View classes. It is fact internally used by the Action class and it is
	 * capable of reporting to the view class which fields of a given form generated an error.
	 */
	class FormValidator 
	{
		var $_fieldValidators;
		var $_validationResults;
		var $_fieldValues;
		var $_formIsValidated;
		var $_formHasRun;
		var $_fieldErrorMessages;
		var $_formDebug;
			
		/**
		 * initializes the form validator
		 */
		function FormValidator()
		{
			
			
			// internal arrays used by the class
			$this->_fieldValidators = Array();
			$this->_validationResults = Array();
			$this->_fieldValues = Array();
			$this->_fieldErrorMessages = Array();
			
			$this->_formDebug = FORM_VALIDATOR_DEBUG;
			
			// the form hasn't been validated yet
			$this->_formIsValidated = false;
			
			// form hasn't run yet
			$this->_formHasRun = false;
		}
		
		/**
		 * registers a new validator, for validating data coming from fields
		 * 
		 * @param fieldName The name of the field from the form that we're going to validate
		 * @param validator A valid class inheriting from the Validator base class and that implements
		 * the validate() method, that will be used for validating fields.
		 * @param onlyIfAvailable validate this field only if its value is not emtpy
		 * @return Always true
		 */
		function registerFieldValidator( $fieldName, $validatorClass, $onlyIfAvailable = false )
		{
			$this->_fieldValidators["$fieldName"] = Array( "validator" => $validatorClass, "onlyIfAvailable" => $onlyIfAvailable );		
		}
		
		/**
		 * it is also possible to specify custom error messages from within the php code,
		 * instead of leaving it up to the templates to decide which error message to show
		 *
		 * @param fieldName
		 * @param errorMessage
		 * @return Always true
		 */
		function setFieldErrorMessage( $fieldName, $errorMessage )
		{
			$this->_fieldErrorMessages["$fieldName"] = $errorMessage;
		}
		
		/**
		 * validates the data in the field
		 *
		 * @return True if all the fields validate or false otherwise
		 */
		function validate( $request )
		{
			if( empty( $this->_fieldValidators ) || !is_array( $this->_fieldValidators ))
				return true;
	
			$validationResult = false;
			$finalValidationResult = true;
			
			foreach( $this->_fieldValidators as $fieldName => $fieldValidationArrayInfo ) {
				// get the validator object
				$fieldValidatorClass = $fieldValidationArrayInfo["validator"];
				// and whether we should use it always or only when the field has a non-empty value
				$onlyIfAvailable = $fieldValidationArrayInfo["onlyIfAvailable"];
				
				// get the value of the field
				$fieldValue = $request->getValue( $fieldName );
				
				if( $fieldValue == "" && $onlyIfAvailable ) {
					$validationResult = true;
				}
				else {
					$validationResult = $fieldValidatorClass->validate( $fieldValue );
				}
				
				$this->_validationResults[$fieldName] = $validationResult;
				if($validationResult){
					$this->_fieldValues[$fieldName] = $fieldValue;
                }
				else {
                        // don't ever display unvalidated data - that causes XSS issues.
                    lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
                    $this->_fieldValues["$fieldName"] = Textfilter::filterAllHTML( $fieldValue );
                    
//					$this->_fieldValues[$fieldName] = "";
				}
				
				// if one of the validations is false, then cancel the whole thing
				$finalValidationResult = $finalValidationResult && $validationResult;				
			}

			// the form has already run
			$this->_formHasRun = true;
			
			// but... has it validated?
			$this->_formIsValidated = $finalValidationResult;
			
			// in case we have to display some debug information
			if( !$finalValidationResult && $this->_formDebug )
				$this->dump();
			
			return $finalValidationResult;
		}
		
		/**
		 * forces a field to be true
		 *
		 * @param fieldName
		 * @return always true
		 */
		function registerField( $fieldName )
		{
			lt_include( PLOG_CLASS_PATH."class/data/validator/emptyvalidator.class.php" );		
			$this->registerFieldValidator( $fieldName, new EmptyValidator());
			
			return true;
		}
		
		/**
		 * returns whether the field was validated successfully or not. Do not use
		 * this method *before* calling FormValidator::validate()
		 *
		 * @param field The name of the field in the form
		 * @return True if it validates or false otherwise
		 */
		function isFieldValid( $field )
		{
			if( $this->formIsValid())
				$valid = true;
			else
				$valid = $this->_validationResults["$field"];
				
			return $valid;
		}
		
		/**
		 * returns an array where the field name is the key and the value will be
		 * either '0' or '1' depending on whether the field validated successfully or not
		 *
		 * @return An associative array
		 */
		function getFormValidationResults()
		{
			return $this->_validationResults;
		}
		
		/**
		 * returns an array where the key is the field and the value is the value of the field, but it
		 * will only contain those fields which have been registered
		 *
		 * @return An associative array
		 */
		function getFieldValues()
		{
			return $this->_fieldValues;
		}
		
		/**
		 * returns whether the form is valid or not
		 *
		 * @return a boolean
		 */
		function formIsValid()
		{
			return $this->_formIsValidated;
		}
		
		/**
		 * changes the form validation status
		 *
		 * @param valid
		 */
		function setFormIsValid( $valid )
		{
			$this->_formIsValidated = $valid;
		}
		
		/**
		 * changes the processing status of a field
		 *
		 * @param fieldName
		 * @return True
		 */
		function setFieldValidationStatus( $fieldName, $newStatus )
		{
			$this->_validationResults["$fieldName"] = $newStatus;
			
			// if we're setting some field to false, then the whole form becomes
			// non-validated too!
			$this->_formIsValidated = false;
			$this->_formHasRun = true;
			
			return true;
		}
		
		/**
		 * returns true if the form has already been executed (if FormValidator::validate()
		 * has already been called or not) Use this function when performing validatdion
		 * of data in your templates, since otherwise FormValidator::fieldIsValid and
		 * FormValidator::formIsValid() will always return false if validate() has not
		 * been called!
		 *
		 * @return returns true if the form has already been validated, or false otherwise
		 */
		function formHasRun()
		{
			return $this->_formHasRun;
		}
		
		/**
		 * returns the custom error message for the field, if any
		 *
		 * @param fieldName
		 */
		function getFieldErrorMessage( $fieldName )
		{
			return $this->_fieldErrorMessages["$fieldName"];
		}
		
		/**
		 * dumps the current status of the form, useful for debugging 
		 * purposes when we know that a field is not validating correctly but there
		 * is no error message displayed on the screen
		 */
		function dump()
		{
			print("<pre>");
			foreach( $this->_fieldValidators as $field => $validationInfo ) {
				print( "field = $field - validator class = ".get_class( $validationInfo["validator"] ).
				       " - status = ".$this->_validationResults["$field"]."<br/>" );
			}
			print("</pre>");
		}
	}
?>