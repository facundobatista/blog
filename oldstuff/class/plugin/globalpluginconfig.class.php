<?php

	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	
	define( "PLUGIN_SETTINGS_USER_CAN_OVERRIDE", 1 );
	define( "PLUGIN_SETTINGS_USER_CANNOT_OVERRIDE", 2 );

	class GlobalPluginConfig
	{

		/**
		 * @static
		 */
		function getValue( $key )
		{
			$config =& Config::getConfig();
			$pluginConfig = $config->getValue( "global_plugin_settings", Array ());
			
			if( isset( $pluginConfig["$key"] ))
				$value = $pluginConfig["$key"];
			else
				$value = null;
				
			return( $value );			
		}
		
		/**
		 * @static
		 * Saves an array of values
		 */
		function setValues( $values )
		{
			$config =& Config::getConfig();
			$config->setValue( "global_plugin_settings", $values );
			return( true );
		}
		
		/**
		 * @static
		 * Save the override settings
		 */
		function setOverrideSettings( $list )
		{
			$config =& Config::getConfig();
			$config->setValue( "global_plugin_overrides", $list );
			return( true );			
		}	
		
		/**
		 * @static
		 * Get the override settings
		 */
		function getOverrideSettings()
		{
			$config =& Config::getConfig();			
			return( $config->getValue( "global_plugin_overrides" ));
		}
		
		function canOverride( $key )
		{
			$config =& Config::getConfig();
			$overrides = $config->getValue( "global_plugin_overrides" );
			isset( $overrides[$key] ) ? $canOverride = $overrides[$key] : $canOverride = PLUGIN_SETTINGS_USER_CAN_OVERRIDE;
			
			return( $canOverride );
		}	
			
		/** 
		 * @static
		 * Returns all the values saved for plugins
		 */
		function getValues()
		{
			$config =& Config::getConfig();
			
			$values = $config->getValue( "global_plugin_settings" );
			
			if( !is_array( $values ))
				$values = Array();
			
			return( $values );	
		}
	}
?>