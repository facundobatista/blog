<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/permissions.class.php" );

    /**
     * \ingroup View
     * @private
     *	
	 * Shows the form to add a new user to the blog, including permissions
     */
    class AdminNewBlogUserView extends AdminTemplatedView 
	{
        function AdminNewBlogUserView( $blogInfo, $params = Array())
        {		
        	$this->AdminTemplatedView( $blogInfo, "addbloguser" );
		}

        function render()
        {
	        $perms = new Permissions();
			$this->setValue( "perms", $perms->getAllPermissions());
	        		
			parent::render();
        }
    }
?>