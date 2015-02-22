<?php

	lt_include( PLOG_CLASS_PATH.'class/view/smartyview.class.php' );
    lt_include( PLOG_CLASS_PATH.'class/plugin/pluginmanager.class.php' );
    
    /**
     * default date format used for the archive links
     */
    define( "ARCHIVE_DEFAULT_DATE_FORMAT", '%B %Y' );

    /**
     * \ingroup View
     *
     * Extends the SmartyView class to provide support for common operations, for example
     * to automatically add support for locale. It is recommended
     * that all classes that generate a view extend from this unless strictly necessary.
     */
	class BlogView extends SmartyView
	{

        var $_pm;
		var $_pageTitle;
		var $_locale;

		/**
		 * @see SmartyView
		 */
		function BlogView( $blogInfo, $template, $cachingEnabled = SMARTY_VIEW_CACHE_CHECK, $data = Array())
        {
			// the SmartyView will generate the right Template object for us
        	$this->SmartyView( $blogInfo, $template, $cachingEnabled, $data );
			
			$this->_pm =& PluginManager::getPluginManager();
			$this->_pm->setBlogInfo( $this->_blogInfo );
			
			// set the character set in the request based on the blog locale
			$this->_locale = $this->_blogInfo->getBlogLocale();
			$this->setCharset( $this->_locale->getCharset());			
			
			// set the initial page title
			$this->_pageTitle = $blogInfo->getBlog();
        }

        /**
         * Generates an html calendar based on the posts for the given month
         *
         * @param year
         * @param month
         * @private
         */
        function generateCalendar( $year = null, $month = null )
        {
            lt_include( PLOG_CLASS_PATH.'class/data/plogcalendar.class.php' );

			$monthPosts = $this->getValue( 'monthposts' );

            $calendar = new PlogCalendar( $monthPosts, $this->_blogInfo, $this->_locale );

			$this->setValue( 'calendar', $calendar->getMonthView( $month, $year ));
        }
        
        /**
         * notifies of a throwable event
         *
         * @param eventType The code of the event we're throwing
         * @param params Array with the event parameters
         */
		function notifyEvent( $eventType, $params = Array())
		{
			$params[ 'from' ] = get_class( $this );
					
			return $this->_pm->notifyEvent( $eventType, $params );
		}        
        
        /**
         * Fetches the stats for the archives
         *
         * @private
         */
        function _getArchives()
        { 
	        lt_include( PLOG_CLASS_PATH.'class/dao/archivelink.class.php' );
            lt_include( PLOG_CLASS_PATH.'class/data/timestamp.class.php' );

			lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
			$articles = new Articles();
            $archiveStats = $articles->getNumberPostsPerMonth( $this->_blogInfo->getId());

            if( $archiveStats == '' )
            	return false;

            $links = Array();
            $urls = $this->_blogInfo->getBlogRequestGenerator();
            
            // format of dates used in the archive, but it defaults to '%B %Y' if none specified
            $archiveDateFormat = $this->_locale->tr( 'archive_date_format' );      
            // need to check whether we got the same thing back, since that's the way Locale::tr() works instead of
            // returning an empty string      
            if( $archiveDateFormat == "archive_date_format" ) $archiveDateFormat = ARCHIVE_DEFAULT_DATE_FORMAT;
            
            foreach( $archiveStats as $yearName => $year) {
            	foreach( $year as $monthName => $month ) {
                	// we can use the Timestamp class to help us with this...
                	$t = new Timestamp();
                    $t->setYear( $yearName );
                    $t->setMonth( $monthName );
                    $archiveUrl = $urls->getArchiveLink( $t->getYear().$t->getMonth());
                    $linkName = $this->_locale->formatDate( $t, $archiveDateFormat );
                	$link = new ArchiveLink( $linkName, '', $archiveUrl, $this->_blogInfo->getId(), 0, $month, 0);
                    $links[] = $link;
                }
            }

            return $links;
        }
        
        /**
         * Retrieves the most recent posts
         *
         * @private
         */
        function _getRecentPosts()
        {
            lt_include( PLOG_CLASS_PATH.'class/dao/recentarticles.class.php' );
			lt_include( PLOG_CLASS_PATH."class/config/siteconfig.class.php" );

            $blogSettings = $this->_blogInfo->getSettings();

			$hardLimit = SiteConfig::getHardRecentPostsMax();
			$amount = $blogSettings->getValue( "recent_posts_max", 15 );	
			if( $amount > $hardLimit ) $amount = $hardLimit;

			$recent = new RecentArticles();
			$recentPosts = $recent->getRecentArticles( $this->_blogInfo->getId(), $amount );
        	$this->_pm->notifyEvent( EVENT_POSTS_LOADED, Array( 'articles' => &$recentPosts ));        	
        	
        	return $recentPosts;
        }
        
        /**
         * Retrieves all the posts for the current month
         *
         * @private
         */
        function _getMonthPosts()
        {
            lt_include( PLOG_CLASS_PATH.'class/data/timestamp.class.php' );

        	$t = new Timestamp();
            $blogSettings = $this->_blogInfo->getSettings();
            // the values for the month and the year have been included in the session
            $month = $this->getValue( 'Month' );
            $year = $this->getValue( 'Year' );

			lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
			$articles = new Articles();
            return( $articles->getDaysWithPosts( $this->_blogInfo->getId(), $year, $month ));
    	}

        /**
         * Retrieves the links
         *
         * @private
         */
        function _getLinkCategories()
        {
            lt_include( PLOG_CLASS_PATH.'class/dao/mylinkscategories.class.php' ); 

			$blogSettings = $this->_blogInfo->getSettings();
			$linkCategoriesOrder = $blogSettings->getValue( 'link_categories_order', MYLINKS_CATEGORIES_NO_ORDER );		
            $myLinksCategories = new MyLinksCategories();
            $blogLinksCategories = $myLinksCategories->getMyLinksCategories( $this->_blogInfo->getId(), $linkCategoriesOrder);
			$this->notifyEvent( EVENT_LINK_CATEGORIES_LOADED, Array( 'linkcategories' => &$blogLinksCategories )); 
			
			return $blogLinksCategories;
        }
        
        /**
         * Fetches the article categories for the given blog
         *
         * @private
         */
        function _getArticleCategories()
        {
            lt_include( PLOG_CLASS_PATH.'class/dao/articlecategories.class.php' ); 

			$blogSettings = $this->_blogInfo->getSettings();
			$categoryOrder = $blogSettings->getValue( 'categories_order' );
        	$categories = new ArticleCategories();
			// we want a list with all the categories, sorted in the way that was configured
            $categories = $categories->getBlogCategories( $this->_blogInfo->getId(), false, $categoryOrder );
            
            return $categories;
        }
		
		/**
		 * @see Smartyview::preProcessViewContents()
		 *
		 * This reimplementation of this method adds support for events that can be caught in a plugin. This can be used
		 * for for example in a plugin that processes the output of a template in order to add ads or do some other
		 * dynamic operation with the content.
		 *
		 * @param content
		 * @return The content
		 */
		function preProcessViewContents( $content )
		{			
			// pass the content and the name of the template file as a parameter to the event
			$this->notifyEvent( EVENT_PROCESS_BLOG_TEMPLATE_OUTPUT, Array( 'content' => &$content, 'template' => $this->_templateName ));
			
			return( $content );
		}		
		
		/**
		 * This method must be implemented by child classes and it is meant
		 * to return the title for the current page, to make it easier for template
		 * designers to automatically provide meaningful page titles
		 *
		 * @return A string containing the appropriate page title
		 */
		function getPageTitle()
		{
			return( $this->_pageTitle );
		}
		
		/**
		 * This method sets the page title and can be called by action classes
		 * instantiating this view to set a meaningful page title.
		 *
		 * @param title A string containing the appropriate page title
		 */		
		function setPageTitle( $title )
		{
			$this->_pageTitle = $title;
		}
		
		/**
		 * Sets some  in this case, we leave it all up to the child classes to reimplement
		 * this and by default call View::render()
		 *
		 * @returns always true
		 */
		function render()
		{		
			if( !$this->isCached() ) {
                lt_include( PLOG_CLASS_PATH.'class/data/plogcalendar.class.php' );
				lt_include( PLOG_CLASS_PATH.'class/misc/version.class.php' );
				lt_include( PLOG_CLASS_PATH.'class/xml/rssparser/rssparser.class.php' );
				lt_include( PLOG_CLASS_PATH.'class/data/timestamp.class.php' );

				// and then add our stuff to the view...
				$this->setValue( 'archives', $this->_getArchives());
				$this->setValue( 'recentposts', $this->_getRecentPosts());
				$this->setValue( 'mylinkscategories', $this->_getLinkCategories());
				$this->setValue( 'monthposts', $this->_getMonthPosts());
				$this->setValue( 'articlecategories', $this->_getArticleCategories());
				$this->generateCalendar( $this->getValue( 'Year' ), $this->getValue( 'Month' ));

                $this->setValue( 'url', $this->_blogInfo->getBlogRequestGenerator());
                $this->setValue( 'utils', $this->_blogInfo->getBlogRequestGenerator());
				$this->setValue( 'rss', new RssParser());
				$this->setValue( 'version', Version::getVersion());
				$this->setValue( 'now', new Timestamp());
				
				// page title
				$this->setValue( "pageTitle", $this->getPageTitle());
				
				// also, let's not forget about the plugins...
				// put the plugins in the context
				$plugins = $this->_pm->getPlugins();
				foreach( $plugins as $name => $plugin ) {
					$this->setValue( $name, $plugin );
				}
			}

			//
			// these things can go in since they do not mean much overhead when generating the view...
			//

			$this->setValue( 'locale', $this->_locale );			
			$this->setValue( 'blog', $this->_blogInfo );			
			$this->setValue( 'blogsettings', $this->_blogInfo->getSettings());
			$this->setValue( 'misctemplatepath', TemplateSetStorage::getMiscTemplateFolder());
			
			// ask the parent to do something, if needed
			parent::render();
		}
    }
?>
