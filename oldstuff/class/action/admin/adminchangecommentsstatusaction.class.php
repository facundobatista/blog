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
    class AdminChangeCommentsStatusAction extends AdminAction 
	{

    	var $_articleId;
        var $_commentIds;
        var $_commentStatus;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminChangeCommentsStatusAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			$this->registerFieldValidator( "articleId", new IntegerValidator());
			$this->registerFieldValidator( "commentIds", new ArrayValidator( new IntegerValidator()));
			$this->registerFieldValidator( "commentStatus", new IntegerValidator());
			$view = new AdminArticleCommentsListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_updating_comments"));
			$this->setValidationErrorView( $view );
        }
		
		/**
		 * sets up the parameters and calls the method below
		 */
		function perform()
		{
			$this->_articleId = $this->_request->getValue( "articleId" );
			$this->_commentIds = $this->_request->getValue( "commentIds" );
			$this->_commentStatus = $this->_request->getValue( "commentStatus" );
				
			$this->_changeComments();
			
			return true;
		}

        /**
         * changes comments status
		 * @private
         */
        function _changeComments()
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
			
			// loop through the comments and change them
            foreach( $this->_commentIds as $commentId ) {
            	// fetch the comment
				$comment = $comments->getComment( $commentId );
				
				if( !$comment ) {
					$errorMessage .= $this->_locale->pr("error_updating_comment_no_comment", $commentId);
				}
				else {
					// fire the pre-event
					$this->notifyEvent( EVENT_PRE_COMMENT_UPDATE, Array( "comment" => &$comment ));
					
					// check if the comment really belongs to this blog...
					$article = $comment->getArticle();
					if( $article->getBlogId() != $this->_blogInfo->getId()) {
						// if not, then we shouldn't be allowed to change anything!						
						$errorMessage .= $this->_locale->pr("error_updating_comment_wrong_blog", $comment->getTopic())."<br/>";
					}
					else
					{
						$preCommentStatus = $comment->getStatus();
						
						if ( $preCommentStatus == $this->_commentStatus )
						{
							$errorMessage .= $this->_locale->pr("error_updating_comment_already_updated", $comment->getTopic())."<br/>";
							continue;
						}

						$comment->setStatus( $this->_commentStatus );
						if( !$comments->updateComment( $comment ))
							$errorMessage .= $this->_locale->pr("error_updating_comment", $comment->getTopic())."<br/>";
						else {
							if( $this->_commentStatus == COMMENT_STATUS_SPAM )
							{
								$this->_markCommentAsSpam($comment);
							}
							elseif( $this->_commentStatus == COMMENT_STATUS_NONSPAM )
							{
								$this->_markCommentAsNonSpam($comment);
							}

							$totalOk++;
							if( $totalOk < 2 )
								$successMessage .= $this->_locale->tr("comment_updated_ok")."<br/>";
							else
								$successMessage = $this->_locale->pr("comments_updated_ok", $totalOk );
							
							// fire the post-event
							$this->notifyEvent( EVENT_POST_COMMENT_UPDATE, Array( "comment" => &$comment ));
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
        
        function _markCommentAsSpam( $comment )
        {
			// throw the pre-event
			$this->notifyEvent( EVENT_PRE_MARK_SPAM_COMMENT, Array( "commentId" => $comment->getId() ));

	        // We should get the comment and train the filter to recognize this as spam...
       	    lt_include( PLOG_CLASS_PATH."class/bayesian/bayesianfiltercore.class.php" );
       	    $bayesian = new BayesianFilterCore();
	
	        $bayesian->untrain( $this->_blogInfo->getId(),
		                        $comment->getTopic(),
		                        $comment->getText(),
		                        $comment->getUserName(),
		                        $comment->getUserEmail(),
		                        $comment->getUserUrl(),
		                        false );
	                                  
	        $bayesian->train( $this->_blogInfo->getId(),
	                          $comment->getTopic(),
	                          $comment->getText(),
	                          $comment->getUserName(),
	                          $comment->getUserEmail(),
	                          $comment->getUserUrl(),
	                          true );
									  
			// throw the post-event if everythign went fine
			$this->notifyEvent( EVENT_POST_MARK_SPAM_COMMENT, Array( "commentId" => $comment->getId() ));								  
        }
        
        /**
         * @private
         */
        function _markCommentAsNonSpam( $comment )
        {
			// throw the pre-event
			$this->notifyEvent( EVENT_PRE_MARK_NO_SPAM_COMMENT, Array( "commentId" => $comment->getId() ));
		
            // we should get the comment and train the filter
       	    lt_include( PLOG_CLASS_PATH."class/bayesian/bayesianfiltercore.class.php" );
       	    $bayesian = new BayesianFilterCore();
               
            $bayesian->untrain( $this->_blogInfo->getId(),
                                $comment->getTopic(),
                                $comment->getText(),
                                $comment->getUserName(),
                                $comment->getUserEmail(),
                                $comment->getUserUrl(),
                                true );
	                                  
            $bayesian->train( $this->_blogInfo->getId(),
                              $comment->getTopic(),
                              $comment->getText(),
                              $comment->getUserName(),
                              $comment->getUserEmail(),
                              $comment->getUserUrl(),
                              false );

			// throw the post-event if everythign went fine
			$this->notifyEvent( EVENT_POST_MARK_NO_SPAM_COMMENT, Array( "commentId" => $comment->getId() ));
        }
    }
?>
