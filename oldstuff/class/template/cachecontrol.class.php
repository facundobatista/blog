<?php

	/**
	 * \ingroup Template
	 * 
	 * Allows to control Smarty's caches in an easier way. You can call any of these methods
	 * whenever you need to clear the contents of the whole site, the summary or just a single
	 * blog.
	 */
	class CacheControl 
	{

		/**
		 * resets all the caches in the site
		 * @static
		 * @return Always true
		 */
		function resetAllCaches()
		{
			// get a list of all the blogs
			lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
			$blogs = new Blogs();
			$siteBlogs = $blogs->getAllBlogIds();
			
			// and loop through them
			foreach( $siteBlogs as $blogId ) {
				CacheControl::resetBlogCache( $blogId, false );
			}
			
			// clear the cache used by the summary
			CacheControl::clearSummaryCache();

			return true;
		}
		
		/**
		 * clears the cache of the summary, since it is also allowed to use it :)
		 * This method is also used when using CacheControl::resetAllCaches()
		 *
		 * @see resetAllCaches
		 * @static
		 */
		function clearSummaryCache()
		{
            $config =& Config::getConfig();
			// nothing to do if caching is not enabled...
			if( !$config->getValue( "template_cache_enabled" ))
				return false;			
				
			// nothing to do either if the summary page lifetime is greater than 0,
			// since smarty will take care of resetting it when needed
			$summaryTemplateLifetime = $config->getValue( "summary_template_cache_lifetime" );
			if( $summaryTemplateLifetime > 0 )
				return true;
				
			// if not then continue...
			lt_include( PLOG_CLASS_PATH."class/template/cachedtemplate.class.php" );			
			$t = new CachedTemplate( null );			
			$tmpFolder = $config->getValue( "temp_folder" );
			$summaryTmpFolder = $tmpFolder."/summary";
			
			// delete the contents of the folder, but not its structure or subfolders
			File::deleteDir( $summaryTmpFolder, true, true );
			
			return true;		
		}
		
		/**
		 * alias for clearSummaryCache
		 * @see clearSummaryCache
		 * @static		 
		 */
		function resetSummaryCache()
		{
			return CacheControl::clearSummaryCache();
		}

		/**
		 * resets the cache of a blog
		 *
		 * @param blogId The id of the blog whose cache we'd like to reset
		 * @param resetSummary whether the summary cache should also be cleaned up,
		 * enabled by default
		 * @static		 
		 */
		function resetBlogCache( $blogId, $resetSummary = true )
		{
            $config =& Config::getConfig();
			// nothing to do if caching is not enabled...
			if( !$config->getValue( "template_cache_enabled" ))
				return false;
		
			$tmpFolder = $config->getValue( "temp_folder" );
			$blogTmpFolder = $tmpFolder."/".$blogId;

			// recursively delete the contents of the folder, but not its structure or subfolders            
			File::deleteDir( $blogTmpFolder, true, true );
			
			//$t->clear_cache( null );
			
			// we need to clear the contents of the summary caches because we have added or removed
			// posts or done something that might affect the summary!
			if( $resetSummary )
				CacheControl::clearSummaryCache();
			
			return true;
		}
	}
?>