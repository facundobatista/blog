<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
	
    /**
     * \ingroup View
     * @private
     *	
	 * shows a list with the users in the blog
	 */
	class AdminBlogUsersListView extends AdminTemplatedView
	{
	
		function AdminBlogUsersListView( $blogInfo )
		{
			$this->AdminTemplatedView( $blogInfo, "blogusers" );
		}
		
		function render()
		{
        	// get the users of the blog
            $users = new Users();
            $blogUsers = $users->getBlogUsers( $this->_blogInfo->getId(), false );
            $this->setValue( "blogusers", $blogUsers );	
			
			// no need to do anything else, so... transfer control to the parent view!
			parent::render();
		}
	}
	
?>