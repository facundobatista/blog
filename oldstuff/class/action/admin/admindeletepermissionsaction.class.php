<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/permissions.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminpermissionslistview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Deletes an permission
     */
    class AdminDeletePermissionsAction extends AdminAction 
	{

    	var $_permId;
        var $_permIds;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminDeletePermissionsAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			$this->_mode = $actionInfo->getActionParamValue();
        	
			if( $this->_mode == "deletePermission" ) 
					$this->registerFieldValidator( "permId", new IntegerValidator());
			else 
					$this->registerFieldValidator( "permIds", new ArrayValidator( new IntegerValidator()));
				
			$view = new AdminPermissionsListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_incorrect_permission_id"));
			$this->setValidationErrorView( $view );		
			
			$this->requireAdminPermission( "update_permission" );
        }

		/**
		 * @private
		 */
		function _deletePermissions()
		{
            $perms = new Permissions();
			
			$errorMessage = "";
			$successMessage = "";
			$totalOk = 0;
			
            foreach( $this->_permIds as $permId ) {
				// get the permission
            	$perm = $perms->getPermission( $permId );
				
				if( $perm ) {
					// get how many articles it has
					$numUsers = $perm->getNumUsersWithPermission();
										
					// if everything correct, we can proceed and delete it
					if( $numUsers > 0 || $perm->isCorePermission()) {
						$errorMessage .= $this->_locale->pr( "error_permission_cannot_be_deleted", $perm->getName())."<br/>";
					}
					else {									
						// fire the pre-event
						$this->notifyEvent( EVENT_PRE_DELETE_PERMISSION, Array( "permission" => &$perm ));
						
						if( !$perms->deletePermission( $permId ))
							$errorMessage .= $this->_locale->pr("error_deleting_permission", $perm->getName())."<br/>";
						else {
							if( $totalOk < 2 )
								$successMessage .= $this->_locale->pr( "permission_deleted_ok", $perm->getName())."<br/>";
							else
								$successMessage = $this->_locale->pr( "permissions_deleted_ok", $totalOk );
								
							// fire the pre-event
							$this->notifyEvent( EVENT_POST_DELETE_PERMISSION, Array( "permission" => &$perm ));
						}
					}
				}
				else {
					$errorMessage .= $this->_locale->pr("error_deleting_permission2", $permId)."<br/>";
				}
        	}
        				
			// prepare the view and all the information it needs to know
			$this->_view = new AdminPermissionsListView( $this->_blogInfo );
			if( $errorMessage != "" ) 
				$this->_view->setErrorMessage( $errorMessage );
			if( $successMessage != "" ) {
				// and clear the cache to avoid outdated information
				CacheControl::resetBlogCache( $this->_blogInfo->getId(), false );			
				$this->_view->setSuccessMessage( $successMessage );
			}
				
			$this->setCommonData();
			
			return true;
		}

        /**
         * Carries out the specified action
         */
        function perform()
        {
			if( $this->_mode == "deletePermission" ) {
				$this->_permIds = Array();
				$this->_permIds[] = $this->_request->getValue( "permId" );
			}
			else
				$this->_permIds = $this->_request->getValue( "permIds" );
			
            return $this->_deletePermissions();
        }
    }
?>
