<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminsiteblogslistview.class.php" );

    /**
     * \ingroup Action
     * @private
	 *
	 * Sets everything so that admins are allowed to log in into anybody's blog
     */
    class AdminAdminBlogSelectAction extends AdminAction 
	{
	
		var $_blogId;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminAdminBlogSelectAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// validate the only field we're expecting, the blog id
			$this->registerFieldValidator( "blogId", new IntegerValidator());
			$view = new AdminSiteBlogsListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr( "error_incorrect_blog_id" ));
			$this->setValidationErrorView( $view );			
			
			$this->requireAdminPermission( "edit_blog_admin_mode" );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
			$this->_blogId = $this->_request->getValue( "blogId" );
		
			// load the blog
            lt_include( PLOG_CLASS_PATH . "class/dao/blogs.class.php" );
			$blogs = new Blogs();
            $blogInfo = $blogs->getBlogInfo( $this->_blogId );
			 
			// check if the blog really exists
			if( !$blogInfo ) {
				$this->_view = new AdminSiteBlogsListView( $this->_blogInfo );
				$this->_view->setValue( "message", $this->_locale->tr("error_incorrect_blog_id" ));
				$this->setCommonData();			
				return false;		 			 
			}
			
            // if all correct, we can now set the blogInfo object in the session for later use
            $this->_session->setValue( "blogInfo", $blogInfo );
            $session = HttpVars::getSession();
            $session["SessionInfo"] = $this->_session;
            $session["SessionInfo"]->setValue( "blogId", $blogInfo->getId() );
            HttpVars::setSession( $session );
			
			lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );
	        $this->_nextAction = $this->_request->getFilteredValue( "action", new HtmlFilter() );

			if ( $this->_nextAction && AdminController::checkActionExist( $this->_nextAction ) ) {
				AdminController::setForwardAction( $this->_nextAction );
		    } else {
				AdminController::setForwardAction( "newPost" );
			}
			
            // better to return true if everything fine
            return true;
        }
    }
?>
