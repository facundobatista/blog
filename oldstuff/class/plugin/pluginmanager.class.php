<?php

    
    lt_include( PLOG_CLASS_PATH."class/file/file.class.php" );
    lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
    lt_include( PLOG_CLASS_PATH."class/misc/glob.class.php" );
//    lt_include( PLOG_CLASS_PATH."class/controller/blogcontroller.class.php" );
//    lt_include( PLOG_CLASS_PATH."class/controller/admincontroller.class.php" );
    lt_include( PLOG_CLASS_PATH."class/plugin/eventlist.properties.php" );
//	lt_include( PLOG_CLASS_PATH."class/plugin/pluginbase.class.php" );
	lt_include( PLOG_CLASS_PATH."class/controller/resourceclassloader.class.php" );

    /**
     * other various constants
     */
    define( "PLUGIN_MANAGER_DEFAULT_PLUGIN_FOLDER", "./plugins/" );
    define( "PLUGIN_MANAGER_DEFAULT_PLUGIN_FILE_PATTERN", "plugin*.class.php" );

    /**
     * \ingroup Plugin
     * 
     * Implements a 'plugin manager' a class that takes care of loading plugins, initializing
     * them, allowing plugins to register for certain events, etc.
     *
     * This class will rarely be called by user classes, since all the plugin initialization and
     * delivery of messages is handled by the core classes.
     *
     * You should not create objects of this class. In case you need a handle to the global 
     * PluginManager, please use the singlethod getPluginManager as follows:
     *
     * <pre>
     *  $pm =& PluginManager::getPluginManager();
     *  $pm->notifyEvent( ... );
     * </pre>
     */
    class PluginManager 
    {

        var $_pluginDir;
        var $_filePattern;
        var $_blogInfo;
        var $_userInfo;
		var $_pluginInstances;
		var $_source;

        /**
         * global variable to save the list of plugins registered so far
         */
        var $_pluginList;

        /**
         * global variable to save which plugins registered which events
         */
        var $_pluginEventList;

        /**
         * Constructor.
         *
         * @param pluginDir Specifies from which folders templates should
         * be loaded from.
         * @param filePattern
         */
        function PluginManager( $pluginDir = PLUGIN_MANAGER_DEFAULT_PLUGIN_FOLDER, $filePattern = PLUGIN_MANAGER_DEFAULT_PLUGIN_FILE_PATTERN )
        {		
            $config =& Config::getConfig();
			
            // initialize the arrays used to keep track of plugins and events
            $this->_pluginEventList = Array();
			$this->_pluginInstances = Array();
			
            $this->_enabled = $config->getValue( "plugin_manager_enabled" );
            $this->_pluginDir = $pluginDir;
            $this->_filePattern = $filePattern;
			
			$this->_pluginList = $config->getValue( "plugin_list" );
			// just in case there is something wrong...
			if( $this->_pluginList == "" )
				$this->_pluginList = Array();
        }
        
        function getPluginList()
        {
            return( $this->_pluginList );
        }

        /**
         * Sets the blog info
         *
         * @param blogInfo
         */
        function setBlogInfo( &$blogInfo )
        {
            $this->_blogInfo = $blogInfo;
        }

        /**
         * Sets the user info
         *
         * @param userInfo
         */
        function setUserInfo( &$userInfo )
        {
            $this->_userInfo = $userInfo;
        }

        /**
         * Returns the current instance of the plugin manager.
         *
         * @param pluginDir
         * @param filePattern
         * @see PluginManager
         * @return Returns an instance of the PluginManager, so that there is only one plugin
         * manager at any given time
         */
        function &getPluginManager( $pluginDir = PLUGIN_MANAGER_DEFAULT_PLUGIN_FOLDER, $filePattern = PLUGIN_MANAGER_DEFAULT_PLUGIN_FILE_PATTERN )
        {
            static $managerInstance;

            // check if there was an instance of the Config class already created
            if( !isset($managerInstance)) {
                // if there wasn't, then create a new one
                $managerInstance = new PluginManager( $pluginDir, $filePattern );
            }

            return $managerInstance;
        }

        /**
         * Returns wether the plugin manager is enabled or not.
         *
         * @return A boolean value telling wether the plugin manager is enabled or not.
         */
        function isEnabled()
        {
            return $this->_enabled;
        }
		
		/**
		 * returns true if the given plugin has been registered as a such
		 *
		 * @param pluginId the identifier of the plugin
		 * @return Returns true if the plugin is registered or false otherwise
		 */
		function pluginIsRegistered( $pluginId )
		{
			return( array_key_exists( $pluginId, $this->_pluginList ));
		}
		
		/**
		 * Checks whether the version reported by the plugin is compatible with the current version of LT
		 *
		 * @return True if compatible or false otherwise.
		 */
		function _pluginCanRun( $version )
		{
			/**
			 * :TODO:
			 * 
			 * to define this!!
			 */
			return true;		
		}

        /**
         * Loads all the plugins from disk
         *
		 * @param source
         */
        function loadPlugins( $source = "" )
        {				
			$classLoader =& ResourceClassLoader::getLoader();
			
			$this->_source = $source;
		
			foreach( $this->_pluginList as $plugin ) {
				$pluginFile = "./plugins/$plugin";
				$classInstance = $this->_createPluginInstance( $plugin );
				if( $classInstance ) {
					// tell the resource loader that it should try to load actions from this folder
					$classLoader->addSearchFolder( PLOG_CLASS_PATH."$pluginFile/class/action/" );
				}
			}
			
			return true;
        }

		/**
		 * refreshes the list of folders from disk
		 */
		function getPluginListFromFolder()
		{
			$pluginList = Array();
			
            $glob = new Glob();
            $pluginFiles = $glob->glob($this->_pluginDir, "*");
			if( !is_array( $pluginFiles ))
				return $pluginList;
			
            foreach( $pluginFiles as $pluginFile ) {
				if( File::isDir($pluginFile)) {
					// build up the name of the file
					$pluginIdElements = explode("/", $pluginFile);
					$pluginId = array_pop($pluginIdElements);
					$pluginFileName = "plugin".$pluginId.".class.php";
					$pluginFullPath = PLOG_CLASS_PATH."$pluginFile/$pluginFileName";
					// and try to include it
					if( File::isReadable( $pluginFullPath )) {
						$pluginList[] = $pluginId;
					}
				}
            }
			
			return $pluginList;
		}
		
		/**
         * saves the list of plugins to the config backend
		 */
		function savePluginList( $list )
		{
			$config =& Config::getConfig();
			$config->setValue( "plugin_list", $list );
			$config->save();
		}
		
		/**
		 * @private
		 *
		 * Given a plugin identifier, creates a new instance of it and returns a reference ot the instance.
		 * The instance is also saved in the PluginBase::_pluginInstances array
		 *
		 * @return Returns a reference to a plugin class extending PluginBase or null if there was an error
		 */
		function _createPluginInstance( $plugin )
		{
			// return null by default
			$classInstance = null;
			
			$pluginFile = "./plugins/$plugin";
            if( File::isDir($pluginFile)) {
				// build up the name of the file
               	$pluginFileName = "plugin{$plugin}.class.php";
               	$pluginFullPath = PLOG_CLASS_PATH."$pluginFile/$pluginFileName";
               	// and try to include it
               	if( File::isReadable( $pluginFile."/".$pluginFileName )) {
					$className = "Plugin".$plugin;
					lt_include( $pluginFullPath );
					$classInstance =& new $className( $this->_source );						
					$classInstance->setPluginFolder( PLOG_CLASS_PATH.$pluginFile."/" );	
					$name = $classInstance->getId();
					
					if( $name == "" ) {
						throw( new Exception( "Plugin file $pluginFile has no identifier defined!" ));
						die();
					}									

					// create an instance only if the plugin is allowed to run in this version of LT					
					if( $this->_pluginCanRun( $classInstance->getVersion())) {
						$this->_pluginInstances["$name"] = &$classInstance;
					}
				}
			}
			
			return( $classInstance );
		}
		
		/**
		 * @private
		 */
		function refreshPluginList()
		{
			$this->_pluginList = $this->getPluginListFromFolder();
			
			foreach( $this->_pluginList as $plugin ) {
				$classInstance = $this->_createPluginInstance( $plugin );
				if( $classInstance ) {
					$classInstance->install();										
				}					
			}
			
			$this->savePluginList( $this->_pluginList );
			
			return true;
		}

        /**
         * @private
         */
        function _loadPluginLocale( $pluginId, $locale )
        {
            lt_include( PLOG_CLASS_PATH . "class/locale/locales.class.php" );

            return( Locales::getPluginLocale( $pluginId, $locale ));
        }

        /**
         * Returns the folder used to store the plugins.
         *
         * @return The folder used to store the plugins.
         */
        function getPluginDir()
        {
            return $this->_pluginDir;
        }

        /**
         * Returns the array of plugins.
         *
         * @return An array of PluginBase objects.
         */
        function getPlugins()
        {
            foreach( $this->_pluginList as $name ) {
                if( array_key_exists( $name, $this->_pluginInstances ) ) {
                    $this->_pluginInstances["$name"]->setBlogInfo( $this->_blogInfo );
                    $this->_pluginInstances["$name"]->setUserInfo( $this->_userInfo );
                    $this->_pluginInstances["$name"]->register();
                }
            }

            return $this->_pluginInstances;
        }

        /**
         * notifies all the event plugins about an event
         *
         * @param eventType
         * @param params
         * @return
         */
        function notifyEvent( $eventType, $params = Array())
        {
            // check if there is any plugin that wants to be notified about this event
            $plugins = Array();
            if(array_key_exists( $eventType, $this->_pluginEventList ))
                $plugins = $this->_pluginEventList["$eventType"];

            if( !is_array($plugins) || empty($plugins))
                return $params;

            // fill in the parameters array with some other useful information
            $params[ "blogInfo" ] = $this->_blogInfo;
            $params[ "userInfo" ] = $this->_userInfo;

            // if so, loop through the plugins
            foreach( $plugins as $plugin ) {
                $plugin->setBlogInfo( $this->_blogInfo );
                $plugin->setUserInfo( $this->_userInfo );
                $plugin->process( $eventType, $params );
            }

            return $params;
        }

        /**
         * tells the plugin manager that a certain event plugin class wants to be notified about
         * a certain event.
         *
         * @param eventType
         * @param pluginClass
         * @return Always true
         */
        function registerNotification( $eventType, &$pluginClass )
        {
            // there can be more than one plugin registered for the same event,
            // so we need an array of plugin classes
            if( !isset($this->_pluginEventList["$eventType"]) || !is_array($this->_pluginEventList["$eventType"])) {
                $this->_pluginEventList["$eventType"] = Array();
            }

                // don't let a plugin register twice for the same event
                // takes care of bad programmers, and also double registering on the
                // plugincenter page
            if(isset($this->_pluginEventList["$eventType"][$pluginClass->id]))
                return false;

                // and then add the plugin to the list
            $this->_pluginEventList["$eventType"][$pluginClass->id] =& $pluginClass;

            return true;
        }

        /**
         * returns the events that have been defined
         *
         * @return An array with the names of the events that have been defined so far
         */
        function getDefinedEvents()
        {
            // get all the constants defined so far
            $constants = get_defined_constants();
            $events = Array();

            foreach( $constants as $constant => $value ) {
                // if the constant starts with "EVENT_", it's one of us
                if( substr( $constant, 0, 6 ) == "EVENT_" ) {
                    $events[ "$constant" ] = $value;
                }
            }

            return $events;
        }
        
        /**
         * returns true if the plugin provides the requested locale
         *
         * @param pluginId The plugin identifier that we're like to check
         * @param localeCode the locale code
         * @return True if the plugin has this locale or false otherwise
         * @static
         */
        function pluginHasLocale( $pluginId, $localeCode ) 
        {
            return( File::isReadable( "plugins/$pluginId/locale/locale_{$localeCode}.php" ));
        }
    }
?>
