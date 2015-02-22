<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminplugintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."plugins/ayearago/class/view/pluginayearagoconfigview.class.php" );	

	/**
	 * shows a form with the current configuration
	 */
	class PluginAYearAgoConfigAction extends AdminAction
	{
		
		function PluginAYearAgoConfigAction( $actionInfo, $request )
		{
			$this->AdminAction( $actionInfo, $request );
		}
		
		function perform()
		{
            $this->_view = new PluginAYearAgoConfigView( $this->_blogInfo );
			
			$this->setCommonData();
			
			return true;
		}
	}
?>
