<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminpostmanagementcommonaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminpostslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admineditpostview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfieldsvalues.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfields.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/cachecontrol.class.php" );
	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/stringutils.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Updates the values of the post in the database
	 *
	 * Btw, this class could very well need some refactoring... :)
     */
    class AdminUpdatePostAction extends AdminPostManagementCommonAction
	{

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminUpdatePostAction( $actionInfo, $request )
        {
        	$this->AdminPostManagementCommonAction( $actionInfo, $request );

        	// postId is the must have field when we update the post.
        	$this->registerFieldValidator( "postId", new IntegerValidator() );
        	$view = new AdminEditPostView( $this->_blogInfo );
        	$view->setErrorMessage( $this->_locale->tr("error_updating_post"));
        	$this->setValidationErrorView( $view );

			$this->requirePermission( "update_post" );
        }
        
        /**
         * Carries out the specified action
         */
        function perform()
        {	
	        // fetch the data
            $this->_fetchCommonData();	        
            $this->_postId = $this->_request->getValue( "postId" );
	        	
            // fetch the old post
            $articles =  new Articles();
            $post     = $articles->getBlogArticle( $this->_postId, $this->_blogInfo->getId());
            // there must be something wrong if we can't fetch the post that we are trying to update...
            if( !$post ) {
				$this->_view = new AdminPostsListView( $this->_blogInfo );
				$this->_view->setErrorMessage( $this->_locale->tr("error_fetching_article"));
                $this->setCommonData();

                return false;
            }

	        // if the user does not have the 'update_all_user_articles' permission, then
            // we have to check whether the original poster of the article and the current
            // user match
            if( !$this->userHasPermission( "update_all_user_articles" )) {
	            if( $post->getUserId() != $this->_userInfo->getId()) {
	            	$this->_view = new AdminPostsListView( $this->_blogInfo );
	                $this->_view->setErrorMessage( $this->_locale->tr("error_can_only_update_own_articles" ));
	                $this->setCommonData();

	                return false;		            
	            }
            }

             // if we got it, update some fields
            $post->setTopic( $this->_postTopic );
            $post->setText( $this->_postText );
            $post->setCategoryIds( $this->_postCategories );
            $post->setStatus( $this->_postStatus );
            $post->setDateObject( $this->_postTimestamp );
            $post->setCommentsEnabled( $this->_commentsEnabled );
			$post->setPostSlug( $this->_postSlug );
			$post->setGlobalCategoryId( $this->_globalArticleCategoryId );		
			$post->setUser( $this->_posterId );
			
			// set the modification date
			$blogSettings = $this->_blogInfo->getSettings();
			$modifDate = Timestamp::getDateWithOffset( new Timestamp(), $blogSettings->getValue( "time_offset", 0 ));
			$post->setModificationDate( $modifDate );			

            // prepare the custom fields
            $post->setFields($this->_getArticleCustomFields());
			
			// fire the pre event
			$this->notifyEvent( EVENT_PRE_POST_UPDATE, Array( "article" => &$post ));
		
        	// and finally save the post to the database
        	if( !$articles->updateArticle( $post )) {
				$this->_view = new AdminPostsListView( $this->_blogInfo );
				$this->_view->setErrorMessage( $this->_locale->tr("error_updating_post"));
            	$this->setCommonData();

            	return false;

        	}
			
			// clean up the cache
			CacheControl::resetBlogCache( $this->_blogInfo->getId());	

        	// create the definitive view
        	$this->_view = new AdminPostsListView( $this->_blogInfo );
        	// show a message saying that the post was updated
        	$message = $this->_locale->pr("post_updated_ok", $post->getTopic());

        	// check if there was a previous notification
        	$notifications = new ArticleNotifications();
        	$artNotification = $notifications->getUserArticleNotification( $this->_postId, $this->_blogInfo->getId(), $this->_userInfo->getId());
        	// check if we have to add or remove a notification
        	if( $this->_sendNotification ) {
        		if( !$artNotification ) {
            		// if there wasn't one and now we want it, we have to add it
                	$notifications->addNotification( $this->_postId, $this->_blogInfo->getId(), $this->_userInfo->getId());
                	$message .= " ".$this->_locale->tr("notification_added");
            	}
        	}
        	else {
        		// if we don't want notifications, then we have to check if there is one since we
            	// should remove it
            	if( $artNotification ) {
            		$notifications->deleteNotification( $this->_postId, $this->_blogInfo->getId(), $this->_userInfo->getId());
                	$message .= " ".$this->_locale->tr("notification_removed");
            	}
        	}
			
                // only send trackbacks and xmlrpc pings
                // when a post is "published"
			if( $post->getStatus() == POST_STATUS_PUBLISHED ) {
				// get the links from the text of the post
                $postLinks = StringUtils::getLinks($post->getText());

                // get the real trackback links from trackbackUrls
                $trackbackLinks = Array();
                foreach(explode( "\r\n", $this->_trackbackUrls ) as $host ) {
                	trim($host);
                	if( $host != "" && $host != "\r\n" &&
                        $host != "\r" && $host != "\n" )
                    {
                    	array_push( $trackbackLinks, $host );
                    }
                }
				
                    // if the "send xmlrpc pings" checkbox was enabled,
                    // do something about it...
                if( $this->_sendPings ) {
                    $t = new Timestamp();
                    $today = $t->getTimestamp();
                    if($today > $post->getDate()){
                        $message .= "<br/><br/>".$this->sendXmlRpcPings();
                    }
				}				
                    // and now check what to do with the trackbacks
                if( $this->_sendTrackbacks ) {
                        // if no links, there is nothing to do
                    if( count($postLinks) == 0 &&
                        count($trackbackLinks) == 0 )
                    {
                        $this->_view = new AdminPostsListView(
                            $this->_blogInfo );
                        $this->_view->setErrorMessage(
                            $this->_locale->tr(
                                "error_no_trackback_links_sent"));
                    }
                    else {
                        $this->_view = new AdminTemplatedView(
                            $this->_blogInfo, "sendtrackbacks" );
                            // get the links from the text of the post
                        $this->_view->setValue( "post", $post );
                        $this->_view->setValue( "postLinks", $postLinks );
                        $this->_view->setValue( "trackbackLinks",
                                                $trackbackLinks );
                    }
                }
            }

        	// show the message
        	$this->_view->setSuccessMessage( $message );
			
			// and fire the post event
			$this->notifyEvent( EVENT_POST_POST_UPDATE, Array( "article" => &$post ));

            $this->setCommonData();

            return true;
        }
    }
?>