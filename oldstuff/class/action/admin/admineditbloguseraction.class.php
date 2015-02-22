<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminbloguserslistview.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/dao/permissions.class.php" );		

    /**
     * \ingroup Action
     * @private
     */
    class AdminEditBlogUserAction extends AdminAction 
	{	
    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminEditBlogUserAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// register one validator
			$this->registerFieldValidator( "userId", new IntegerValidator());
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
			
			// load the user
			$users = new Users();
			$userInfo = $users->getUserInfoFromId( $userId );
			if( !$userInfo ) {
				$errorView = new AdminBlogUsersListView( $this->_blogInfo );
				$errorView->setErrorMessage( $this->_locale->tr("error_invalid_user_id" ));
				$this->setCommonData();
				return( false );
			}

			// pass all the information to the view
			$this->_view = new AdminTemplatedView( $this->_blogInfo, "editbloguser" );
			$this->_view->setValue( "edituser", $userInfo );			
	        $perms = new Permissions();
			$this->_view->setValue( "perms", $perms->getAllPermissions());			
			$this->setCommonData();
			
			return( true );
        }
    }
?>