<?php

	lt_include( PLOG_CLASS_PATH."class/config/properties.class.php" );
	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	
	/**
	 * default time offset that is applied to new blogs
	 */
	define( "DEFAULT_TIME_OFFSET", 0 );

	/**
	 * \ingroup DAO
	 *
	 * Encapsulation of the settings for each blog
	 *
	 */
	class BlogSettings extends Properties 
	{

		function BlogSettings()
		{
			$this->Properties();

			$this->_setDefaults();
		}

		/**
		 * Sets some reasonable defaults for all the parameters, based on
		 * the system-wide settings from config/config.properties.php
		 */
		function _setDefaults()
		{
			$config =& Config::getConfig();

			$this->setValue( "locale", $config->getValue("default_locale"));
			$this->setValue( "show_posts_max", $config->getValue( "show_posts_max" ));
			$this->setValue( "template", $config->getValue("default_template"));
			$this->setValue( "show_more_enabled", $config->getValue( "show_more_enabled"));
            $this->setValue( "recent_posts_max", $config->getValue( "recent_posts_max" ));
            $this->setValue( "show_comments_max", $config->getValue( "show_comments_max" ));
            $this->setValue( "xmlrpc_ping_hosts", $config->getValue( "xmlrpc_ping_hosts" ));
            $this->setValue( "htmlarea_enabled", $config->getValue( "htmlarea_enabled" ));
			$this->setValue( "pull_down_menu_enabled", $config->getValue("pull_down_menu_enabled"));
            $this->setValue( "comments_enabled", $config->getValue( "comments_enabled" ));
			$this->setValue( "categories_order", 0 );
			$this->setValue( "comments_order", $config->getValue( "comments_order" ));
			$this->setValue( "time_offset", $config->getValue( "default_time_offset", DEFAULT_TIME_OFFSET ));
			$this->setValue( "articles_order", 2 );  // :TODO: we should be using a constant here
		}
		
		function getValue( $key, $defaultValue = null, $filterClass = null )
		{
			// is it a plugin key?
			if( substr( $key, 0, strlen( "plugin_" )) == "plugin_" ) {
				lt_include( PLOG_CLASS_PATH."class/plugin/globalpluginconfig.class.php" );
				// check if users can override the plugin setting. If so, return the blog plugin settings
				// and if not return the global setting
				if( GlobalPluginConfig::canOverride( $key ) == PLUGIN_SETTINGS_USER_CAN_OVERRIDE ) {
					// load the value from our settings, but if it isn't available, then return whatever the global
					// plugin settings say					
					$value = parent::getValue( $key, GlobalPluginConfig::getValue( $key ), $filterClass );
				}
				else {
					$value = GlobalPluginConfig::getValue( $key );
				}
				// If there no values from user or site plugin config, then we get it from $defaultValue
				if( empty( $value ) )
					$value = $defaultValue;
			}
			else {
				$value = parent::getValue( $key, $defaultValue );
			}
			
			return( $value );
		}
	}
?>
