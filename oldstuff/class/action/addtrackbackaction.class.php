<?php

	lt_include( PLOG_CLASS_PATH."class/action/action.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/trackbackview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
    lt_include( PLOG_CLASS_PATH."class/net/http/httpvars.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/articlenotifications.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/cachecontrol.class.php" );
    lt_include( PLOG_CLASS_PATH."class/plugin/pluginmanager.class.php" );
	lt_include( PLOG_CLASS_PATH."class/net/client.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/trackbacks.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/httpurlvalidator.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/security/pipeline.class.php" );
	
	/**
	 * Class that takes care of adding trackbacks
	 *
	 * \ingroup Action
	 * @private
	 */
	class AddTrackbackAction extends Action
	{
	
		function AddTrackbackAction( $actionInfo, $request )
		{
			$this->Action( $actionInfo, $request );
			
			// we need certain data
			$this->registerFieldValidator( "id", new IntegerValidator() );
			$this->registerFieldValidator( "url", new HttpUrlValidator() );
			$this->setValidationErrorView( new TrackbackView( "Error incorrect parameters", true ) );
		}
				
		/**
		 * @private
		 * @static
		 */
		function tblog( $message )
		{
		    lt_include( PLOG_CLASS_PATH . "class/logger/loggermanager.class.php" );

			$logger =& LoggerManager::getLogger( "trackback" );
			$logger->debug( $message );
		}
		
		function perform()
        {
	        // check if we should be receiving trackbacks at all
	        $config =& Config::getConfig();
	        if( !$config->getValue( "trackback_server_enabled", false )) {
                $this->tblog( "ERROR: Trackbacks are not enabled in this site" );
                $this->_view = new TrackbackView( "Trackbacks are not enabled in this site", true );
                return( false );		        
	        }
	        
            // for security, we will strip _ANY_ html tag from the tags
            $tf = new TextFilter();
            $blogName  = $tf->filterAllHTML( $this->_request->getValue( "blog_name" ));
            $excerpt   = $tf->filterAllHTML( $this->_request->getValue( "excerpt" ));
            $title     = $tf->filterAllHTML( $this->_request->getValue( "title" ));
            $articleId = $this->_request->getValue( "id" );
            $url       = $tf->filterAllHTML( $this->_request->getValue( "url" ));
            
            $this->tblog( "** Incoming request **" );
            $this->tblog( "Blog name = ".$blogName );
            $this->tblog( "Excerpt = ".$excerpt );
            $this->tblog( "Title = ".$title );
            $this->tblog( "Article ID = ".$articleId );
            $this->tblog( "url = ".$url );      

            // try to see if the article is correct
            $articles = new Articles();
            $article = $articles->getBlogArticle( $articleId );
            if( !$article ) {
                $this->tblog( "ERROR: Incorrect error identifier" );
                $this->_view = new TrackbackView( "Incorrect article identifier", true );
                return( false );
            }
    
            // try to load the blog info too, as we are going to need it
            $blogs = new Blogs();
            $blogInfo = $blogs->getBlogInfo( $article->getBlog());
    
            // a bit of protection...
            if( !$blogInfo ) {
                $this->tblog( "ERROR: Article id ".$article->getId()." points to blog ".$article->getBlog()." that doesn't exist!" );
                $this->_view = new TrackbackView( "The blog does not exist", true );
                return( false );
            }
    
            // if the blog is disabled, then we shoulnd't take trackbacks...
            if( $blogInfo->getStatus() != BLOG_STATUS_ACTIVE ) {
                $this->tblog( "ERROR: The blog ".$blogInfo->getBlog()." is set as disabled and cannot receive trackbacks!" );
                $this->_view = new TrackbackView( "The blog is not active", true );
                return( false );
            }
            
            // if everything went fine, load the plugins so that we can throw some events...
            $pm =& PluginManager::getPluginManager();
            $pm->loadPlugins();
            // and also configure the BlogInfo and UserInfo objects so that they know
            // who threw the events...
            $pm->setBlogInfo( $blogInfo );
            $userInfo = $blogInfo->getOwnerInfo();
            $pm->setUserInfo( $userInfo );                                  
            
            // let's take a look at the security stuff, once we've made sure that the
            // blog and the article are both valid
            $pipeline = new Pipeline( $this->_request, $blogInfo );
            $result = $pipeline->process();
            // let the sender of the trackback know that something went wrong
            if( !$result->isValid()) {
                // use the default view
                $this->tblog( "The trackback was blocked by a filter" );
                $this->_view = new TrackbackView( $result->getErrorMessage(), true );
                print($this->_view->render());
                die();
            }
    
            // receives the request and adds it to the database
            $trackbacks = new TrackBacks();
            // create teh trackback object
            $now = new Timestamp();
            $ip = Client::getIp();
            $trackback = new Trackback( $url, 
                                        $title, 
                                        $articleId, 
                                        $blogInfo->getId(),
                                        $excerpt, 
                                        $blogName, 
                                        $now->getTimestamp(), 
                                        $ip );

            // this code probably needs some explanation... 
            // Basically, if the bayesian filter is configured to save spam to the database marked as spam,
            // we would end up with two identical trackbacks: one marked as spam and the other one not marked
            // as spam. The first one would be created by the spam filter and the second one would be created
            // by us here, so we need to know if the trackback is already there and if not, don't add it.
            // This also works as an additional protection feature agains repeating trackback spammers.
            if( !$trackbacks->getIdentical( $trackback )) {
                // throw the event in case somebody is listening to it!
                $pm->notifyEvent( EVENT_PRE_TRACKBACK_ADD, Array( "trackback" => &$trackback ));
                $result = $trackbacks->addTrackBack( $trackback );
                if( !$result ) {
                    $this->tblog( "There was an error saving the trackback!" );
                }
            }
            
            // throw the post event too...
            $pm->notifyEvent( EVENT_POST_TRACKBACK_ADD, Array( "trackback" => &$trackback ));
            
            // everything went fine so let's create a normal view, without a message 
            // (the message is not needed if there is no error)
            $this->_view = new TrackbackView( "", false );          

            // notify the user that a new trackback has been received, if the article was
            // configured to receive notifications
            // but first make sure, the trackback was not removed by some plugins like validatetrackback...
            if( $trackbacks->getTrackBack( $trackback->getId() ) ) {
                $notifier = new ArticleNotifications();
                $notifier->notifyUsers( $article->getId(), $blogInfo);
            } 
            // clear the blog cache
            CacheControl::resetBlogCache( $article->getBlog());
            
            $this->tblog( "** End **" );
		}
	}
?>