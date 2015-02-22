<?php
	
	lt_include( PLOG_CLASS_PATH."class/controller/controller.class.php" );
	lt_include( PLOG_CLASS_PATH."class/net/http/session/sessionmanager.class.php" );
	lt_include( PLOG_CLASS_PATH."class/net/http/httpvars.class.php" );	
	
	define( "SEQUENTIAL_CONTROLLER_SESSION_PARAMETER", "ltSeqControllerCurrentStep" );

	/**
	 * \ingroup Controller
	 *
	 * This is a simplified version of the Controller pattern that executes its actions in a
	 * pre-defined sequence. This allows to easily create multi-step forms that are executed
	 * only in the pre-defined order.
	 *
	 * This controller uses the session to store its current position in the list of steps/action
	 * and it will use it to compute its next step after the next request. Support for sessions 
	 * on both the client side and the server side is required.
	 *
     * The next step in the sequence will be calculated based on the outcome of the action that
	 * was last executed: if the validate() method or the perform() method both returned true, then
	 * the controller will proceed to execute the next action. If either of the conditions above
	 * was not true, this controller will keep retrying the current step until successful. If you are
	 * having problems with this controller executing the same action class over and over again,
	 * make sure you are not returning 'false' from any of those two methods.
	 *
	 * If you'd like to restart the sequence of this controller, please include a "start=1" parameter
	 * in any request.
	 *
	 * Please see the RegistrationController class for a simple example of how to use
	 * this controller.
	 *
	 * @author The LifeType Project
	 * @see RegistrationController
	 */
	class SequentialController extends Controller
	{
	
		var $_steps;
		
		/**
		 * Constructor of the class.
		 *
		 * @param steps An array containing the name of the action classes that are going to be
		 * executedin sequence. Please do not use an associative array but a normal array, since
		 * controller will internally use an integer counter to store the current position.
		 * @return Nothing
		 */
		function SequentialController( $steps )
		{
			$this->Controller( Array());		
			$this->_steps = $steps;
		}
		
		/**
		 * Returns the next step in the sequence based on the current step
		 *
		 * @param currentStep The current step in the sequence
		 * @private
		 * @return
		 */
		function getNextStep( $currentStep )
		{
			return(($currentStep + 1) % count($this->_steps ));
		}
		
		/**
		 * Returns the current step, based on the contents from the session. If there is
		 * no information in the session, processing will start at the action class whose
		 * index in the sequence array is '0'
		 *
		 * @return The current step in the sequence
		 */
		function getCurrentStep()
		{
			$curStep = SessionManager::getSessionValue( SEQUENTIAL_CONTROLLER_SESSION_PARAMETER );
			$start = HttpVars::getRequestValue("start");

			if( !$curStep || $start == "1" ) {
				$curStep = 0;
			}
			
			return( $curStep );
		}

		/**
		 * Main method of the controller, as it processes the current HTTP request. Since this is a sequential
		 * controller, it does not take into account any of the parameters in the request (as opposed to a normal
		 * Controller class, which would for example check the "op" parameter) but it instead relies on its
		 * internal counter and the outcome of the last action to find out which action to execute next.
		 *
		 * @param httpRequest The current HTTP Request
		 * @return Always true
		 */
		function process( $httpRequest )
		{
            global $_plogController_previousAction;

            lt_include( PLOG_CLASS_PATH."class/action/actioninfo.class.php" );
            
            $currentStep = $this->getCurrentStep();
            $actionClass = $this->_steps[ $currentStep ];
            
            $this->loadActionClass( $actionClass );

            $actionInfo   = new ActionInfo( $this->_actionParam, "sequential" );
            $actionObject = new $actionClass( $actionInfo, $httpRequest );
			$actionObject->setPreviousAction( $_plogController_previousAction );

            // we can use the validate method to check the values of the form variables. If validate()
            // returns 'true', then we call the 'perform' method. If not, then we won't :)
            if( $actionObject->validate()) {
                if( $actionObject->perform()) {
					// if everything went ok, let's move to the next step
                	$actionObject->setSuccess( true );
                	$nextStep = $this->getNextStep( $currentStep );
            	}
                else {
					// if there was an issue in the perform() method, let's retry the current step
                	$actionObject->setSuccess( false );
					$nextStep = $currentStep;	                	
				}
            }
            else {
				// retry the current step if there was a validation issue
            	$nextStep = $currentStep;	
            }

            // store the next step in the sequence, regardless of what it was
			SessionManager::setSessionValue( SEQUENTIAL_CONTROLLER_SESSION_PARAMETER, $nextStep );            
			
            $view = $actionObject->getView();

            if( empty( $view )) {
                $e = new Exception( 'The view is empty after calling the perform method.' );
                throw( $e );
            }
            else
                $view->render();
		}
	}
?>