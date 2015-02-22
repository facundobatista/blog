<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminsiteblogslistview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Shows a list with all the blogs available in the site
     */
    class AdminSiteBlogsAction extends AdminAction 
	{

    	function AdminSiteBlogsAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->requireAdminPermission( "view_site_blogs" );
        }
        
        function perform()
        {
	        $this->_view = new AdminSiteBlogsListView( $this->_blogInfo );
	        $this->setCommonData();
	        
            return true;
        }
    }
?>