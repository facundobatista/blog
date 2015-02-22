<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminsiteuserslistview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * This one only shows some statistics about the site...
     */
    class AdminSiteUsersAction extends AdminAction 
	{

    	function AdminSiteUsersAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->requireAdminPermission( "view_users" );
        }

        function perform()
        {
	        $this->_view = new AdminSiteUsersListView( $this->_blogInfo );
	        $this->setCommonData();

            return true;
        }
    }
?>
