<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/passwordvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/emailvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/usernamevalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/permissions.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminadduserview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminsiteuserslistview.class.php" );	

    /**
     * \ingroup Action
     * @private
     *
     * Adds a new user to the database.
     */
    class AdminAddUserAction extends AdminAction 
	{

    	var $_userName;
        var $_userPassword;
        var $_userEmail;
		var $_userBlog;
		var $_userFullName;
		var $_properties;
		var $_userStatus;
		var $_permissions;

    	function AdminAddUserAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
        	
        	// for data validation purposes
        	$this->registerFieldValidator( "userName", new UsernameValidator() );
        	$this->registerFieldValidator( "newUserPassword", new PasswordValidator() );
        	$this->registerFieldValidator( "userEmail", new EmailValidator() );
			$this->registerFieldValidator( "userStatus", new IntegerValidator() );
        	$this->registerFieldValidator( "userFullName", new StringValidator(), true );
        	$this->registerFieldValidator( "blogId", new IntegerValidator(), true );
			$this->registerFieldValidator( "userPermissions", new ArrayValidator( new IntegerValidator() ), true );

			$view = new AdminAddUserView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_adding_user" ));
        	$this->setValidationErrorView( $view );

			$this->requireAdminPermission( "add_user" );
        }

        function perform()
        {
	        // fetch the validated data
        	$this->_userName = Textfilter::filterAllHTML($this->_request->getValue( "userName" ));
            $this->_userPassword = $this->_request->getValue( "newUserPassword" );
            $this->_userEmail = Textfilter::filterAllHTML($this->_request->getValue( "userEmail" ));
            $this->_userFullName = Textfilter::filterAllHTML($this->_request->getValue( "userFullName" ));
			$this->_userStatus = $this->_request->getValue( "userStatus" );
			$this->_userBlog = $this->_request->getValue( "blogId" );
			$this->_permissions = $this->_request->getValue( "userPermissions" );
	        
        	// now that we have validated the data, we can proceed to create the user, making
            // sure that it doesn't already exists
            $users = new Users();
            $userInfo = $users->getUserInfoFromUsername( $this->_userName );
            if( $userInfo ) {
                $this->_form->setFieldValidationStatus( "userName", false );            	
                $this->_view = new AdminAddUserView( $this->_blogInfo );
                $this->setCommonData( true );
                return false;
            }

            // otherwise, we can create a new one
			$user = new UserInfo( $this->_userName, 
			                      $this->_userPassword, 
								  $this->_userEmail, 
								  "", 
								  $this->_userFullName,
								  0,
								  $this->_properties );
			$user->setStatus( $this->_userStatus );
			$this->notifyEvent( EVENT_PRE_USER_ADD, Array( "user" => &$user ));
			$newUserId = $users->addUser( $user );
			
            if( !$newUserId ) {
                $this->_view = new AdminAddUserView( $this->_blogInfo );
                $this->_form->setFieldValidationStatus( "userName", false );
                $this->setCommonData( true );
                return false;
            }

			// grant the site-wide permissions, if any granted
			$userPerms = new UserPermissions();
			if( is_array( $this->_permissions )) {
				foreach( $this->_permissions as $val => $permId ) {
		            $perm = new UserPermission( $user->getId(), 0, $permId );
		            $res = $userPerms->grantPermission( $perm );
				}
			}			
			
			// if the userBlog parameter is different than 0, we should somehow allow the user
			// to log into that blog although he/she won't have much to do with only the
			// blog_access permission
			if( $this->_userBlog > 0 ) {
				$perms = new Permissions();
				$blogAccess = $perms->getPermissionByName( "blog_access" );
				$perm = new UserPermission( $newUserId, $this->_userBlog, $blogAccess->getId() );
				$result = $userPerms->grantPermission( $perm );
			}
			
			$this->notifyEvent( EVENT_POST_USER_ADD, Array( "user" => &$user ));

			if( !$this->userHasPermission( "view_users", 0 ))
            	$this->_view = new AdminAddUserView( $this->_blogInfo );
			else
				$this->_view = new AdminSiteUsersListView( $this->_blogInfo );

            $this->_view->setSuccessMessage( $this->_locale->pr("user_added_ok", $user->getUsername()));
            $this->setCommonData();

            return true;
        }
    }
?>