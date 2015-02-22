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
     * Deletes a post from the database
     */
    class AdminDeletePostAction extends AdminAction 
	{

        var $_postIds;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminDeletePostAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// data validation stuff...
			$this->_mode = $actionInfo->getActionParamValue();
			
			if( $this->_mode == "deletePost" )
				$this->registerFieldValidator( "postId", new IntegerValidator());
			else 
				$this->registerFieldValidator( "postIds", new ArrayValidator( new IntegerValidator()));

			$view = new AdminPostsListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_incorrect_article_id"));
			$this->setValidationErrorView( $view );	
			
			$this->requirePermission( "update_post" );	
        }

        /**
         * Carries out the specified action
         */
        function _deletePosts()
        {
        	// delete the post (it is not physically deleted but rather, we set
            // the status field to 'DELETED'
            $articles = new Articles();
            $errorMessage = "";
			$successMessage = "";
			$totalOk = 0;
			
            foreach( $this->_postIds as $postId ) {
            	// get the post
                $post = $articles->getBlogArticle( $postId, $this->_blogInfo->getId());
				
				if( $post ) {
					// fire the event
					$this->notifyEvent( EVENT_PRE_POST_DELETE, Array( "article" => &$post ));
					
					$canDelete = false;
					$userId = 0;
					if( $this->userHasPermission( "update_all_user_articles" ))
						$canDelete = true;				
					else {
			            if( $post->getUserId() != $this->_userInfo->getId()) 
							$canDelete = false;		            
						else
							$canDelete = true;
					}

					if( $canDelete ) 
						$result = $articles->deleteArticle( $postId, $post->getUser(), $this->_blogInfo->getId(), false );
					else {
						$errorMessage .= $this->_locale->tr("error_can_only_update_own_articles")." ";
						$result = false;
					}
					
					if( !$result ) {
						$errorMessage .= $this->_locale->pr("error_deleting_article", $post->getTopic())."<br/>";
					}
					else {
						$totalOk++;
						if( $totalOk < 2 ) 
							$successMessage .= $this->_locale->pr("article_deleted_ok", $post->getTopic())."<br/>";
						else
							$successMessage = $this->_locale->pr("articles_deleted_ok", $totalOk );
						// fire the post event
						$this->notifyEvent( EVENT_POST_POST_DELETE, Array( "article" => &$post ));					
					}
				}
				else {
					$errorMessage .= $this->_locale->pr( "error_deleting_article2", $postId )."<br/>";
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
			if( $this->_mode == "deletePost" ) {
				$this->_postIds = Array();
				$this->_postIds[] = $this->_request->getValue( "postId" );
			}
			else
				$this->_postIds = $this->_request->getValue( "postIds" );
				
			$this->_deletePosts();
		}
    }
?>