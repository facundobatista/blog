<?php

    
    lt_include( PLOG_CLASS_PATH."class/config/configabstractstorage.class.php" );
	lt_include( PLOG_CLASS_PATH."class/cache/cachemanager.class.php" );
	lt_include( PLOG_CLASS_PATH.'class/dao/daocacheconstants.properties.php' );

    /**
	 * \ingroup Config
	 *
     * Storage backend that stores/retrieves the data from the plog_config
     * table.
     * The structure of the table is as follows:
	 *
     * - id: setting identifier
     * - config_key: Name of the setting. Can't be empty
     * - config_value: Value assigned to the key
     * - value_type: This field can take several values and gives the class
     *               a hint regarding the type of the value:
     * -- TYPE_INTEGER: It is saved as is.
     * -- TYPE_BOOLEAN: It is saved as 1 == true and 0 == false.
     * -- TYPE_STRING: It is saved as is.
     * -- TYPE_OBJECT: The object is saved in a seralized way.
     * -- TYPE_ARRAY: The arrays are also saved serialized. This is transparently
     * done inside the save() and saveValue() methods, and therefore the user
     * does not have to worry about doing it.
     * -- TYPE_FLOAT: It is saved as is.
     * 
     * Type detection is provided via the built-in mechanisms that PHP offers.
     * </ul>
     */
    class ConfigDbStorage extends ConfigAbstractStorage 
    {
        // ADOdb handler
        var $_db;

        // array used to store the options
        var $_data = array();
		
    	// information needed to connect to the db server
        var $_dbPrefix;		

		// cache object
		var $_cache;
        
        /**
         * Connects to the database using the parameters in the config file.
         *
         */
    	function ConfigDbStorage()
        {            
		    $this->_cache =& CacheManager::getCache();
		
			// load data from the databas, but only if it was already not in the cache
			if( (!$this->_data = $this->_cache->getData( CACHE_CONFIGDBSTORAGE, CACHE_GLOBAL ))) {
				$this->_loadAllValuesFromDatabase();				
			}
        }

        function _loadAllValuesFromDatabase() 
		{
            // initialize the database
            $this->_initializeDatabase();

            // load the whole data
            $this->_loadData();

            // and build the cache
            $this->_cache->setData( CACHE_CONFIGDBSTORAGE, CACHE_GLOBAL, $this->_data );
        }

        /**
         * Initialize the Database to allow db access
         *
         */
        function _initializeDatabase() 
		{
            if ($this->_db == null) {
                // source the neccessary class files
                lt_include( PLOG_CLASS_PATH."class/database/db.class.php" );

                // initialize the connection
                $this->_db =& Db::getDb();
                // get the prefix
                $this->_dbPrefix = Db::getPrefix();
            }
        }

        /**
         * Internal function that loads all the data from the table and puts in into
         * our array. It should be apparently faster that making an SQL query every time
         * we need to get a value.
         *
         * @private
         */
        function _loadData()
        {
        	$this->_data = Array();

            // build and execute the query
            $query = "SELECT * FROM ".$this->_dbPrefix."config";
            $result = $this->_db->Execute( $query );

            // this is a severe error
            if( !$result ) {
	            print($this->_db->ErrorMsg());
            	throw( new Exception( "There was an error loading the configuration data from the database. And this is bad..." ));
                die();
            }

            // otherwise, go through the records and put them in the array
            while( $row = $result->FetchRow()) {
            	$key = $row["config_key"];
                $value = $row["config_value"];
                $dataType = $row["value_type"];

                // arrays and objects are saved serialized so we should check
                // the type and deserialize that if necessary
                if( $dataType == TYPE_OBJECT || $dataType == TYPE_ARRAY ) {
                	$this->_data[$key] = unserialize( stripslashes($value));
                    if( $dataType == TYPE_ARRAY && $this->_data[$key] == "" )
                    	$this->_data[$key] = Array();
                }
                else
                	$this->_data[$key] = $value;
            }
            $result->Close();
            
            return true;
        }

		/**
		 * @see ConfigAbstractStorage::getValue()
		 */
        function getValue( $key, $defaultValue = null )
        {
            if( array_key_exists($key, $this->_data) ) {
                if ($this->_data[$key] == "" || $this->_data[$key] == null) {
                    return $defaultValue;
                } else {
                    return $this->_data[$key];
                }
            } else {
            	return $defaultValue;
            }
        }

		/**
		 * @see ConfigAbstractStorage::setValue()
		 */
        function setValue( $key, $value )
        {

        	$this->_data[$key] = $value;

            return true;
        }

		/**
		 * @see ConfigAbstractStorage::getAsArray()
		 */
        function getAsArray()
        {
        	return $this->_data;
        }

		/**
		 * @see ConfigAbstractStorage::getConfigFileName()
		 */
        function getConfigFileName()
        {
        	return "database";
        }

		/**
		 * Resets the current configuration settings and loads them from the database.
		 *
		 * @see ConfigAbstractStorage::reload()
		 */
        function reload()
        {
        	$this->_loadData();
        }

		/**
		 * @see ConfigAbstractStorage::getKeys()
		 */
        function getKeys()
        {
        	return array_keys($this->_data);
        }

		/**
		 * @see ConfigAbstractStorage::getValues()
		 */
        function getValues()
        {
        	return array_values($this->_data);
        }

        /**
         * Internal function that returns true if the given key exists in the database.
         *
         * @private
         * @param key The name of the key we'd like to check
         * @return Returns true if it exists or false otherwise.
         */
        function _keyExists( $key )
        {
            // initialize the database
            $this->_initializeDatabase();

            $query = "SELECT * FROM ".$this->_dbPrefix."config WHERE config_key = '$key'";

            //$this->_db->debug=true;
            $result = $this->_db->Execute( $query );

            if( !$result )
            	return false;

            $ret = ($result->RowCount() > 0);
            $result->Close();
            if($ret)
            	return true;
            else
            	return false;
        }

		/**
		 * @private
		 */
        function _updateValue( $key, $value )
        {
            // initialize the database
            $this->_initializeDatabase();

        	// if the key exists, we have to update it
            $type = $this->_getType( $value );
            switch( $type ) {
				 case TYPE_INTEGER:
                 case TYPE_BOOLEAN:
                 case TYPE_FLOAT:
                       // TODO: Note, this is a little broken.  We ask PHP for the type of
                       // the variable, so it always returns a string, so this code isn't used.
                       // Probably the _getType function should be rewritten to use the values
                       // from the database instead, rather than rewriting the types that are
                       // stored in the database.  We should fix this in the 2.0 wizard, since
                       // all int/bool/floats have been stored in the database as a string.
                       // All that is required is to change the value_type column, the data is fine.
                 	$query = "UPDATE ".$this->_dbPrefix."config SET config_value =
                             '$value', value_type = $type WHERE config_key = '$key'";
                    break;
                 case TYPE_STRING: // need to add quotes here
                 	$query = "UPDATE ".$this->_dbPrefix."config SET config_value =
                             '".Db::qstr($value)."', value_type = $type WHERE config_key = '$key'";
                    break;
                 case TYPE_ARRAY:
                 case TYPE_OBJECT:	// need to serialize here
                 	$serValue = addslashes(serialize( $value ));
                 	$query = "UPDATE ".$this->_dbPrefix."config SET config_value =
                             '$serValue', value_type = $type WHERE config_key = '$key'";
                    break;
                 default:
                 	throw( new Exception( "_updateValue: _getType produced an unexpected value of $type when checking value \"$value\""));
                    die();
             }

             $result = $this->_db->Execute( $query );

             if( $result )
             	return true;
             else
             	return false;
        }

		/**
		 * @private
		 */
        function _insertValue( $key, $value )
        {
            // initialize the database
            $this->_initializeDatabase();

        	$type = $this->_getType( $value );
            switch( $type ) {
            	case TYPE_INTEGER:
                case TYPE_BOOLEAN:
                case TYPE_FLOAT:
                	$query = "INSERT INTO ".$this->_dbPrefix."config (config_key,config_value,value_type)
                              VALUES( '$key', '$value', $type )";
                    break;
                case TYPE_STRING: // need to add quotes here
                     $query = "INSERT INTO ".$this->_dbPrefix."config (config_key,config_value,value_type)
                              VALUES( '$key', '".Db::qstr($value)."', $type )";
                     break;
                case TYPE_ARRAY:
                case TYPE_OBJECT:	// need to serialize here
                 	$serValue = addslashes(serialize( $value ));
                    $query = "INSERT INTO ".$this->_dbPrefix."config (config_key,config_value,value_type)
                              VALUES( '$key', '$serValue', $type )";
                    break;
                default:
                    throw( new Exception( "_insertValue: _getType produced an unexpected value of $type" ));
                    die();
             }

             $result = $this->_db->Execute( $query );

             if( $result )
             	return true;
             else
             	return false;
        }

        /**
         * Puts all the settings back to the database.
         *
         * It is done so that we first check if the key exists. If it does, we then
         * send an update query and update it. Otherwise, we add it.
         *
         * @param key The name of the key
         * @param The value.
         * @return True if successful or false otherwise
         */
        function save()
        {
            // load all the data
        	foreach( $this->_data as $key => $value ) {
            	$this->saveValue( $key, $value );
            }

            // update the cache
            $this->_cache->setData( CACHE_CONFIGDBSTORAGE, CACHE_GLOBAL, $this->_data );

            // saveValue is already reloading the data for us everytime!
            return true;
        }

        /**
         * Puts just one setting back to the database.
         *
         * It is done so that we first check if the key exists. If it does, we then
         * send an update query and update it. Otherwise, we add it.
         *
         * @param key The name of the key
         * @param The value.
         * @return True if successful or false otherwise
         */
        function saveValue( $key, $value )
        {
            if( $this->_keyExists( $key )) {
                // just update it in the db
                $result = $this->_updateValue( $key, $value );
            }
            else {
            	 // we have to first insert the data into the db
                 $result = $this->_insertValue( $key, $value );
            }

            // update the cache
            $this->_data[$key] = $value;
            $this->_cache->removeData( CACHE_CONFIGDBSTORAGE, CACHE_GLOBAL );

            // we better reload the data just in case
            // $this->reload();

            return $result;
        }
    }
?>