<?php

	/**
	 * \defgroup Config
	 *
	 * The Config package is the central place where all configuration settings are stored in pLog.
	 *
	 * It works based on the Config::getConfig() method, which is a singleton method that will return a
	 * class that extends the "interface" (never mind that those things don't exist in PHP4) 
	 * ConfigAbstractStorage. Classes implementing this interface take care of storing, retrieving and updating
	 * configuration parameters from a certain backend. At the moment, only a file-based backend and a database-based
	 * backend are supported, and there are no plans to implement any more backends since these have proven
	 * enough for the needs of pLog.
	 *
	 * The default backend is the database, implemented by the class ConfigDbStorage, while the file-storage backend
	 * is implemented by the ConfigFileStorageBackend. If needed, it is possible to directly create an instance of 
	 * any of the storage backends but it is advisable to use Config::getConfig()
	 * 
	 * There is more information about which methods are available from classes implementing a storage backend in the
	 * documentation of the ConfigAbstractStorage class, and examples of use of this module in the documentation of the
	 * Config class.
	 *
	 * Please also see this wiki page: http://wiki.plogworld.net/index.php/PLog_1.0/Global_Configuration_API
	 */

	

    /**
     * Set it to either "file" or "db"
     */
    define( "DEFAULT_STORAGE_BACKEND", "db" );

    /**
	 * \ingroup Config
	 * 
	 * This class is the main entry point to the Config module. It provides a static method that will return
	 * a reference to the right config storage class.
	 *
	 * Example of usage for retrieving a particular key:
	 * <pre>
	 *  $config =& Config::getConfig();
	 *  $config->getValue( "my_config_value", $defaultValue );
	 * </pre>
	 *
	 * Example of saving some data back to the storage backend (we don't care which one it is):
	 * <pre>
	 *  $config =& Config::getConfig();
	 *  $config->setValue( "my_new_key", $newValue );
	 *  ...
	 *  $config->save();
	 * </pre>
	 *
     * @see ConfigAbstractStorage
	 * @see ConfigDbStorage
	 * @see ConfigFileStorage
     * @see getConfig
     */
	class Config  
	{

        /**
         * Makes sure that there is <b>only one</b> instance of this class for everybody, instead of creating
		 * a new instance every time we need to load some configuration. This will save some resources
		 * if we keep in mind that for example the database backend will load the entire table into memory
		 * every time a new instance is created.
         *
         * @param storage One of the storage methods implemented. Available ones are
         * "db" and "file", but any other can be implemented.
         * @param params An array containing storage backend specific parameters. In the case
         * of the file-based storage it could be the name of the file to use (for example)
         * @return Returns an instance of the Config class, be it a new one if this is the first
         * time we were calling it or an already created one if somebody else called
         * this method before.
         * @see ConfigDbStorage
         * @see ConfigFileStorage
		 * @static
         */
        function &getConfig( $storage = DEFAULT_STORAGE_BACKEND, $params = null )
        {
        	static $configInstance;

	    	// mappings to the storage classes
	        // more can be added any time
    		$storageTypes = Array(
        		"file" => "ConfigFileStorage",
	            "db"   => "ConfigDbStorage"
        	);

            // check if there was an instance of the Config class already created
            if( !isset($configInstance[$storage])) {
            	// now we have to instantiate the right storage class
            	if( $storage == "" || !array_key_exists( $storage, $storageTypes )) {
            		// there is no class to implement this storage method, so we quite
                	// because this is quite a severe error
            		throw(new Exception( "Config class Exception: no storage class found for storage parameter = ".$storage ));
                	die();
            	}

	            // if all went fine, get the name for that class
        	    $className = $storageTypes[$storage];
                lt_include( PLOG_CLASS_PATH.'class/config/'.strtolower($className).'.class.php' );
	            // and create an object
        	    $configInstance[$storage] = new $className( $params );
            }

            // return the instance
            return $configInstance[$storage];
        }
	}
?>
