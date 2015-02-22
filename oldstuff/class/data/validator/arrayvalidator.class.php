<?php

	lt_include( PLOG_CLASS_PATH."class/data/validator/validator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/rules/arrayrule.class.php" );

    /**
     * \ingroup Validator
     *
     * validates if a value is a php array. Useful when we are expecting
	 * parameters as array in a request and we'd like to know whether we're really
	 * receiving an array.
	 *
	 * @see ArrayRule
     */
    class ArrayValidator extends Validator
    {
        var $_elementValidator;

		/**
		 * Constructor
		 *
		 * @param elementValidator An instance of the Validator class that will be used to validate
		 * each one of the elements in the array, if any. If none is specified, then this class will 
		 * only validate that the given valu is an array.
		 *
		 * When passing a Validator object, the given array will validate if and only if all 
		 * the elements of the array pass the validation of the given validation class.
		 */
        function ArrayValidator( $elementValidator = null )
        {
            $this->Validator();
			$this->_elementValidator = $elementValidator;

            $this->addRule(new ArrayRule());
        }

        /**
         * ArrayValidator's own validate, it will validate the array itself first, then
         * validate each element.
         *
         * @param values The array that we're going to validate.
         */
        function validate($values)
        {
            $validateOk = parent::validate($values);
            if( !$validateOk )
            	return false;
            	
			if( $this->_elementValidator ) {
	            foreach( $values as $value ) {
	                if (!$this->_elementValidator->validate($value))
	                     return false;
	 	        }
			}

            return true;
        }
    }
?>
