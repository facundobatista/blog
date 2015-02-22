<?php
  
	lt_include( PLOG_CLASS_PATH."class/cache/basecacheprovider.class.php" );
	 
	/**
	 * \ingroup Cache
	 *
	 * This is a cache class that does nothing, and that should be used whenever we want to
	 * totally deactivate the data cache. Just set $config["cache_method"] to 'null' in file
	 * config/cache.properties.php and the CacheManager factory will take care of loading
	 * this file.
	 *
	 * This cache provider implements the BaseCacheProvider interface but it does no caching
	 * and methods such as NullCache::getData() always return false.   
	 */
	class NullCache extends BaseCacheProvider	
	{
		function BaseCacheProvider()
		{			
		}
		
		/** 
		 * Sets the lifetime of cached data
		 *
		 * @param lifeTime
		 */
		function setLifeTime( $lifeTime )		
		{
			return( true );
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
			return( true );
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
			return( true );
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
			return( false );
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
			return( false );
        }

		/**
		 * Clears the data of a whole cache group.
		 *
		 * @param group The group identifer whose data we'd like to cleanup
		 * @return
		 */
        function clearCacheByGroup( $group )
        {
			return( true );
        }

		/**
		 * Clears the entire cache, use with care.
		 *
		 * @return Returns true if successful or false otherwise
		 */
        function clearCache()
        {
			return( false );
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
			return( true );
        }
		
		/**
		 * Returns the total count of cache hits, miss and total queries over the lifetime of the
		 * script so far.
		 *
		 * @return An array with 3 keys: "hits", "total" and "miss"
		 */
		function getCacheStats()
		{
			return( Array( "hits" => 0, "total" => 0, "miss" => 0 ));
		}		
	}
?>