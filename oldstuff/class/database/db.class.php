<?php

	/**
	 * \defgroup Database
	 *
	 * <p>This module includes all database-related objects.</p>
	 *
	 * <p>The most important one is the Db object, which provides one centralized way to get
	 * access to the current database connection. In fact, there is no reason for classes that require
	 * database access to use the PDb framework directly, as the Db::getDb() singleton method will
	 * transparently load the database information from the configuration file, connect to it and return
	 * a working connection.</p>
	 *
	 * <p>In order to get a working database connection, this is all you need to do:</p>
	 *
	 * <pre>
	 * $db =& Db::getDb();
	 * </pre>
	 *
	 * <p>For those classes that extend the Model base class, the private attribute Model::_db is a database
	 * connection so there is no need to initialize a new one.</p>
	 */
	
    lt_include( PLOG_CLASS_PATH."class/config/configfilestorage.class.php" );
    lt_include( PLOG_CLASS_PATH."class/database/pdb/pdb.class.php" );

    /**
	 * \ingroup Database
	 *
     * Provides a singleton for accessing the db and interfaces with PDb. Please use the 
     * getDb() singleton method to get access to the global database object instead of creating
 	 * new objects every time.
     */
	class Db  
	{
        var $_prefix;

		/**
		 * Constructor of the class
		 */
		function Db()
		{
        	
		}

		/**
		 * Singleto method that should be used to get access to the global database connection. This method
		 * will load the database information from the database configuration file (config/config.properties.php) and
		 * will initialize a connection based on its information. If it is unable to start a new database connection, this
		 * method wil stop all processing and display an error message.
		 *
		 * @return Returns a reference to a PDb driver, with a working connection to the database.
		 * @see PDb::getDriver()
		 */
		function &getDb()
		{
			static $db;

            if( !isset( $db )) {
            	// we need to connect to the db
                $fileConfig = new ConfigFileStorage();

				//$db = NewADOConnection('mysql');
                $db = PDb::getDriver('mysql');

                $username  = $fileConfig->getValue( "db_username" );
                $password  = $fileConfig->getValue( "db_password" );
                $host      = $fileConfig->getValue( "db_host" );
                $dbname    = $fileConfig->getValue( "db_database" );
                $dbcharset = $fileConfig->getValue( "db_character_set" );
                $dbpersistent   = $fileConfig->getValue( "db_persistent" );
                if($dbpersistent == true) {
	            	if( !$db->PConnect( $host, $username, $password, $dbname, $dbcharset )) {
	            	    $message = "Fatal error: could not connect to the database!".
	            	               " Error: ".$db->ErrorMsg();
	            		throw( new Exception( $message ));
	            		die();
	            	}
            	}
            	else {
	            	if( !$db->Connect( $host, $username, $password, $dbname, $dbcharset )) {
	            	    $message = "Fatal error: could not connect to the database!".
	            	               " Error: ".$db->ErrorMsg();
	            		throw( new Exception( $message ));
	            		die();
	            	}
            	}

				// pass the options to the driver, if any
				$db->setDriverOpts( $fileConfig->getValue( "db_options" ));
            }
            
            return $db;
		}
		
		/**
		 * Creates a new database instance based on the information provided
		 *
		 * @param host The database host where to connect
		 * @param username The username used for the connection
		 * @param password The password used for the connection
		 * @param dbname The name of the database
		 * @param returns a reference to a PDb driver or dies if there was an error connecting
		 */
		function &getNewDb( $host, $username, $password, $dbname ) 
		{
			static $dbs;
			
			if( !is_array( $dbs ))
				$dbs = Array();
				
			$key = $username.$password.$host.$dbname;
				
			if( !isset( $dbs[$key] )) {
                $dbs[$key] = PDb::getDriver('mysql');
            	if( !$dbs[$key]->PConnect( $host, $username, $password, $dbname )) {
            		throw( new Exception( "getNewDb: Fatal error: could not connect to the database!" ));
                	die();
            	}
			}	
			
			$conn = $dbs[$key];	
			
			return( $conn );				
		}

		/**
		 * returns the prefix as configured for this database connection
		 *
		 * @return A string containing the database prefix
		 * @static
		 */
		function getPrefix()
		{
            static $prefix;

            if ( isset($prefix) ) {
                return $prefix;
            } else {
			    $fileConfig = new ConfigFileStorage();
			    $prefix = $fileConfig->getValue( "db_prefix" );
			    return( $prefix );	
            }
		}

		/**
		 * Prepares a string for an SQL query by escaping apostrophe
		 * characters. Apostrophe
		 * characters are doubled, conforming with the ANSI SQL standard.
		 * The SQL parser makes sure that the escape token is not entered
		 * in the database so there is no need to modify the data when it
		 * is read from the database.
         *
         * TODO: use mysql_real_escape_string instead.  Code should be refactored
         *   to account for different database engines.  e.g. this function should
         *   be completely removed, and each db engine should quote its own stuff
		 *
		 * @param  string $string
		 * @return string
		 * @access public
		 */
		function qstr($string) {
			$string = str_replace("\\", "\\\\", $string);
 			$string = str_replace("'", "''", $string);
			return $string;
		}
    }
?>