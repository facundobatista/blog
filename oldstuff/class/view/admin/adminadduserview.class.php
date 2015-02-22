<?php

	lt_include( PLOG_CLASS_PATH.'class/view/admin/admintemplatedview.class.php' );
	lt_include( PLOG_CLASS_PATH.'class/dao/blogs.class.php' );
	lt_include( PLOG_CLASS_PATH.'class/dao/userstatus.class.php' );
	lt_include( PLOG_CLASS_PATH.'class/dao/permissions.class.php' );	
	

    /**
     * \ingroup View
     * @private
     *	
	 * shows a special view to add a user to the site
	 */
	class AdminAddUserView extends AdminTemplatedView
	{
	
		function AdminAddUserView( $blogInfo )
		{
			$this->AdminTemplatedView( $blogInfo, 'createuser' );	
		}
		
		function render()
		{
			$this->setValue( 'userStatusList', UserStatus::getStatusList());		
			$perms = new Permissions();
			$this->setValue( 'permissions', $perms->getAllPermissions());
			parent::render();
		}
	}
?>