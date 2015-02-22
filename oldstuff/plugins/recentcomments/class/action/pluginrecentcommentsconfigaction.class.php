<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminplugintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."plugins/recentcomments/class/view/pluginrecentcommentsconfigview.class.php" );	

	/**
	 * shows a form with the current configuration
	 */
	class PluginRecentCommentsConfigAction extends AdminAction
	{
		
		function PluginRecentCommentsConfigAction( $actionInfo, $request )
		{
			$this->AdminAction( $actionInfo, $request );
		}
		
		function perform()
		{
            $this->_view = new PluginRecentCommentsConfigView( $this->_blogInfo );
			
			$this->setCommonData();
			
			return true;
		}
	}
?>