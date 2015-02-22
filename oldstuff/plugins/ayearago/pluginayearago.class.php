<?php
	lt_include( PLOG_CLASS_PATH."class/plugin/pluginbase.class.php" );
	
    /**
     * Plugin that returns an array of posts written a year before
     */
    class PluginAYearAgo extends PluginBase 
    {
        var $pluginEnabled;
        var $maxPosts;
        
    	function PluginAYearAgo($source = "" )
        {
        	$this->PluginBase($source);

            $this->id        = "ayearago";
            $this->author    = "Mariano Draghi (cHagHi)";
            $this->desc      = "This plugin offers various methods to get a list of posts written a year before a given post, or based on the current date.";
            $this->version   = '20070502';
			$this->locales   = Array( "en_UK", "es_ES" );

			if( $source == "admin" )
				$this->initAdmin();
        }

		function initAdmin()
		{
            $this->registerAdminAction( "ayearago", "PluginAYearAgoConfigAction" );
			$this->registerAdminAction( "updateAYearAgoConfig",
                                        "PluginAYearAgoUpdateConfigAction" );
			
            $this->addMenuEntry( "/menu/controlCenter/manageSettings",
                                 "AYearAgo", "?op=ayearago", "" );
		}
        
		function register()
		{
		    $blogSettings = $this->blogInfo->getSettings();
			$this->pluginEnabled = $blogSettings->getValue( "plugin_ayearago_enabled" );
			$this->maxPosts = $blogSettings->getValue( "plugin_ayearago_maxposts", 3 );
		}
        
	    function isEnabled()
	    {
	        return $this->pluginEnabled;
	    }
		
        function getOneYearAgoPosts( $post, $maxPosts = -1 )
        {
            if ( $maxPosts < 0 )
            {
                $maxPosts = $this->maxPosts;
            }
            
            // Timestamp used to find the articles based on a post
            $timestamp = $this->_getAYearAgoTimestamp( $post->getDateObject() );

            // Get the articles                        
            $minDate = $timestamp->getYear().$timestamp->getMonth().$this->_getDay( $timestamp );
            $maxDate = $timestamp->getYear().$timestamp->getMonth().$this->_getDay( $timestamp )."235959";
            $articles = new Articles();
            return ( $articles->getBlogArticles( $this->blogInfo->getId(), $minDate, $maxPosts, 0, 0, 0, $maxDate, "", -1 ) );
        }
        
        function getOneYearAgoPostsFuzzy( $post, $maxPosts = -1 )
        {
            if ( $maxPosts < 0 )
            {
                $maxPosts = $this->maxPosts;
            }
            
            // Timestamp used to find the articles based on a post
            $timestamp = $this->_getAYearAgoTimestamp( $post->getDateObject() );

            // Get the articles                        
            $maxDate = $timestamp->getYear().$timestamp->getMonth().$this->_getDay( $timestamp )."235959";
            $articles = new Articles();
            $posts = $articles->getBlogArticles( $this->blogInfo->getId(), -1, $maxPosts, 0, 0, 0, $maxDate, "", -1 );
            
            // Now check that the retrieved posts don't get overlaped with the previous post
            $prevArticle = $post->getPrevArticle();
            if(!is_object($prevArticle))
                return( $posts );

            $prevTimestamp = $this->_getAYearAgoTimestamp( $prevArticle->getDateObject(), true );
            $validPosts = Array();
            foreach ( $posts as $post )
            {
                $t = new Timestamp( $post->getDateObject() );
                if ( $t > $prevTimestamp )
                {
                    $validPosts[] = $post;
                }
            }
            
            return( $validPosts );
        }
        
        function getRecentArticlesAYearAgo( $maxPosts = -1 )
        {
            if ( $maxPosts < 0 )
            {
                $blogSettings = $this->blogInfo->getSettings();
                $maxPosts = $blogSettings->getValue( 'recent_posts_max' );
            }

            // Timestamp for a year ago            
            $timestamp = $this->_getAYearAgoTimestamp( new Timestamp() );

            // Get the articles
            $maxDate = $timestamp->getYear().$timestamp->getMonth().$this->_getDay( $timestamp )."235959";
            $articles = new Articles();
            return ( $articles->getBlogArticles( $this->blogInfo->getId(), -1, $maxPosts, 0, 0, 0, $maxDate, "", -1 ) );
        }
        
        function _getAYearAgoTimestamp( $date, $end = false )
        {
            if ( $date->getMonth() == 2 && $date->getDay() == 29 )
            {
                $date->setDay( 28 );
            }

            $date->setYear( $date->getYear() - 1 );
            
            if ( $end )
            {
                $date->setHour( 23 );
                $date->setMinute( 59 );
                $date->setSecond( 59 );
                
            }            
            
            return ( new Timestamp( $date ) );
        }

        function _getDay( $timestamp )
        {
            // returns a day ensuring 2 digits
            $day = $timestamp->getDay();
            if( $day < 10 && $day[0] != "0" )
            $day = "0".$day;

            return $day;
        }

        function getPluginConfigurationKeys(){
            lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
            return( Array(
                        Array( "name" => "plugin_ayearago_enabled", "type" => "boolean" ),
                        Array( "name" => "plugin_ayearago_maxposts",
                               "validator" => new IntegerValidator(), "type" => "integer", "allowEmpty" => true ),
                        ));
        }
        
    }
?>
