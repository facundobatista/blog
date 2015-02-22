<?php
	
	lt_include( PLOG_CLASS_PATH."class/view/plugintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/articlecomments.class.php" );

	class PluginRecentCommentsRssView extends PluginTemplatedView
	{
		function PluginRecentCommentsRssView($blogInfo){
			$this->PluginTemplatedView( $blogInfo, "recentcomments", "rss20" );
		}
		
		function render(){
			$blogSettings = $this->_blogInfo->getSettings();
			$pluginEnabled = $blogSettings->getValue( "plugin_recentcomments_enabled" );
			$maxComments = $blogSettings->getValue( "plugin_recentcomments_maxcomments" );
			if($maxComments == "")
                $maxComments = DEFAULT_ITEMS_PER_PAGE;
		    $includeComments = $blogSettings->getValue( "plugin_recentcomments_include_comments" );
		    $includeTrackbacks = $blogSettings->getValue( "plugin_recentcomments_include_trackbacks" );

            $commentType = COMMENT_TYPE_ANY;
            if($includeComments == 0 || $includeTrackbacks == 0){
                if($includeComments){
                    $commentType = COMMENT_TYPE_COMMENT;
                }
                else if($includeTrackbacks){
                    $commentType = COMMENT_TYPE_TRACKBACK;
                }
                else{
                        // TODO: they unchecked both??
                        // for now, assume that since the plugin is enabled,
                        // they wanted data to be shown...
                }
            }

            $commentsCommon = new CommentsCommon();
            $blogComments = $commentsCommon->getBlogComments($this->_blogInfo->getId(), COMMENT_ORDER_NEWEST_FIRST,
                                                             COMMENT_STATUS_NONSPAM, $commentType,
                                                             "", 1, $maxComments );
            $this->setValue("comments", $blogComments);
            $this->setContentType( 'text/xml' );
            parent::render();
		}

    }
?>