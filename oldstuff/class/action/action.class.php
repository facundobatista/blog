<?php


	/**
	 * \defgroup Action
	 *
	 * Action in pLog are the classes that implement the application logic. Action classes are always
	 * instantiated and executed by the Controller after a parameter in an incoming request
	 * matched an action class as specified in the action class map.
	 *
	 * The most basic action class is Action, though API users will generally not need to create objects
	 * of that class directly. Users wishing to extend the features of pLog via plugins will likely
	 * use BlogAction for actions in the public interface, and AdminAction, SiteAdminAction or
	 * BlogOwnerAdminAction depending on which user privileges are required to use the given action. 	 
	 *
	 * @see Action
	 * @see Controller
	 * @see ActionInfo
	 * @see BlogAction
	 * @see SiteAdminAction
	 * @see BlogOwnerAdminAction
	 */

	

	/**
	 * \ingroup Action
	 *
	 * The base most basic action class is Action, which provides some common code and the Action::perform()
	 * method which should be implemented by child classes wishing to provide some business logic. At the end
	 * of the method, an Action class is expected to generate a valid View object (or a child class of View)
	 * that will be used by the Controller to render some contents to be sent to the client.
	 *
	 * Action classes are expected to at least provide their own logic in the Action::perform() method. In previous
	 * versions of pLog it was also necessary to provide data validation logic in the Action::validate()
	 * method, but that is not necessary anymore (albeit possible if needed) since the introduction of the
	 * new data validation framework (See the FormValidator class and the Validator and Validator_Rules modules)
	 * The Action::validate() method now provides some code of its own that triggers the data validation process
	 * if there is any data to be validated.
	 *
	 * There is a lot more information about the data validation framework here: http://wiki.plogworld.net/index.php/PLog_1.0/Forms_and_data_validation 
	 *
	 * The View object that Action classes must create can be set via the private attribute
	 * Action::_view or te method Action::setView(), though the first one is the most widely used
	 * throughout the core code.
	 *
	 * Please keep in mind that it is advisable to call the Action::setCommonData() method at the very end
	 * of the Action::perform() method in our custom classes because it needs to perform some extra
	 * operations right before the view is sent back to the controller.
     */
	class Action 
	{

    	// this is the pointer to the view associated with this action
		var $_view;
        var $_request;
        var $_actionInfo;
        var $_fieldValidators;
		var $_validationErrorView;
		var $_previousAction;
		var $_isSuccess;
        var $_form;
        
        /**
         * Constructor.
         *
         * @param actionInfo An ActionInfo object contaning information about the action
         * @param httpRequest the HTTP request.
         */
    	function Action( $actionInfo, $httpRequest )
        {
            lt_include( PLOG_CLASS_PATH."class/net/request.class.php" );
	        lt_include( PLOG_CLASS_PATH."class/data/forms/formvalidator.class.php" );			

            $this->_request = new Request( $httpRequest );

            $this->_actionInfo = $actionInfo;
			
			$this->_fieldValidators = Array();
			
			// initialize the views to empty values
			$this->_view = null;
			$this->_validationErrorView = null;
			
			// form object, used for data validation
			$this->_form = new FormValidator();
			
			// no previous action
			$this->_previousAction = null;
			
			// by default, the action is successful
			$this->_isSuccess = true;
        }
		
		/**
		 * @private
		 *
		 * @param previousAction a valid Action object
		 * @return Always true
		 */
		function setPreviousAction( $previousAction )
		{
			$this->_previousAction = $previousAction;
		}
		
		/** 
		 * returns the reference to the previous action in the process flow
		 *
		 * @return A valid Action object, or null if there is no previous action
		 * @private
		 */
		function getPreviousAction()
		{
			return $this->_previousAction;
		}
        
        /**
         * Receives the HTTP request from the client as parameter, so that we can
         * extract the parameters and perform our business logic.
         *
         * The result of this will be a view, which will normally be the output of the
         * processing we just did or for example an error view showing an error message.
         * Once we have completed processing, the controller will call the getView() method
         * to get the resulting view and send it back to the customer.
         *
         * @return Returns nothing
         */
        function perform()
        {	
			// to be implemented by child classes...
        }
        
        /**
         * This method can be used for data validation and is <b>always</b> executed
         * before perform(). If it returns 'true', execution will continue as normal. If it 
         * returns 'false', the process will be stopped and the current contents of the
         * view will be returned. If the view is empty, an exception will be thrown.
         *
         * As of pLog 1.0, it is not necessary to implement data validation code here and it is recommended
         * to use the data validation framework (see methods Action::registerFieldValidator() and related) There is
         * more information about the data validation framework in the wiki: http://wiki.plogworld.net/index.php/PLog_1.0/Forms_and_data_validation.
         *
         * With the default code provided in the Action::validate() method, the callback method Action::validationErrorProcessing()
         * will be called and after that, the view set via the Action::setValidationErrorView() will be used to
         * generate the contents of the error message.
         *
         * @return Returns true if data is correct or false otherwise. See above for more details.
         */
        function validate()
        {
			// use the FormValidator object to validate the data
			$validationOk =  $this->_form->validate( $this->_request );
			
			// if something went wrong... let's do something about it :)
			if( !$validationOk ) {
				$this->validationErrorProcessing();
			}
			
			return $validationOk;
		}
		
		/**
		 * This method will be called when a validation error happens. Child classes are
		 * free to extend or reimplement this one and can be used as some sort of a trigger
		 * in order to do some cleanup if needed.
		 *
		 *Ê@return nothing
		 */
		function validationErrorProcessing()
		{
			// if there was a validation error, then inform the view
			$this->_view = $this->_validationErrorView;
			$this->_view->setError( true );
				
			$this->_form->setFormIsValid( false );
			$this->setCommonData( true );
	
			return true;
		}
		
		/**
		 * sets the view that will be shown in case there is an error during
		 * the validation process... It makes things a bit easier for us when 
		 * it comes to validate data. This view will only be used if validate() generates a validation
		 * error or if we force the action to generate an error via Action::setSuccess()
		 *
		 * @param view A valid View object
		 * @return Always true
		 * @see View
		 */
		function setValidationErrorView( $view )
		{
			$this->_validationErrorView = $view;
			
			return true;
		}
		
		/**
		 * registers a new validator, for validating data coming from fields
		 * 
		 * @param fieldName The name of the field from the form that we're going to validate
		 * @param validator A valid class inheriting from the Validator base class and that implements
		 * the validate() method, that will be used for validating fields.
		 * @param onlyIfAvailable validate this field only if its value is not emtpy		 
		 * @return Always true
		 * @see FormValidator
		 * @see Validator
		 */
		function registerFieldValidator( $fieldName, $validator, $onlyIfAvailable = false )
		{
			$this->_form->registerFieldValidator( $fieldName, $validator, $onlyIfAvailable );
			
			return true;
		}
		
		/**
		 * registers a new field whose value should be available to the view/template in case
		 * it needs to be reshown.Those fields that haven't been registered, will *not* be shown
		 * when rerunning the view.
		 * 
		 * @param fieldName The name of the field from the form that we're going to register
		 * @see FormValidator
		 */
		function registerField( $fieldName )
		{
			$this->_form->registerField( $fieldName );
			
			return true;
		}
		

        /**
         * This function does not need to be reimplemented by the childs of this class.
         * It just returns the resulting view of the operation.
		 *
		 * @return A valid View object
         */
        function getView()
        {
        	return $this->_view;
        }
		
		/**
		 * we can do things here that are common to all the views. This method must be called just before
		 * the Action::perform() method has finished its processing since most of the child Action
		 * classes use it to do some kind of post-processing.
		 *
		 * @param copyFormValues Whether the values from fields that were registered via
		 * Action::registerFieldValidator() and Action::registerField() should be passed back to the view
		 * as variables or not. It defaults to 'false' but this parameter is useful in those cases
		 * when we would like to force an error to happen (not a validation error) and still keep the
		 * form values.
		 * @return Always true
		 */
		function setCommonData( $copyFormValues = false )
		{
			$this->_view->setValue( "form", $this->_form );
			
			if( $copyFormValues ) {
				// in case we'd like to copy the values from the form
				$fieldValues = $this->_form->getFieldValues();
				foreach( $fieldValues as $fieldName => $fieldValue ) {
					$this->_view->setValue( $fieldName, $fieldValue );
				}
			}
			return true;
		}
		
		 /**
		  * This method can be used to trigger validation errors even if they did not really happen, or
		  * to disable errors if they happened.
		  *
		  * @param success Whether to force or unforce an error
		  */
		 function setSuccess( $success )
		 {
			 $this->_isSuccess = $success;
			 $this->_form->setFormIsValid( $success );
		 }
		
		 /**
		  * This method will be executed to check whether this action can be executed or not. This means
		  * that this method will be executed before the perform() method. If this method returns 'false',
		  * the controller will then load the action defined via the Controller::setCannotPerformAction()
		  *
		  * @return True if the controller is allowed to call the Action::perform() action or not.
		  * @see Controller
		  */
		function canPerform()
		{
			return( true );
		}			
    }
?>