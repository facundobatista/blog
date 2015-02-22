<?php
	
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminplugintemplatedview.class.php" );

	/**
	 * implements the main view of the a year ago plugin
	 */
	class PluginAYearAgoConfigView extends AdminPluginTemplatedView
	{

		function PluginAYearAgoConfigView( $blogInfo )
		{
			$this->AdminPluginTemplatedView( $blogInfo, "ayearago", "ayearago" );
		}
		
		function render()
		{
			// load some configuration settings
			$blogSettings = $this->_blogInfo->getSettings();
			$pluginEnabled = $blogSettings->getValue( "plugin_ayearago_enabled" );
			$maxPosts = $blogSettings->getValue( "plugin_ayearago_maxposts" );
			if ($maxPosts == "") $maxPosts = 3;
			
			// create a view and export the settings to the template
			$this->setValue( "pluginEnabled", $pluginEnabled );
			$this->setValue( "maxPosts", $maxPosts );		
			
			parent::render();
		}
	}
?>
