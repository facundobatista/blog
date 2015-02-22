<?php
    
   /**
    * \ingroup Cache
    *
    * Provides a singleton for storing and retrieving data from a global cache. You probably
    * want to use the getCache() method in this class instead of creating objects of the Cache
    * class.
    *
    * @see Cache
    */
    class CacheManager 
    {
		/**
		 * Returns an instance of the cache.
		 *
		 * @param cacheEnabled Set this to false if you wish this class to always return no data,
		 * meaning that it will have to be loaded every time.
		 * @return The current global instance of the Lite class
		 */
        function &getCache( $cacheEnabled = true )
        {
            static $cache;

            if( $cache == null ) {
                // source the neccessary files
                lt_include( PLOG_CLASS_PATH . "class/config/configfilestorage.class.php" );

				$config = new ConfigFileStorage( Array( "file" => PLOG_CLASS_PATH."config/cache.properties.php" ));
				      
				$cacheProvider = $config->getValue( 'cache_method' );
				if( $cacheProvider == 'memcached' ) {
					lt_include( PLOG_CLASS_PATH . "class/cache/memcache.class.php" );
					// define defaults
					$cacheParameter = array(
						'servers' => $config->getValue( 'memcached_servers' ),
						'life_time' => $config->getValue( 'memcached_life_time' ),
						'debug' => $config->getValue( 'memcached_debug' ),
						'compress_threshold' => $config->getValue( 'memcached_compress_threshold' ),
						'persistant' => $config->getValue( 'memcached_persistant' ),
					);
					
					// build a new cache object
					$cache = new MemCache( $cacheParameter);
				}
				elseif( $cacheProvider == 'null' ) {
					// 'null' cache means that it does no caching, it basically is some sort of a
					// pass-through cache
					lt_include( PLOG_CLASS_PATH . "class/cache/nullcache.class.php" );					
					
	                // build a new cache object
	                $cache = new NullCache();					
				}				
				else {					
                	lt_include( PLOG_CLASS_PATH . "class/cache/cache.class.php" );					
	                // configure the Cache_Lite parameters, but providing some defaults in case the config file isn't there
	                $cacheParameter = array(
						'cacheDir' => $config->getValue( 'cache_lite_cache_dir', "./tmp/" ),
						'lifeTime' => $config->getValue( 'cache_lite_life_time', 604800 ),
						'readControl' => $config->getValue( 'cache_lite_read_control', false ),
						'automaticSerialization' => $config->getValue( 'cache_lite_automatic_serialization', true ),
						'hashedDirectoryLevel' => $config->getValue( 'cache_lite_hashed_directory_level', 2 ),
						'fileNameProtection' => $config->getValue( 'cache_lite_filename_protection', true ),
						'caching' => $cacheEnabled
	                );

                        // build a new cache object
	                $cache = new Cache( $cacheParameter);
				}
            }
            return $cache;
        }
    }
?>