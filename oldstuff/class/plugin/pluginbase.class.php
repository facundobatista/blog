<?php

    /**
     * \defgroup Plugin
     *
     * There is better documentation concerning plugins in the wiki:
     *
     * http://wiki.plogworld.net/index.php/PLog_1.0/Plugins
     *
     * The documentation here is only to be used as a reference for the methods
     * available in this class.
     */


    /**
     * \ingroup Plugin
     *
     * This is the base class from which every plugin object should inherit.
     */
    class PluginBase  
	{

        /**
         * This should be filled by the objects that extend this class to provide
         * some help on how to use the plugin.
         */
        var $desc = "This plugin has no description";

        /**
         * This should be filled with the author of the plugin.
         */
        var $author = "This plugin has no author";

        /**
         * This should not be modified as it is set by the plugin manager.
         * @private
         */
        var $id = "";

        /**
         * This should be filled with the author of the plugins
         */
        var $version = "1.0";

        /**
         * Set this to the html code that you would like to display when
         * configuring this plugin.
         */
        var $configMessage = "No configuration options available";

        /**
         * Error message, if any, that happened when configuring the plugin.
         */
        var $errorMessage = "No error message";

        /**
         * This contains information about the blog that is currently executing or configuring
         * this plugin.
         * @see BlogInfo
         */
        var $blogInfo;

        /**
         * This one contains the blog specific settings
         * @see BlogSettings
         */
        var $blogSettings;

    	/**
	     * User who is running the plugin.
	     */
	     var $userInfo;
	     
	   /**
	    * folder where this plugins is stored. We shouldn't need to mess
	    * around with this one, as it is automatically filled by the
	    * plugin manager when the plugin loads
	    */
	   var $pluginFolder;
	
		/**
		 * This attribute will store the original value of the $source parameter,
		 * which was passed to the plugin in the constructor. The $source identifies
		 * the code that initialized the plugin, and it should mainly be used to identify
		 * whether the plugin is being called from admin.php ("admin") or index.php ("index")
		 */
		var $source;
		

    	/**
         * Constructor. Feel free to do here whatever you need to.
		 *
		 * @param source Only defined to be 'index' or 'admin'. This parameter should be used to determine
		 * whether it is index.php registering this plugin ("index") or admin.php doing it ("admin") so that
		 * we can perform different things. This means that the plugin can be initialized in two different ways
		 * depending on whether we're being called via index.php or via admin.php so things like menu entries
		 * or admin actions do not need to be registered unless it's admin.php calling, and so on.
         */
    	function PluginBase( $source = "" )
        {
        	$this->source = $source;
            if($this->source == "admin")
                lt_include( PLOG_CLASS_PATH."class/template/menu/menu.class.php" );
                
        }

        /**
         * Returns a string with some information about the plugin.
         *
         * @return Returns a string describing the plugin.
         */
        function getDescription()
        {
        	return $this->desc;
        }

        /**
         * Returns the string describing the author of this plugin.
         *
         * @return The author of the plugin.
         */
        function getAuthor()
        {
        	return $this->author;
        }

        /**
         * Returns the identifier of the plugin, the name which can be used
         * in templates.
         *
         * @return The plugin identifier.
         */
        function getId()
        {
        	return $this->id;
        }

        /**
         * This function is called only once when the plugin is registered. Please use this method
		 * in case your plugin needs to perform some initializations before it is used, specially
		 * if the initialization process requires access to the plugin/blog settings (because the
		 * BlogInfo and UserInfo objects are not available in the constructor *yet* so this method
		 * will be called once they are available)
         *
         * @return Nothing.
         */
        function register()
        {
			return true;
        }

        /**
         * @private
         */
        function setBlogInfo( &$blogInfo )
        {
        	$this->blogInfo = $blogInfo;
			if( $this->blogInfo != null ) 
				$this->blogSettings = $blogInfo->getSettings();
        }

        /**
         * @private
         */
        function setUserInfo( &$userInfo )
        {
			$this->userInfo = $userInfo;
        }

		/**
		 * Changes the folder where plugins are loaded from
		 *
		 * @param folder The folder where plugins are stored
		 */
        function setPluginFolder( $folder ) 
        {
            $this->pluginFolder = $folder;
        }
        
		/**
		 * Returns the folder where plugins are located, ./plugins by default
		 *
		 * @return A string
		 */
        function getPluginFolder()
        {
            return $this->pluginFolder;
        }
        
        /**
         * registers an admin action
         *
         * @param key
         * @param actionClass
         * @return true         
         */
        function registerAdminAction( $key, $actionClass )
        {
            lt_include( PLOG_CLASS_PATH."class/controller/admincontroller.class.php" );

            AdminController::registerAction( $key, $actionClass );
            
            return true;
        }
        
        /**
         * register a blog action
         *
         * @param key
         * @param actionClass
         * @return true
         */
        function registerBlogAction( $key, $actionClass )
        {
            lt_include( PLOG_CLASS_PATH."class/controller/blogcontroller.class.php" );

            BlogController::registerAction( $key, $actionClass );
            
            return true;
        }
        
        /**
         * registers a filter for the pipeline
         *
         * @param filterName Name of the class that implements the pipeline filter. It must implement the
         * PipelineFilter interface.
         * @return true
         */
        function registerFilter( $filterName )
        {
            lt_include( PLOG_CLASS_PATH."class/security/pipeline.class.php" );

            Pipeline::registerFilter( $filterName );
            
            return true;
        }
		
		/**
		 * Adds a new entry to the admin menu, in case the plugin is registering anything there
		 *
         * @param path The XPath to the option
         * @param id The identifier of the new option
         * @param url The url where this new option is pointing to
         * @param localeId
         * @param orPerms An array with permissions that will be ORed to determine whether this entry
         * can be shown to users or not.
         * @param andPerms An array with permission that will be ANDed to determine whether this entry
         * can be show to users or not.
         * @param siteAdmin Whether this new option can only be used by site admins		 
		 * @see Menu::addEntry
		 */
		function addMenuEntry( $path, 
			                   $id, 
			                   $url, 
			                   $localeId = null, 
			                   $orPerms = Array( "manage_plugins" ), 
			                   $andPerms = Array( "manage_plugins" ), 
			                   $siteAdmin = false )
		{
	        lt_include( PLOG_CLASS_PATH."class/template/menu/menu.class.php" );
	
			// for 1.1 compatibility
			if( is_bool( $orPerms ) && is_bool( $andPerms )) {
				if( $orPerms == true )
					$orPerms = Array();
				else
					$orPerms = Array( "manage_plugins" );
					
				if( $andPerms == true ) {
					$siteAdmin = true;
					$andPerms = Array();
				}
				else {
					$siteAdmin = false;
					$andPerms = Array( "manage_plugins" );
				}
			}
			
			$orPermsString = implode( ",", $orPerms );
			$andPermsString = implode( ",", $andPerms );

			// get hold of the menu structure
			$menu =& Menu::getMenu();
			// and create a valid menuEntry object
			$menuEntry = new MenuEntry( $id,
			                            Array( 
										    "url" => $url, 
											"localeId" => $localeId,
											"orPerms" => $orPermsString,
											"andPerms" => $andPermsString,
											"siteAdmin" => (int)$siteAdmin ));
			// add the entry and return the result
			return $menu->addEntry( $path, $menuEntry );
		}
		
		/**
		 * registers an event plugin
		 *
		 * @param eventType
		 * @param eventClass
		 * @return True if successful or false otherwise
		 */
		function registerNotification( $eventType )
		{
			// get a reference to the current plugin manager object
			$pm =& PluginManager::getPluginManager();
			return $pm->registerNotification( $eventType, $this );
		}
		
		/**
		 * allows the plugin to throw any event, be it one of the core/standard ones
		 * or a new custom one
		 *
		 * @see PluginManager::notifyEvent
		 * @param eventType the event code
		 * @param params an associative array with the even parameters, if any
		 * @return true
		 */
		function notifyEvent( $eventType, $params = Array())
		{
			// get a handle to the plugin manager and throw the event with its parameters
			$pm =& PluginManager::getPluginManager();
			return( $pm->notifyEvent( $eventType, $params ));
		}

		/**
		 * this method should be implemented by plugins, and will tell the plugin manager 
		 * which configuration settings are stored in the database by this plugin. This will be
		 * used later on, in case users want to completely remove any trace of this plugin
		 * configuration.
		 *
		 * It is not mandatory to do so but it would help.
		 *
		 * @return An array of strings containing the configuration keys that are saved in the
		 * database.
		 */
		function getPluginConfigurationKeys()
		{
			return Array();
		}
		
		/**
		 * Returns true if the plugin has any global configuration key or false otherwise
		 *
		 * @return true if the plugin has any global configuration key or false otherwise
		 */
		function hasPluginConfigurationKeys()
		{
			return( count( $this->getPluginConfigurationKeys()) > 0 );
		}
		
		/**
		 * Tells the plugin manager which custom tables have been created by this plugin. It will
		 * help when making a back-up of the database structure, since the back-up feature will be able
		 * to back-up the plugins' database tables too.
		 *
		 * @return An array of string with the name of the database tables used by the plugin.
		 */
		function getPluginDatabaseTables()
		{
			return Array();
		}
		
		/**
		 * returns true whether the plugin has the given locale
		 *
		 * @param localeCode the locale that we'd like to check
		 * @return whether the plugin provides the requested locale
		 */
		function hasLocale( $localeCode )
		{
		  $path = "plugins/".$this->getId()."/locale/locale_".$localeCode.".php";
		  
		  return( File::isReadable( $path ));
		}
		
		/**
		 * this method must be implemented by plugins that wish to "listen" for events
		 * It will throw an Exception by default, since it means that the plugin registered for
		 * an event but no implementation of the process() method is provided
		 *
		 * @param eventType The event identifier.
		 * @param params An array with the parameters thrown by the event. The parameters are 
		 * dependant on the event.
		 */
        function process( $eventType, $params )
        {
            lt_include( PLOG_CLASS_PATH."class/object/exception.class.php" );
            throw( new Exception( "Plugin ".$this->id." registered for event $eventType but did not provide its own process() method!" ));
            die();
        }
        
        /**
         * Please use this method to perform any tasks that should be done only once when the plugin is installed
         * for the first time such as creation of new database tables, etc. 
         * This method will be called every time the "Plugin Centre" page is refreshed, so you should
         * not assume that this method will be called only once. 
         *
         * @return Always true
         */
        function install()
        {
	     	return true;
        }
		
		/**
		 * returns the plugin version
		 *
		 * @return the version of LT for which this plugin was created. This is later on used by the plugin loader
		 * to determine whether a plugin can be used with a certain version of LT. If 
		 * PluginBase::version is not defined, this method will return "1.0".
		 */
		function getVersion()
		{
			if( $this->version == "" )
				$this->version = "1.0";
				
			return( $this->version );
		}
		
		/**
		 * Returns the value of the source parameter
		 *
		 * @param return Source
		 */
		function getSource()
		{
			return( $this->source );
		}
    }
?>
