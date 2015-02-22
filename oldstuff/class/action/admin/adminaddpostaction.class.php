<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminpostmanagementcommonaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminpostslistview.class.php" );	
    lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminnewpostview.class.php" );    
	
    /**
     * \ingroup Action
     * @private
     *
     * Action that adds a new post to the database.
     */
    class AdminAddPostAction extends AdminPostManagementCommonAction
	{
    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminAddPostAction( $actionInfo, $request )
        {
        	$this->AdminPostManagementCommonAction( $actionInfo, $request );

        	$view = new AdminNewPostView( $this->_blogInfo );
        	$view->setErrorMessage( $this->_locale->tr("error_adding_post"));
        	$this->setValidationErrorView( $view );

			// security checks
			$this->requirePermission( "add_post" );
        }
        
		/**
		 * @private
		 *
		 * If the form is not validate, we need to clean the autosave cookie
		 */
		function validate()
		{
			$validateOk = parent::validate();
			if( !$validateOk )
				$this->clearAutoSaveCookie();
            
			return $validateOk;
		}

		/**
		 * @private
		 *
		 * returns the id of the post or 'false' if it couldn't be saved
		 */
		function _savePostData( $article )
		{
            lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );

			$articles = new Articles();
			$article->setFields( $this->_getArticleCustomFields());			
			//print_r($article->_customFields);
			
			// notifiy about this event
			$this->notifyEvent( EVENT_PRE_POST_ADD, Array( "article" => &$article ));				
			
			// in case the post is already in the db
            // TODO: I am guessing this is for updating drafts?
            // this allows people with only add permissions to update any article
			if( $this->_postId != "" ) {
				$article->setId( $this->_postId );
				$artId = $this->_postId;
				$postSavedOk = $articles->updateArticle( $article );
				
				if( $postSavedOk )
					$artId = $this->_postId;
				else
					$artId = false;
			}
			else {
				$artId = $articles->addArticle( $article );
			}
			
			return $artId;
		}

        /**
         * Carries out the specified action
         */
        function perform()
        {
            lt_include( PLOG_CLASS_PATH."class/dao/article.class.php" );
	        lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );

			$this->_fetchCommonData();

			$this->_postId = $this->_request->getValue( "postId" );

            // we know for sure that the information is correct so we can now add
            // the post to the database
			$postText = Textfilter::xhtmlize($this->_postText);
			
			$article  = new Article( $this->_postTopic, 
			                         $postText, 
			                         $this->_postCategories,
									 $this->_posterId, 
									 $this->_blogInfo->getId(), 
									 $this->_postStatus, 
									 0, 
									 Array(), 
									 $this->_postSlug );
			// set also the date before it's too late
			$article->setDateObject( $this->_postTimestamp );
            $blogSettings = $this->_blogInfo->getSettings();
            $article->setTimeOffset($blogSettings->getValue("time_offset"));
			$article->setCommentsEnabled( $this->_commentsEnabled );
			$article->setGlobalCategoryId( $this->_globalArticleCategoryId );
		
			// save the article to the db
			$artId = $this->_savePostData( $article );
			
			// depending on the permission that the user has, we'll show one view or another
			if( !$this->userHasPermission( "view_posts" ))
				$view = "AdminNewPostView";
			else
				$view = "AdminPostsListView";			
			
        	// once we have built the object, we can add it to the database
        	if( $artId ) {
	            // clear autosave cookie
	            $this->clearAutoSaveCookie();

                $this->_view = new $view( $this->_blogInfo );
            	//$article->setId( $artId );
                $message = $this->_locale->tr("post_added_ok");
                
                // train the filter, but only if enabled
				if( $this->_config->getValue( "bayesian_filter_enabled" ) == true ) {
		            lt_include( PLOG_CLASS_PATH."class/bayesian/bayesianfiltercore.class.php" );
	                BayesianFilterCore::trainWithArticle( $article );
				}
                                
        		// add the article notification if requested to do so
            	if( $this->_sendNotification ) {
                    lt_include( PLOG_CLASS_PATH."class/dao/articlenotifications.class.php" );

                	$artNotifications = new ArticleNotifications();
	            	$artNotifications->addNotification( $artId, $this->_blogInfo->getId(), $this->_userInfo->getId());
                    $message .= " ".$this->_locale->tr("send_notifications_ok");
        	    }

                // we only have to send trackback pings if the article was published
                // otherwise there is no need to...
				$article->setId( $artId );
                if( $article->getStatus() == POST_STATUS_PUBLISHED) {
                	// get the output from the xmlrpc pings but only if the user decided to do so!

					if( $this->_sendPings) {
                        $t = new Timestamp();
                        $today = $t->getTimestamp();
                        if($today > $article->getDate()){
                            $pingsOutput = $this->sendXmlRpcPings();
                            $message .= "<br/><br/>".$pingsOutput;
                        }
					}

                    // and now check what to do with the trackbacks
                    if( $this->_sendTrackbacks ) {
                        lt_include( PLOG_CLASS_PATH."class/data/stringutils.class.php" );
                        lt_include( PLOG_CLASS_PATH."class/data/filter/urlconverter.class.php" );
                        $f = new UrlConverter();

                    	// get the links from the text of the post
        				$postLinks = StringUtils::getLinks( $article->getText());

		                // get the real trackback links from trackbackUrls
		                $trackbackLinks = Array();
		                foreach(explode( "\r\n", $this->_trackbackUrls ) as $host ) {
		                	trim($host);
		                	if( $host != "" && $host != "\r\n" && $host != "\r" && $host != "\n" ){
                                $host = $f->filter($host);
		                    	array_push( $trackbackLinks, $host );
                            }
		                }

        				// if no links, there is nothing to do
        				if( count($postLinks) == 0 && count($trackbackLinks) == 0 ) {
        					$this->_view = new $view( $this->_blogInfo );
			                $this->_view->setErrorMessage( $this->_locale->tr("error_no_trackback_links_sent"));
        				}
        				else {
            				$this->_view = new AdminTemplatedView( $this->_blogInfo, "sendtrackbacks" );
            				$this->_view->setValue( "post", $article );
            				$this->_view->setValue( "postLinks", $postLinks );
							$this->_view->setValue( "trackbackLinks", $trackbackLinks );            				
         				}
                    }
                    $this->_view->setSuccessMessage( $message );
					
					$this->notifyEvent( EVENT_POST_POST_ADD, Array( "article" => &$article )); 
					
					// empty the cache used by this blog
	                lt_include( PLOG_CLASS_PATH."class/template/cachecontrol.class.php" );

					CacheControl::resetBlogCache( $this->_blogInfo->getId());						
                }
                else {
                	$this->_view = new $view( $this->_blogInfo );
                    $this->_view->setSuccessMessage( $this->_locale->tr("post_added_not_published") );
					
					$this->notifyEvent( EVENT_POST_POST_ADD, Array( "article" => &$article ));
                }
        	}
        	else {
        		$this->_view = new $view( $this->_blogInfo );
            	$this->_view->setErrorMessage( $this->_locale->tr("error_adding_post") );
        	}

            $this->setCommonData();
            
            // better to return true if everything fine
            return true;
        }
        
        function clearAutoSaveCookie()
        {
        	$rg = $this->_blogInfo->getBlogRequestGenerator();
        	$plogBaseUrl = $rg->getBaseUrl(false);
        	$cookieBaseName = "LT" . preg_replace("/[^a-zA-Z0-9]/", "", $plogBaseUrl).$this->_blogInfo->getId();

        	// set the auto save cookie as false
	    	setcookie( $cookieBaseName.'postNotSaved', '0', -1, '/' );
                // cookies always have a 'cookieNum' suffix, if we ever go to using multiple cookies
                // we'll need to be smarter here - TODO: just delete 0-5 or something?  How else do
                // we get the maxBackupCookiesPerBlog value from autosave.js?
	    	setcookie( $cookieBaseName.'postTopic0', '', -1, '/' );
	    	setcookie( $cookieBaseName.'postText0', '', -1, '/' );
        }
    }
?>