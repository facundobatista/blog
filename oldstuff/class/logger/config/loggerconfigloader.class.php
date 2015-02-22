<?php

	define( "LOGGER_DEFAULT_CONFIG_FILE_PATH", PLOG_CLASS_PATH."config/logging.properties.php" );
	
	lt_include( PLOG_CLASS_PATH."class/file/file.class.php" );

	/**
	 * loads the config file of the logger. By default it uses config/logging.properties.php
	 * as the config file. See the documentation for the LoggerManager for more details on how
	 * the configuration file should be laid out.
	 *
	 * \ingroup logger
	 */
	class LoggerConfigLoader
	{
	
		/**
		 * array used to store the keys
		 */
		var $_keys;
	
		function LoggerConfigLoader( $defaultFilePath = LOGGER_DEFAULT_CONFIG_FILE_PATH )
		{
			// load the config file if it is readable
			if( File::isReadable( $defaultFilePath )) {
				include( $defaultFilePath );
				$this->_keys = $config;
			}
			else {
				throw( new Exception( "There was an error loading logger config file: $defaultFilePath" ));
				die();
			}
		}
		
		/**
		 * @param key
		 * @return Returns the value assigned to the given key
		 */
		function getValue( $key )
		{
			return( $this->_keys[$key] );
		}
		
		/**
		 * returns a list of all the configured loggers
		 *
		 * @return An arrary with all the configured loggers or an empty array if no
		 * loggers are configured
		 */
		function getConfiguredLoggers()
		{
			if( is_array( $this->_keys )) 
				return( array_keys( $this->_keys ));
			else
				return( Array());
		}
		
		/**
		 * returns all the properties given a logger
		 *
		 * @param logger The logger name
		 * @return An associative array
		 */
		function getLoggerProperties( $logger )
		{
			return( $this->getValue( $logger ));
		}
		
		/**
		 * returns the default layout for a given logger
		 *
		 * @param logger The logger name
		 * @return The layout/pattern for messages logged via this logger
		 */
		function getLoggerLayout( $logger )
		{
			return( $this->getLoggerProperty( $logger, "layout" ));
		}
		
		/**
		 * returns the filename where the given logger is going to write its messages
		 *
		 * @param logger The logger name
		 * @return The filename. But this parameter is only meaningful for loggers writing to a file!
		 */
		function getLoggerFilename( $logger )
		{
			return( $this->getLoggerProperty( $logger, "file" ));
		}
		
		/**
		 * returns the appender used by the given logger
		 *
		 * @param logger the logger name
		 * @return the appender configured for this logger
		 */
		function getLoggerAppender( $logger )
		{
			return( $this->getLoggerProperty( $logger, "appender" ));
		}
		
		/**
		 * returns all properties of a given logger
		 *
		 * @param logger The logger name
		 * @param property The property name
		 * @private
		 * @return the value of the property for the given logger
		 */
		function getLoggerProperty( $logger, $property )
		{
			$loggerProperties = $this->getLoggerProperties( $logger );
			
			return( $loggerProperties[$property] );		
		}
	}
?>