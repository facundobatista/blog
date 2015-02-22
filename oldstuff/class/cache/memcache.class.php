<?php

	lt_include( PLOG_CLASS_PATH."class/cache/basecacheprovider.class.php" );

	$__memcache_hits = 0;
	$__memcache_misses = 0;
	$__memcache_queries = 0;

	/**
	 * \ingroup Cache
	 *
	 * Support for caching via memcached
	 */
    class MemCache extends BaseCacheProvider
    {
        var $cache;
        var $lifeTime;

        var $_disabledCacheCategories = array();

        function MemCache( $cacheProperties )
        {
			$this->BaseCacheProvider();
	
            lt_include( PLOG_CLASS_PATH . "class/cache/Memcached_Client/memcached-client.php" );
            
            $this->cache = new memcached( $cacheProperties );
            $this->lifeTime = $cacheProperties['life_time'];
        }

		function setLifeTime( $lifeTime )
		{
			$this->lifeTime = $lifeTime;
		}

        function setData( $id, $group, $data )
        {
			$key = $this->getKey( $id, $group );
            return $this->cache->set( $key, $data, $this->lifeTime );
        }
		
		/** 
		 * Works in the same way as Cache::setData does, but instead of setting single values,
		 * it assumes that the value we're setting for the given key is part of an array of values. This
		 * method is useful for data which we know is not unique.
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

			return $this->setData( $id, "$group", $currentData );
		}

        function getData( $id, $group )
        {
			global $__memcache_hits;			
			global $__memcache_queries;
			global $__memcache_misses;		
		
			$__memcache_queries++;

			$key = $this->getKey( $id, $group );
			$data = $this->cache->get( $key );

			if ($data) {
				$__memcache_hits++;
			}
			else {
				$__memcache_misses++;						
			}

			return $data;
        }

        function removeData( $id, $group )
        {
			$key = $this->getKey( $id, $group );
			return $this->cache->delete( $key, $group );
        }

        function clearCacheByGroup( $group )
        {
            return true;
        }

        function clearCache()
        {
            return $this->cache->flush_all();
        }

		/**
		 * returns the total count of cache hits, miss and total queries over the lifetime of the
		 * script so far.
		 *
		 * @return An array with 3 keys: "hits", "total" and "miss"
		 */
		function getCacheStats()
		{
			global $__memcache_hits;
			global $__memcache_misses;
			global $__memcache_queries;
		
			return( Array( "total" => $__memcache_queries,
			               "hits"  => $__memcache_hits,
						   "miss"  => $__memcache_misses )); 
		}
		
		function getKey( $id, $group )
		{
			return $group.':'.$id;	
		}
    }
?>