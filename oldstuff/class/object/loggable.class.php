<?php

	/**
	 * \ingroup Core
	 *
	 * Please extend this class if you wish to provide a basic loggin channel for your classes via the
	 * <b>$this->log</b> public attribute. The "default" logger will be used, make sure it is configured
	 * properly in the file config/logging.properties.php.
	 *
	 * @see LoggerManager
	 */
	class Loggable 
	{

		var $log;

    	/**
         * Constructor
         */
		function Loggable()
		{
			lt_include( PLOG_CLASS_PATH."class/logger/loggermanager.class.php" );
		
			// initialize logging -- enable this only for debugging purposes
			$this->log =& LoggerManager::getLogger( "default" );
		}
	}
?>
