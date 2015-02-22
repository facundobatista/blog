<?php                                                           

	lt_include( PLOG_CLASS_PATH."class/cache/basecacheprovider.class.php" );

	/**
     * \defgroup Cache
	 *
	 * The Cache group is made up of the Cache class, which provides the basic methods to store and
	 * retrieve data based on keys, and the CacheManager class which provides a singleton to access to
	 * the global.
	 */

	$__cache_hits = 0;
	$__cache_misses = 0;
	$__cache_queries = 0;

	/** 
	 * \ingroup Cache 
	 *
     * <p>This class wraps around the Cache_Lite class and provides a set of basic methods for storing
     * and retrieving data from the cache. As of LifeType 1.1, this class is only used by the DAO layer as its
     * caching mechanism but it can only be used by any other class if necessary.</p>
     *
     * <p>You probably don't want to create objects of this class, but instead use the CacheManager class
     * that provides a singleton to access one global instance of this class</p>
     *
     * @see CacheManager
     * @see Cache_Lite
	 * @see BaseCacheProvider
     */
    class Cache extends BaseCacheProvider
    {
        var $cache;
        var $lifeTime;

		/** 
	     * Constructor of the class. 
	     *
		 * @param cacheProperties An array with parameters as required by Cache_Lite		
		 */
        function Cache( $cacheProperties )
        {
	    	$this->BaseCacheProvider();
		
            lt_include( PLOG_CLASS_PATH . "class/cache/Cache_Lite/Lite.php" );
            
            $this->cache = new Cache_Lite( $cacheProperties );
            $this->lifeTime = $cacheProperties['lifeTime'];
        }

		/** 
		 * Sets the lifetime of cached data
		 *
		 * @param lifeType
		 */
		function setLifeTime( $lifeTime )
		{
			$this->lifeTime = $lifeTime;
		}		

		/**
		 * Saves data to the cache. Data is identified by an id and a group. Any data
		 * can be saved to the cache but please check that you are using unique keys for
		 * different data or else data will be overwritten. If you have data that you know
		 * beforehand is not going to be unique, please use the setMultipleData method.
		 *
		 * @param id Unique identifier for the data.
		 * @param group The cache group
		 * @param data Data that is going to be cached
		 * @return Returns true if successful or false otherwise
		 */
        function setData( $id, $group, $data )
        {
			$this->cache->setLifeTime( $this->lifeTime );	
			return $this->cache->save( $data, $id, "$group" );
        }
		
		/** 
		 * Works in the same way as Cache::setData does, but instead of setting single values,
		 * it assumes that the value we're setting for the given key is part of an array of values. This
		 * method is useful for data which we know is not unique, since it will store the data
		 * identified by data in an array identified by $id, alongside with any other values
		 * that are sharing the same key.		
		 *
		 * @param id Unique identifier for the data.
		 * @param group The cache group
		 * @param data Data that is going to be cached
		 * @return Returns true if successful or false otherwise
		 */
		function setMultipleData( $id, $group, $data )
		{
			$currentData = $this->getData( $id, $group );
			if( !$currentData ) $currentData = Array();
				
			/**
			 * :TODO:
			 * It's clear that we're only going to cache DbObjects using this method
			 * but what happens if we don't? Should we force developers to provide a method
			 * to uniquely identify their own objects? We definitely need a unique id here so that
			 * the array doesn't grow forever...
			 */
			$currentData[$data->getId()] = $data;
				
            return $this->cache->save( $currentData, $id, "$group" );
		}

		/**
		 * Retrieves previously stored data given its key.
		 *
		 * @param id Unique identifier under which the data was cached
		 * @param group Cache group where data was stored
		 * @return Returns the cached data if found or false otherwise
		 */
        function getData( $id, $group )
        {
			global $__cache_hits;			
			global $__cache_queries;
			global $__cache_misses;		
			$__cache_queries++;
			
			$data = $this->cache->get( $id, $group );

			if ($data) {
				$__cache_hits++;
			}
			else {
				$__cache_misses++;						
			}

			return $data;
        }

		/**
		 * Removes cached data from the cache, given its key and cache group.
		 *
		 * @param id Unique identifier under which the data was cached
		 * @param group Cache group where data was stored
		 * @return Returns the cached data if found or false otherwise
		 */
        function removeData( $id, $group )
        {
			return $this->cache->remove( $id, $group );
        }

		/**
		 * Clears the data of a whole cache group.
		 *
		 * @param group The group identifer whose data we'd like to cleanup
		 * @return
		 */
        function clearCacheByGroup( $group )
        {
			return $this->cache->clean( $group );
        }

		/**
		 * Clears the entire cache, use with care.
		 *
		 * @return Returns true if successful or false otherwise
		 */
        function clearCache()
        {
            return $this->cache->clean();
        }

		/**
		 * Sets the directory where Cache_Lite will store its data. By default this is set to
		 * ./tmp.
		 *
		 * @param temp_folder Folder where cache data will be stored
		 * @return Always true
		 */
        function setCacheDir( $temp_folder )
        {
            $this->cache->cacheDir = $temp_folder;
        }
		
		/**
		 * Returns the total count of cache hits, miss and total queries over the lifetime of the
		 * script so far.
		 *
		 * @return An array with 3 keys: "hits", "total" and "miss"
		 */
		function getCacheStats()
		{
			global $__cache_hits;
			global $__cache_misses;
			global $__cache_queries;
		
			return( Array( "total" => $__cache_queries,
			               "hits"  => $__cache_hits,
						   "miss"  => $__cache_misses )); 
		}
    }
?>
