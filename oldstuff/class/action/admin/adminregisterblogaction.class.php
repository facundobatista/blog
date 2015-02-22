<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminregisterblogview.class.php" );
	
	class AdminRegisterBlogAction extends AdminAction
	{
	
		function AdminRegisterBlogAction( $actionInfo, $request )
		{
			$this->AdminAction( $actionInfo, $request );
		}
		
		function perform()
		{
			$this->_view = new AdminRegisterBlogView( $this->_userInfo );
			$this->_view->setValue( "form", $this->_form );
		}
	}
?>