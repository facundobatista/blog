<?php

	lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/daocacheconstants.properties.php" );	

	/**
	 * small wrapper to Articles that loads and caches recent articles. As an API user, you probably
	 * don't need to use this class unless you're implementing your own front-end to LifeType.
	 */
	
	/**
	 * @see Articles
	 */
	class RecentArticles extends Articles
	{
		/**
		 * @param blogId
		 * @param amount
		 * @return An array of Article objects
		 * @see Article 
		 */
		function getRecentArticles( $blogId, $amount ) 
		{
			// check if the data is there
			$recentPosts = $this->_cache->getData( $blogId, CACHE_RECENT_ARTICLES_BY_BLOG );
			if( !$recentPosts ) {		
				lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );
				$t = new Timestamp();
				$todayTimestamp = $t->getTimestamp();
				
				// load the data if not available
				$recentPosts = $this->getBlogArticles( $blogId,
													   -1, 
													   $amount,
													   0, 
													   1, 
													   0, 
													   $todayTimestamp);
													   
				// and cache it for future use
				$this->_cache->setData( $blogId, CACHE_RECENT_ARTICLES_BY_BLOG, $recentPosts );
			}
			
			return( $recentPosts );
		}
		
		/**
		 * Called whenever the cache needs to be cleaned up.
		 * @static
		 */
		function resetRecentArticlesCache( $blogId ) 
		{
			$cache =& CacheManager::getCache();
			$cache->removeData( $blogId, CACHE_RECENT_ARTICLES_BY_BLOG );
		}
	}
?>