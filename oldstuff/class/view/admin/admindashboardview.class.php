<?php

	lt_include( PLOG_CLASS_PATH."class/view/view.class.php" );
    lt_include( PLOG_CLASS_PATH."class/template/templateservice.class.php" );
    lt_include( PLOG_CLASS_PATH."class/template/menu/menurenderer.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/articlecomments.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/trackbacks.class.php" );
	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
//	lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresources.class.php" );
	
	/**
	 * maximum number of recent items that we will show in the statistics
	 */
	define( "DASHBOARD_MAX_RECENT_ITEMS", 5 );	
	
	/**
	 * how many blogs a user can own, by default
	 */
	define( "DEFAULT_MAX_BLOGS_PER_USER", 1 ); 

    /**
     * \ingroup View
     * @private
     *	
     * Generates the view with the summary
     */
    class AdminDashboardView extends View
	{
	
		var $_userInfo;
		var $_userBlogs;
		var $_config;

    	/**
         * This initializes the class, but normally we'll only have to initialize the parent
         *
         * It gets the BlogSettings object since we need to know a few things about the blog we're
         * rendering before doing this.
         */
        function AdminDashboardView( $userInfo, $userBlogs )
        {
        	$this->View();
			
			// keep the paramters for later use
			$this->_userInfo = $userInfo;
			$this->_userBlogs = $userBlogs;
            $this->_config =& Config::getConfig();			
			
			$this->_loadViewData();
        }
		
		/**
		 * normally views do not meddle with data but in this case moving all
		 * this data fetching here is benefitial from a coding point of view, because it
		 * allows this code to be reused by several action classes... In the worst case
		 * we would have to copy+paste the code or put in a separate class only for this bit
		 * of code. By moving it here, the view itself can handle everything
		 */
		function _loadViewData()
		{			
			// for each blog, load some statistics
			$articles = new Articles();
			$comments = new ArticleComments();
			$trackbacks = new Trackbacks();
//			$resources = new GalleryResources();
			$recentPosts = Array();
			$recentComments = Array();
			$recentResources = Array();
			
			// load some statistics	for each one of the blogs
			$numOwnedBlogs = 0;			
			foreach( $this->_userBlogs as $userBlog ) {
				$recentPosts[$userBlog->getId()] = $articles->getBlogArticles( $userBlog->getId(), 
																			   -1,  // no date,																			   
																			   DASHBOARD_MAX_RECENT_ITEMS,
																			   -1, 																			   
																			   POST_STATUS_PUBLISHED );
				$recentComments[$userBlog->getId()] = $comments->getBlogComments ( $userBlog->getId(), 
				                                                                   COMMENT_ORDER_NEWEST_FIRST, 
																				   COMMENT_STATUS_ALL,
																				   "",  // no search terms
																				   1,     // first page
																				   DASHBOARD_MAX_RECENT_ITEMS );
				$recentTrackbacks[$userBlog->getId()] = $trackbacks->getBlogTrackbacks( $userBlog->getId(), 
																						COMMENT_STATUS_ALL,
																						"",
																						1,
																						DASHBOARD_MAX_RECENT_ITEMS );				
				if( $userBlog->getOwner() == $this->_userInfo->getId())
					$numOwnedBlogs++;
			}
		
			$this->_params->setValue( "userblogs", $this->_userBlogs );
			$this->_params->setValue( "recentposts", $recentPosts );
			$this->_params->setValue( "recentcomments", $recentComments );
			$this->_params->setValue( "recenttrackbacks", $recentTrackbacks );
			$this->_params->setValue( "user", $this->_userInfo );
			
			// check whether the user can create new blogs
			$maxBlogsPerUser = $this->_config->getValue( "num_blogs_per_user" );
			if( !is_numeric( $maxBlogsPerUser ))
				$maxBlogsPerUser = DEFAULT_MAX_BLOGS_PER_USER;
			$numOfUserBlogs = count( $this->_userInfo->getOwnBlogs() );
				
			if( $numOfUserBlogs < $maxBlogsPerUser )
				$userCanCreateBlog = true;
			else
				$userCanCreateBlog = false;
			
			$this->_params->setValue( "userCanCreateBlog", $userCanCreateBlog );
		}

        /**
         * Renders the view. It simply gets all the parameters we've been adding to it
         * and puts them in the context of the template renderer so that they can be accessed
         * as normal parameters from within the template
         */
        function render()
        {
			// set the view character set based on the default locale
			if( empty( $this->_userBlogs ))
            	$locale =& Locales::getLocale( $this->_config->getValue( "default_locale" ));			
			else
				$locale = $this->_userBlogs[0]->getLocale();
				
			$this->setCharset( $locale->getCharset());		
		
			parent::render();
		
        	// to find the template we need, we can use the TemplateService
            $ts = new TemplateService();
        	$template = $ts->AdminTemplate( "dashboard" );
            $this->setValue( "locale", $locale );
			$this->setValue( "bayesian_filter_enabled", $this->_config->getValue( "bayesian_filter_enabled" ));
            // assign all the values
            $template->assign( $this->_params->getAsArray());

            // and send the results
            print $template->fetch();
        }
        
        function setUserInfo()
        {
        	// ...
        }
    }
?>