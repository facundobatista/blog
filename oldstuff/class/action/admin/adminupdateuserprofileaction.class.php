<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/usernamevalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/emailvalidator.class.php" );	
    lt_include( PLOG_CLASS_PATH."class/view/admin/admineditsiteuserview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminsiteuserslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/passwordvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );	
    lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );	
    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );

    /**
     * \ingroup Action
     * @private
     *
	 * updates user settings
	 */
    class AdminUpdateUserProfileAction extends AdminAction 
	{

    	var $_userId;
        var $_userPassword;
        var $_userEmail;
        var $_userAbout;
        var $_userFullName;
        var $_adminPrivs;
		var $_userProperties;

    	function AdminUpdateUserProfileAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// data validation
			$this->registerFieldValidator( "userFullName", new StringValidator(),true );
			$this->registerFieldValidator( "userEmail", new EmailValidator() );
			$this->registerFieldValidator( "userId", new IntegerValidator() );
			$this->registerFieldValidator( "userAbout", new StringValidator(), true );
			$this->registerFieldValidator( "userIsSiteAdmin", new IntegerValidator(), true );
			$this->registerFieldValidator( "userPermissions", new ArrayValidator( new IntegerValidator() ), true );
			$this->registerFieldValidator( "userProfilePassword", new PasswordValidator(), true );
			$this->registerFieldValidator( "userStatus", new IntegerValidator());

                // TODO: these aren't used in this class, but they are in the form
			$this->registerFieldValidator( "userPictureId", new IntegerValidator() );
			$this->registerFieldValidator( "userName", new UsernameValidator());

			$view = new AdminEditSiteUserView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_updating_user"));
			$this->setValidationErrorView( $view );
			
			$this->requireAdminPermission( "update_user" );			
        }
		
        function perform()
        {
			// get the data
        	$this->_userId = $this->_request->getValue( "userId" );
            $this->_userPassword = trim(Textfilter::filterAllHTML($this->_request->getValue( "userProfilePassword" )));
            $this->_userEmail = Textfilter::filterAllHTML($this->_request->getValue( "userEmail" ));
            $this->_userAbout = Textfilter::filterAllHTML($this->_request->getValue( "userAbout" ));
            $this->_userFullName = Textfilter::filterAllHTML($this->_request->getValue( "userFullName" ));
            $this->_adminPrivs = ( $this->_request->getValue( "userIsSiteAdmin" ) != "" );
			$this->_userStatus = $this->_request->getValue( "userStatus" );
			$this->_perms = $this->_request->getValue( "userPermissions" );

        	// load the user settings
            $users = new Users();
            $user  = $users->getUserInfoFromId( $this->_userId );

            // if no info could be fetched, shown an error and quit
            if( !$user ) {
            	$this->_view = new AdminSiteUsersListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_invalid_user") );
                $this->setCommonData();
                return false;
            }
			
            // update the user settings
            $user->setEmail( $this->_userEmail );
            $user->setAboutMyself( $this->_userAbout );
            $user->setSiteAdmin( $this->_adminPrivs );
            $user->setFullName( $this->_userFullName );
			$user->setStatus( $this->_userStatus );
            if( $this->_userPassword != "" )
            	$user->setPassword( $this->_userPassword );

			// and finally update the global permissions
			// first revoke all permissions
			$userPerms = new UserPermissions();
			$userPerms->revokePermissions( $user->getId(), 0 );
			
			// and then assign the new ones
			if( is_array( $this->_perms )) {
				foreach( $this->_perms as $val => $permId ) {
		            $perm = new UserPermission( $user->getId(), 0, $permId );
		            $res = $userPerms->grantPermission( $perm );
				}
			}
				
			$this->notifyEvent( EVENT_PRE_USER_UPDATE, Array( "user" => &$user ));

            // and now update them
            if( !$users->updateUser( $user )) {
            	$this->_view =  new AdminSiteUsersListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_updating_user") );
                $this->setCommonData();
                return false;
            }
			
			// the post-update event... if needed
			$this->notifyEvent( EVENT_POST_USER_UPDATE, Array( "user" => &$user ));			

            $this->_view = new AdminSiteUsersListView( $this->_blogInfo );
            $this->_view->setSuccessMessage( $this->_locale->pr("user_updated_ok", $user->getUsername()));
            $this->setCommonData();

            return true;
        }
    }
?>