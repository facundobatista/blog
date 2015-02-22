<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminerrorview.class.php" );

	/**
	 * This is the action loaded every time there is a problem with permissions
	 *
	 * @see Controller::setCannotPerformAction()
	 */
	class AdminPermissionRequiredAction extends AdminAction
	{
		function perform()
		{
			$this->_view = new AdminTemplatedView( $this->_blogInfo, "main" );
			$this->_view->setErrorMessage( $this->_locale->tr( "error_permission_required" ));
			$this->setCommonData();
		}
	}
?>