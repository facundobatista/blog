<?php

	lt_include( PLOG_CLASS_PATH."class/net/baserequestgenerator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );

    /**
     * \ingroup Net
     *
     * Generates requests using a custom format.
     *
     * @see RequestGenerator
     * @see BaseRequestGenerator
     */
	class CustomRequestGenerator extends BaseRequestGenerator 
	{
	
	   var $_config;

    	/**
         * Constructor.
         *
         * @param blogInfo A BlogInfo object
         */
    	function CustomRequestGenerator( $blogInfo = null )
        {
        	$this->BaseRequestGenerator( $blogInfo );
            $this->_config =& Config::getConfig();
        }

    	/**
         * Returns the permalink for a post
         *
         * @param post The Article object
         * @return The link for the permalink
         */
		function postPermalink( $post )
        {
            $postPermalinkFormat = $this->_config->getValue( "permalink_format");
            
            $params = $this->_fillPostParameters( $post );
                            
            $permaLink = $this->getBaseUrl().$this->_replaceTags( $postPermalinkFormat, $params );

            return $permaLink;
        }
        
        /**
         * @private
         */
        function _fillPostParameters( $post )
        {
           $t = $post->getDateObject();
            $categories = $post->getCategories();
            $category = array_pop( $categories );
			$user = $post->getUserInfo();
			$ownerInfo = $this->_blogInfo->getOwnerInfo();
			
			//print_r($post);
			
			$day = $t->getDay();
			if( $day < 10 ) $day = "0".$day;
            
            $params = Array( '{blogname}' => $this->_blogInfo->getMangledBlog(),
                             '{blogid}' => $this->_blogInfo->getId(),
							 '{blogowner}' => $ownerInfo->getUsername(),
                             '{year}' => $t->getYear(),
                             '{month}' => $t->getMonth(),
                             '{day}' => $day,
                             '{hour}' => $t->getHour(),
                             '{minute}' => $t->getMinutes(),
                             '{postid}' => $post->getId(),
                             '{postname}' => $post->getPostSlug(),
                             '{catname}' => $category->getMangledName(),
                             '{catid}' => $category->getId(),
							 '{userid}' => $user->getId(),
							 '{username}' => $user->getUsername()
                            );
                            
            return $params;
        }

        /**
         * generates an archive link given a date. 
         *
         * @param date A String in the format yyyymm
         * @return A valid archive link
         */        
        function getArchiveLink( $date )
        {
            $archiveLinkFormat = $this->_config->getValue( 'archive_link_format' );
			$ownerInfo = $this->_blogInfo->getOwnerInfo();
            
            // this is quite a dirty one but it works...
            $newDate = $date;
            if( strlen($date) == 6 ) $newDate = $date."00000000";
            if( strlen($date) == 8 ) $newDate = $date."000000";      
            $t = new Timestamp( $newDate );
            
            $params = Array( "{year}" => $t->getYear(),
                             "{month}" => $t->getMonth(),
                             "{blogname}" => $this->_blogInfo->getMangledBlog(),
							 "{blogowner}" => $ownerInfo->getUsername(),
                             "{blogid}" => $this->_blogInfo->getId());
			if( strlen($date) == 6 ) $params["{day}"] = "";
			else {
				$day = $t->getDay();
				if( $day < 10 )
					$day = "0".$day;			
				$params["{day}"] = $day;
			}
                             
            $archiveLink = $this->getBaseUrl().$this->_replaceTags( $archiveLinkFormat, $params );
            
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
            $commentLink = $this->getIndexUrl()."?op=Comment&amp;blogId=".$this->_blogInfo->getId()."&amp;articleId=".$post->getId();

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
            return $this->postPermalink( $post );
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
            $categoryFormat = $this->_config->getValue( "category_link_format" );
			$ownerInfo = $this->_blogInfo->getOwnerInfo();			
            $params = Array( "{catid}" => $category->getId(),
                             "{catname}" => $category->getMangledName(),
                             "{blogid}" => $this->_blogInfo->getId(),
							 "{blogowner}" => $ownerInfo->getUsername(),
                             "{blogname}" => $this->_blogInfo->getMangledBlog());
            $result = $this->_replaceTags( $categoryFormat, $params );
            
            $categoryLink = $this->getBaseUrl().$result;

            return $categoryLink;
        }

        /**
         * Returns a link to only the articles of the user
         *
         * @param user The user whose posts we would like to see
         * @param category Optionally, we can specify an ArticleCategory object
         * @return A string containing the right url to only the posts of the user.
         * @see UserInfo
         * @see ArticleCategory
         */
        function postUserLink( $user, $category = null )
        {
            $userPostsLink = $this->_config->getValue( "user_posts_link_format" );
			$ownerInfo = $this->_blogInfo->getOwnerInfo();			
            
            $params = Array( "{blogid}" => $this->_blogInfo->getId(),
                             "{blogname}" => $this->_blogInfo->getMangledBlog(),
							 "{blogowner}" => $ownerInfo->getUsername(),
                             "{username}" => $user->getUsername(),
                             "{userid}" => $user->getId(),
							 "{year}" => "",
							 "{month}" => "",
							 "{day}" => "" );
            
            $userLink = $this->getBaseUrl().$this->_replaceTags( $userPostsLink, $params );

            return $userLink;
        }

        /**
         * Returns the url of the host where the blog is running
         *
         * @return Returns the url where the blog is running.
         */
        function blogLink( $blogInfo = null )
        {
		if( $blogInfo == null )
			$blogInfo = $this->_blogInfo;
			
			// get information about the owner
			$ownerInfo = $blogInfo->getOwnerInfo();

            $blogLinkFormat = $this->_config->getValue( "blog_link_format" );
            $params = Array( "{blogid}" => $blogInfo->getId(),
                             "{blogname}" => $blogInfo->getMangledBlog(),
							 "{blogowner}" => $ownerInfo->getUsername());
            $result = $this->getBaseUrl().$this->_replaceTags( $blogLinkFormat, $params );
            
            return $result;
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
			$rssLink = $this->getRssUrl();
        	if( $blogInfo == null )
            	$rssLink .= "?blogId=".$this->_blogInfo->getId();
            else
            	$rssLink .= "?blogId=".$blogInfo->getId();

            if( $profile != "" )
            	$rssLink .= "&amp;profile=$profile";

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
			$rssLink = $this->rssLink( $profile, $blogInfo );
			$rssLink .= "&amp;categoryId=".$category->getId();
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
            $postTrackbacksFormat = $this->_config->getValue( "post_trackbacks_link_format");
            
            $params = $this->_fillPostParameters( $post );
                            
            $permaLink = $this->getBaseUrl().$this->_replaceTags( $postTrackbacksFormat, $params );

            return $permaLink;
        }

        /**
         * Returns the link to an album
         *
         * @param album The GalleryAlbum object.
		 * @param page current page
         */
        function albumLink( $album = null )
        {
            $albumLinkFormat = $this->_config->getValue( "album_link_format" );
			$ownerInfo = $this->_blogInfo->getOwnerInfo();			
			
			$params = Array( "{blogid}" => $this->_blogInfo->getId(),
			                 "{blogname}" => $this->_blogInfo->getMangledBlog(),
							 "{blogowner}" => $ownerInfo->getUsername(),							 
							 "{albumid}" => "",
							 "{albumname}" => "" );
							 
			if( $album != null ) {
				$params["{albumid}"] = $album->getId();
				$params["{albumname}"] = $album->getMangledName();
			}
							 
			$albumLink = $this->getBaseUrl().$this->_replaceTags( $albumLinkFormat, $params );

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
			if( $album->getParentId() == 0 ) {
				// if the parent album is the root album, let's make it easy...
				$albumLink = $this->albumLink( null );
			}
			else {
				// i don't really like this bit because suddenly, a request generator class
				// starts loading objects from the db... 
				$albums = new GalleryAlbums();
				$parentAlbum = $albums->getAlbum( $album->getParentId());
				$albumLink = $this->albumLink( $parentAlbum );
			}

            return $albumLink;
        }
		
        /**
         * Given the name of a template file, generate the right link to it. 
         *
         * @param template
         * @return A link to the given template file/static page
         */
        function templatePage( $template )
        {
            $templateLinkFormat = $this->_config->getValue( "template_link_format" );
			$ownerInfo = $this->_blogInfo->getOwnerInfo();			
			
			$params = Array( "{blogid}" => $this->_blogInfo->getId(),
			                 "{blogname}" => $this->_blogInfo->getMangledBlog(),
							 "{blogowner}" => $ownerInfo->getUsername(),
							 "{templatename}" => $template );
			
        	$templatePage = $this->getBaseUrl().$this->_replaceTags( $templateLinkFormat, $params );

            return $templatePage;
        }

        /**
         * Returns the link to a resource
         *
         * @param album Generates the correct link to fetch a resource
         */
        function resourceLink( $resource )
        {
            $resourceLinkFormat = $this->_config->getValue( "resource_link_format" );
			$params = $this->_fillResourceParameters( $resource );			
			$resourceLink = $this->getBaseUrl().$this->_replaceTags( $resourceLinkFormat, $params );
			
			return $resourceLink;
        }

		/**
		 * @private
		 */
		function _fillResourceParameters( $resource ) 
		{
			$album = $resource->getAlbum();
            $blogId = $this->_blogInfo->getId();
            $ownerId = $resource->getOwnerId();
            
            if ( $blogId != $ownerId ) {
            	$blogId = $ownerId;
				lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );				
            	$blogs =& new Blogs();
				$blogInfo = $blogs->getBlogInfo($blogId);
				$blogShortName = $blogInfo->getMangledBlog();
				$ownerInfo = $blogInfo->getOwnerInfo();
			} else {
				$blogShortName = $this->_blogInfo->getMangledBlog();
				$ownerInfo = $this->_blogInfo->getOwnerInfo();
			}
			
			$params = Array( "{blogid}" => $blogId,
			                 "{blogname}" => $blogShortName,
							 "{blogowner}" => $ownerInfo->getUsername(),							 
							 "{resourceid}" => $resource->getId(),
							 "{resourcename}" => rawurlencode($resource->getFileName()),
							 "{albumid}" => $album->getId(),
							 "{albumname}" => $album->getMangledName());
							 
			return $params;
		}		


        /**
         * Returns a string representing the request
         *
         * @return A String object representing the request
         */
        function getRequest()
        {
        	throw( new Exception( "CustomRequestGenerator::getRequest: function not implemented" ));
            die();
        }

        /**
         * @private
         * @params format
         * @params tags
         * @return string
         */
        function _replaceTags( $format, $tags )
        {
                // allow such things as archive_link_format:
                // /archives/(?:{year}/{month}/{day}/)?{postname}$
                // /(?:post|archives)/(?:{year}/{month}/{day}/)?{postname}$
            $result = preg_replace("/\(\?:([^\)\|]*)([^\)]*)\)/", "$1", $format);
            $result = preg_replace("/[$()?]/", "", $result);
            foreach( $tags as $key => $value ) {
                $result = str_replace( $key, $value, $result );
            }
            
            return $result;
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
		function getCurrentUrl( $category = null, $userInfo = null, $date = -1 )
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
				$url = $this->blogLink();
			}		
			
			$pageFormat = $this->getPageSuffix();
                // remove double slashes that can occur in certain combinations
                // of base_url/blog_url/pager/etc.
            if($pageFormat[0] == "/" && $url[strlen($url)-1] == "/")
                return substr($url, 0, -1).$pageFormat;
			return( $url.$pageFormat );
		}
		
		/**
		 * Returns the page format for this URL generator
		 *
		 * @return A page suffix
		 */
		function getPageSuffix()
		{
			$pageFormat = $this->_config->getValue( "page_suffix_format" );
			
			return( $pageFormat );
		}
    }
?>
