<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminsiteblogslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/cachecontrol.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Implements bulk changes of blogs
     */
    class AdminChangeBlogStatusAction extends AdminAction 
	{

        var $_blogIds;
        var $_blogStatus;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminChangeBlogStatusAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			$this->registerFieldValidator( "blogIds", new ArrayValidator( new IntegerValidator()));
			$this->registerFieldValidator( "blogStatus", new IntegerValidator() );
			$view = new AdminSiteBlogsListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_incorrect_blog_id"));
			$this->setValidationErrorView( $view );
			
			$this->requireAdminPermission( "update_site_blog" );	
        }

        /**
         * Carries out the specified action
         */
        function _changeBlogsStatus()
        {
        	// Chanages the post status field by selection
            $blogs = new Blogs();
            $errorMessage = "";
			$successMessage = "";
			$totalOk = 0;
			
            foreach( $this->_blogIds as $blogId ) {
            	// get the post
                $blog = $blogs->getBlogInfo( $blogId );
				
				if( $blog ) {
					// fire the event
					$this->notifyEvent( EVENT_PRE_BLOG_UPDATE, Array( "blog" => &$blog ));
					
					// update the post status
					$blog->setStatus( $this->_blogStatus );
					$result = $blogs->updateBlog( $blog );
					
					if( !$result ) {
						$errorMessage .= $this->_locale->pr("error_updating_blog", $blog->getBlog())."<br/>";
					}
					else {
						$totalOk++;
						if( $totalOk < 2 ) 
							$successMessage .= $this->_locale->pr("blog_updated_ok", $blog->getBlog())."<br/>";
						else
							$successMessage = $this->_locale->pr("blogs_updated_ok", $totalOk );
						// fire the post event
						$this->notifyEvent( EVENT_POST_BLOG_UPDATE, Array( "article" => &$blog ));					
					}
				}
				else {
					$errorMessage .= $this->_locale->pr( "error_updating_blog2", $blogId )."<br/>";
				}
            }
			
			// clean up the cache
			CacheControl::resetBlogCache( $this->_blogInfo->getId());

			$this->_view = new AdminSiteBlogsListView( $this->_blogInfo );
			if( $errorMessage != "" ) 
				$this->_view->setErrorMessage( $errorMessage );
			if( $successMessage != "" )
				$this->_view->setSuccessMessage( $successMessage );
				
			$this->setCommonData();
			
            return true;
        }
		
		function perform()
		{
			// prepare the parameters.. If there's only one category id, then add it to
			// an array.
			$this->_blogIds = $this->_request->getValue( "blogIds" );
			$this->_blogStatus = $this->_request->getValue( "blogStatus" );
				
			$this->_changeBlogsStatus();
		}
    }
?>