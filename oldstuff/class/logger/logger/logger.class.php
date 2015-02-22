<?php

    
	lt_include( PLOG_CLASS_PATH."class/logger/logger/loggedmessage.class.php" );
	
	/**
	 * different priorities that can be used for logging
	 */
	define( "LOGGER_PRIO_INFO", "info" );
	define( "LOGGER_PRIO_WARN", "warn" );
	define( "LOGGER_PRIO_ERROR", "error" );
	define( "LOGGER_PRIO_DEBUG", "debug" );

    /**
     * Logger provides an interface for logging messages to multiple appenders. A logger is the base
	 * class used to log. Each logger has an appender (currently only one appender per logger) and
	 * each appender has its own layout. Also each logger can have its own custom parameter that depend
	 * on the logger such as priority, etc. 
	 *
	 * Loggers are defined in the configuration file (config/logger.properties.php) and it is usually
	 * a better idea to access them via LoggerManager::getLogger($loggerId) Of course we can always built our
	 * own custom loggers if needed.
	 *
	 * @see LoggerManager
     */
    class Logger
    {
        /**
         * An associative array of appenders.
         */
        var $appenders;

        /**
         * Create a new Logger instance.
         *
         * @access public
         * @since  1.0
         */
        function Logger( $loggerProperties )
        {
			// get the priority at which we're logging
			$this->prio = $loggerProperties["prio"];
        }

		/**
		 * Given a priority and the current priority used by this logger, returns
		 * true whether this message is loggable or not.
		 *
		 * @param prio The priority which we're checking whether it is loggable or not
		 * @return true if it is loggable or false if not
		 */
		function isLoggable( $prio )
		{
			$loggable = false;
		
			switch( $this->prio ) {
				case LOGGER_PRIO_DEBUG:
					$loggable = true;
					break;
				case LOGGER_PRIO_ERROR:
					$loggable = ( $prio == LOGGER_PRIO_ERROR );
					break;
				case LOGGER_PRIO_WARN:
					$loggable = ( $prio == LOGGER_PRIO_WARN || $prio == LOGGER_PRIO_ERROR );
					break;
				case LOGGER_PRIO_INFO:
					$loggable = ( $prio != LOGGER_PRIO_DEBUG );
					break;
			}
			
			return( $loggable );
		}

        /**
         * Add an appender.
         *
         * <br/><br/>
         *
         * <note>
         *     If an appender with the given name already exists, an error will be
         *     reported.
         * </note>
         *
         * @param string   An appender name.
         * @param Appender An Appender instance.
         *
         * @access public
         * @since  1.0
         */
        function addAppender( $appender )
        {
            $this->appenders[] = $appender;
            return;
        }
		
		/**
		 * logs a message to all appenders, using the given layout with priority DEBUG
		 *
		 * @param message
		 * @return always true
		 */
		function debug( $message )
		{
			return( $this->log( $message, LOGGER_PRIO_DEBUG ));		
		}

		/**
		 * logs a message to all appenders, using the given layout with priority INFO
		 *
		 * @param message
		 * @return always true
		 */		
		function info( $message )
		{
			return( $this->log( $message, LOGGER_PRIO_INFO ));
		}
		
		/**
		 * logs a message to all appenders, using the given layout with priority WARN
		 *
		 * @param message
		 * @return always true
		 */		
		function warn( $message )
		{
			return( $this->log( $message, LOGGER_PRIO_WARN ));		
		}

		/**
		 * logs a message to all appenders, using the given layout with priority ERROR
		 *
		 * @param message
		 * @return always true
		 */		
		function error( $message )
		{
			return( $this->log( $message, LOGGER_PRIO_ERROR ));
		}

        /**
         * Log a message.
         *
         * @param Message A string to log
         *
		 * @private
         */
        function log( $message, $prio )
        {
			if( $this->isLoggable( $prio )) {
				// if the event is loggable, build the message and log it
				$msgObject = new LoggedMessage( $message );
				$msgObject->setParameter( "prio", $prio );
		
				// loop through appenders and write to each one
				foreach( $this->appenders as $appender ) {
					$layout   = $appender->getLayout();
					$result   = $layout->format( $msgObject );
					$appender->write($result);
				}
			}
			
			return true;
        }
    }

?>
