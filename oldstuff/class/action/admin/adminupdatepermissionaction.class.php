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
 	 * Updates an existing permission in the database
     */
    class AdminUpdatePermissionAction extends AdminAction 
	{
		function AdminUpdatePermissionAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// register two validators
			$this->registerFieldValidator( "permissionName", new StringValidator());
			$this->registerFieldValidator( "permissionDescription", new StringValidator());
			$this->registerFieldValidator( "permissionId", new IntegerValidator());
			$this->registerFieldValidator( "corePermission", new IntegerValidator(), true );
			$this->registerFieldValidator( "adminOnlyPermission", new IntegerValidator(), true );			
			// and the view we should show in case there is a validation error
			$errorView = new AdminTemplatedView( $this->_blogInfo, "editpermission" );
			$errorView->setErrorMessage( $this->_locale->tr("error_updating_permission" ));			
			$this->setValidationErrorView( $errorView );
			
			$this->requireAdminPermission( "update_permission" );			
		}
		
        function perform()
        {
			// load the permission			
			$perms = new Permissions();
			$perm = $perms->getPermission( $this->_request->getValue( "permissionId" ));
            $this->_view = new AdminPermissionsListView( $this->_blogInfo );
			
			if( !$perm ) {
				$this->_view->setErrorMessage( $this->_locale->tr("error_fetching_permission" ));			
				$this->setCommonData();
				return( false );
			}
			
			$perm->setName( $this->_request->getValue( "permissionName" ));			
			$perm->setDescription( $this->_request->getValue( "permissionDescription" ));
			$perm->setCorePermission(( $this->_request->getValue( "corePermission" ) == "" ? false : true ));
			$perm->setAdminOnlyPermission(( $this->_request->getValue( "adminOnlyPermission" ) == "" ? false : true ));
			
			if( $perms->updatePermission( $perm )) {
				$this->_view->setSuccessMessage( $this->_locale->tr("permission_updated_ok" ));
				$this->setCommonData();
			}
			else {
				$this->_view->setErrorMessage( $this->_locale->tr("error_updating_permission" ));
				$this->_view->setError( true );
				$this->setCommonData( true );
			}
        }
    }
?>
