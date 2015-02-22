<?php

	lt_include( PLOG_CLASS_PATH."class/action/blogaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/rssview.class.php" );	
    lt_include( PLOG_CLASS_PATH."class/data/validator/templatenamevalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );


    /**
     * \ingroup Action
     * @private
     *
     * This class is used by the controller that takes care of handling the requests for the
     * RSS feed.
     */
	class RssAction extends BlogAction 
	{

    	/**
         * Constructor.
         */
    	function RssAction( $blogInfo, $request )
        {
        	$this->BlogAction( $blogInfo, $request );
			
			$this->registerFieldValidator( "categoryId", new IntegerValidator(), true );
			$this->registerFieldValidator( "userId", new IntegerValidator(), true );			
			$this->registerFieldValidator( "profile", new TemplateNameValidator(), true);

			// generate a dummy view with nothing in it to signal an error
			$view = new RssView( $this->_blogInfo, RSS_VIEW_DEFAULT_PROFILE );
			$view->setValue( "articles", Array());
			$this->setValidationErrorView( $view );			
        }

        /**
         * Performs the action.
         */
        function perform()
        {
        	//Check the rdf syndication is allowed or not
        	$rdfEnabled = $this->_config->getValue( "rdf_enabled" );
        	if ( !$rdfEnabled ) {
                lt_include( PLOG_CLASS_PATH."class/view/errorview.class.php" );
				$message = $this->_locale->tr('error_rdf_syndication_not_allowed').'<br/><br/>';
            	$this->_view = new ErrorView( $this->_blogInfo, $message );
                $this->setCommonData();
                $this->_view->render();

                die();
            }        		
        	
        	// fetch the articles for the given blog
            $blogSettings = $this->_blogInfo->getSettings();


            // fetch the default profile as chosen by the administrator
            $defaultProfile = $this->_config->getValue( "default_rss_profile" );
            if( $defaultProfile == "" || $defaultProfile == null )
            	$defaultProfile = RSS_VIEW_DEFAULT_PROFILE;

            // fetch the profile
            // if the profile specified by the user is not valid, then we will
            // use the default profile as configured
            $profile = $this->_request->getValue( "profile" );
			if( $profile == "" ) $profile = $defaultProfile;
			
			// sanitize the profile variable
			$profile = str_replace( ".", "", $profile );
			$profile = str_replace( "/", "", $profile );
			$profile = str_replace( "%", "", $profile );			

            // fetch the category, or set it to '0' otherwise, which will mean
            // fetch all the most recent posts from any category
            $categoryId = $this->_request->getValue( "categoryId" );
            if( !is_numeric($categoryId))
            	$categoryId = 0;

			// fetch the user id, if any
			$userId = $this->_request->getValue( "userId", -1 );
				
            // check if the template is available
            $this->_view = new RssView( $this->_blogInfo, $profile, 
			                            Array( "profile" => $profile,
										       "categoryId" => $categoryId,
										        "userId" => $userId ));
			
			// do nothing if the view was already cached
			if( $this->_view->isCached()) {
                $this->setCommonData();
				return true;
			}
			
    		lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
    		lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );			
    		lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );
			lt_include( PLOG_CLASS_PATH."class/config/siteconfig.class.php" );
    		
        	$articles = new Articles();    		

            // fetch the posts, though we are going to fetch the same amount in both branches
			$hardLimit = SiteConfig::getHardRecentPostsMax();
			$amount = $blogSettings->getValue( "recent_posts_max", 15 );			
			if( $amount > $hardLimit ) $amount = $hardLimit;

			$t = new Timestamp();
			if( $blogSettings->getValue( 'show_future_posts_in_calendar' )) {
				$blogArticles = $articles->getBlogArticles( $this->_blogInfo->getId(), 
				                                            -1, 
				                                            $amount, 
															$categoryId, 
															POST_STATUS_PUBLISHED, 
															$userId );
			}
			else {
				$today = $t->getTimestamp();
				$blogArticles = $articles->getBlogArticles( $this->_blogInfo->getId(), 
				                                            -1, 
				                                            $amount, 
															$categoryId, 
															POST_STATUS_PUBLISHED, 
															$userId, 
															$today );			
			}
			
			// load the category
			if( $categoryId > 0 ) {
			     lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );
			     $articleCategories = new ArticleCategories();
			     $category = $articleCategories->getCategory( $categoryId );
			     $this->_view->setValue( "rsscategory", $category );
			}   
														
			$pm =& PluginManager::getPluginManager();
			$pm->setBlogInfo( $this->_blogInfo );
			$pm->setUserInfo( $this->_userInfo );
			$result = $pm->notifyEvent( EVENT_POSTS_LOADED, Array( 'articles' => &$blogArticles ));
			$articles = Array();
			
			foreach( $blogArticles as $article ) {
				$postText = $article->getIntroText();
				$postExtendedText = $article->getExtendedText();
				$pm->notifyEvent( EVENT_TEXT_FILTER, Array( "text" => &$postText ));
				$pm->notifyEvent( EVENT_TEXT_FILTER, Array( "text" => &$postExtendedText ));
				$article->setIntroText( $postText );
				$article->setExtendedText( $postExtendedText );
				array_push( $articles, $article );
			}														
            
            $this->_view->setValue( "locale", $this->_locale );
            $this->_view->setValue( "posts", $articles );
            $this->setCommonData();

            return true;
        }
    }
?>