<?php

	lt_include( PLOG_CLASS_PATH.'class/view/admin/adminview.class.php' );
	lt_include( PLOG_CLASS_PATH.'class/template/templateservice.class.php' );

    /**
     * \ingroup View
     *	
	 * Loads template files from the plugins/ folder
     */
    class AdminPluginTemplatedView extends AdminView 
	{

    	var $_templateName;
		var $_pluginId;

    	/**
         * This initializes the class, but normally we'll only have to initialize the parent
		 *
		 * @param blogInfo
		 * @param pluginId
		 * @param templateName
         */
        function AdminPluginTemplatedView( $blogInfo, $pluginId, $templateName )
        {
        	$this->AdminView( $blogInfo );

            $this->_templateName = $templateName;
			$this->_pluginId     = $pluginId;
        }

        /**
         * Renders the view. It simply gets all the parameters we've been adding to it
         * and puts them in the context of the template renderer so that they can be accessed
         * as normal parameters from within the template
		 *
		 * @return Returns a rendered template
         */
        function render()
        {
			parent::render();
			
			// now, load the plugin's own template
        	$template = $this->_templateService->PluginTemplate( $this->_pluginId, $this->_templateName );
            // assign all the values
            $template->assign( $this->_params->getAsArray());
			
            // and return the results
            print $template->fetch();
        }
    }
?>