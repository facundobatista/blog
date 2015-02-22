<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminpermissionslistview.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );		
	lt_include( PLOG_CLASS_PATH."class/dao/permissions.class.php" );	

    /**
     * \ingroup Action
     * @private
 	 *
 	 * Adds a new permission to the database
     */
    class AdminAddPermissionAction extends AdminAction 
	{
		function AdminAddPermissionAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// register two validators
			$this->registerFieldValidator( "permissionName", new StringValidator());
			$this->registerFieldValidator( "permissionDescription", new StringValidator());
			$this->registerFieldValidator( "corePermission", new IntegerValidator(), true );
			$this->registerFieldValidator( "adminOnlyPermission", new IntegerValidator(), true );			
			// and the view we should show in case there is a validation error
			$errorView = new AdminTemplatedView( $this->_blogInfo, "newpermission" );
			$errorView->setErrorMessage( $this->_locale->tr("error_adding_permission" ));			
			$this->setValidationErrorView( $errorView );
			
			$this->requireAdminPermission( "add_permission" );			
		}
		
        function perform()
        {
			// add the permission and check success
			$perm = new Permission( 
				$this->_request->getValue( "permissionName"), 
				$this->_request->getValue( "permissionDescription" )
			);
			if( $this->_request->getValue( "corePermission" ) == 1 )
				$perm->setCorePermission( true );
			if( $this->_request->getValue( "adminOnlyPermission" ) == 1 )
				$perm->setAdminOnlyPermission( true );
			
			$perms = new Permissions();
			$this->notifyEvent( EVENT_PRE_PERMISSION_ADD, Array( "permission" => &$perm ));			
			if( $perms->addPermission( $perm )) {
				$this->notifyEvent( EVENT_POST_PERMISSION_ADD, Array( "permission" => &$perm ));
				if( $this->_userInfo->hasPermissionByName( "view_permissions", 0 )) 
					$this->_view = new AdminPermissionsListView( $this->_blogInfo );
				else
					$this->_view = new AdminTemplatedView( $this->_blogInfo, "newpermission" );
				$this->_view->setSuccessMessage( $this->_locale->tr("permission_added_ok" ));
				$this->setCommonData();
			}
			else {
				$this->_view->setErrorMessage( $this->_locale->tr("error_adding_permission" ));
				$this->_view->setError( true );
				$this->setCommonData( true );
			}
        }
    }
?>
