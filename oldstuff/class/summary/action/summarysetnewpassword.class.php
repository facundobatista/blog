<?php

	lt_include( PLOG_CLASS_PATH."class/summary/action/summaryaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/summary/data/summarytools.class.php" );

	/**
	 * verifys that the request to reset the password is ok and if so, shows the form where the user
	 * can type a new password
	 */
	class SummarySetNewPassword extends SummaryAction
	{
	
		var $_userNameHash;
		var $_requestHash;
	
        function SummarySetNewPassword( $actionInfo, $request )
        {
            $this->SummaryAction( $actionInfo, $request );
        }
		
		function validate()
		{
			$this->_userNameHash = $this->_request->getValue( "b" );
			$this->_requestHash = $this->_request->getValue( "a" );
			
			// check that the parameters are there...
			$val = new StringValidator();
			if( !$val->validate( $this->_userNameHash ) || !$val->validate( $this->_requestHash )) {
				$this->_view = new SummaryView( "summaryerror" );
				$this->_view->setErrorMessage( $this->_locale->tr("error_incorrect_request" ));
				return false;			
			}
			
			return true;
		}
		
		function perform()
		{
			// make sure that the request is correct
			$userInfo = SummaryTools::verifyRequest( $this->_userNameHash, $this->_requestHash );
			if( !$userInfo ) {
				$this->_view = new SummaryView( "summaryerror" );
				$this->_view->setErrorMessage( $this->_locale->tr("error_incorrect_request" ));
				return false;			
			}
						
			// so if everything went fine, we can now show a form to allow the user to finally
			// set a new password...
			$this->_view = new SummaryView( "changepassword" );
			$this->_view->setValue( "a", $this->_requestHash );
			$this->_view->setValue( "b", $this->_userNameHash );
			$this->_view->setValue( "userId", $userInfo->getId());
			$this->setCommonData();
			
			return true;
		}
	}
?>
