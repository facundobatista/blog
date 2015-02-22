<?php

	
	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	lt_include( PLOG_CLASS_PATH."class/locale/locale.class.php" );
	lt_include( PLOG_CLASS_PATH."class/plugin/pluginmanager.class.php" );
	lt_include( PLOG_CLASS_PATH.'class/dao/daocacheconstants.properties.php' );

    define("DEFAULT_LOCALE", "en_UK");
    define( "REGEXP_VALID_LOCALE", "/.*locale_([a-z]{2}_[A-Z]{2}_?([a-zA-Z0-9\-]*)+)\.php$/" );

	/**
	 * \ingroup Locale
	 *
     * Class that supports methods such as getAvailableLocales, addLocale
     * removeLocale and so on, basically to deal with locale files.
     *
     * It also provides a singleton-like method called Locales::getLocale to load and cache locale
     * files from disk, so that we don't have to fetch the same file as many times
     * as we ask for it but else, we keep a cached copy of it for later use. It is advised to
	 * use this method instead of creating a new Locale object every time we need a Locale.
	 *
     * @see Locales::getLocale
     * @see Locale
     */
	class Locales 
	{

    	function Locales()
        {
            
        }

        /**
         * @static
         * Static method that offers some kind of locale factory. Since the Locale object
         * better not use a Singleton (otherwise we couldn't use more than one locale file
         * at a time) this function has been included here to provide a system similar to
         * a singleton: we keep an static array inside the function, that contains all the
         * locale files that have been loaded so far. Whenever somebody requests a locale
         * to be fetched from disk, we will first check that we have not loaded it before. If
         * we have, then we only have to return the same object we were keeping.
         * If the locale wasn't there, we will then load it from disk and store/cache the
         * resulting object for future use.
         * It is recommended to use this method over creating new Locale objects every time
         * we need one.
         *
         * @param localeCode The code (eg. en_UK, es_ES) of the locale we want to get.
         * @return Returns a Locale object corresponding to the requested locale.
         * @see Locale
         */
        function &getBlogLocale( $localeCode = null )
        {
			lt_include( PLOG_CLASS_PATH."class/locale/bloglocale.class.php" );
	
        	// array to keep track of the locales that we have already loaded, so that
            // we don't have to fetch them from disk
        	static $loadedLocales;        	

            // if there is no locale parameter, we use the default one
        	if( $localeCode == null ) {
            	$config =& Config::getConfig();
                $localeCode = $config->getValue( "default_locale" );
            }

            // check if we have already loaded that locale or else, load it from
            // disk and keep it for later, just in case anybody asks again
            if( isset($loadedLocales[$localeCode]) ) {
            	$locale = $loadedLocales[$localeCode];
            } 
			else {
                lt_include( PLOG_CLASS_PATH . "class/cache/cachemanager.class.php" );
                $cache =& CacheManager::getCache();
                $locale = $cache->getData( $localeCode, CACHE_BLOG_LOCALES );
                if ( !$locale ) {
                    $locale = new BlogLocale( $localeCode );
                    $cache->setData( $localeCode, CACHE_BLOG_LOCALES, $locale );
                }
				
				Locales::_loadPluginLocales( $locale );
            }
                
           $loadedLocales[$localeCode] = $locale;
			
            return $locale;
        }

		function _loadPluginLocales( &$locale )
		{
			$localeCode = $locale->getLocaleCode();
			
            $pm =& PluginManager::getPluginManager();                
            foreach( $pm->_pluginList as $pluginId ) {
                if( $pm->pluginHasLocale( $pluginId, $localeCode )) {
                    // if the plugin provides the locale that we need, continue
                    $pluginLocale = Locales::getPluginLocale( $pluginId, $localeCode );                        
                }
                else {
                    // if not, try to load en_UK by default
                    if( $pm->pluginHasLocale( $pluginId, "en_UK" )) {
                        $pluginLocale = Locales::getPluginLocale( $pluginId, "en_UK" );
                    }
                    // if not en_UK locale available, forget about it...
                }
                
                // merge the plugin locale with the big locale
                if ( isset( $pluginLocale ) ) {
                    $locale->mergeLocale( $pluginLocale );                    
                }
            }

			return( true );
		}

        /**
         * @static
         * Static method that offers some kind of locale factory. Since the Locale object
         * better not use a Singleton (otherwise we couldn't use more than one locale file
         * at a time) this function has been included here to provide a system similar to
         * a singleton: we keep an static array inside the function, that contains all the
         * locale files that have been loaded so far. Whenever somebody requests a locale
         * to be fetched from disk, we will first check that we have not loaded it before. If
         * we have, then we only have to return the same object we were keeping.
         * If the locale wasn't there, we will then load it from disk and store/cache the
         * resulting object for future use.
         * It is recommended to use this method over creating new Locale objects every time
         * we need one.
         *
         * @param localeCode The code (eg. en_UK, es_ES) of the locale we want to get.
         * @return Returns a Locale object corresponding to the requested locale.
         * @see Locale
         */
        function &getLocale( $localeCode = null )
        {
        	// array to keep track of the locales that we have already loaded, so that
            // we don't have to fetch them from disk
        	static $loadedLocales;        	

            // if there is no locale parameter, we use the default one
        	if( $localeCode == null ) {
            	$config =& Config::getConfig();
                $localeCode = $config->getValue( "default_locale" );
            }

            // check if we have already loaded that locale or else, load it from
            // disk and keep it for later, just in case anybody asks again
            if( isset($loadedLocales[$localeCode]) ) {
            	$locale = $loadedLocales[$localeCode];
            } 
			else {
                lt_include( PLOG_CLASS_PATH . "class/cache/cachemanager.class.php" );
                $cache =& CacheManager::getCache();
                $locale = $cache->getData( $localeCode, CACHE_LOCALES );
                if ( !$locale ) {
                    $locale = new Locale( $localeCode );
                    $cache->setData( $localeCode, CACHE_LOCALES, $locale );
                }

				Locales::_loadPluginLocales( $locale );
            }
                
           $loadedLocales[$localeCode] = $locale;
			
            return $locale;
        }

		/**
		 * loads the locale file provided by a plugin. First it will try to load the locale specified in
		 * the $pluginId parameter and if not available, it will load the current default locale.
		 *
		 * @param localeCode The locale code we would like to load
		 * @return A PluginLocale object with the translated strings loaded from the plugin's own folder
		 */
		function getPluginLocale( $pluginId, $localeCode = null )
		{
        	global $_plugins_loadedLocales;        	

        	if( $localeCode == null ) {
            	$config =& Config::getConfig();
                $localeCode = $config->getValue( "default_locale" );
            }

			//$pluginLocaleKey = "plugin_".$pluginId."_".$localeCode;
			$pluginLocaleKey = $pluginId;

            // check if we have already loaded that locale or else, load it from
            // disk and keep it for later, just in case anybody asks again
            if( isset($_plugins_loadedLocales[$pluginLocaleKey][$localeCode] )) {
            	$locale = $_plugins_loadedLocales[$pluginLocaleKey][$localeCode];
            }
            else {
	            lt_include( PLOG_CLASS_PATH."class/locale/pluginlocale.class.php" );

            	$locale = new PluginLocale( $pluginId, $localeCode );
                $_plugins_loadedLocales[$pluginLocaleKey][$localeCode] = $locale;
            }

            return $locale;
		}

        /**
         * Returns an array with the codes of the locale files available.
         *
         * @return An array containing all the locale codes available in the system.
		 * @static
         */
        function getAvailableLocales()
        {
        	$config =& Config::getConfig();

            $locales = $config->getValue( "locales" );
            
            // in order to prevent some ugly error messages
            if( !is_array( $locales ))
            	$locales = Array();
            	
            return( $locales );
        }

        /**
         * Returns default locale code
         *
         * @return default locale code
         */

        function getDefaultLocale()
        {
            return DEFAULT_LOCALE;
        }

        /**
         * Returns true if the given locale code is a valid one (i.e. if it is amongst
         * the available ones
         *
         * @param localeCode The code of the locale we'd like to check
         * @return Returns 'true' if the locale file is already available in the system
         * or false otherwise.
		 * @static
         */
        function isValidLocale( $localeCode )
        {
        	$availableLocales = Locales::getAvailableLocales();

            return in_array( $localeCode, $availableLocales );
        }

		/**
		* returns whether a file has the corect name format
		 *
		 * @param fileName
		 * @return true if format is correct or false otherwise
		 */
		function isValidLocaleFileName( $fileName )
		{
			return( preg_match( REGEXP_VALID_LOCALE, $fileName ));
		}

        /**
         * Returns an array with all the locales available in the system.
         *
         * This is quite memory and disk-intensive since we are loading a lot of lines
         * from possibly a lot of files!!!
         *
         * @return Returns an array of Locale objects, which represent *all* the locales
         * that have been installed in this system.
		 * @static
         */
        function getLocales()
        {
        	$localeCodes = Locales::getAvailableLocales();        	

            $locales = Array();
            foreach( $localeCodes as $code ) {
            	array_push( $locales, Locales::getLocale( $code ));
            }

            return $locales;
        }

        /**
         * Given a locale code, returns the path to the file that contains it.
         *
         * @param localeCode The code representing the locale
         * @return A string containing the path (may be absolute or relative, it depends)
         * to the file containing the given locale. It does *not* check if the file exists
         * or not, simply returns its path.
         */
        function getLocaleFilename( $localeCode )
        {
        	return (Locale::getLocaleFolder()."/locale_".$localeCode.".php");
        }

        /**
         * Given a locale code, returns the path to the file that contains its admin version.
         *
         * @param localeCode The code representing the locale
         * @return A string containing the path (may be absolute or relative, it depends)
         * to the file containing the given locale. It does *not* check if the file exists
         * or not, simply returns its path.
         */
        function getAdminLocaleFilename( $localeCode )
        {
        	return (Locale::getLocaleFolder()."/admin/locale_".$localeCode.".php");
        }

        /**
         * Removes a locale from the system. First the file containing it is deleted
         * and then we also remove its entry from the configuration.
         *
         * @param localeCode The code of the locale that we would like to delete.
         * @return Returns true if the locale was removed succesfully or false otherwise.
         */
        function removeLocale( $localeCode )
        {
        	$config =& Config::getConfig();

            // if we don't have permissions on the folder where the locale files
            // are stored, we don't even have to bother about it...
            if(!File::isWritable(Locale::getLocaleFolder()))
            	return false;

            // does the locale really exist?
            if( !$this->isValidLocale( $localeCode ))
            	return false;

			// remove the blog locale
            $fileName = $this->getLocaleFilename( $localeCode );
			if( File::exists( $fileName )) {
	            if( !unlink( $fileName ))
        	    	return false;
			}
			// and the admin locale
            $fileName = $this->getAdminLocaleFilename( $localeCode );
			if( File::exists( $fileName )) {
	            if( !unlink( $fileName ))
        	    	return false;
			}			

            $availableLocales = $config->getValue( "locales" );

            $newLocaleList = Array();
            foreach( $availableLocales as $locale ) {
            	if( $locale != $localeCode )
                	array_push( $newLocaleList, $locale );
            }

            $config->saveValue( "locales", $newLocaleList );

            return true;
        }

        /**
         * Adds a new locale to the list of available/supported locale. It checks to make
         * sure that the file exists and that has proper access permissions.
         *
         * @param localeCode the code of the locale we'd like to add.
         * @return Returns true if the file was successfully added or false otherwise.
         */
        function addLocale( $localeCode )
        {
        	$config =& Config::getConfig();

            // if the locale is already there, there is no need to add it,
            // since the user is *maybe* just updating the file.
            if( $this->isValidLocale( $localeCode ))
            	return true;

            $fileName = $this->getLocaleFilename( $localeCode );

            // make sure that the file exists and can be read and
            // give up if not so
            if( !File::isReadable( $fileName ))
            	return false;

            // otherwise, we can happily add it to the list of supported locales
            $availableLocales = $this->getAvailableLocales();
            array_push( $availableLocales, $localeCode );
            $config->saveValue( "locales", $availableLocales );

            return true;
        }
    }
?>
