<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminpermissionslistview.class.php" );

    /**
     * \ingroup Action
     * @private
 	 *
 	 * Lists all the permissions available
     */
    class AdminPermissionsListAction extends AdminAction 
	{
		
		function AdminPermissionsListAction( $actionInfo, $request )
		{
			$this->AdminAction( $actionInfo, $request );
			
			$this->requireAdminPermission( "view_permissions" );
		}
		
        function perform()
        {
			$this->_view = new AdminPermissionsListView( $this->_blogInfo );
			$this->setCommonData();
        }
    }
?>
