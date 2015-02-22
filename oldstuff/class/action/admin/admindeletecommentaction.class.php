<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminarticlecommentslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );	
    lt_include( PLOG_CLASS_PATH."class/dao/articlecomments.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows a list of all the comments for a given post
     */
    class AdminDeleteCommentAction extends AdminAction 
	{

    	var $_articleId;
        var $_commentIds;
		var $_mode;        

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminDeleteCommentAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			$this->_mode = $actionInfo->getActionParamValue();
			$this->registerFieldValidator( "articleId", new IntegerValidator());
			if( $this->_mode == "deleteComment" )
				$this->registerFieldValidator( "commentId", new IntegerValidator());
			else
				$this->registerFieldValidator( "commentIds", new ArrayValidator( new IntegerValidator()));
				
			$view = new AdminArticleCommentsListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_deleting_comments"));
			$this->setValidationErrorView( $view );

			$this->requirePermission( "update_comment" );			
        }
		
		/**
		 * sets up the parameters and calls the method below
		 */
		function perform()
		{
			$this->_articleId = $this->_request->getValue( "articleId" );
			if( $this->_mode == "deleteComment" ) {
				$commentId = $this->_request->getValue( "commentId" );
				$this->_commentIds = Array();
				$this->_commentIds[] = $commentId;
			}
			else
				$this->_commentIds = $this->_request->getValue( "commentIds" );
				
			$this->_deleteComments();
			
			return true;
		}

        /**
         * deletes comments
		 * @private
         */
        function _deleteComments()
        {
            $comments = new ArticleComments();
            $errorMessage = "";
			$successMessage = "";
			$totalOk = 0;
			
			if( $this->_articleId > 0 ) {
				// if we can't even load the article, then forget it...
				$articles = new Articles();
				$article = $articles->getBlogArticle( $this->_articleId, $this->_blogInfo->getId());
				if( !$article ) {
					$this->_view = new AdminArticleCommentsListView( $this->_blogInfo );
					$this->_view->setErrorMessage( $this->_locale->tr("error_fetching_article" ));
					$this->setCommonData();
					
					return false;
				}
			}
			else {
				// there was no article, so this probably was the view that shows all comments...
				$article = null;
			}
			
			// loop through the comments and remove them
            foreach( $this->_commentIds as $commentId ) {
            	// fetch the comment
				$comment = $comments->getComment( $commentId );
				
				if( !$comment ) {
					$errorMessage .= $this->_locale->pr("error_deleting_comment2", $commentId);
				}
				else {
					// fire the pre-event
					$this->notifyEvent( EVENT_PRE_COMMENT_DELETE, Array( "comment" => &$comment ));
					
					// check if the comment really belongs to this blog...
					$article = $comment->getArticle();
                    if(!($topic = $comment->getTopic()))
                        $topic = $this->_locale->tr("comment_default_title");
					if( $article->getBlogId() != $this->_blogInfo->getId()) {
						// if not, then we shouldn't be allowed to remove anything!						
						$errorMessage .= $this->_locale->pr("error_deleting_comment", $topic)."<br/>";
					}
					else {
						if( !$comments->deleteComment( $commentId ))
							$errorMessage .= $this->_locale->pr("error_deleting_comment", $topic)."<br/>";
						else {
							$totalOk++;
							if( $totalOk < 2 )
								$successMessage .= $this->_locale->pr("comment_deleted_ok", $topic)."<br/>";
							else
								$successMessage = $this->_locale->pr("comments_deleted_ok", $totalOk );
							
							// fire the post-event
							$this->notifyEvent( EVENT_POST_COMMENT_DELETE, Array( "comment" => &$comment ));
						}
					}
				}
            }

			// if everything fine, then display the same view again with the feedback
			if( $this->_articleId == 0 )
				$this->_view = new AdminArticleCommentsListView( $this->_blogInfo, Array( "article" => null ));
			else
				$this->_view = new AdminArticleCommentsListView( $this->_blogInfo, Array( "article" => $article ));
				
			if( $successMessage != "" ) {
				$this->_view->setSuccessMessage( $successMessage );
				// clear the cache
				CacheControl::resetBlogCache( $this->_blogInfo->getId());
			}
			if( $errorMessage != "" ) $this->_view->setErrorMessage( $errorMessage );
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>