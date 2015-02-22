<?php

	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	
	/**
	 * some default values for the method below
	 */
	define( "DEFAULT_TMP_FOLDER", "./tmp" );	
	define( "DEFAULT_HARD_SHOW_POSTS_MAX", 50 );	
	define( "DEFAULT_HARD_RECENT_POSTS_MAX", 25 );	
	define( "DEFAULT_HARD_SHOW_COMMENTS_MAX", 20 );		

	class SiteConfig extends Config
	{
		function _getConfigValueWithDefault( $key, $default )
		{
			$config =& Config::getConfig();
			return( $config->getValue( $key, $default ));			
		}
		
		/** 
		 * @static
		 * Returns the temporaray folder
		 */
		function getTempFolder()
		{
			return( SiteConfig::_getConfigValueWithDefault( "temp_folder", DEFAULT_TMP_FOLDER ));
		}

		/** 
		 * @static
		 * Returns the maximum value for the hard_show_posts_max key
		 */		
		function getHardShowPostsMax()
		{
			return( SiteConfig::_getConfigValueWithDefault( "hard_show_posts_max", DEFAULT_HARD_SHOW_POSTS_MAX ));
		}
		
		/** 
		 * @static
		 * Returns the maximum value for the hard_recent_posts_max key
		 */		
		function getHardRecentPostsMax()
		{
			return( SiteConfig::_getConfigValueWithDefault( "hard_recent_posts_max", DEFAULT_HARD_RECENT_POSTS_MAX ));
		}
		
		/** 
		 * @static
		 * Returns the maximum value for the hard_recent_posts_max key
		 */		
		function getHardShowCommentsMax()
		{
			return( SiteConfig::_getConfigValueWithDefault( "hard_show_comments_max", DEFAULT_HARD_SHOW_COMMENTS_MAX ));
		}		
		
		/**
		 * @static
		 * @see Config
		 */
		function getValue( $key, $default )
		{
			$config =& parent::getConfig();
			return( $config->getValue( $key, $default ));
		}
	}
?>