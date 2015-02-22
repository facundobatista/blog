<?php
        
	/**
	 * \ingroup Cache
	 *
	 * This is the base class that all cache providers should extends. It defines all the base methods
	 * that are expected from a base provider.
	 *
	 * :TODO:
	 * This needs to be turned into an interface or base abstract class when moving to PHP5
	 */
	class BaseCacheProvider
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
			throw( new Exception( "This method must be implemented by child classes!" ));
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
			throw( new Exception( "This method must be implemented by child classes!" ));	
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
			throw( new Exception( "This method must be implemented by child classes!" ));			
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
			throw( new Exception( "This method must be implemented by child classes!" ));	
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
			throw( new Exception( "This method must be implemented by child classes!" ));	
        }

		/**
		 * Clears the data of a whole cache group.
		 *
		 * @param group The group identifer whose data we'd like to cleanup
		 * @return
		 */
        function clearCacheByGroup( $group )
        {
			throw( new Exception( "This method must be implemented by child classes!" ));	
        }

		/**
		 * Clears the entire cache, use with care.
		 *
		 * @return Returns true if successful or false otherwise
		 */
        function clearCache()
        {
			throw( new Exception( "This method must be implemented by child classes!" ));	
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
			throw( new Exception( "This method must be implemented by child classes!" ));	
        }
		
		/**
		 * Returns the total count of cache hits, miss and total queries over the lifetime of the
		 * script so far.
		 *
		 * @return An array with 3 keys: "hits", "total" and "miss"
		 */
		function getCacheStats()
		{
			throw( new Exception( "This method must be implemented by child classes!" ));			
		}
	}
?>