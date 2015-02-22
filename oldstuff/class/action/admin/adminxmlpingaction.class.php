<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminxmlview.class.php" );

    /**
     * \ingroup Action
     * @private
     */	
	class AdminXmlPingAction extends AdminAction
	{
		
		function AdminXmlPingAction( $actionInfo, $request )
		{
			$this->AdminAction( $request, $actionInfo );
		}
		
		function perform()
		{
			$this->_view = new AdminXmlView( $this->_blogInfo, "response" );
			$this->_view->setValue( "method", "sessionPing" );
			$this->_view->setValue( "result", "OK" );
			
			return true;
		}
	}
?>