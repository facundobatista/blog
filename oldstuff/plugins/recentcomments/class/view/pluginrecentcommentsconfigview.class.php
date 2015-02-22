<?php
	
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminplugintemplatedview.class.php" );

	/**
	 * implements the main view of the feed reader plugin
	 */
	class PluginRecentCommentsConfigView extends AdminPluginTemplatedView
	{

		function PluginRecentCommentsConfigView( $blogInfo )
		{
			$this->AdminPluginTemplatedView( $blogInfo, "recentcomments", "recentcomments" );
		}
		
		function render()
		{
			// load some configuration settings
			$blogSettings = $this->_blogInfo->getSettings();
			$pluginEnabled = $blogSettings->getValue( "plugin_recentcomments_enabled" );
			$maxComments = $blogSettings->getValue( "plugin_recentcomments_maxcomments" );
			$includeComments = $blogSettings->getValue( "plugin_recentcomments_include_comments", 1);
			$includeTrackbacks = $blogSettings->getValue( "plugin_recentcomments_include_trackbacks", 0);
			if ($maxComments == "") $maxComments = DEFAULT_ITEMS_PER_PAGE;
			
			// create a view and export the settings to the template
			$this->setValue( "pluginEnabled", $pluginEnabled );
			$this->setValue( "maxComments", $maxComments );		
			$this->setValue( "includeComments", $includeComments );		
			$this->setValue( "includeTrackbacks", $includeTrackbacks );		
			
			parent::render();
		}
	}
?>