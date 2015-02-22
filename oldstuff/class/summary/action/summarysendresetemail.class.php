<?php

	lt_include( PLOG_CLASS_PATH."class/summary/action/summaryaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/summary/view/summarymessageview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/usernamevalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/emailvalidator.class.php" );    
    lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );    
    lt_include( PLOG_CLASS_PATH."class/data/filter/htmlspecialcharsfilter.class.php" );    
    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
	lt_include( PLOG_CLASS_PATH."class/summary/data/summarytools.class.php" );
	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );

	/**
	 * sends an email to reset a password, first checking whether the username
	 * and the given mailbox really exist
	 */
	class SummarySendResetEmail extends SummaryAction
	{
	
		var $_userName;
		var $_userEmail;
	
        function SummarySendResetEmail( $actionInfo, $request )
        {
            $this->SummaryAction( $actionInfo, $request );

			// data filtering
			$f = new HtmlFilter();
			$f->addFilter( new HtmlSpecialCharsFilter());
			$this->_request->registerFilter( "userName", $f );
			$this->_request->registerFilter( "userEmail", $f );
            
            // data validation
            $this->registerFieldValidator( "userName", new UsernameValidator());
            $this->registerFieldValidator( "userEmail", new EmailValidator());
            $this->setValidationErrorView( new SummaryView( "resetpassword" ));
        }
	
		function perform()
		{		
			// fetch the data
			$this->_userName = $this->_request->getValue( "userName" );
			$this->_userEmail = $this->_request->getValue( "userEmail" );			
			
			// try to see if there is a user who has this username and uses the
			// given mailbox as the email address
			$users = new Users();
			$userInfo = $users->getUserInfoFromUsername( $this->_userName );
			
			// if the user doesn't exist, quit
			if( !$userInfo ) {
				$this->_view = new SummaryView( "resetpassword" );
				$this->_form->setFieldValidationStatus( "userName", false );
				$this->setCommonData( true );
				return false;
			}
			
			// if the user exists but this is not his/her mailbox, then quit too
			if( $userInfo->getEmail() != $this->_userEmail ) {	
				$this->_view = new SummaryView( "resetpassword" );
				$this->_form->setFieldValidationStatus( "userEmail", false );
				$this->setCommonData( true );				
				return false;
			}
			
			// if everything's fine, then send out the email message with a request to
			// reset the password
			$requestHash = SummaryTools::calculatePasswordResetHash( $userInfo );
			$config =& Config::getConfig();
			$baseUrl = $config->getValue( "base_url" );			
			$resetUrl = $baseUrl."/summary.php?op=setNewPassword&a=$requestHash&b=".md5($userInfo->getUsername());

			SummaryTools::sendResetEmail( $userInfo, $resetUrl );
			
			$this->_view = new SummaryMessageView( $this->_locale->tr( "password_reset_message_sent_ok" ));
			
			$this->setCommonData();			
	
			return true;
		}
	}
?>