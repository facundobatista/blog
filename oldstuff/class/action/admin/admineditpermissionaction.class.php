<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminpermissionslistview.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/dao/permissions.class.php" );	

    /**
     * \ingroup Action
     * @private
 	 *
 	 * Adds a new permission to the database
     */
    class AdminEditPermissionAction extends AdminAction 
	{
		function AdminEditPermissionAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// register two validators
			$this->registerFieldValidator( "permId", new IntegerValidator());
			$errorView = new AdminPermissionsListView( $this->_blogInfo );
			$errorView->setErrorMessage( $this->_locale->tr("error_fetching_permission" ));			
			$this->setValidationErrorView( $errorView );
			
			$this->requireAdminPermission( "update_permission" );
		}
		
        function perform()
        {
			// add the permission and check success
			$perms = new Permissions();			
			$perm = $perms->getPermission( $this->_request->getValue( "permId" ));
			
			if( !$perm ) {
				$this->_view = new AdminPermissionsListView( $this->_blogInfo );
				$this->_view->setErrorMessage( $this->_locale->tr("error_fetching_permission" ));			
				$this->setCommonData();
				return( false );
			}

			$this->_view = new AdminTemplatedView( $this->_blogInfo, "editpermission" );
			$this->_view->setValue( "permissionName", $perm->getName());
			$this->_view->setValue( "permissionDescription", $perm->getDescription());
			$this->_view->setValue( "permissionId", $perm->getId());
			$this->_view->setValue( "corePermission", $perm->isCorePermission());
			$this->_view->setValue( "adminOnlyPermission", $perm->isAdminOnlyPermission());
			$this->setCommonData();
			
			return( true );
        }
    }
?>
