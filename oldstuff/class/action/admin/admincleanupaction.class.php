<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/commentscommon.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows a form to change the settings of the current blog.
     */
    class AdminCleanupAction extends AdminAction
	{
		var $_message;
		var $_op;
		var $_continue;
		var $_url;

		function AdminCleanupAction( $actionInfo, $request )
		{
			$this->AdminAction( $actionInfo, $request );

			// since we've got two submit buttons in that form, we need to decide what to do
			// depending on which button was clicked
			if( $this->_request->getValue( "purgePosts" ))
				$this->_op = "cleanupPosts";
			elseif( $this->_request->getValue( "purgeSpam" ))
				$this->_op = "cleanupSpam";
			elseif( $this->_request->getValue( "purgeUsers" ))
				$this->_op = "cleanupUsers";
			elseif( $this->_request->getValue( "purgeBlogs" ))
				$this->_op = "cleanupBlogs";
			elseif( $this->_request->getValue( "purgeTemp" ))
				$this->_op = "cleanupTemp";
            else
                $this->_op = "";
            
			$this->_message = '';
			$this->_continue = false;
			
			$this->requireAdminPermission( "purge_data" );
		}

		/**
		 * cleans up posts. Returns true if successful or false otherwise
		 */
		function cleanupPosts()
		{
			lt_include( PLOG_CLASS_PATH."class/dao/purgedata.class.php" );
			
			$purge = new PurgeData();
            $result = $purge->purgePosts();
            if($result === false){
				$this->_continue = false;
				$this->_message = $this->_locale->tr( "purging_error" );
                return false;
            }
			else if($result > 0 ) {
				$this->_message = $this->_locale->tr( "purging_please_wait" );
				// flags to indicate that we show refresh the page and continue at the given URL
				$this->_continue = true;				
				$this->_url = "?op=doCleanUp&purgePosts=Purge";
			}
			else {
				$this->_continue = false;
				$this->_message = $this->_locale->tr( "posts_purged_ok" );
			}
			
			return true;
		}

		/**
		 * cleans up users. Returns true if successful or false otherwise
		 */		
		function cleanupUsers()
		{
			lt_include( PLOG_CLASS_PATH."class/dao/purgedata.class.php" );
			
			$purge = new PurgeData();
			$result = $purge->purgeUsers();
            if($result === false){
				$this->_continue = false;
				$this->_message = $this->_locale->tr( "purging_error" );
                return false;
            }
			else if($result > 0){
				$this->_message = $this->_locale->tr( "purging_please_wait" );
				// flags to indicate that we show refresh the page and continue at the given URL
				$this->_continue = true;				
				$this->_url = "?op=doCleanUp&purgeUsers=Purge";
			}
			else {
				$this->_continue = false;
				$this->_message = $this->_locale->tr( "users_purged_ok" );
			}
			
			return( true );
		}		

		/**
		 * cleans up blogs. Returns true if successful or false otherwise
		 */		
		function cleanupBlogs()
		{
			lt_include( PLOG_CLASS_PATH."class/dao/purgedata.class.php" );
			
			$purge = new PurgeData();
			$result = $purge->purgeBlogs(1);

            if($result === false){
				$this->_continue = false;
				$this->_message = $this->_locale->tr( "purging_error" );
                return false;
            }
            else if($result > 0 ) {
				$this->_message = $this->_locale->tr( "purging_please_wait" );
				// flags to indicate that we show refresh the page and continue at the given URL
				$this->_continue = true;				
				$this->_url = "?op=doCleanUp&purgeBlogs=Purge";
			}
			else {
				$this->_continue = false;
				$this->_message = $this->_locale->tr( "blogs_purged_ok" );
			}
			
			return( true );
		}

		/**
		 * cleans up spam comments. Returns true if successful or false otherwise
		 */
		function cleanupComments()
		{
			lt_include( PLOG_CLASS_PATH."class/dao/purgedata.class.php" );
			
			$purge = new PurgeData();
			$result = $purge->purgeSpamComments();
            if($result === false){
				$this->_continue = false;
				$this->_message = $this->_locale->tr( "purging_error" );
                return false;
            }
			else if($result > 0) {
				$this->_message = $this->_locale->tr( "purging_please_wait" );
				// flags to indicate that we show refresh the page and continue at the given URL
				$this->_continue = true;				
				$this->_url = "?op=doCleanUp&purgeSpam=Purge";
			}
			else {
				$this->_continue = false;
				$this->_message = $this->_locale->tr( "spam_comments_purged_ok" );
			}
			
			return( true );
		}
		
		/**
		 * cleans up temp directory, excluding .htaccess and the .svn directory
         *
         * @return Returns true
		 */
		function cleanupTemp()
		{
			lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
			$config =& Config::getConfig();
			$tmpFolder = $config->getValue( "temp_folder" );
			$excludes = array('.htaccess', '.svn');
			File::deleteDir( $tmpFolder, true, true, $excludes );
			
			// Iterate through all of the blogs, and give the 
			// plugins a chance to regenerate any files that 
			// were deleted
			lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );

			$blogs = new Blogs();
        	$activeBlogs = $blogs->getAllBlogs( BLOG_STATUS_ACTIVE );
                // Get the plugin manager
//            $plugMgr =& PluginManager::getPluginManager();

        	foreach( $activeBlogs as $blogInfo ) {
	            $this->_pm->setBlogInfo( $blogInfo);
	            $this->_pm->loadPlugins( "admin" );
                $this->_pm->getPlugins();
	            // Send the EVENT_POST_ADMIN_PURGE_TEMP_FOLDER message
	            $this->_pm->notifyEvent( EVENT_POST_ADMIN_PURGE_TEMP_FOLDER );            
        	}
			
			$this->_message = $this->_locale->tr( "temp_folder_reset_ok" );
			
			return true ;
		}

		function perform()
		{
			$result = false;
			
			// decide what we're going to do...
			if( $this->_op == "cleanupSpam" ) {
				$result = $this->cleanupComments();
			}
			elseif( $this->_op == "cleanupPosts" ) {
				$result = $this->cleanupPosts();
			}
			elseif( $this->_op == "cleanupUsers" ) {
				lt_include( PLOG_CLASS_PATH."class/template/cachecontrol.class.php" );
				$result = $this->cleanupUsers();
				CacheControl::resetSummaryCache();
			}
			elseif( $this->_op == "cleanupBlogs" ) {
				lt_include( PLOG_CLASS_PATH."class/template/cachecontrol.class.php" );
				$result = $this->cleanupBlogs();
				CacheControl::resetSummaryCache();
			}
			elseif( $this->_op == "cleanupTemp" ) {
				$result = $this->cleanupTemp();
			}

			// create the view and see if there was a success message
			$this->_view = new AdminTemplatedView( $this->_blogInfo, "cleanup" );
			if( $result ) {
				$this->_view->setSuccessMessage( $this->_message );
        	}	
            elseif ($this->_message != '') {
				$this->_view->setErrorMessage( $this->_message );
		    }
		    
		    
			$this->_view->setValue( "continue", $this->_continue );
			$this->_view->setValue( "dest", $this->_url );

			$this->setCommonData();

			return true;
		}
	}
?>
