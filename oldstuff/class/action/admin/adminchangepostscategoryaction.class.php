<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminpostslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/cachecontrol.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Massive changes posts categories
     */
    class AdminChangePostsCategoryAction extends AdminAction 
	{

        var $_postIds;
        var $_postCategories;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminChangePostsCategoryAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			$this->registerFieldValidator( "postIds", new ArrayValidator( new IntegerValidator()));
			$this->registerFieldValidator( "postCategories", new ArrayValidator( new IntegerValidator()));
			$view = new AdminPostsListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_incorrect_article_id"));
			$this->setValidationErrorView( $view );		
        }

        /**
         * Carries out the specified action
         */
        function _changePostsCategory()
        {
        	// Chanages the post category field by selection
            $articles = new Articles();
            $errorMessage = "";
			$successMessage = "";
			$totalOk = 0;
			
            foreach( $this->_postIds as $postId ) {
            	// get the post
                $post = $articles->getBlogArticle( $postId, $this->_blogInfo->getId());
				
				if( $post ) {
					// fire the event
					$this->notifyEvent( EVENT_PRE_POST_UPDATE, Array( "article" => &$post ));
					
					// update the post category
					$post->setCategoryIds( $this->_postCategories );
					$result = $articles->updateArticle( $post );
					
					if( !$result ) {
						$errorMessage .= $this->_locale->pr("error_updating_post", $post->getTopic())."<br/>";
					}
					else {
						$totalOk++;
						if( $totalOk < 2 ) 
							$successMessage .= $this->_locale->pr("post_updated_ok", $post->getTopic())."<br/>";
						else
							$successMessage = $this->_locale->pr("posts_updated_ok", $totalOk );
						// fire the post event
						$this->notifyEvent( EVENT_POST_POST_UPDATE, Array( "article" => &$post ));					
					}
				}
				else {
					$errorMessage .= $this->_locale->pr( "error_updating_posts2", $postId )."<br/>";
				}
            }
			
			// clean up the cache
			CacheControl::resetBlogCache( $this->_blogInfo->getId());

			$this->_view = new AdminPostsListView( $this->_blogInfo );
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
			$this->_postIds = $this->_request->getValue( "postIds" );
			$this->_postCategories = $this->_request->getValue( "postCategories" );
				
			$this->_changePostsCategory();
		}
    }
?>