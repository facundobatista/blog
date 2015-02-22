<?php

	

    define( "TYPE_INTEGER", 1 );
    define( "TYPE_BOOLEAN", 2 );
    define( "TYPE_STRING",  3 );
    define( "TYPE_OBJECT",  4 );
    define( "TYPE_ARRAY",   5 );
    define( "TYPE_FLOAT",   6 );

    /**
	 * \ingroup Config
	 *
     * Interface class that defines the methods that should be implemented
     * by child classes wishing to implement a configuratino settings storage backend.
	 * 
	 * This class cannot be instantiated directly, and an attempt to call any of its methods
	 * will end our script.
     */
    class ConfigAbstractStorage  
	{

    	function ConfigAbstractStorage( $params = null )
        {
        	
        }

        /**
         * Returns a constant determining the type of the value passed as parameter. The constants
         * are defined above.
         *
         * @param value The value from which we'd like to know its type
         * @return Returns one of the above.
         */
        function _getType( $value )
        {
        	if( is_integer( $value ))
            	$type = TYPE_INTEGER;
			elseif( is_float( $value ))
            	$type = TYPE_FLOAT;
            elseif( is_bool( $value ))
            	$type = TYPE_BOOLEAN;
            elseif( is_string( $value ))
            	$type = TYPE_STRING;
            elseif( is_object( $value ))
            	$type = TYPE_OBJECT;
            elseif( is_array( $value ))
            	$type = TYPE_ARRAY;
            else
            	$type = TYPE_STRING;

            //print("type = ".$type."<br/>" );

            return $type;
        }

		/**
		 * Given a key, gets its value from the storage backend. 
		 *
		 * @param key The key that we're looking for
		 * @param defaultValue Optional, if the key does not exist we can provide a second parameter that
		 * will be returned as the default value
		 * @return The value assigned to the key or $defaultValue if there was no such key found
		 */
        function getValue( $key, $defaultValue = null )
        {
        	throw( new Exception( "ConfigAbstractStorage::getValue: This method must be implemented by child classes." ));
            die();
        }

		/**
		 * Sets a value in the storage area. It is up to the storage backend implementation to either 
		 * save the data right away after calling this method or whether it is needed to call
		 * ConfigAbstractStorage::save() in order to do so.
		 *
		 * @param key The key that we'd like to save
		 * @param value The value that should be assigned to this key
		 * @return True if successful or false otherwise
		 */
        function setValue( $key, $value )
        {
        	throw( new Exception( "ConfigAbstractStorage::setValue: This method must be implemented by child classes." ));
            die();
        }

		/**
		 * Returns all the configuration parameters as an associative array
		 *
		 * @return An associative array
		 */
        function getAsArray()
        {
        	throw( new Exception( "ConfigAbstractStorage::getAsArray: This method must be implemented by child classes." ));
            die();
        }

		/**
		 * triggers a reload of all the settings from the backend.
		 *
		 * @return True if successful or false otherwise
		 */
        function reload()
        {
        	throw( new Exception( "ConfigAbstractStorage::reload: This method must be implemented by child classes." ));
            die();
        }

		/**
		 * returns the name of the configuration file where data is being saved. If the backend does not
		 * use a configuration file, the result of this method is an empty string
		 *
		 * @return name of the config file, or empty if not used
		 */
        function getConfigFileName()
        {
        	throw( new Exception( "ConfigAbstractStorage::getConfigFileName: This method must be implemented by child classes." ));
            die();
        }

		/**
		 * Returns an associative array with all the keys that are in the storage backend
		 *
		 * @return An associative array, or an empty array if there are no keys
		 */
        function getKeys()
        {
        	throw( new Exception( "ConfigAbstractStorage::getKeys: This method must be implemented by child classes." ));
            die();
        }

		/**
		 * Returns an array including only the values that are availalbe in the storage backend
		 *
		 * @return An array with only the values, or an empty array if there are no values.
		 */
        function getValues()
        {
        	throw( new Exception( "ConfigAbstractStorage::getValues: This method must be implemented by child classes." ));
            die();
        }

		/**
		 * saves only one value to the configuration backend. This should save the value right away
		 * without the need to call ConfigAbstractStorage::save() afterwards
		 *
		 * @param key The name of the key that we'd like to save
		 * @param value The value of the key that we'd like to save
		 * @return true if successful or false otherwise
		 */
        function saveValue( $key, $value )
        {
        	throw( new Exception( "ConfigAbstractStorage::saveValue: This method must be implemented by child classes." ));
            die();
        }

		/**
		 * saves all the keys and their values to disk
		 *
		 * @return true if successful or false otherwise
		 */
        function save()
        {
        	throw( new Exception( "ConfigAbstractStorage::saveValue: This method must be implemented by child classes." ));
            die();
        }
		

        /**
         * shortcut for one of the most sought after config keys: temp_folder
         *
         * not really needed, but it makes my life easier since I never remember
         * whether it is tmp_folder, temp_folder, temp_dir, or whatever :)
         *
         * @return The name of the folder used for temporary storage
         */
		function getTempFolder()
        {
            return $this->getValue( "temp_folder" );
        }
    }
?>
