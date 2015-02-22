<?php

    lt_include( PLOG_CLASS_PATH."class/net/baserequestgenerator.class.php" );

    /**
     * \ingroup Net
     *
     * Generates normal, plain HTTP requests.
     *
     * @see RequestGenerator
     * @see BaseRequestGenerator
     */
    class RawRequestGenerator extends BaseRequestGenerator
    {

    	/**
         * Constructor.
         *
         * @param blogInfo A BlogInfo object
         */
    	function RawRequestGenerator( $blogInfo )
        {
        	$this->BaseRequestGenerator( $blogInfo );
        }

        /**
         * Adds a parameter to the request
         *
         * @param paramName Name of the parameter
         * @param paramValue Value given to the parameter
         * @private
         */
        function addParameter( $paramName, $paramValue )
        {
        	$this->_params[$paramName] = $paramValue;
        }

        /**
         * @private
         */
		function removeParameter( $paramName )
		{
			unset( $this->_params[$paramName] );
		}

    	/**
         * Returns the permalink for a post
         *
         * @param post The Article object
         * @return The link for the permalink
         */
		function postPermalink( $post )
        {
            $this->addParameter( "op", "ViewArticle" );
            $this->addParameter( "articleId", $post->getId());
            if( $this->_blogInfo != null )
            	$this->addParameter( "blogId", $this->_blogInfo->getId());

            $permaLink = $this->getIndexUrl().$this->getRequest();

            return $permaLink;
        }

        /**
         * Returns the comment link for a post
         *
         * @param post The Article object
         * @return The correct string
         */
        function postCommentLink( $post )
        {
            $this->addParameter( "op", "Comment" );
            $this->addParameter( "articleId", $post->getId());
            if( $this->_blogInfo != null )
            	$this->addParameter( "blogId", $this->_blogInfo->getId());

            $commentLink = $this->getIndexUrl().$this->getRequest();

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
            $this->addParameter( "op", "ViewArticle" );
            $this->addParameter( "articleId", $post->getId());
            if( $this->_blogInfo != null )
            	$this->addParameter( "blogId", $this->_blogInfo->getId());

            $postLink = $this->getIndexUrl().$this->getRequest();

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
             $this->addParameter( "op", "Default" );
             $postCategoryIds = $post->getCategoryIds();
             $this->addParameter( "postCategoryId", $postCategoryIds[0]);
             if( $this->_blogInfo != null )
               $this->addParameter( "blogId", $this->_blogInfo->getId());

             $categoryLink = $this->getIndexUrl().$this->getRequest();

             return $categoryLink;
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
            $this->addParameter( "op", "Default" );
            $this->addParameter( "postCategoryId", $category->getId());
            if( $this->_blogInfo != null )
            	$this->addParameter( "blogId", $this->_blogInfo->getId());

            $categoryLink = $this->getIndexUrl().$this->getRequest();

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
            $this->addParameter( "op", "Default" );
            $this->addParameter( "userId", $user->getId());
            if( $category != null )
            	$this->addParameter( "postCategoryId", $category->getId());
            if( $this->_blogInfo != null )
            	$this->addParameter( "blogId", $this->_blogInfo->getId());
            $userLink = $this->getIndexUrl().$this->getRequest();

            return $userLink;
        }

        /**
         * Returns the url of the host where the blog is running
         *
         * @return Returns the url where the blog is running.
         */
        function blogLink( $blogInfo = null )
        {
        	if( $blogInfo == null ) {
				$blogInfo = $this->_blogInfo;
            }


			$this->addParameter( "blogId", $blogInfo->getId());
			$link = $this->getIndexUrl().$this->getRequest();

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
        	if( $blogInfo == null )
            	$this->addParameter( "blogId", $this->_blogInfo->getId());
            else
            	$this->addParameter( "blogId", $blogInfo->getId());

            if( $profile != "" )
            	$this->addParameter( "profile", $profile );

            $rssLink = $this->getRssUrl().$this->getRequest(false);

            return $rssLink;
        }

        /**
         * Returns the url for the rss feed of a category
         *
         * @param category The ArticleCategory object with information about the category
         * whose RSS feed we'd like to generate
         * @�aram profile The profile we'd like to generate: RSS 0.90, RSS 1.0, RSS 2.0
         * or XML.
         * @param blogInfo A BlogInfo object containing information about the blog.
         * @return The url pointing to the rss feed of the journal.
         * @see BlogInfo
         */
        function categoryRssLink( $category, $profile = "", $blogInfo = null )
        {
        	$this->addParameter( "categoryId", $category->getId());

        	if( $blogInfo == null )
            	$this->addParameter( "blogId", $this->_blogInfo->getId());
            else
            	$this->addParameter( "blogId", $blogInfo->getId());

            if( $profile != "" )
            	$this->addParameter( "profile", $profile );

            $rssLink = $this->getRssUrl().$this->getRequest(false);

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
            $this->addParameter( "op", "Comment" );
            $this->addParameter( "articleId", $post->getId());
            $this->addParameter( "parentId", $comment->getId());
            if( $this->_blogInfo != null )
            	$this->addParameter( "blogId", $this->_blogInfo->getId());

            $replyCommentLink = $this->getIndexUrl().$this->getRequest();

            return $replyCommentLink;
        }

        /**
         * generates an archive link given a date.
         *
         * @param date A String in the format yyyymm
         * @return A valid archive link
         */
        function getArchiveLink( $date )
        {
        	$this->addParameter( "op", "Default" );
            $this->addParameter( "Date", $date);
            if( $this->_blogInfo != null )
            	$this->addParameter( "blogId", $this->_blogInfo->getId());

            $archiveUrl = $this->getIndexUrl().$this->getRequest();

            return $archiveUrl;
        }

        /**
         * Returns the link to the page showing the trackback statistics for a given post.
         *
         * @param post The post with the information.
         * @return Returns a string with the valid link.
         */
        function postTrackbackStatsLink( $post )
        {
            $this->addParameter( "op", "Trackbacks" );
            $this->addParameter( "articleId", $post->getId());
            $this->addParameter( "blogId", $this->_blogInfo->getId());

            $tbStatsLink = $this->getIndexUrl().$this->getRequest();

            return $tbStatsLink;
        }

        /**
         * Returns the link to an album
         *
         * @param album The GalleryAlbum object.
         */
        function albumLink( $album = null )
        {
        	$this->addParameter( "op", "ViewAlbum" );
			if( $album == null )
				$this->addParameter( "albumId", "0" );
			else
				$this->addParameter( "albumId", $album->getId());
            $this->addParameter( "blogId", $this->_blogInfo->getId());

            $albumLink = $this->getIndexUrl().$this->getRequest();

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
        	$this->addParameter( "op", "ViewAlbum" );
            $this->addParameter( "albumId", $album->getParentId());
            $this->addParameter( "blogId", $this->_blogInfo->getId());

            $albumLink = $this->getIndexUrl().$this->getRequest();

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
        	$this->addParameter( "op", "Template" );
            $this->addParameter( "blogId", $this->_blogInfo->getId());
            $this->addParameter( "show", $template );

            $templatePage = $this->getIndexUrl().$this->getRequest();

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

            $this->addParameter( "op", "ViewResource" );
            $this->addParameter( "blogId", $blogId);
            $this->addParameter( "resource", $resource->getFileName());

            $resourceLink = $this->getIndexUrl().$this->getRequest();

            return $resourceLink;
        }

        /**
         * Returns a string representing the request
         *
         * @return A String object representing the request
         */
        function getRequest( $removeBlogIdIfNecessary = true )
        {
        	$request = "";

			if( $this->isXHTML())
				$amp = "&amp;";
			else
				$amp = "&";

			if( !$this->getIncludeBlogId() && $removeBlogIdIfNecessary )
				$this->removeParameter( "blogId" );

        	foreach( $this->_params as $name => $value )
            {
               	if( $request == "" )
                	$request .= "?";
                else
                	$request .= $amp;

                $request .= urlencode($name) ."=".urlencode($value);
            }

            $this->reset();

            return $request;
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
			if( $this->isXHTML())
				$amp = "&amp;";
			else
				$amp = "&";

			$url = $this->getIndexUrl()."?op=Default{$amp}blogId=".$this->_blogInfo->getId()."{$amp}";
			$params = "";

			if( $category )
				$params .= "postCategoryId=".$category->getId().$amp;
			if( $userInfo )
				$params .= "userId=".$userInfo->getId().$amp;
			if( $date != -1 )
				$params .= "Date=".$date.$amp;

			if( $params )
				$url .= $params;

			$url .= $this->getPageSuffix();

			return( $url );
		}

		/**
		 * Returns the page format for this URL generator
		 *
		 * @return A page suffix
		 */
		function getPageSuffix()
		{
			if( $this->isXHTML())
				$amp = "&amp;";
			else
				$amp = "&";

			$pageFormat = $amp."page=";

			return( $pageFormat );
		}
    }
?>
