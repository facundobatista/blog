<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminsiteblogslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admineditsiteblogview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows a form to change the settings of a blog.
     */
    class AdminEditBlogAction extends AdminAction 
	{

    	var $_editBlogId;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminEditBlogAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// data validation
			$this->registerFieldValidator( "blogId", new IntegerValidator());
			$view = new AdminSiteBlogsListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_incorrect_blog_id" ));
			$this->setValidationErrorView( $view );
			
			$this->requireAdminPermission( "update_site_blog" );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
        	// get the blog and its settings
        	$this->_editBlogId = $this->_request->getValue( "blogId" );			
            $blogs = new Blogs();
            $blogInfo = $blogs->getBlogInfo( $this->_editBlogId);

            if( !$blogInfo ) {
            	$this->_view = new AdminSiteBlogsListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_incorrect_blog_id" ));
                $this->setCommonData();
                return false;
            }

			$this->notifyEvent( EVENT_BLOG_LOADED, Array( "blog" => &$blogInfo ));

			// create the view and render the contents
        	$this->_view = new AdminEditSiteBlogView( $this->_blogInfo, $blogInfo );
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>
