<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminsiteblogslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );

    /**
     * \ingroup Action
     * @private
     *
	 * it does not delete blogs from the system but simply set them to disabled
	 */
	class AdminDeleteBlogAction extends AdminAction
	{
		var $_op;
		var $_blogIds;

    	function AdminDeleteBlogAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			// set up the data validation stuff
        	$this->_op = $actionInfo->getActionParamValue();
        	if( $this->_op == "deleteBlogs" )
        		$this->registerFieldValidator( "blogIds", new ArrayValidator( new IntegerValidator()));
        	else
        		$this->registerFieldValidator( "blogId", new IntegerValidator());
        	$view = new AdminSiteBlogsListView( $this->_blogInfo );
        	$view->setErrorMessage( $this->_locale->tr("error_no_blogs_selected"));
        	$this->setValidationErrorView( $view );

			$this->requireAdminPermission( "update_site_blog" );
        }

        function perform()
        {
        	if( $this->_op == "deleteBlog" ) {
        		$this->_blogIds = Array();
        		$blogId = $this->_request->getValue( "blogId" );
        		$this->_blogIds[] = $blogId;
        	}
        	else
        		$this->_blogIds = $this->_request->getValue( "blogIds" );

        	$this->_disableBlogs();
        }
        
//        function disableBlog( $blogIn

		/**
		 * @private
		 */
        function _disableBlogs()
        {
        	// get the default blog id
        	$config =& Config::getConfig();
            $defaultBlogId = $config->getValue( "default_blog_id" );

        	$errorMessage = "";
        	$successMessage = "";
        	$totalOk = 0;

            $blogs = new Blogs();
        	foreach( $this->_blogIds as $blogId ) {
            	// get some info about the blog before deleting it
                $blogInfo = $blogs->getBlogInfo( $blogId );
				if( !$blogInfo ) {
					$errorMessage .= $this->_locale->pr("error_deleting_blog2", $blogId)."<br/>";
				}
				else {
					$this->notifyEvent( EVENT_PRE_BLOG_DELETE, Array( "blog" => &$blogInfo ));
					// make sure we're not deleting the default one!
					if( $defaultBlogId == $blogId ) {
						$errorMessage .= $this->_locale->pr("error_blog_is_default_blog", $blogInfo->getBlog())."<br />";
					}
					else {
					   // disable the blog
					    $blogInfo->setStatus( BLOG_STATUS_DISABLED );
						if( $blogs->updateBlog( $blogInfo )) {
							$totalOk++;						
							if( $totalOk < 2 )
								$successMessage = $this->_locale->pr("blog_deleted_ok", $blogInfo->getBlog());
							else
								$successMessage = $this->_locale->pr( "blogs_deleted_ok", $totalOk );
							
							$this->notifyEvent( EVENT_POST_BLOG_DELETE, Array( "blog" => &$blogInfo ));
						}
						else
							$errorMessage .= $this->_locale->pr("error_deleting_blog", $blogInfo->getBlog())."<br/>";
					}
				}
			}

            $this->_view = new AdminSiteBlogsListView( $this->_blogInfo );
            if( $errorMessage != "" ) $this->_view->setErrorMessage( $errorMessage );
            if( $successMessage != "" ) $this->_view->setSuccessMessage( $successMessage );
            $this->setCommonData();

            return true;
        }
    }
?>
