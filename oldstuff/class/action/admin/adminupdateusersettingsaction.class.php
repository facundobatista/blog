<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/emailvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/passwordvalidator.class.php" );	
    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminuserprofileview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows a form to change the settings of the current user.
     */
    class AdminUpdateUserSettingsAction extends AdminAction 
	{

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminUpdateUserSettingsAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			$this->registerFieldValidator( "userFullName", new StringValidator(), true );
			$this->registerFieldValidator( "userEmail", new EmailValidator());
			$this->registerFieldValidator( "userPictureId", new IntegerValidator());
			$this->registerFieldValidator( "userAbout", new StringValidator(), true );
			$this->registerFieldValidator( "userSettingsPassword", new PasswordValidator(), true );
            $this->registerFieldValidator( "confirmPassword", new PasswordValidator(), true );

			$view = new AdminUserProfileView( $this->_blogInfo, $this->_userInfo );
			$view->setErrorMessage( $this->_locale->tr("error_updating_user_settings"));
			$this->setValidationErrorView( $view );
        }

        /**
         * Validates that the information we've just received from the blog settings
         * form is valid... We have to be really sure about this one!!!
         */
        function validate()
        {
            if(!parent::validate())
                return false;

            $userPassword = trim($this->_request->getValue( "userSettingsPassword" ));
            $userConfirmPassword = trim($this->_request->getValue( "confirmPassword" ));
			
            // check that the passwords match
            if( $userPassword != "" || $userConfirmPassword != "") {
            	if( $userPassword != $userConfirmPassword ) {
					$this->_form->setFieldValidationStatus( "confirmPassword", false );
					$this->_view = $this->_validationErrorView;
                	$this->setCommonData( true );
                	return false;
            	}
            }
			
			return true;
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
        	// update the user information
            $this->_userInfo->setEmail( Textfilter::filterAllHTML($this->_request->getValue( "userEmail" )));
            $userPassword = trim($this->_request->getValue( "userSettingsPassword" ));
            if( $userPassword != "" )
            	$this->_userInfo->setPassword( $userPassword );
            $this->_userInfo->setAboutMyself( Textfilter::filterAllHTML($this->_request->getValue( "userAbout" )));
            $this->_userInfo->setFullName( Textfilter::filterAllHTML($this->_request->getValue( "userFullName" )));
			$this->_userInfo->setPictureId( $this->_request->getValue( "userPictureId" ));
			$this->notifyEvent( EVENT_PRE_USER_UPDATE, Array( "user" => &$this->_userInfo ));			
            $this->_session->setValue( "userInfo", $this->_userInfo );
            $this->saveSession();

            // update the user information
           	$this->_view =  new AdminUserProfileView( $this->_blogInfo, $this->_userInfo );
            $users = new Users();
            if( !$users->updateUser( $this->_userInfo ))
                $this->_view->setErrorMessage( $this->_locale->tr("error_updating_user_settings"));
			else {
				$this->_view->setSuccessMessage( $this->_locale->pr("user_settings_updated_ok", $this->_userInfo->getUsername()));
				// if everything fine, also say so...
				$this->notifyEvent( EVENT_POST_USER_UPDATE, Array( "user" => &$this->_userInfo ));
                CacheControl::resetBlogCache( $this->_blogInfo->getId());	
			}

            $this->setCommonData();

            return true;
        }
    }
?>
