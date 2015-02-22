<?php

    lt_include( PLOG_CLASS_PATH."class/plugin/pluginbase.class.php" );
    
    /**
     * Plugin that offers features to return a recent article comments from the current blog
     */
    class PluginRecentComments extends PluginBase
    {
        var $pluginEnabled;
        var $maxComments;
        var $includeComments;
        var $includeTrackbacks;
        
        function PluginRecentComments($source = "")
        {
            $this->PluginBase($source);
            $this->id = "recentcomments";
            $this->version = "20070830";

            $this->author = "Mark Wu";
            $this->desc = "This plugin offers the most recently posted article comments.";
            $this->locales = Array( "en_UK" , "zh_TW" , "zh_CN", "es_ES" );

			if( $source == "admin" )
				$this->initAdmin();
			else
				$this->init();
        }

		function initAdmin(){
            $this->registerAdminAction( "recentcomments", "PluginRecentCommentsConfigAction" );
			$this->registerAdminAction( "updateRecentCommentsConfig",
                                        "PluginRecentCommentsUpdateConfigAction" );
			$menu =& Menu::getMenu();
			if( !$menu->entryExists( "/menu/controlCenter/manageRecentPlugins" ))
				$this->addMenuEntry( "/menu/controlCenter", "manageRecentPlugins", "", "", true, false );			
            $this->addMenuEntry( "/menu/controlCenter/manageRecentPlugins", "RecentComments", "?op=recentcomments", "" );            
		}

        function init(){
            $this->registerBlogAction( "recentcommentsrss", "PluginRecentCommentsRssAction" );
		}

		function register()
		{
		    $blogSettings = $this->blogInfo->getSettings();
		    $this->pluginEnabled = $blogSettings->getValue( "plugin_recentcomments_enabled" );
	        $this->maxComments = $blogSettings->getValue( "plugin_recentcomments_maxcomments", DEFAULT_ITEMS_PER_PAGE );
		    $this->includeComments = $blogSettings->getValue( "plugin_recentcomments_include_comments", 1);
		    $this->includeTrackbacks = $blogSettings->getValue( "plugin_recentcomments_include_trackbacks", 0);
	    }
	    
	    function isEnabled()
	    {
	        return $this->pluginEnabled;
	    }

        /**
         * Returns the recent comments and/or trackbacks object of current blog
         */
        function getRecentComments()
        {
            lt_include( PLOG_CLASS_PATH."class/dao/commentscommon.class.php" );

            $commentType = COMMENT_TYPE_ANY;
            if($this->includeComments == 0 || $this->includeTrackbacks == 0){
                if($this->includeComments){
                    $commentType = COMMENT_TYPE_COMMENT;
                }
                else if($this->includeTrackbacks){
                    $commentType = COMMENT_TYPE_TRACKBACK;
                }
                else{
                        // TODO: they unchecked both??
                        // for now, assume that since the plugin is enabled,
                        // they wanted data to be shown...
                }
            }
            
            $blogId = $this->blogInfo->getId();
            $commentsCommon = new CommentsCommon();
            $comments = $commentsCommon->getBlogComments( $blogId, COMMENT_ORDER_NEWEST_FIRST,
                                                          COMMENT_STATUS_NONSPAM, $commentType,
                                                          "", 1, $this->maxComments );

            return( $comments );
        }

        /**
         * Returns an article given an id
         * This is needed here because we need to get various
         * articles, not just the most recent, etc. that are generally
         * available to the templates.
         */
        function getArticle( $artId )
        {
			lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );				
            $articles = new Articles();
            return $articles->getArticle($artId);
        }

        function getRssFeedUrl(){
            lt_include( PLOG_CLASS_PATH."class/net/rawrequestgenerator.class.php" );

            $rg = new RawRequestGenerator($this->blogInfo);
            $rg->addParameter( "op", "recentcommentsrss" );
            $rg->addParameter( "blogId", $this->blogInfo->getId());

            $feedUrl = $rg->getIndexUrl().$rg->getRequest();
            return $feedUrl;
        }

        function getPluginConfigurationKeys(){
			lt_include(PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php");
            return (Array(
                        Array("name" => "plugin_recentcomments_enabled",
                              "type" => "boolean"),
                        Array("name" => "plugin_recentcomments_maxcomments",
                              "validator" => new IntegerValidator(),
                              "type" => "integer", "allowEmpty" => true ),
                        Array("name" => "plugin_recentcomments_include_comments",
                              "type" => "boolean"),
                        Array("name" => "plugin_recentcomments_include_trackbacks",
                              "type" => "boolean"),
                        )
                   );
        }
        
    }
?>