<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminnewbloguserview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Shows a form to add a user to the blog
     */
    class AdminNewBlogUserAction extends AdminAction 
    {

    	function AdminNewBlogUserAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->requirePermission( "add_blog_user" );
        }

        function perform()
        {
        	$this->_view = new AdminNewBlogUserView( $this->_blogInfo );
            $this->setCommonData();

            return true;
        }
    }
?>