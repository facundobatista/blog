<?php

	/**
	 * global variables to take care of the timing stuff
	 */
	$__metrics_start_time = 0;
	$__metrics_end_time = 0;

	/**
	 * Basic class for obtaining certain information about the script and the server
	 * itself. This class will in the future have benchmarking methods, etc.
	 */
	class Info
	{
		/**
		 * @return Returns the peak memory usage (probably more useful, though only exists on php5)
                       or current memory usage (on PHP4),
                       or -1 if it cannot be determined.
		 * @static
		 */		 
		function getMemoryUsage()
		{
			if( function_exists( "memory_get_peak_usage" )) {
				return( memory_get_peak_usage());
			}
            else if( function_exists( "memory_get_usage" )) {
				return( memory_get_usage());
			}
            return "";
		}
		
		/**
		 * Starts the measurement. This method should be called before logMetrics is called, and preferably,
		 * at the beginning of the script
		 *
		 * @static
		 * @return Always true
		 */
		function startMetrics()
		{
			global $__metrics_start_time;
			
			$mtime = microtime(); 
			$mtime = explode(" ",$mtime); 
			$mtime = $mtime[1] + $mtime[0]; 
			$__metrics_start_time = $mtime;
			
			return( true );
		}
		
		
		/**
		 * sends performance statistics in CSV format to the output defined by the 'metricslog' logger.
		 *
		 * @static
		 */
		function logMetrics()
		{
			lt_include( PLOG_CLASS_PATH."class/logger/loggermanager.class.php" );
			lt_include( PLOG_CLASS_PATH."class/cache/cachemanager.class.php" );
			lt_include( PLOG_CLASS_PATH."class/database/db.class.php" );
			lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );
		
			global $__metrics_start_time;
			
			$mtime = microtime(); 
			$mtime = explode(" ",$mtime); 
			$mtime = $mtime[1] + $mtime[0]; 
			$__metrics_end_time = $mtime;			
			
			// calculate the execution time
			$totalTime = ( $__metrics_end_time - $__metrics_start_time );
			
			// load cache statistics
			$cache =& CacheManager::getCache();
			$cacheData = $cache->getCacheStats();
			
			// get information from the request
			$script = $_SERVER["SCRIPT_NAME"];
			$requestUri = $_SERVER["REQUEST_URI"];
			
			// get the number of sql queries
			$db =& Db::getDb();
			$numQueries = $db->getNumQueries();
			if( $numQueries == "" ) $numQueries = 0;
			
			// build the message
			$t = new Timestamp();
			$message = $t->getTimestamp().",".
			           Info::getMemoryUsage().",".
			           $totalTime.",".
					   count(get_included_files()).",".
					   $numQueries.",".
					   $cacheData["total"].",".
					   $cacheData["hits"].",".
					   $cacheData["miss"].",\"".
					   $script."\",\"".
					   $requestUri."\"";
			
			// get the logger and log the message
			$logger =& LoggerManager::getLogger( "metricslog" );			
			$logger->debug( $message );
		}
	}
?>