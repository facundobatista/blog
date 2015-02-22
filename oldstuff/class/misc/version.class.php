<?php

	/**
	 * URL pointing to the project page
	 */
    define( "LIFETYPE_PROJECT_PAGE", "http://www.lifetype.net" );

	/**
	 * This needs to be udpated after every major release, so that the version feed
	 * is fetched from the right place
	 */	
	define( "LIFETYPE_DEFAULT_VERSION_FEED", "http://www.lifetype.net/feeds/1.2/lifetype.xml" );	
	
	/**
	 * This needs to be udpated after every major release, so that the plugin feed
	 * is fetched from the right place
	 */
	define( "LIFETYPE_DEFAULT_PLUGIN_VERSION_FEED", "http://www.lifetype.net/feeds/1.2/plugins.xml" );	
    
	/**
	 * File where the version string is stored
	 */
    if ( !defined("DEFAULT_VERSION_FILE") )
      define( "DEFAULT_VERSION_FILE", PLOG_CLASS_PATH . "version.php" );

    /**
     * \ingroup Misc
     *
     * Returns the current version of plog as well as a link to the project page
     */
    class Version  
    {

        /**
         * Returns the current version of pLog, determined by the value of the $version
         * variable in the version.php file.
         * If the file is not available, the result is unknown.
         * @static
         * @return The version identifier.
         */
        function getVersion()
        {
			lt_include( PLOG_CLASS_PATH."class/file/file.class.php" );		
            $versionFile = PLOG_CLASS_PATH."version.php";
			$version = "undefined";

            if( File::isReadable( $versionFile )) {
                    // NOTE: this is a valid use of include()
                include( $versionFile );
            } else {
                $version = "UNKNOWN";
            }
            return $version;
        }

        /**
         * Returns the official page of the project.
         *
         * @return The official project page.
         * @static
         */
        function getProjectPage()
        {
            return LIFETYPE_PROJECT_PAGE;
        }

		/**
		 * Returns the link to the current Lifetype version feed
		 *
		 * @static
		 */
		function getLifetypeVersionFeed()
		{
			return( LIFETYPE_DEFAULT_VERSION_FEED );
		}
		
		/**
		 * Returns the link to the current Lifetype version feed
		 *
		 * @static
		 */		
		function getPluginVersionFeed()
		{
			return( LIFETYPE_DEFAULT_PLUGIN_VERSION_FEED );
		}
		
		/**
		 * Returns true if the current version is a development version
		 * 
		 * @static
		 */
		function isDevelopment( $version = "" )
		{
			if( $version == "" )
				$version = Version::getVersion();
				
			return( substr( $version, -3, 3 ) == "dev" );
		}		
		
		/**
		 * Compares two LT versions
		 *
		 * @param v1
		 * @param v2
		 * @return 1 if v1>v2, 0 if v1==v2 and -1 if v1<v2
		 * @see http://www.php.net/manual/en/function.version-compare.php
		 * @static
		 */
		function compare( $v1, $v2 )
		{
			// remove the "lifetype-" string from both versions in case if it's there
			// as it will make it easier for version_compare to work
			$v1 = str_replace( "lifetype-", "", $v1 );
			$v2 = str_replace( "lifetype-", "", $v2 );

                // version_compare doesn't like dashes/underscores at the end of a number
			$v1 = str_replace( "_", "-", $v1 );
			$v2 = str_replace( "_", "-", $v2 );

            list($v1, $rev1) = explode("-r", $v1);
            list($v2, $rev2) = explode("-r", $v2);

                // compare major versions first
			$res = version_compare( $v1, $v2 );

            if($res == -1 || $res == 1)
                return $res;

            if(!$rev1) $rev1 = 0;
            if(!$rev2) $rev2 = 0;
            return (strcmp($rev1, $rev2));
		}
		
		/**
		 * Returns true if the given version is newer than the current one
		 *
		 * @param v1 The version that we'd like to compare to
		 * @param v1 If specified, the version that we should check whether it's older than v1 or not.
		 * If not specified, the current version will be used.
		 * @return True if the given version is newer than the current one or false otherwise
		 */
		function isNewer( $v1, $v2 = "" )
		{
			if( $v2 == "" )
				$v2 = Version::getVersion();
				
			return( Version::compare( $v2, $v1 ) == -1 );
		}
    }
?>
