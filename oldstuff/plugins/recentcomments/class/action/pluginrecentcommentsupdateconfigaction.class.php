<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminplugintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."plugins/recentcomments/class/view/pluginrecentcommentsconfigview.class.php" );	
		
	/**
	 * updates the plugin configuration
	 */
	class PluginRecentCommentsUpdateConfigAction extends AdminAction
	{
		var $_pluginEnabled;
		var $_maxComments;
		var $_includeComments;
		var $_includeTrackbacks;
		
		function PluginRecentCommentsUpdateConfigAction( $actionInfo, $request )
		{
			$this->AdminAction( $actionInfo, $request );
		}
		
		function validate()
		{
            $this->_pluginEnabled = $this->_request->getValue( "pluginEnabled" );
            $this->_pluginEnabled = ($this->_pluginEnabled != "" );			
            $this->_maxComments = $this->_request->getValue( "maxComments" );
        	$val = new IntegerValidator();
        	if( !$val->validate( $this->_maxComments ) ) {
                $this->_view = new PluginRecentCommentsConfigView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("recentcomments_error_maxcomments"));
                $this->setCommonData();

                return false;
            }        	                
            $this->_includeComments = $this->_request->getValue( "includeComments" );
            $this->_includeComments = ($this->_includeComments != "" );			
            $this->_includeTrackbacks = $this->_request->getValue( "includeTrackbacks" );
            $this->_includeTrackbacks = ($this->_includeTrackbacks != "" );			
			
			return true;
		}
		        
		function perform()
		{
            // // update the plugin configurations to blog setting
			$blogSettings = $this->_blogInfo->getSettings();
            $blogSettings->setValue( "plugin_recentcomments_enabled", $this->_pluginEnabled );
            $blogSettings->setValue( "plugin_recentcomments_maxcomments", $this->_maxComments );
            $blogSettings->setValue( "plugin_recentcomments_include_comments", $this->_includeComments );
            $blogSettings->setValue( "plugin_recentcomments_include_trackbacks", $this->_includeTrackbacks );
            $this->_blogInfo->setSettings( $blogSettings ); 
		
			// save the blogs settings
			$blogs = new Blogs();
            if( !$blogs->updateBlog( $this->_blogInfo )) {
                $this->_view = new PluginRecentCommentsConfigView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_updating_settings"));
                $this->setCommonData();

                return false;
            }
			
			// if everything went ok...
            $this->_blogInfo->setSettings( $blogSettings );
            $this->_session->setValue( "blogInfo", $this->_blogInfo );
            $this->saveSession();

			$this->_view = new PluginRecentCommentsConfigView( $this->_blogInfo );
			$this->_view->setSuccessMessage( $this->_locale->tr("recentcomments_settings_saved_ok"));
			$this->setCommonData();
			
			// clear the cache
			CacheControl::resetBlogCache( $this->_blogInfo->getId());				
            
            return true;		
		}
	}
?>