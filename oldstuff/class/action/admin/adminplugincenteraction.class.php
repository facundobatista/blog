<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminerrorview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/plugin/pluginmanager.class.php" );
	lt_include( PLOG_CLASS_PATH."class/xml/rssparser/rssparser.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Shows the list of plugins loaded.
     *
     * In the future it'll be possible to do more things from within the
     * "Plugin Center", but for now this is enough... Planned things:
     * <ul>
     * <li>per blog enable/disable plugin</li>
     * <li>add/remove plugins</li>
     * <li>I'll add more when I can think of more :)</li>
     * </ul>
     */
    class AdminPluginCenterAction extends AdminAction 
	{

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminPluginCenterAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->requireAdminPermission( "view_plugins" );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
        	// initialize the plugin manager and load the plugins
        	$pluginManager =& PluginManager::getPluginManager();

            lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php");
			$doVersionCheck = TextFilter::checkboxToBoolean( $this->_request->getValue( "doVersionCheck" ) );

            // check if the plugin manager is enabled or not, since if it's not
            // there is no point in this feature...
            if( !$pluginManager->isEnabled()) {
            	$this->_view = new AdminErrorView( $this->_blogInfo );
                $this->_view->setMessage( $this->_locale->tr("error_plugins_disabled"));
                $this->setCommonData();

                return false;
            }

            // we need to get an array with the plugins
	    	$pluginManager->refreshPluginList();
            $pluginManager->setBlogInfo( $this->_blogInfo );
            $pluginManager->setUserInfo( $this->_userInfo );
            $plugins = $pluginManager->getPlugins();

            // create a view and put the plugin objects in the template
            $this->_view = new AdminTemplatedView( $this->_blogInfo, "plugincenter" );
            $this->_view->setValue( "plugins", $plugins );

			// load the feed with plugin versions and display an error if there was a problem
			if( $doVersionCheck ) {
				$rss = new RssParser();
				if( $rss->parse( Version::getPluginVersionFeed())) {
					$items = $rss->getItems();

					// process the parsed rss feed
					$data = Array();
					foreach( $items as $pluginData ) {
						$data[$pluginData->getTitle()] = Array(
							"version" => $pluginData->_item["lt"]["version"],
							"downloadLink" => $pluginData->getLink()
						);
					}

					$this->_view->setValue( "pluginData", $data );
				}
				else {
					$doVersionCheck = false;
				}
			}
			else {
				$doVersionCheck = false;
			}

			$this->_view->setValue( "versionCheck", $doVersionCheck );
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>