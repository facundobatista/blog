<?php

	lt_include( PLOG_CLASS_PATH."class/summary/action/summaryaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/summary/dao/summarystats.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
	lt_include( PLOG_CLASS_PATH."class/summary/view/summaryrssview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/net/rawrequestgenerator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	
	define( "SUMMARY_RSS_TYPE_DEFAULT", "default" );
	define( "SUMMARY_RSS_TYPE_MOST_COMMENTED", "mostcommented" );
	define( "SUMMARY_RSS_TYPE_MOST_READ", "mostread" );
	define( "SUMMARY_RSS_TYPE_MOST_ACTIVE_BLOGS", "mostactiveblogs" );
	define( "SUMMARY_RSS_TYPE_NEWEST_BLOGS", "newestblogs" );
	define( "SUMMARY_RSS_TYPE_POSTS_LIST", "postslist" );
	define( "SUMMARY_RSS_TYPE_BLOGS_LIST", "blogslist" );	

     /**
      * This is the one and only default action. It simply fetches all the most recent
      * posts from the database and shows them. The default locale is the one specified
      * in the configuration file and the amount of posts shown in this page is also
      * configurable through the config file.
      */
     class SummaryRssAction extends SummaryAction
     {
         var $_mode;
         var $_profile;
         var $_globalArticleCategoryId;
         var $_blogCategoryId;
         
        function SummaryRssAction( $actionInfo, $request )
        {
            $this->SummaryAction( $actionInfo, $request );
        }
		
		function validate()
		{
			// make sure that the mode is set to something meaningful...
			$this->_mode = $this->_request->getValue( "type" );
			if( $this->_mode != SUMMARY_RSS_TYPE_DEFAULT &&
			    $this->_mode != SUMMARY_RSS_TYPE_MOST_COMMENTED &&
				$this->_mode != SUMMARY_RSS_TYPE_MOST_READ &&
				$this->_mode != SUMMARY_RSS_TYPE_MOST_ACTIVE_BLOGS &&
				$this->_mode != SUMMARY_RSS_TYPE_NEWEST_BLOGS &&
				$this->_mode != SUMMARY_RSS_TYPE_POSTS_LIST &&
				$this->_mode != SUMMARY_RSS_TYPE_BLOGS_LIST ) {
				
				// in case the parameter looks weird, let's use a default one...
				$this->_mode = SUMMARY_RSS_TYPE_DEFAULT;
			}

			$this->_profile = $this->_request->getValue( "profile" );
            $profileValidator = new StringValidator();
            $profileValidator->addRule( new RegexpRule( "^([a-zA-Z0-9]*)$" ));
            if(!$profileValidator->validate($this->_profile)){
                $this->_profile = "";
            }

            $val = new IntegerValidator();

            $this->_globalArticleCategoryId = $this->_request->getValue("globalArticleCategoryId");
            if(!$val->validate( $this->_globalArticleCategoryId)){
                $this->_globalArticleCategoryId = ALL_GLOBAL_ARTICLE_CATEGORIES;
            }
            else{
                    // id is an integer, now lets see it is a valid category id
                lt_include( PLOG_CLASS_PATH."class/dao/globalarticlecategories.class.php" );
                $categories = new GlobalArticleCategories();
                if(!$categories->getGlobalArticleCategory( $this->_globalArticleCategoryId ))
                    $this->_globalArticleCategoryId = ALL_GLOBAL_ARTICLE_CATEGORIES;
            }
            
            
            $this->_blogCategoryId = $this->_request->getValue("blogCategoryId");
            if(!$val->validate($this->_blogCategoryId)){
                $this->_blogCategoryId = ALL_BLOG_CATEGORIES;
            }
            else{
                lt_include( PLOG_CLASS_PATH."class/dao/blogcategories.class.php" );
                $categories = new BlogCategories();
                if(!$categories->getBlogCategory( $this->_blogCategoryId ))
                    $this->_blogCategoryId = ALL_BLOG_CATEGORIES;
            }
			
			return true;
		}

        /**
         * Loads the posts and shows them.
         */
        function perform()
        {
            if( $this->_mode == SUMMARY_RSS_TYPE_MOST_COMMENTED ||
                $this->_mode == SUMMARY_RSS_TYPE_MOST_READ ||
                $this->_mode == SUMMARY_RSS_TYPE_DEFAULT ||
                $this->_mode == SUMMARY_RSS_TYPE_POSTS_LIST )
            {
	                
	            // RSS feeds for posts stuff
	            $this->_view = new SummaryRssView( $this->_profile, Array( "summary" => "rss", 
			                                       "globalArticleCategoryId" => $this->_globalArticleCategoryId,
			                                       "mode" => $this->_mode,
												   "profile" => $this->_profile ));
				if( $this->_view->isCached()) {
					$this->setCommonData();
					return true;
				}
		
            	$blogs       = new Blogs();
            	$stats       = new SummaryStats();
	                
				if( $this->_mode == SUMMARY_RSS_TYPE_MOST_COMMENTED ) {
					$postslist = $stats->getMostCommentedArticles();
				}
				elseif( $this->_mode == SUMMARY_RSS_TYPE_MOST_READ ) {
					$postslist = $stats->getMostReadArticles();			
				}
				elseif( $this->_mode == SUMMARY_RSS_TYPE_POSTS_LIST ) {
            		lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
            		
            		// get the summary_items_per_page from config
            		$config =& Config::getConfig();
            		$summaryItemsPerPage = $config->getValue( "summary_items_per_page", SUMMARY_DEFAULT_ITEMS_PER_PAGE );
					
					$postslist = $stats->getPostsByGlobalCategory( $this->_globalArticleCategoryId,
        										 					 $page = 1, 
        										 					 $summaryItemsPerPage );
				}				
				else {
					$postslist = $stats->getRecentArticles( $this->_globalArticleCategoryId );
				}
	
	            if( !$postslist ) {
					$postslist = Array();
	            }
	
				$this->_view->setValue( "posts", $postslist );
			}
			elseif( $this->_mode == SUMMARY_RSS_TYPE_MOST_ACTIVE_BLOGS ||
			        $this->_mode == SUMMARY_RSS_TYPE_NEWEST_BLOGS ||
			        $this->_mode == SUMMARY_RSS_TYPE_BLOGS_LIST ) {
				
				// RSS feeds for blogs, need different template sets...
	            $this->_view = new SummaryRssView( "blogs_".$this->_profile, Array( "summary" => "rss",
	            								   "blogCategoryId" => $this->_blogCategoryId, 
			                                       "mode" => $this->_mode,
												   "profile" => $this->_profile ));
				if( $this->_view->isCached()) {
					$this->setCommonData();
					return true;
				}
				
				// load the stuff
				$blogs = new Blogs();
				$stats = new SummaryStats();
				
				if( $this->_mode == SUMMARY_RSS_TYPE_MOST_ACTIVE_BLOGS ) {
					$blogslist = $stats->getMostActiveBlogs();	
				}
				elseif( $this->_mode == SUMMARY_RSS_TYPE_BLOGS_LIST ) {
            		lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
            		
            		// get the summary_items_per_page from config
            		$config =& Config::getConfig();
            		$summaryItemsPerPage = $config->getValue( "summary_items_per_page", SUMMARY_DEFAULT_ITEMS_PER_PAGE );

                    $blogslist = $blogs->getAllBlogs( BLOG_STATUS_ACTIVE, 
													  $this->_blogCategoryId, 
													  "", 
													  1, 
													  $summaryItemsPerPage );
				}
				else {
					$blogslist = $stats->getRecentBlogs();
				}
				
				// in case there is really no data to fetch...
				if( !$blogslist )
					$blogslist = Array();
					
				$this->_view->setValue( "blogs", $blogslist );								
			}
			
			$this->_view->setValue( "type", $this->_mode );
			$this->_view->setValue( "summary", true );

			// this 'url' object is just a dummy one... But we cannot get it from the list
			// of blogs that we fetched because it could potentially be null! Besides, we only
			// need it to generate the base url to rss.css and to summary.php, so no need to
			// have a fully-featured object
			$this->_view->setValue( "url", new RawRequestGenerator( null ));
			
			$this->setCommonData();		

            return true;
        }
     }
?>