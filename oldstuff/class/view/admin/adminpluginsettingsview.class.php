<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/plugin/pluginmanager.class.php" );
	lt_include( PLOG_CLASS_PATH."class/plugin/globalpluginconfig.class.php" );

	class AdminPluginSettingsView extends AdminTemplatedView
	{
		var $_userInfo;
		var $_setData;

		function AdminPluginSettingsView( $blogInfo, $userInfo )
		{
			$this->AdminTemplatedView( $blogInfo, "pluginsettings" );
			
			$this->_userInfo = $userInfo;
			$this->_setData = true;
		}
		
		function setData( $set ) 
		{
			$this->_setData = $set;
		}
		
		/**
		 * load the fields and pass them to the view
		 */
		function render()
		{
        	// initialize the plugin manager and load the plugins
        	$pluginManager =& PluginManager::getPluginManager();

            // we need to get an array with the plugins
	    	$pluginManager->refreshPluginList();
            $pluginManager->setBlogInfo( $this->_blogInfo );
            $pluginManager->setUserInfo( $this->_userInfo );
            $plugins = $pluginManager->getPlugins();

            // put the plugin objects in the template
            $this->setValue( "plugins", $plugins );

			// load the settings unless we were configured not to
			if( $this->_setData ) {
				// plugin values
				$values = GlobalPluginConfig::getValues();
				foreach( $values as $key => $value ) {
					$this->setValue( $key, $value );
				}
				// and override settings
				$this->setValue( "canOverride", GlobalPluginConfig::getOverrideSettings());
			}

			return( parent::render());
		}
	}
?>