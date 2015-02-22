<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );

    /**
     * \ingroup Action
     * @private
 	 *
 	 * Shows the form to add a new permission
     */
    class AdminNewPermissionAction extends AdminAction 
	{
		function AdminNewPermissionAction( $actionInfo, $request )
		{
			$this->AdminAction( $actionInfo, $request );
			
			$this->requireAdminPermission( "add_permission" );
		}
		
        function perform()
        {
			$this->_view = new AdminTemplatedView( $this->_blogInfo, "newpermission" );
			$this->setCommonData();
        }
    }
?>
