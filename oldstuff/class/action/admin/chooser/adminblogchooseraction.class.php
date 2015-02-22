<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/chooser/adminblogchooserview.class.php" );

	class AdminBlogChooserAction extends AdminAction
	{
	
		function AdminBlogChooserAction( $actionInfo, $request )
		{
			$this->AdminAction( $actionInfo, $request );
		}
		
		function perform()
		{
			// load the mode (single or multiple)
			$mode = $this->_request->getValue( "mode" );
			if( $mode == "" )
				$mode = 1;		
		
			// load the view in chooser mode
			$this->_view = new AdminBlogChooserView( $this->_blogInfo );
			$this->_view->setValue( "mode", $mode );			
			$this->setCommonData();
			
			return true;
		}
	}
?>