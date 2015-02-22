<?php

	lt_include( PLOG_CLASS_PATH."class/action/blogaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/blogview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/errorview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Adds a comment
     */
	class CommentAction extends BlogAction 
	{

    	var $_articleId;
        var $_parentId;

		function ViewArticleAction( $actionInfo, $request )
        {
			$this->BlogAction( $actionInfo, $request );
			
			// data validation
			$this->registerFieldValidator( "articleId", new IntegerValidator());
			$this->registerFieldValidator( "parentId", new IntegerValidator());			
            $this->setValidationErrorView( new ErrorView( $this->_blogInfo, "error_incorrect_article_id" ));
        }

        function perform()
        {
			// fetch the data and make some arrangements if needed
        	$this->_parentId  = $this->_request->getValue( "parentId" );
            $this->_articleId = $this->_request->getValue( "articleId" );	
					
            if( $this->_parentId < 0 || $this->_parentId == "" )
            	$this->_parentId = 0;
				
            // check if comments are enabled
            $blogSettings = $this->_blogInfo->getSettings();
            if( !$blogSettings->getValue( "comments_enabled" )) {
            	$this->_view = new ErrorView( $this->_blogInfo, "error_comments_not_enabled" );
                $this->setCommonData();

                return false;
            }									

            // fetch the article
            $blogs    = new Blogs();
            $articles = new Articles();
            $article  = $articles->getBlogArticle( $this->_articleId, $this->_blogInfo->getId());
			
            // if there was a problem fetching the article, we give an error and quit
            if( $article == false ) {
            	$this->_view = new ErrorView( $this->_blogInfo );
                $this->_view->setValue( "message", "error_fetching_article" );
                $this->setCommonData();
                return false;
            }			

            if(!$article->getCommentsEnabled()) {
                    // NOTE: this is a slightly wrong error message, but why is someone
                    // clicking on a "reply" link if comments aren't enabled anyway?
                $this->_view = new ErrorView( $this->_blogInfo, "error_comments_not_enabled" );
                $this->setCommonData();
                return false;
            }									
            
        	$this->_view = new BlogView( $this->_blogInfo, "commentarticle", SMARTY_VIEW_CACHE_CHECK, 
											   Array( "articleId" => $this->_articleId, "parentId" => $this->_parentId ));
			// do nothing if the view was already cached
			if( $this->_view->isCached()){
                $this->setCommonData();
				return true;
            }

                // if we got this far, comments are allowed
			$this->_view->setValue( "allowComments", true );
            
			// fetch the comments so far
            lt_include( PLOG_CLASS_PATH."class/dao/articlecomments.class.php" );
			$comments = new ArticleComments();
			$postComments = $comments->getPostComments( $article->getId());
			if( $this->_parentId > 0 ) {
				// get a pre-set string for the subject field, for those users interested
				$comment = $comments->getComment( $this->_parentId );
				// create the string
				if( $comment ) {
					$replyString = $this->_locale->tr("reply_string").$comment->getTopic();
					$this->_view->setValue( "comment", $comment );
				}
			}

            // if everything's fine, we set up the article object for the view
            $this->_view->setValue( "post", $article );
            $this->_view->setValue( "parentId", $this->_parentId );
			$this->_view->setValue( "comments", $postComments );
			$this->_view->setValue( "postcomments", $postComments );
			$this->_view->setValue( "topic", $replyString );			

            $this->setCommonData();

            // and return everything normal
            return true;
        }
    }
?>
