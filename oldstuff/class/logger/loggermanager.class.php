<?php

	lt_include( PLOG_CLASS_PATH."class/logger/logger/logger.class.php" );
	lt_include( PLOG_CLASS_PATH."class/logger/config/loggerconfigloader.class.php" );
	lt_include( PLOG_CLASS_PATH."class/logger/layout/patternlayout.class.php" );

	/**
	 * \defgroup logger
	 *
	 * The logger package takes care of providing logging facilities for the pLog API. It is a very simple
	 * API but on the other hand, it follows some of the principles establishes by log4j and log4php, with
	 * a very similar system of layouts, formatters and appenders.
	 *
	 * See the documentation of the LoggerManager class for more information on how to use the class and how
	 * to format the messages
	 */
	 
	/** 
	 * \ingroup logger
	 *
	 * The logger manages a bunch of loggers configured in the config/logging.properties.php file. 
     * By default if no loggers are specified it will create one logger called "default".
	 * In order to define new loggers, the configuration file has to look like this:
	 *
	 * <pre>
	 * $config["logger_name"] = Array( 
	 *       "appender" => "name_of_the_appender_class",
	 *       "layout"   => "a pattern definition for the log messages",
	 *       "file"     => "file_for_fileappender",
	 *       "prio"     => "debug" );
	 * </pre>
	 *
	 * Where "logger_name" is the identifer that we will use later on in the call to 
     * LoggerManager::getLogger() later on to retrieve a handle to this particular logger.
	 * 
	 * The available appenders are: "file", "stdout", "null"
	 *   The FileAppender   class writes data to a log file, which must be specified via a 
     *                      property called "file"
	 *   The StdoutAppender class writes data to the console
	 *   The NullAppender   does not write data anywhere, it's a "dumb" logger that does nothing.
     *                      If you do not want a logfile to be written, but want to safe your
     *                      configuration, just change the appender from 'file' to 'null' :-)
	 *
	 * Layout formats: 
	 *   PatternLayout: allows to specify a pattern for our messages. Patterns are specified as follows:
	 * <ul>
	 *     <li><b>%c</b>               - the class where message was logged</li>
	 *     <li><b>%d</b>               - current date</li>
	 *     <li><b>%f</b>               - the file where the message was logged</li>
	 *     <li><b>%F</b>               - the function where the message was
	 *                                   logged</li>
	 *     <li><b>%l</b>               - the line where the message was logged</li>
	 *     <li><b>%m</b>               - the log message</li>
	 *     <li><b>%n</b>               - a newline</li>
	 *     <li><b>%N</b>               - the level name</li>
	 *     <li><b>%p</b>               - the level of priority</li>
	 *     <li><b>%r</b>               - a carriage return</li>
	 *     <li><b>%t</b>               - a horizontal tab</li>
	 *     <li><b>%T</b>               - a unix timestamp (seconds since January 1st, 1970)</li>
	 * </ul>
	 *
	 * The available priorities are: error > warn > info
	 *
	 * NOTE: There can only be one appender and one layout class per logger.
	 *
	 * In order to retrieve a handle to one of our loggers later on in our code, we should use
	 * LoggerManager::getLogger()
	 *
	 * <pre>
	 *    $log =& LoggerManager::getLogger( "logger_name" );
	 *    $log->debug( $message );
	 *    ...
	 *    $log->info( $message );
	 * </pre>
	 */
    class LoggerManager
    {
        /**
         * An associative array of loggers.
         */
        var $loggers;

        /**
         * Create a new LogManager instance.
         * This should never be called manually.
         */
        function LoggerManager()
        {
            $this->loggers = array();
			
			$this->_loadLoggerConfig();
        }
		
		/**
		 * @private
		 * Loads the configuration from the config file
		 */
		function _loadLoggerConfig()
		{
			// load the config file and see how many loggers are configued
			$config = new LoggerConfigLoader();
			$loggers = $config->getConfiguredLoggers();
			
			if( count($loggers) == 0 ) {
				// add a default logger
				$loggers["default"] = Array( "layout" => "%d %N - [%f:%l (%c:%F)] %m%n",
											 "appender" => "stdout",
											 "prio" => "debug" );
			}
			
			// loop through the loggers and configure them
			foreach( $config->getConfiguredLoggers() as $logger ) {		
				// get the logger config properties
				$properties = $config->getLoggerProperties( $logger );
				
				// create the logger
				$layout = new PatternLayout( $config->getLoggerLayout( $logger ));
				$appenderObject = $this->createAppenderInstance( $config->getLoggerAppender( $logger ), 
                                                                 $layout, $properties );
				
				// create the logger, set the appender and it to the list
				$loggerObject = new Logger( $properties );
				$loggerObject->addAppender( $appenderObject );
				$this->addLogger( $logger, $loggerObject );
			}
		}
		
		/**
		 * dynamically loads a layout formatter
		 *
		 * @param appenderName
		 * @return a qLayout class
		 */
		function createAppenderInstance( $appender, $layout, $properties )
		{
			$appenderClassName = $appender."appender";
			$appenderClassFile = PLOG_CLASS_PATH."class/logger/appender/".$appenderClassName.".class.php";
			
			// load the class but first check if it exists...
			if( !File::isReadable( $appenderClassFile )) {
				throw( new Exception( "Cannot find an appender suitable for appender type '$appender'" ));
				die();
			}
			
			// if so, load the class and create an object
			lt_include( $appenderClassFile );			
			$appender = new $appenderClassName( $layout, $properties );
			
			return( $appender );
		}

        /**
         * Add a logger.
         * If a logger with the given name already exists, an error will be
         * reported.
         *
         * @param string A logger name.
         * @param Logger A Logger instance.
         */
        function addLogger ($name, $logger)
        {
            if (isset($this->loggers[$name])) {
                throw(new Exception("LogManager::addLogger: LogManager already contains logger " . $name));
                die();
            }

            $this->loggers[$name] = $logger;
			
            return;
        }

        /**
         * Retrieve the single instance of LogManager.
         *
         * @return qLogManager A qLogManager instance.
         */
        function &getInstance()
        {
            static $instance = NULL;

            if ($instance === NULL) {
                $instance = new LoggerManager();
            }

            return $instance;
        }

        /**
         * Retrieve a logger.
         * If a name is not specified, the default logger is returned.
         *
         * @param string A logger name.
         * @return Logger A Logger instance, if the given Logger exists, otherwise NULL.
         */
        function &getLogger ($name = "default")
        {
			$instance =& LoggerManager::getInstance();
		
            if (isset($instance->loggers[$name])) {
                return $instance->loggers[$name];
            }
		
            return NULL;
        }
    }
?>
