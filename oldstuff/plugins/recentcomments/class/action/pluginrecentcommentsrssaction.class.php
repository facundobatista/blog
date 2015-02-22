<?php

	lt_include( PLOG_CLASS_PATH."class/action/blogaction.class.php" );
	lt_include( PLOG_CLASS_PATH."plugins/recentcomments/class/view/pluginrecentcommentsrssview.class.php" );	

	class PluginRecentCommentsRssAction extends BlogAction
	{
		
		function PluginRecentCommentsRssAction( $actionInfo, $request )
		{
			$this->BlogAction( $actionInfo, $request );
		}
		
		function perform()
		{
            $this->_view = new PluginRecentCommentsRssView( $this->_blogInfo );
			
			$this->setCommonData();
			
			return true;
		}
	}
?>