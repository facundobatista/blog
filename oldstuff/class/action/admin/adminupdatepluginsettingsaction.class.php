<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminpluginsettingsview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/plugin/pluginmanager.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/emptyvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/plugin/globalpluginconfig.class.php" );

    /**
     * \ingroup Action
     * @private
     */
    class AdminUpdatePluginSettingsAction extends AdminAction 
	{
		
		var $pm;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminUpdatePluginSettingsAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

        	// initialize the plugin manager and load the plugins
        	$this->pm =& PluginManager::getPluginManager();			

			// register the validators for all the plugin data
			$this->registerValidators();
			$view = new AdminPluginSettingsView( $this->_blogInfo, $this->_userInfo );
			$view->setData( false );
			$view->setErrorMessage( $this->_locale->tr("error_updating_global_plugin_settings" ));
			$this->setValidationErrorView( $view );
			
			$this->requireAdminPermission( "update_plugin_settings" );			
        }

		/**
		 * @private
		 */
		function registerValidators()
		{
            // get all the plugins
	    	$this->pm->refreshPluginList();
            $this->pm->setBlogInfo( $this->_blogInfo );
            $this->pm->setUserInfo( $this->_userInfo );

			// now one by one, query their public configuration values and set the validators
			foreach( $this->pm->getPlugins() as $plugin ) {
				foreach( $plugin->getPluginConfigurationKeys() as $key ) {
					isset( $key["validator"] ) ? $validator = $key["validator"] : $validator = new EmptyValidator();
					isset( $key["allowEmpty"] ) ? $allowEmpty = $key["allowEmpty"] : $allowEmpty = false;
					$this->registerFieldValidator( $key["name"], $validator, $allowEmpty );
				}
			}
			
			// "can override" fields
			$this->registerFieldValidator( "canOverride", new ArrayValidator( new IntegerValidator() ), true );
		}

        /**
         * Carries out the specified action
         */
        function perform()
        {
			// load the plugin config values
			$settings = Array();
			foreach( $this->pm->getPlugins() as $plugin ) {
				foreach( $plugin->getPluginConfigurationKeys() as $key ) {
					$keyName = $key["name"];
					$settings[$keyName] = $this->_request->getValue( $keyName );
				}
			}			
			// and now save them
			GlobalPluginConfig::setValues( $settings );
			
			// load the override settings and save them
			GlobalPluginConfig::setOverrideSettings( $this->_request->getValue( "canOverride" ) );
			
			$this->_view = new AdminPluginSettingsView( $this->_blogInfo, $this->_userInfo );
			$this->_view->setData( true );
			$this->_view->setSuccessMessage( $this->_locale->tr( "global_plugin_settings_saved_ok" ));
			$this->setCommonData();
			
			return( true );			
        }
    }
?>