<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminpluginsettingsview.class.php" );

    /**
     * \ingroup Action
     * @private
     */
    class AdminPluginSettingsAction extends AdminAction 
	{

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminPluginSettingsAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->requireAdminPermission( "update_plugin_settings" );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
            // create a view and put the plugin objects in the template
            $this->_view = new AdminPluginSettingsView( $this->_blogInfo, $this->_userInfo );
            $this->setCommonData();
        }
    }
?>