<?php

	lt_include( PLOG_CLASS_PATH."class/net/baserequestgenerator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/stringutils.class.php" );
	lt_include( PLOG_CLASS_PATH."class/database/db.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );

    /**
     * \ingroup Net
     *
     * Generates search-engine friendly URLs and uses Apache mod_rewrite to parse them
     *
     * @see RequestGenerator
     * @see BaseRequestGenerator
     */
	class ModRewriteRequestGenerator extends BaseRequestGenerator 
	{

    	/**
         * Constructor.
         *
         * @param blogInfo A BlogInfo object
         */
    	function ModRewriteRequestGenerator( $blogInfo )
        {
        	$this->BaseRequestGenerator( $blogInfo );
          
        }

        /**
         * Adds a parameter to the request
         *
         * @param paramName Name of the parameter
         * @param paramValue Value given to the parameter
         */
        function addParameter( $paramName, $paramValue )
        {
        	$this->_params[$paramName] = $paramValue;
        }

    	/**
         * Returns the permalink for a post
         *
         * @param post The Article object
         * @return The link for the permalink
         */
		function postPermalink( $post )
        {
            $permaLink = $this->getBaseUrl().'/'.$this->_blogInfo->getId().'_'.StringUtils::text2url( $this->_blogInfo->getBlog() ).'/archive/'.$post->getId().'_'.StringUtils::text2url( $post->getTopic() ).'.html';

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
            $commentLink = $this->getBaseUrl().'/'.$this->_blogInfo->getId().'_'.StringUtils::text2url( $this->_blogInfo->getBlog() ).'/comment/'.$post->getId().'_'.StringUtils::text2url( $post->getTopic() ).'.html';

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
            $postLink = $this->getBaseUrl().'/'.$this->_blogInfo->getId().'_'.StringUtils::text2url( $this->_blogInfo->getBlog() ).'/archive/'.$post->getId().'_'.StringUtils::text2url( $post->getTopic() ).'.html';

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
            //throw( new Exception( "DEPRECATED" ));
            //die();
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

            $categoryLink = $this->getBaseUrl().'/'.$this->_blogInfo->getId().'_'.StringUtils::text2url( $this->_blogInfo->getBlog() ).'/categories/'.$category->getId().'_'.StringUtils::text2url( $category->getName() ).'.html';

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
                $link = $this->getBaseUrl().'/'.$this->_blogInfo->getId().'_'.StringUtils::text2url( $this->_blogInfo->getBlog() );
            }
            else {
                $link = $this->getBaseUrl().'/'.$blogInfo->getId().'_'.StringUtils::text2url( $blogInfo->getBlog() );
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
        	if( $blogInfo == null )
                $link = $this->getBaseUrl().'/'.$this->_blogInfo->getId().'_'.StringUtils::text2url( $this->_blogInfo->getBlog() ).'/feeds/';

            else
                $link = $this->getBaseUrl().'/'.$blogInfo->getId().'_'.StringUtils::text2url( $bloginfo->getBlog() ).'/feeds/';
            
            if( $profile != "" )
                $link .= $profile;


            return $link;
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

        	if( $blogInfo == null )
                $link = $this->getBaseUrl().'/'.$this->_blogInfo->getId().'_'.StringUtils::text2url( $this->_blogInfo->getBlog() ).'/feeds/categories/'.$category->getId().'_'.StringUtils::text2url( $category->getName() ).'/';
            else
                $link = $this->getBaseUrl().'/'.$blogInfo->getId().'_'.StringUtils::text2url( $blogInfo->getBlog() ).'/feeds/categories/'.$category->getId().'_'.StringUtils::text2url( $category->getName() ).'/';

            if( $profile != "" )
                $link .= $profile;

            return $link;
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

            if( $this->_blogInfo == null )
                $blogID = 1;
            else
                $blogID = $this->_blogInfo->getId();
            
            // well, does this function actually ever get called without a blogInfo Object? if so this won't work!
            $link = $this->getBaseUrl().'/'.$blogID.'_'.StringUtils::text2url( $this->_blogInfo->getBlog() ).'/archive/'.$date.'.html';

            return $link;
        }

        /**
         * Returns the link to the page showing the trackback statistics for a given post.
         *
         * @param post The post with the information.
         * @return Returns a string with the valid link.
         */
        function postTrackbackStatsLink( $post )
        {
            $tbStatsLink = $this->getBaseUrl().'/'.$this->_blogInfo->getId().'_'.StringUtils::text2url( $this->_blogInfo->getBlog() ).'/trackbacks/'.$post->getId().'_'.StringUtils::text2url( $post->getTopic() ).'.html';

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
                $link = $this->getBaseUrl().'/'.$this->_blogInfo->getId().'_'.StringUtils::text2url( $this->_blogInfo->getBlog() ).'/albums/';

            else
                $link = $this->getBaseUrl().'/'.$this->_blogInfo->getId().'_'.StringUtils::text2url( $this->_blogInfo->getBlog() ).'/albums/'.$album->getId().'_'.StringUtils::text2url( $album->getName() ).'.html';

            return $link;
        }

        /**
         * Returns the link to a resource
         *
         * @param album Generates the correct link to fetch a resource
         */
        function resourceLink( $resource )
        {
            $blogId = $this->_blogInfo->getId();
            $ownerId = $resource->getOwnerId();
            
            if ( $blogId != $ownerId ) {
            	$blogId = $ownerId;
            	$blogs =& new Blogs();
				$blogInfo = $blogs->getBlogInfo($blogId);
				$blogShortName = $blogInfo->getBlog();
			} else {
				$blogShortName = $this->_blogInfo->getBlog();
			}
            
            $resourceLink = $this->getBaseUrl().'/'.$blogId.'_'.StringUtils::text2url( $blogShortName ).'/resources/'.rawurlencode($resource->getFileName()).'.html';

            return $resourceLink;
        }

        /**
         * Given an album, generates a link to its parent. Must be implemented by child classes to generate
         * a valid URL.
         *
         * @param album The album
         */                                
        function parentAlbumLink( $album )
        {
            if( $album->getParentId() > 0 )
            {
            	$galleryAlbums = new GalleryAlbums();
                $parentAlbum = $galleryAlbums->getAlbum( $album->getParentId() );
                
                $albumLink =  $this->getBaseUrl().'/'.$this->_blogInfo->getId().'_'.StringUtils::text2url( $this->_blogInfo->getBlog() ).'/albums/'.$album->getParentId().'_'.StringUtils::text2url( $parentAlbum->getName() ).'.html';
            	
            } else {
            	// this will return the default link to the albums
                $albumLink = $this->getBaseUrl().'/'.$this->_blogInfo->getId().'_'.StringUtils::text2url( $this->_blogInfo->getBlog() ).'/albums/';
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
            $templatePage = $this->getBaseUrl().'/'.$this->_blogInfo->getId().'_'.StringUtils::text2url( $this->_blogInfo->getBlog() ).'/'.$template;

            return $templatePage;
        }

        /**
         * Returns a string representing the request
         *
         * @return A String object representing the request
         */
        function getRequest()
        {
        	$request = "";

            $amp = "&amp;";

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
				$url = $this->blogLink();
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
			$pageFormat = ".page.";			
			return( $pageFormat );
		}
    }
?>
