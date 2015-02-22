<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/adminresourceslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/stringutils.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );
	
    /**
     * \ingroup View
     * @private
     */	
	class AdminSimpleResourcesListView extends AdminResourcesListView
	{
	
		function AdminSimpleResourcesListView( $blogInfo, $params = Array())
		{
			$this->AdminResourcesListView( $blogInfo, $params );
			
			// change the template, the one chosen by the view above is not
			// exactly the one we needed
			$this->_templateName = "chooser/resourcelist";
		}
		
		function render()
		{
			$config =& Config::getConfig();
            $this->setValue( "blogname", StringUtils::text2url( $this->_blogInfo->getBlog() )); 			
            $this->setValue( "requestformat", $config->getValue( "request_format_mode"));
            $this->setValue( "baseurl", $config->getValue( "base_url" ));

			// whether resources are enabled or not
			$this->setValue( "resources_enabled", $config->getValue( "resources_enabled", true ));		

			// check if htmlarea is enabled
			$blogSettings = $this->_blogInfo->getSettings();
            $this->setValue( "htmlarea", $blogSettings->getValue( "htmlarea_enabled", false ));

			$this->_pagerUrl = "?op=resourceList&amp;page=";

			parent::render();
		}
	}
?>