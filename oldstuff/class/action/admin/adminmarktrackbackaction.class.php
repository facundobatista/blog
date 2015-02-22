<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/trackbacks.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
    lt_include( PLOG_CLASS_PATH."class/bayesian/bayesianfiltercore.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminpostslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminarticletrackbackslistview.class.php" );
	
    /**
     * \ingroup Action
     * @private
     *     
	 * sets the spam status for a post
     */
    class AdminMarkTrackbackAction extends AdminAction
    {

    	var $_trackbackId;
        var $_articleId;
        var $_mode;
		var $_article;
		var $_comment;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminMarkTrackbackAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// data validation
			$this->registerFieldValidator( "trackbackId", new IntegerValidator());
			$this->registerFieldValidator( "articleId", new IntegerValidator());
			$this->registerFieldValidator( "mode", new IntegerValidator());
			$view = new AdminPostsListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_incorrect_trackback_id"));
			$this->setValidationErrorView( $view );
			
			$this->requirePermission( "view_trackbacks" );			
        }

        /**
         * @private
         * Returns true wether the comment whose status we're trying to change
         * really belongs to this blog, just in case somebody's trying to mess
         * around with that...
         */
        function _checkTrackback( $trackbackId, $articleId, $blogId )
        {
        	$trackbacks = new Trackbacks();
            $articles = new Articles();

            // fetch the comment
            $this->_trackback = $trackbacks->getTrackBack( $trackbackId );
            if( !$this->_trackback )
            	return false;

            // fetch the article
            $this->_article = $articles->getBlogArticle( $this->_trackback->getArticleId(), $blogId );
            if( !$this->_article )
            	return false;

            return true;
        }

        /**
         * @private
         */
        function _markTrackbackAsSpam()
        {
			// throw the pre-event
			$this->notifyEvent( EVENT_PRE_MARK_SPAM_TRACKBACK, Array( "trackbackId" => $this->_trackbackId ));
			
           	$this->_view = new AdminArticleTrackbacksListView( $this->_blogInfo, Array( "article" => $this->_article ));
			
        	$trackbacks = new Trackbacks();
        	$this->_trackback->setStatus( COMMENT_STATUS_SPAM );
            if( !$trackbacks->updateComment( $this->_trackback )) {
                $this->_view->setErrorMessage( $this->_locale->tr("error_marking_trackback_as_spam" ));
                $this->setCommonData();
				
				$res = false;
            }
            else {
                $this->_view->setSuccessMessage( $this->_locale->tr("trackback_marked_as_spam_ok" ));				
                $this->setCommonData();
				
                $res = true;

                // before exiting, we should get the comment and train the filter
                // to recognize this as spam...
                $trackback = $trackbacks->getTrackBack( $this->_trackbackId );
                $bayesian = new BayesianFilterCore();

                $bayesian->untrain( $this->_blogInfo->getId(),
	                                $trackback->getTopic(),
	                                $trackback->getText(),
	                                $trackback->getUserName(),
	                                $trackback->getUserEmail(),
	                                $trackback->getUserUrl(),
	                                false );
                                  
                $bayesian->train( $this->_blogInfo->getId(),
                                  $trackback->getTopic(),
                                  $trackback->getText(),
                                  $trackback->getUserName(),
                                  $trackback->getUserEmail(),
                                  $trackback->getUserUrl(),
                                  true );
								  
				// throw the post-event if everythign went fine
				$this->notifyEvent( EVENT_POST_MARK_SPAM_TRACKBACK, Array( "trackbackId" => $this->_trackbackId ));								  
            }

            return $res;
        }

        /**
         * @private
         */
        function _markTrackbackAsNonSpam()
        {
			// throw the pre-event
			$this->notifyEvent( EVENT_PRE_MARK_NO_SPAM_TRACKBACK, Array( "trackbackId" => $this->_trackbackId ));
		
           	$this->_view = new AdminArticleTrackbacksListView( $this->_blogInfo, Array( "article" => $this->_article ));
		
        	$trackbacks = new Trackbacks();
        	$this->_trackback->setStatus( COMMENT_STATUS_NONSPAM );
            if( !$trackbacks->updateComment( $this->_trackback )) {
                $this->_view->setErrorMessage( $this->_locale->tr("error_marking_trackback_as_nonspam" ));
                $this->setCommonData();
				
				$res = false;
            }
            else {
                $this->_view->setSuccessMessage( $this->_locale->tr("trackback_marked_as_nonspam_ok" ));				
                $this->setCommonData();
				
                $res = true;

                // before exiting, we should get the comment and train the filter
                // to recognize this as spam...
                $trackback = $trackbacks->getTrackBack( $this->_trackbackId, $this->_articleId );
                $bayesian = new BayesianFilterCore();
                
                $bayesian->untrain( $this->_blogInfo->getId(),
	                                $trackback->getTopic(),
	                                $trackback->getText(),
	                                $trackback->getUserName(),
	                                $trackback->getUserEmail(),
	                                $trackback->getUserUrl(),
	                                true );
                                  
                $bayesian->train( $this->_blogInfo->getId(),
                                  $trackback->getTopic(),
                                  $trackback->getText(),
                                  $trackback->getUserName(),
                                  $trackback->getUserEmail(),
                                  $trackback->getUserUrl(),
                                  false );
								  
				// throw the post-event if everythign went fine
				$this->notifyEvent( EVENT_POST_MARK_NO_SPAM_TRACKBACK, Array( "trackbackId" => $this->_trackbackId ));
            }

            return $res;
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
			// fetch the data

        	$this->_trackbackId = $this->_request->getValue( "trackbackId" );
            $this->_articleId = $this->_request->getValue( "articleId" );
            $this->_mode = $this->_request->getValue( "mode" );		
		
        	// first, let's make sure that the user is trying to edit the right
            // comment...
            if( !$this->_checkTrackback( $this->_trackbackId, $this->_articleId, $this->_blogInfo->getId())) {
            	// if things don't match... (like trying to set the status of an article
                // from another blog, then quit...)				
            	$this->_view = new AdminPostsListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_incorrect_trackback_id"));
				$this->setCommonData();
                return false;
            }

        	// depending on the mode, we have to do one thing or another
            if( $this->_mode == 0 )
            	$result = $this->_markTrackbackAsNonSpam();
            else
            	$result = $this->_markTrackbackAsSpam();
				
			// clear the cache
			CacheControl::resetBlogCache( $this->_blogInfo->getId());

            // better to return true if everything fine
            return $result;
        }
    }
?>
