<?php

    lt_include( PLOG_CLASS_PATH."class/config/configabstractstorage.class.php" );

    if (!defined("DEFAULT_CONFIG_FILE")) {
		define( "DEFAULT_CONFIG_FILE", PLOG_CLASS_PATH."config/config.properties.php" );
	}

    /**
	 * \ingroup Config
	 *
	 * Implements a file-based configuration backend. The data in the file has to be arranged
	 * in a php array called $config, in the same way the file config/config.properties.php is arranged.
	 *
	 * The backend will use the array keys as its own keys and the values as its own values, and will take
	 * care of serializing and unserializing data as needed (so that we can for example save Objects and
	 * Arrays to the config file)
     */
	class ConfigFileStorage extends ConfigAbstractStorage 
	{

    	var $_configFile;
        var $_props;

        /**
         * Opens the configuration file. By default it is config/config.properties.php
         * if no parameter is specified. If there is a parameter specified, that
         * is the file the constructor will try to open.
         * If no file name is specified, it defaults to config/config.properties.php.
         *
         * @param configFile The name of the file we would like to use.
         */
		function ConfigFileStorage( $params = null )
		{
        	$this->ConfigAbstractStorage( $params );

			if( !isset($params["file"]))
				$configFile = DEFAULT_CONFIG_FILE;
			else
				$configFile = $params["file"];
				
            $this->_configFile = $configFile;

            $this->reload();
		}


        /**
         * Reloads the contents from the configuration file.
         *
         * @return Returns true if successul or false otherwise
         */
        function reload()
        {
			lt_include( PLOG_CLASS_PATH."class/file/file.class.php" );
			lt_include( PLOG_CLASS_PATH."class/config/properties.class.php" );			
            if( File::isReadable( $this->_configFile )) {
                    // Note: It is correct to not use lt_include() here
                include( $this->_configFile );
                $this->_props = new Properties( $config );
                $result = true;
            }
            else {
                $this->_props = new Properties();
                $result = false;
            }
            return( $result );
        }

        /**
         * Returns the name of the configuration file being used.
         *
         * @return The name of the configuration file being used.
         */
        function getConfigFileName()
        {
        	return $this->_configFile;
        }

        /** 
         * Create a new config file from scratch.
         *
         * $return true if config file could be created, false otherwise
         */
        function createConfigFile( $configFileName = null ) 
        {
            // please keep this synced to the release/config.properties.php.dist file.
            $defaultConfigFile = '<?php
            #
            # database settings
            #
            $config[\'db_host\'] = \'\';
            $config[\'db_username\'] = \'\';
            $config[\'db_password\'] = \'\';
            $config[\'db_database\'] = \'\';
            $config[\'db_character_set\'] = \'default\';
            $config[\'db_persistent\'] = true;
            #
            # the database prefix will be appended to the name of each database tables in case you want
            # to have more than one version of plog running at the same time, such as the stable and
            # unstable one for testing. Each one could use a different prefix and therefore they could
            # coexist in the same unique database. If you change this after the initial configuration done
            # with the installation wizard, please make sure that you also rename the tables.
            #
            $config[\'db_prefix\'] = \'\';
            ?>';
              
			lt_include( PLOG_CLASS_PATH."class/file/file.class.php" );			  
            $file = new File( $configFileName );
            $writable = $file->open( 'w' );
            if ($writable) {
                $file->write( $defaultConfigFile );
                $file->close();
                return true;
            } else {
                return false;
            }
        }

        /**
         * Private function that given a piece of PHP data, will return an string representing
         * it, literally. Examples:
         *
         * data is a boolean type. Result --> the string 'true'
         * data is string type. Result --> string "value_of_the_string"
         * data is an array. Result --> string containing "Array( "..", "...", "..") "
         *
         * Objects are saved serialized and since there is no way to detect if it's an object
         * or not, it will be up to the user of the class to de-serialize it.
         *
         * <b>:TODO:</b> This function does not handle very well sparse arrays, but it does
         * handles arrays within arrays.
         *
         * @private
         * @param data The data we'd like to get the string representation
         * @return An string representing the data, so that eval'ing it would yield
         * the the same result as the $data parameter.
         */
        function _getDataString( $data )
        {
        	if( $this->_getType( $data ) == TYPE_INTEGER ) {
            	$dataString = $data;
            }
            elseif( $this->_getType( $data ) == TYPE_BOOLEAN ) {
            	if( $data )
                	$dataString = "true";
                else
                	$dataString = "false";
            }
            elseif( $this->_getType( $data ) == TYPE_STRING ) {
            	$dataString = "'".$data."'";
            }
            elseif( $this->_getType( $data ) == TYPE_ARRAY ) {
            	// arrays can be recursive, so...
                $dataString = "Array (";
                foreach( $data as $key => $item ) {

                	if( $key != "" ) {
                    	if( !is_numeric($key)) {
                        	$dataString .= "'".$key."' => ";
                        }
                    }

                	$dataString .= $this->_getDataString( $item ).",";
                }
                if( $dataString[strlen($dataString)-1] == "," )
                	$dataString[strlen($dataString)-1] = ")";
                else
                	$dataString .= ")";
            }
            elseif( $this->_getType( $data ) == TYPE_OBJECT ) {
            	$dataString = serialize( $data );
            }

            return $dataString;
        }

        /**
         * Saves a setting to the configuration file. If the setting already exists, the current
         * value is overwritten. Otherwise, it will be appended in the end of the file.
         * <b>NOTE:</b> This method is highly unoptimized because every time that we call saveValue,
         * we are writing the whole file to disk... Bad ;) But it works, so we'll leave it as it
         * is for the time being...
         *
         * @param name Name of the setting.
         * @param value Value of the setting.
         * @return True if success or false otherwise.
         */
        function saveValue( $name, $value )
        {
        	// open the config file
			lt_include( PLOG_CLASS_PATH."class/file/file.class.php" );			
            $f = new File( $this->_configFile );

            // there was a problem opening the file
            if( !$f->open("r+"))
            	return false;

            // now we have to process each of the lines
            $contents = $f->readFile();
            
            // escape the value
            $value = str_replace( "'", "\'", $value );

            $i = 0;
            $result = Array();

            $valueString = $this->_getDataString( $value );

            // depending if it's a string or not, we need a different regexp and a
            // expression that will replace the original
            if( $this->_getType( $value ) == TYPE_STRING ) {
            	$regexp = "/ *\\\$config\[[\"']".$name."[\"']\] *= *[\"'](.*)[\"']; */";
                $replaceWith = "\$config['".$name."'] = ".$valueString.";";
            }
            else {
            	$regexp = "/ *\\\$config\[[\"']".$name."[\"']\] *= *(.*); */";
                $replaceWith = "\$config['".$name."'] = ".$valueString.";";
            }

            while( $i < count($contents)) {
            	$line = $contents[$i];

                $newline = preg_replace($regexp, $replaceWith, $line );
                $i++;


                // for some reason, looks like we're getting some garbage in the end
                // of the file... couldn't find any better way to do it
                if( $newline != "?>" ) {
                	$newline .= "\n";
                	array_push($result, $newline );
                }
                else {
                	array_push( $result, "?>");
                    break;
                }
            }

            // the only thing we have to do know is save the contents of $result
            // to the output file
            //$result = str_replace( "'", "\\'", $result );
            $f->writeLines( $result );

        	return true;
        }

        function getValue( $key, $defaultValue = null )
        {
        	$value = $this->_props->getValue( $key );
            if( $value === "" || $value === null )
            	if(isset($defaultValue))
                	$value = $defaultValue;

            return $value;
        }

		/**
		 * @see ConfigAbstractStorage::setValue()
		 */
        function setValue( $key, $value )
        {
        	return $this->_props->setValue( $key, $value );
        }

		/**
		 * @see ConfigAbstractStorage::getKeys()
		 */
        function getKeys()
        {
        	return $this->_props->getKeys();
        }

		/**
		 * @see ConfigAbstractStorage::getValues()
		 */
        function getValues()
        {
        	return $this->_props->getValues();
        }

		/**
		 * @see ConfigAbstractStorage::getAsArray()
		 */
        function getAsArray()
        {
        	return $this->_props->getAsArray();
        }

		/**
		 * @see ConfigAbstractStorage::save()
		 */
        function save()
        {
        	foreach( $this->_props->getAsArray() as $key => $value ) {
            	$this->saveValue( $key, $value );
            }

            return true;
        }
	}
?>
