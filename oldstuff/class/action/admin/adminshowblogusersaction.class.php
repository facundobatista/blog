<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminbloguserslistview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Shows the list of users that belong to this blog
     */
    class AdminShowBlogUsersAction extends AdminAction 
	{

    	function AdminShowBlogUsersAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->requirePermission( "view_blog_users" );
        }

        function perform()
        {
			// load the right view... and that's it :-)
			$this->_view = new AdminBlogUsersListView( $this->_blogInfo );
            $this->setCommonData();

            return true;
        }
    }
?>