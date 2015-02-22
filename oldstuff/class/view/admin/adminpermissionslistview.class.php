<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/permissions.class.php" );
	
    /**
     * \ingroup View
     * @private
     */	
	class AdminPermissionsListView extends AdminTemplatedView
	{
		
		function AdminPermissionsListView( $blogInfo, $params = Array())
		{
			$this->AdminTemplatedView( $blogInfo, "permissions" );
		}
		
        /**
         * Carries out the specified action
         */
        function render()
        {
			// load all permissions available
			$perms = new Permissions();
			$allPerms = $perms->getAllPermissions();
			
			$this->setValue( "perms", $allPerms );

			parent::render();
        }
	}
?>