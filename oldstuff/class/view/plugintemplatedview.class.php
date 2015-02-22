<?php

	lt_include( PLOG_CLASS_PATH."class/view/blogview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/templateservice.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/templatesets/templatesets.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/templatesets/templatesetstorage.class.php" );

    /**
     * \ingroup View
     *
	 * Loads template files from the plugins/ folder. This is the view class that plugins should use when
	 * creating their own views. It works in exactly the same way as the BlogView class but it knows how 
	 * to load custom templates as provided by templates, which are located under
	 * plugins/plugin-name/templates/ instead of templates/template-set-name/
	 *
	 * @see BlogView
     */
    class PluginTemplatedView extends BlogView 
	{

    	var $_templateName;
		var $_pluginId;

    	/**
         * This initializes the class, but normally we'll only have to initialize the parent
		 *
		 * @param blogInfo
		 * @param pluginId
		 * @param templateName
		 * @param useHeaderFooter Whether to use the templates/admin/header.template and
		 * templates/admin/footer.template templates automatically
         */
        function PluginTemplatedView( $blogInfo, $pluginId, $templateName, $cachingEnabled = SMARTY_VIEW_CACHE_CHECK, $data = Array())
        {
			if( $cachingEnabled == SMARTY_VIEW_CACHE_CHECK ) {
				// detect whether caching should be enabled or not
				$config =& Config::getConfig();			
				$cachingEnabled = $config->getValue( "template_cache_enabled" );
			}

        	$this->BlogView( $blogInfo, "", $cachingEnabled, $data );

			$this->_templateService = new TemplateService();	
            $this->_templateName = $templateName;
			$this->_pluginId     = $pluginId;
			
        	if ($cachingEnabled == SMARTY_VIEW_CACHE_DISABLED) {
        	    $this->_template = $this->_templateService->PluginTemplate( $this->_pluginId, $this->_templateName, $blogInfo );	
        	} else {
        	    $this->_template = $this->_templateService->PluginCachedTemplate( $this->_pluginId, $this->_templateName, $blogInfo );	
        	}
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
            // assign all the values
            $blogSettings = $this->_blogInfo->getSettings();
			$templateSet = $blogSettings->getValue( "template" );            
            $this->_template->assign( $this->_params->getAsArray());
			$ts = new TemplateSets();
			$storage = new TemplateSetStorage();
			if( $ts->isBlogTemplate( $templateSet, $this->_blogInfo->getId()))
				$blogTemplate = $storage->getTemplateFolder( $templateSet, $this->_blogInfo->getId());
			else
				$blogTemplate = $storage->getTemplateFolder( $templateSet );
			
			$this->_template->assign( "blogtemplate", $blogTemplate );
			$this->_template->assign( "misctemplatepath", $storage->getMiscTemplateFolder());

			parent::render();
        }
    }
?>
