<?php

	lt_include( PLOG_CLASS_PATH."class/summary/action/registeraction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/emailvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/usernamevalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/passwordvalidator.class.php" );    
    lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/filter/htmlspecialcharsfilter.class.php" );

    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
    lt_include( PLOG_CLASS_PATH."class/summary/view/doblogregistrationview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/summary/view/summaryusercreationview.class.php" );    

    /**
     * starts the user and blog registration process
     */
    class doUserCreation extends RegisterAction 
	{
        var $_config;
        
		function doUserCreation( $actionInfo, $request )
		{
			$this->RegisterAction( $actionInfo, $request );
			
			// apply some filters to the data in the request
			$f = new HtmlFilter();
			$f->addFilter( new HtmlSpecialCharsFilter());
			$this->_request->registerFilter( "userName", $f );
			$this->_request->registerFilter( "userFullName", $f );
			$this->_request->registerFilter( "userEmail", $f );
			$this->_request->registerFilter( "userPassword", $f );
			$this->_request->registerFilter( "userPasswordCheck", $f );
            
			// data validation and stuff like that :)
			$this->registerFieldValidator( "userName", new UsernameValidator());
			$this->registerFieldValidator( "userPassword", new PasswordValidator());
			$this->registerFieldValidator( "userPasswordCheck", new PasswordValidator());
			$this->registerFieldValidator( "userEmail", new EmailValidator());
            $this->registerFieldValidator( "userAuth", new StringValidator(), true);
			$this->registerFieldValidator( "userFullName", new StringValidator(), true );
            $this->_config =& Config::getConfig();

			$view = new SummaryUserCreationView();
			$view->setErrorMessage( $this->_locale->tr("error_adding_user" ));
			$this->setValidationErrorView( $view );
		}	

        function perform()
        {
	        // if all data is correct, then we can proceed and use it
            $this->userName = $this->_request->getValue( "userName" );
            $this->userPassword = $this->_request->getValue( "userPassword" );
            $this->confirmPassword = $this->_request->getValue( "userPasswordCheck" );
            $this->userEmail = $this->_request->getValue( "userEmail" );
			$this->userFullName = $this->_request->getValue( "userFullName" );
			
			// check if there is already a user with the same username and quit if so
			$users = new Users();
			if( $users->getUserInfoFromUsername( $this->userName )) {
				$this->_view = new SummaryUserCreationView();
				//$this->_form->hasRun( true );
				$this->_form->setFieldValidationStatus( "userName", false );
				$this->setCommonData( true );
				return false;
			}

            // check if this email account has registered and quit if so, but only if the configuration
			// says that we should only allow one blog per email account
			if( $this->_config->getValue( "force_one_blog_per_email_account" )) {
        	    if( $users->emailExists($this->userEmail)) {
					$this->_view = new SummaryUserCreationView();
					//$this->_form->hasRun( true );
					$this->_form->setFieldValidationStatus( "userEmail", false );
					$this->setCommonData( true );
					return false;
            	}
			}
			
			// check if the passwords match, and stop processing if so too
            if( $this->userPassword != $this->confirmPassword ) {
	            $this->_view = new SummaryUserCreationView();
                $this->_view->setErrorMessage( $this->_locale->tr("error_passwords_dont_match"));
				$this->_form->setFieldValidationStatus( "userPasswordCheck", false );                
                $this->setCommonData( true );
                return false;
            }			
            
            // check if the captcha matches
            if( $this->_config->getValue( "use_captcha_auth")) {
                $this->captcha = $this->_request->getValue( "userAuth" );
            	lt_include( PLOG_CLASS_PATH."class/data/captcha/captcha.class.php" );
            	$captcha = new Captcha();
            	if( !$captcha->validate( $this->captcha )) {
		            $this->_view = new SummaryUserCreationView();
    	            $this->_view->setErrorMessage( $this->_locale->tr("error_invalid_auth_code"));
					$this->_form->setFieldValidationStatus( "userAuth", false );                
            	    $this->setCommonData( true );
                	return false;            	
            	}
            }

			// save the data to the session
			SessionManager::setSessionValue( "userName", $this->userName );
			SessionManager::setSessionValue( "userPassword", $this->userPassword );
			SessionManager::setSessionValue( "userEmail", $this->userEmail );
			SessionManager::setSessionValue( "userFullName", $this->userFullName );

            // if everything went fine, then proceed
            $this->_view = new doBlogRegistrationView();
            $this->setCommonData();
            return true;
        }

    }
?>
