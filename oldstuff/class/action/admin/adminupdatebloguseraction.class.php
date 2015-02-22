<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminbloguserslistview.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/permissions.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );	

    /**
     * \ingroup Action
     * @private
     */
    class AdminUpdateBlogUserAction extends AdminAction 
	{	
    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminUpdateBlogUserAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// register one validator
			$this->registerFieldValidator( "userId", new IntegerValidator());
			$this->registerFieldValidator( "perm", new ArrayValidator( new IntegerValidator() ), true );

			// and the view we should show in case there is a validation error
			$errorView = new AdminBlogUsersListView( $this->_blogInfo );
			$errorView->setErrorMessage( $this->_locale->tr("error_invalid_user_id" ));			
			$this->setValidationErrorView( $errorView );
			
			$this->requirePermission( "update_blog_user");
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
			// fetch the data, we already know it's valid and that we can trust it!
			$userId = $this->_request->getValue( "userId" );
			$this->_perms = $this->_request->getValue( "perm" );			
			
			// load the user
			$users = new Users();
			$userInfo = $users->getUserInfoFromId( $userId );
			if( !$userInfo ) {
				$errorView = new AdminBlogUsersListView( $this->_blogInfo );
				$errorView->setErrorMessage( $this->_locale->tr("error_invalid_user_id" ));
				$this->setCommonData();
				return( false );
			}
			
			$this->notifyEvent( EVENT_PRE_USER_UPDATE, Array( "user" => &$user ));			
			
			// first revoke all permissions
			$userPerms = new UserPermissions();
			$userPerms->revokePermissions( $userInfo->getId(), $this->_blogInfo->getId());
			
			// and then assign the new ones
			if( is_array( $this->_perms )) {
				foreach( $this->_perms as $val => $permId ) {
		            $perm = new UserPermission( $userInfo->getId(), $this->_blogInfo->getId(), $permId );
		            $res = $userPerms->grantPermission( $perm );
				}
			}
			
			$this->notifyEvent( EVENT_POST_USER_UPDATE, Array( "user" => &$user ));			
			
            $this->_view = new AdminBlogUsersListView( $this->_blogInfo );
            $this->_view->setSuccessMessage( $this->_locale->pr("user_permissions_updated_ok", $userInfo->getUsername()));
            $this->setCommonData();			
			
			return( true );
        }
    }
?>