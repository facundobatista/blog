<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that adds a new blog category
     */
    class AdminNewBlogCategoryAction extends AdminAction
    {

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminNewBlogCategoryAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->requireAdminPermission( "add_blog_category" );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
        	// initialize the view
        	$this->_view = new AdminTemplatedView( $this->_blogInfo, "newblogcategory" );
            $this->setCommonData();
            return true;
        }
    }
?>