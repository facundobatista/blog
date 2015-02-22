<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admincreateblogview.class.php" );

    /**
     * \ingroup Action
     * @private
     */
    class AdminCreateBlogAction extends AdminAction 
	{

    	function AdminCreateBlogAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->requireAdminPermission( "add_site_blog" );
        }

        function perform()
        {
	        $this->_view = new AdminCreateBlogView( $this->_blogInfo );
            $this->setCommonData();

            return true;
        }
    }
?>