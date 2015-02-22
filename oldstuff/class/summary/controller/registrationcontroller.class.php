<?php

	lt_include( PLOG_CLASS_PATH."class/controller/sequentialcontroller.class.php" );
	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	
	/**
	 * \ingroup Controller
	 *
	 * Controller for the registration process. It doesn't really do much, other than
	 * setting the array with the sequence with the action classes and telling the resource
	 * loader of the controller in which folder it can find the classes.
	 *
	 * @author The LifeType Project
	 * @see SequentialController
	 */	
	class RegistrationController extends SequentialController
	{
		/**
		 * Constructor of the class.
		 */
		function RegistrationController()
		{
			$config =& Config::getConfig();
			
			if( $config->getValue( "summary_show_agreement", true )) {			
				$this->SequentialController( Array (
					"doReadAgreement",
					"doUserRegister",
					"doUserCreation",
					"doBlogRegistration",
					"doFinishRegister"
				));
			}
			else {
				$this->SequentialController( Array (
					"doUserRegister",
					"doUserCreation",
					"doBlogRegistration",
					"doFinishRegister"
				));				
			}
			
			$this->setActionFolderPath( PLOG_CLASS_PATH."class/summary/action/" );			
		}	
	}	
?>