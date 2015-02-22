<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/chooser/adminuserchooserview.class.php" );

	class AdminUserChooserAction extends AdminAction
	{
	
		function AdminUserChooserAction( $actionInfo, $request )
		{
			$this->AdminAction( $actionInfo, $request );
		}
		
		function perform()
		{
			// load the mode
			$mode = $this->_request->getValue( "mode" );
			if( $mode == "" )
				$mode = 1;
		
			// load the view in chooser mode
			$this->_view = new AdminUserChooserView( $this->_blogInfo );
			$this->_view->setValue( "mode", $mode );
			$this->setCommonData();
			
			return true;
		}
	}
?>