<?php

    /**
     * @package net
     */


	lt_include( PLOG_CLASS_PATH."class/net/baserequestgenerator.class.php" );

    /**
     * \ingroup Net
     *
     * Generates prettier but non-customizable requests.
     *
     * @see RequestGenerator
     * @see BaseRequestGenerator
     */
	class PrettyRequestGenerator extends BaseRequestGenerator 
	{

    	/**
         * Constructor.
         *
         * @param blogInfo A BlogInfo object
         */
    	function PrettyRequestGenerator( $blogInfo )
        {
        	$this->BaseRequestGenerator( $blogInfo );

        }

    	/**
         * Returns the permalink for a post
         *
         * @param post The Article object
         * @return The link for the permalink
         */
		function postPermalink( $post )
        {
            $permaLink = $this->getBaseUrl()."/post/".$this->_blogInfo->getId()."/".$post->getId();

            return $permaLink;
        }

        /**
         * generates an archive link given a date. 
         *
         * @param date A String in the format yyyymm
         * @return A valid archive link
         */                
        function getArchiveLink( $date )
        {
        	$archiveLink = $this->getBaseUrl()."/archives";
            if( $this->_blogInfo != null )
            	$archiveLink .= "/".$this->_blogInfo->getId();
            $archiveLink .= "/$date";

            return $archiveLink;
        }

        /**
         * Returns the comment link for a post
         *
         * @param post The Article object
         * @return The correct string
         */
        function postCommentLink( $post )
        {
            $commentLink = $this->getBaseUrl()."/comment/".$this->_blogInfo->getId()."/".$post->getId();

            return $commentLink;
        }

        /**
         * Returns the link for the post
         *
         * @param post The Article object
         * @return The link for the post
         */
        function postLink( $post )
        {

            $postLink = $this->postPermalink( $post );

            return $postLink;
        }

        /**
         * Returns the link for the post. This is kind of a dirty trick... :( This is only meant to be
         * used in the template that generates the rss feed for a blog.
         *
         * @param post The Article object
         * @return The link for the post
         */
        function postRssLink( $post )
        {
        	$postLink = $this->postPermalink($post);

            return $postLink;
        }

        /**
         * Returns the link of a category.
         *
         * @param post The post from which we'll fetch the category and then generate the right link.
         * @return The url pointing to the page with only the posts belonging to that category.
         * @see Article
         * @see categoryLink
         */
        function postCategoryLink( $post )
        {
            throw( new Exception( "DEPRECATED!"));
            die();
        }

        /**
         * Returns the link but given a category. Does the same as postCategoryLink but this time we don't need
         * a post but an ArticleCategory object.
         *
         * @see postCategoryLink
         * @see ArticleCategory
         * @param An ArticleCategory object containing the information regarding the category.
         * @return A string with the correct url pointing to the page that will show only the posts that belong
         * to the given category.
         */
        function categoryLink( $category )
        {
            $categoryLink = $this->getBaseUrl()."/category/".$this->_blogInfo->getId()."/".$category->getId();

            return $categoryLink;
        }

        /**
         * Returns a link to only the articles of the user
         *
         * @param user The user whose posts we would like to see
         * @return A string containing the right url to only the posts of the user.
         * @see UserInfo
         * @see ArticleCategory
         */
        function postUserLink( $user )
        {
            $userLink = $this->getBaseUrl()."/user/".$this->_blogInfo->getId()."/".$user->getId();

            return $userLink;
        }

        /**
         * Returns the url of the host where the blog is running
         *
         * @return Returns the url where the blog is running.
         */
        function blogLink( $blogInfo = null, $ignoreSubdomainSettings = false )
        {
          $config =& Config::getConfig();          
          // if subdomains are enabled, there is no need to do much more here... 
          if( $config->getValue( "subdomains_enabled" ) && !$ignoreSubdomainSettings ) {
            $link = $this->getBaseUrl();
          }
          else {
                // if not, we need some additional logic
    		$path = "/blog/";
		
                if( $blogInfo == null ) {
                    if( $config->getValue( "pretty_urls_force_use_username" )) {
                        $userInfo = $this->_blogInfo->getOwnerInfo();
                        $link = $this->getBaseUrl().$path.$userInfo->getUsername();
                    }
                    else {
                	   $link = $this->getBaseUrl().$path.$this->_blogInfo->getId();
                    }
                }
                else {
                	$link = $this->getBaseUrl().$path.$blogInfo->getId();
                }
            }

            return $link;
        }

        /**
         * Returns the url where the rss feed is running
         *
         * @param blogInfo A BlogInfo object containing information about the blog.
         * @param profile The RSS profile we'd like to use. It defaults to none.
         * @return The url pointing to the rss feed of the journal.
         * @see BlogInfo
         */
        function rssLink( $profile = "", $blogInfo = null )
        {
        	$rssBase = $this->getBaseUrl()."/rss/";
            if( $profile != "" )
            	$rssBase .= $profile."/";

        	if( $blogInfo == null )
            	$rssLink = $rssBase.$this->_blogInfo->getId();
            else
            	$rssLink = $rssBase.$blogInfo->getId();

            return $rssLink;
        }

        /**
         * Returns the url for the rss feed of a category
         *
         * @param category The ArticleCategory object with information about the category
         * whose RSS feed we'd like to generate
         * @þaram profile The profile we'd like to generate: RSS 0.90, RSS 1.0, RSS 2.0
         * or XML.
         * @param blogInfo A BlogInfo object containing information about the blog.
         * @return The url pointing to the rss feed of the journal.
         * @see BlogInfo
         */
        function categoryRssLink( $category, $profile = "", $blogInfo = null )
        {
        	$rssBase = $this->getBaseUrl()."/rss/";

            if( $profile != "" )
            	$rssBase .= $profile."/";

        	if( $blogInfo == null )
            	$rssLink = $rssBase.$this->_blogInfo->getId();
            else
            	$rssLink = $rssBase.$blogInfo->getId();

            $rssLink .= "/".$category->getId();

            return $rssLink;
        }

        /**
         * Returns the url to reply to a given comment.
         *
         * @param post An Article object with information about the post
         * @param commen A UserComment object containing information about the post we'd like to reply to.
         * @return The right url to reply to this comment.
         * @see UserComment
         * @see Article
         */
        function replyCommentLink( $post, $comment )
        {
            $replyCommentLink = $this->getIndexUrl()."?op=Comment&amp;articleId=".$post->getId()."&amp;parentId=".$comment->getId()."&amp;blogId=".$this->_blogInfo->getId();

            return $replyCommentLink;
        }

        /**
         * Returns the link to the page showing the trackback statistics for a given post.
         *
         * @param post The post with the information.
         * @return Returns a string with the valid link.
         */
        function postTrackbackStatsLink( $post )
        {
            $tbStatsLink = $this->getBaseUrl()."/trackbacks/".$this->_blogInfo->getId()."/".$post->getId();

            return $tbStatsLink;
        }

        /**
         * Returns the link to an album
         *
         * @param album The GalleryAlbum object.
         */
        function albumLink( $album = null )
        {
			if( $album == null )
				$albumLink = $this->getBaseUrl()."/album/".$this->_blogInfo->getId()."/0";
			else 
				$albumLink = $this->getBaseUrl()."/album/".$this->_blogInfo->getId()."/".$album->getId();

            return $albumLink;
        }

        /**
         * Given an album, generates a link to its parent. Must be implemented by child classes to generate
         * a valid URL.
         *
         * @param album The album
         */            
        function parentAlbumLink( $album )
        {
        	$albumLink = $this->getBaseUrl()."/album/".$this->_blogInfo->getId()."/".$album->getParentId();

            return $albumLink;
        }

        /**
         * Given the name of a template file, generate the right link to it. Must be implemented by child
         * classes to generate a valid URL.
         *
         * @param template
         * @return A link to the given template file/static page
         */
        function templatePage( $template )
        {
        	$templatePage = $this->getBaseUrl()."/static/".$this->_blogInfo->getId()."/".$template;

            return $templatePage;
        }

        /**
         * Returns the link to a resource
         *
         * @param album Generates the correct link to fetch a resource
         */
        function resourceLink( $resource )
        {
        	$blogId = ($resource->getOwnerId() ? $resource->getOwnerId() : $this->_blogInfo->getId());
        	
        	return $resourceLink = $this->getBaseUrl()."/resource/".$blogId."/".$resource->getId();
        }

        /**
         * Returns a string representing the request
         *
         * @return A String object representing the request
         */
        function getRequest()
        {
        	throw( new Exception( "PrettyRequestGenerator::getRequest: function not implemented" ));
            die();
        }
		
		/**
		 * given the parameters, recalculates the current URL. This method also has support
		 * for paged urls
		 *
		 * @param category
		 * @param userInfo
		 * @param date
		 * @return the current url with its page
		 */
		function getCurrentUrl( $category = null, $userInfo = null, $date = null )
		{
			if( $category ) {
				$url = $this->categoryLink( $category );
			}
			elseif( $userInfo ) {
				$url = $this->postUserLink( $userInfo );
			}
			elseif( $date > -1 ) {
				$url = $this->getArchiveLink( $date );
			}
			else {
				// if none of the above, we should at least get a link to the blog!
				$url = $this->blogLink( null, true );
			}		
			
			return( $url.$this->getPageSuffix());
		}
		
		/**
		 * Returns the page format for this URL generator
		 *
		 * @return A page suffix
		 */
		function getPageSuffix()
		{
			$pageFormat = "/page/";			
			return( $pageFormat );
		}		
    }
?>
