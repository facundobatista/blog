<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminarticletrackbackslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );	
    lt_include( PLOG_CLASS_PATH."class/dao/trackbacks.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Allows to remove trackbacks from a certain article
     */
    class AdminChangeTrackbacksStatusAction extends AdminAction 
	{

    	var $_articleId;
        var $_trackbackIds;
		var $_trackbackStatus;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminChangeTrackbacksStatusAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			$this->registerFieldValidator( "articleId", new IntegerValidator());
			$this->registerFieldValidator( "trackbackIds", new ArrayValidator( new IntegerValidator())); 
			$this->registerFieldValidator( "trackbackStatus", new IntegerValidator());
			$view = new AdminArticleTrackbacksListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_updating_trackbacks"));
			$this->setValidationErrorView( $view );
        }

        /**
         * Carries out the specified action
         */
		function perform()
		{
			$this->_articleId = $this->_request->getValue( "articleId" );
			$this->_trackbackIds = $this->_request->getValue( "trackbackIds" );
			$this->_trackbackStatus = $this->_request->getValue( "trackbackStatus" );			
				
			$this->_changeTrackbacks();
			
			return true;
		}
		 
		/**
         * change trackbacks status
		 * @private
		 */
        function _changeTrackbacks()
        {
            $trackbacks = new Trackbacks();
            $errorMessage = "";
			$successMessage = "";
			$totalOk = 0;
			
			if( $this->_articleId > 0 ) {
				// if we can't even load the article, then forget it...
				$articles = new Articles();
				$article = $articles->getBlogArticle( $this->_articleId, $this->_blogInfo->getId());
				if( !$article ) {
					$this->_view = new AdminArticleTrackbacksListView( $this->_blogInfo );
					$this->_view->setErrorMessage( $this->_locale->tr("error_fetching_article" ));
					$this->setCommonData();
					
					return false;
				}
			}
			else {
				// there was no article, so this probably was the view that shows all trackbacks...
				$article = null;
			}
			
            // loop through the trackbacks and remove them
            foreach( $this->_trackbackIds as $trackbackId ) {
            	// fetch the trackback
				$trackback = $trackbacks->getTrackBack( $trackbackId );
				
				if( !$trackback ) {
					$errorMessage .= $this->_locale->pr("error_updating_trackback2", $trackbackId)."<br/>";				
				}
				else {
					// fire the pre-event
					$this->notifyEvent( EVENT_PRE_TRACKBACK_UPDATE, Array( "trackback" => &$trackback ));
					
					// check if the trackback really belongs to this blog...
					$article = $trackback->getArticle();
					if( $article->getBlogId() != $this->_blogInfo->getId()) {
						// if not, then we shouldn't be allowed to remove anything!						
						$errorMessage .= $this->_locale->pr("error_updating_trackback", $trackback->getExcerpt())."<br/>";
					}
					else
					{
						$preTrackbackStatus = $trackback->getStatus();
						
						if ( $preTrackbackStatus == $this->_trackbackStatus )
						{
							$errorMessage .= $this->_locale->pr("error_updating_trackback", $trackback->getExcerpt())."<br/>";
							continue;
							
						}

						$trackback->setStatus( $this->_trackbackStatus );
						if( !$trackbacks->updateComment( $trackback ))
							$errorMessage .= $this->_locale->pr("error_updating_trackback", $trackback->getExcerpt())."<br/>";
						else {
							if( $this->_trackbackStatus == COMMENT_STATUS_SPAM )
							{
								$this->_markTrackbackAsSpam($trackback);
							}
							elseif( $this->_trackbackStatus == COMMENT_STATUS_NONSPAM )
							{
								$this->_markTrackbackAsNonSpam($trackback);
							}
							
							$totalOk++;
							if( $totalOk < 2 ) 
								$successMessage .= $this->_locale->pr("trackback_updated_ok", $trackback->getExcerpt());
							else
								$successMessage = $this->_locale->pr("trackbacks_updated_ok", $totalOk );
							
							// fire the post-event
							$this->notifyEvent( EVENT_POST_TRACKBACK_UPDATE, Array( "trackback" => &$trackback ));
						}
					}				
				}				
            }

			// if everything fine, then display the same view again with the feedback
			if( $this->_articleId == 0 )
				$this->_view = new AdminArticleTrackbacksListView( $this->_blogInfo, Array( "article" => null ));
			else
				$this->_view = new AdminArticleTrackbacksListView( $this->_blogInfo, Array( "article" => $article ));
				            
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
        
        function _markTrackbackAsSpam( $trackback )
        {
			// throw the pre-event
			$this->notifyEvent( EVENT_PRE_MARK_SPAM_TRACKBACK, Array( "trackbackId" => $trackback->getId() ));

	        // We should get the trackback and train the filter to recognize this as spam...
       	    lt_include( PLOG_CLASS_PATH."class/bayesian/bayesianfiltercore.class.php" );
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
			$this->notifyEvent( EVENT_POST_MARK_SPAM_TRACKBACK, Array( "trackbackId" => $trackback->getId() ));
        }
        
        /**
         * @private
         */
        function _markTrackbackAsNonSpam( $trackback )
        {
			// throw the pre-event
			$this->notifyEvent( EVENT_PRE_MARK_NO_SPAM_TRACKBACK, Array( "trackbackId" => $trackback->getId() ));
		
            // we should get the trackback and train the filter
       	    lt_include( PLOG_CLASS_PATH."class/bayesian/bayesianfiltercore.class.php" );
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
			$this->notifyEvent( EVENT_POST_MARK_NO_SPAM_TRACKBACK, Array( "trackbackId" => $trackback->getId() ));
        }
    }
?>