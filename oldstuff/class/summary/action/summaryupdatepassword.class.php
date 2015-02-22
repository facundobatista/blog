<?php

	lt_include( PLOG_CLASS_PATH."class/summary/action/summaryaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/passwordvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
	lt_include( PLOG_CLASS_PATH."class/summary/data/summarytools.class.php" );

	class SummaryUpdatePassword extends SummaryAction
	{
		var $_userNameHash;
		var $_requestHash;
		var $_newPassword;
		var $_retypeNewPassword;
		var $_userId;
	
        function SummaryUpdatePassword( $actionInfo, $request )
        {
            $this->SummaryAction( $actionInfo, $request );
			
			$this->registerFieldValidator( "a", new StringValidator());
			$this->registerFieldValidator( "b", new StringValidator());
			$this->registerFieldValidator( "newPassword", new PasswordValidator());
			$this->registerFieldValidator( "retypePassword", new PasswordValidator());
			$this->registerFieldValidator( "userId", new IntegerValidator());
			$view = new SummaryView( "changepassword" );
			$view->setErrorMessage( $this->_locale->tr("error_updating_password"));
			$this->setValidationErrorView( $view );
        }
		
		function perform()
		{
			$this->_userNameHash = $this->_request->getValue( "b" );
			$this->_requestHash = $this->_request->getValue( "a" );
			$this->_newPassword = $this->_request->getValue( "newPassword" );
			$this->_retypeNewPassword = $this->_request->getValue( "retypePassword" );
			$this->_userId = $this->_request->getValue( "userId" );
			
			// check if the passwords are correct and are the same
			if( $this->_newPassword != $this->_retypeNewPassword ) {
				$this->_view = new SummaryView( "changepassword" );
				$this->_view->setErrorMessage( $this->_locale->tr("error_passwords_do_not_match" ));
				$this->setCommonData( true );
				return false;					
			}			

			$userInfo = SummaryTools::verifyRequest( $this->_userNameHash, $this->_requestHash );
			if( !$userInfo ) {
				$this->_view = new SummaryView( "summaryerror" );
				$this->_view->setErrorMessage( $this->_locale->tr("error_incorrect_request" ));
				$this->setCommonData( true );
				return false;			
			}
			
			// so if everything went fine, we can *FINALLY* change the password!
			$users =  new Users();
			$userInfo->setPassword( $this->_newPassword );
			$users->updateUser( $userInfo );
			$this->_view = new SummaryView( "message" );
			$this->_view->setSuccessMessage( $this->_locale->tr("password_updated_ok" ));
			
			return true;
		}	
	}
?>
