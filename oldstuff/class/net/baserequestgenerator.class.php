<?php

	/** 
	 * \defgroup Net
	 *
	 * The Net package includes all the classes that are related to network functionality in one way
	 * or another.
	 *
	 * @see Net_HTTP
	 */
    
    define( "DEFAULT_SCRIPT_NAME", "index.php" );

	/**
	 * Default folder where resources are installed
	 */
	define( "DEFAULT_GALLERY_RESOURCES_FOLDER", "./gallery/" );


    /**
     * \ingroup Net
     *
     * The BaseRequestGenerator class is some sort of proxy class that defines an interface that should be implemented
     * by classes wishing to provide a new request generator (plus some code that can be reused by all request
     * generators)
     * 
     * Each request generator defines its own format for its URLs, and by using request generators we can easily
     * change the format of our URLs without the need to alter our templates (and therefore, without the need to hardcode
     * URLs in our templates)
     *
     * You should never create instances of BaseRequestGenerator as it defines no code for most of its methods and
     * will instead throw exceptions. The correct way of getting a request generator is by using the
     * RequestGenerator factory or whenever we have a BlogInfo object available, by using the
     * BlogInfo::getBlogRequestGenerator() method.
     *
     * @see RequestGenerator
     */
	class BaseRequestGenerator  
	{

    	var $_params;
        var $_blogInfo;
        var $_baseUrl;
		var $_xhtmlEnabled;
		var $_subdomainsEnabled;
		var $_includeBlogId;
		var $_subdomainsBaseUrl;
		var $_scriptName;

        /**
         * Constructor. It initializes certain things such as the base url, checks whether subdomains are
         * enabled, etc. This method will fetch the values of the following configuration settings:
         *
         * - base_url: this is the base URL that will be used to generate all other URLs in the system
         * - subdomains_base_url: if subdomains are enabled, we should also set this URL to something valid. We can
         *   either use {blogname} or {username} to specify whether the URLs should be generated including the
         *   name of the user who owns the blog or the name of the blog.
         * - include_blog_id_in_url
         * - script_name: use this setting to rename index.php to something else and still have pLog generate
         *   URLs pointing to the right script.
         *
         * @param blogInfo A valid BlogInfo object
         */
    	function BaseRequestGenerator( $blogInfo = null )
        {
        	

            $this->_params = Array();

            $this->_blogInfo = $blogInfo;
            $config =& Config::getConfig();	
            $this->_baseUrl = $config->getValue( "base_url" );
			$this->_subdomainsBaseUrl = $config->getValue( "subdomains_base_url" );
			
			// get some information about the configuration of subdomains
			$this->_subdomainsEnabled = $config->getValue( "subdomains_enabled" );
			if( $this->_subdomainsEnabled ) 
				$this->_includeBlogId = $config->getValue( "include_blog_id_in_url" );
			else
				$this->_includeBlogId = true;
		
			// prepare the correct url if subdomains are enabled...
			if( $this->_subdomainsEnabled && $blogInfo != null ) {
				$this->_subdomainsBaseUrl = str_replace("{blogname}",
                                                        $blogInfo->getMangledBlog(),
                                                        $this->_subdomainsBaseUrl);
				$ownerInfo = $blogInfo->getOwnerInfo();
				$this->_subdomainsBaseUrl = str_replace("{username}",
                                                        $ownerInfo->getUsername(),
                                                        $this->_subdomainsBaseUrl);
                $this->_subdomainsBaseUrl = str_replace("{blogdomain}",
                                                        $blogInfo->getCustomDomain(),
                                                        $this->_subdomainsBaseUrl);
			}
            $this->_scriptName = $config->getValue( "script_name", DEFAULT_SCRIPT_NAME );
			
			// enable the xhtml mode by default, but it can be turned off
			// via the setXHTML() method
			$this->_xhtmlEnabled = true;
        }
		
        /**
         * @return Returns true if subdomains are enabled
         */
		function getSubdomainsEnabled()
		{
			return $this->_subdomainsEnabled;
		}
		
		/**
		 * @returns true if the blog identifier should be included in the  URL. This setting is only meaningful
		 * when subdomains are enabled and is only used by "raw" URLs
		 */
		function getIncludeBlogId()
		{
			return $this->_includeBlogId;
		}

		/**
		 * Returns the base URL that has been configured
		 *
		 * @param useSubdomains If set to true and subdomains are enabled, it will return the base URL as specified
		 * in the subdomains_base_url setting instead of base_url. It defaults to 'true'
		 */
        function getBaseUrl( $useSubdomains = true )
        {
			if( $useSubdomains && $this->_subdomainsEnabled )
				return $this->_subdomainsBaseUrl;
			else
				return $this->_baseUrl;
        }
        
        /**
         * @return Returns the name of the script to which for example forms will be submitted. Defaults to
         * index.php but it can be changed via the script_name configuration parameter.
         */
        function getScriptName()
        {
            return $this->_scriptName;
        }
		
        /** 
         * @return Returns the URL pointing to the main index file. This URL is built by querying the current
         * base URL and then appending the value of the script_name configuration setting.
         *
		 * @param useSubdomains If set to true and subdomains are enabled, it will use the base URL as specified
		 * in the subdomains_base_url setting instead of base_url. It defaults to 'true'.
		 */
        function getIndexUrl( $useSubdomains = true )
        {
			
            $url = $this->getBaseUrl( $useSubdomains )."/".$this->getScriptName();

            return $url;
        }

        /** 
         * @return Returns the URL pointing to the admin.php file. This URL is built by querying the current
         * base URL and then appending the value of the script_name configuration setting.
         *
		 * @param useSubdomains If set to true and subdomains are enabled, it will use the base URL as specified
		 * in the subdomains_base_url setting instead of base_url. It defaults to 'true'.
		 */        
        function getAdminUrl( $useSubdomains = true )
        {
            $url = $this->getBaseUrl( $useSubdomains )."/admin.php";

            return $url;
        }

        /** 
         * @return Returns the URL pointing to the rss.php file. This URL is built by querying the current
         * base URL and then appending the value of the script_name configuration setting.
         *
		 * @param useSubdomains If set to true and subdomains are enabled, it will use the base URL as specified
		 * in the subdomains_base_url setting instead of base_url. It defaults to 'true'.
		 */        
        function getRssUrl( $useSubdomains = false )
        {
            $url = $this->getBaseUrl( $useSubdomains )."/rss.php";

            return $url;
        }

        /** 
         * @return Returns the URL pointing to the trackback.php file. This URL is built by querying the current
         * base URL and then appending the value of the script_name configuration setting.
         *
		 * @param useSubdomains If set to true and subdomains are enabled, it will use the base URL as specified
		 * in the subdomains_base_url setting instead of base_url. It defaults to 'true'.
		 */        
        function getTrackbackUrl( $useSubdomains = false )
        {
            $url = $this->getBaseUrl( $useSubdomains )."/trackback.php";

            return $url;
        }

        /** 
         * @return Returns the URL pointing to the resserver.php file. This URL is built by querying the current
         * base URL and then appending the value of the script_name configuration setting.
         *
		 * @param useSubdomains If set to true and subdomains are enabled, it will use the base URL as specified
		 * in the subdomains_base_url setting instead of base_url. It defaults to 'true'.
		 */        
        function getResourceServerUrl( $useSubdomains = false )
        {
            $url = $this->getBaseUrl( $useSubdomains )."/resserver.php";

            return $url;
        }

		/**
		 * Returns the base URL to resources
		 */
		function getResourcesBaseUrl()
		{
			lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
			$config =& Config::getConfig();
			// the default value for this setting is to use a relative path that starts with './' so we have to make sure that
			// that bit is not included in the URL (it wouldn't have any harmful effect, but it'd look ugly)
			$galleryFolder = str_replace( "./", "/", $config->getValue( "resources_folder", DEFAULT_GALLERY_RESOURCES_FOLDER ));
			// make sure that the base URL ends with a forward slash
			if( $galleryFolder[strlen($galleryFolder)-1] != "/" )
				$galleryFolder .= "/";
			
			$url = $this->getBaseUrl().$galleryFolder;
			
			return( $url );
		}

        /** 
         * @return Returns the URL pointing to the given parameter. This URL is built by querying the current
         * base URL and then appending the value of the $res parameter
         *
         * @param res A valid URL path
		 * @param useSubdomains If set to true and subdomains are enabled, it will use the base URL as specified
		 * in the subdomains_base_url setting instead of base_url. It defaults to 'true'.
		 */        
        function getUrl( $res, $useSubdomains = false )
        {
        	$baseUrl = $this->getBaseUrl( $useSubdomains );
            $url = $baseUrl.$res;

            return $url;
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
         * @private
         */
        function reset()
        {
        	$this->_params = Array();
        }

        /**
         * Returns a string representing the request. Child classes should implement this method in order
         * to return a meaningful URL.
         *
         * @return A String object representing the request
         */
        function getRequest()
        {
        	throw( new Exception( "This function must be implemented by child classes." ));
        }

    	/**
         * Returns the permalink for a post. Must be implemented by child classes to generate a valid URL.
         *
         * @param post The Article object
         * @return The link for the permalink
         */
		function postPermalink( $post )
        {
        	throw( new Exception( "This function must be implemented by child classes." ));
        }

        /**
         * Returns the comment link for a post. Must be implemented by child classes to generate a valid URL.
         *
         * @param post The Article object
         * @return The correct string
         */
        function postCommentLink( $post )
        {
        	throw( new Exception( "This function must be implemented by child classes." ));
        }

        /**
         * Returns the link for the post. Must be implemented by child classes to generate a valid URL.
         *
         * @param post The Article object
         * @return The link for the post
         */
        function postLink( $post )
        {
        	throw( new Exception( "This function must be implemented by child classes." ));
        }

        /**
         * Returns the link for the post. Must be implemented by child classes to generate a valid URL.
         *
         * @param post The Article object
         * @return The link for the post
         */
        function postRssLink( $post )
        {
        	throw( new Exception( "This function must be implemented by child classes." ));
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
			throw( new Exception( "This function must be implemented by child classes." ));
	    }	
	
        /**
         * Returns the url for the rss feed of user posts. All URL generators use the same
 	 	 * format for this for the time being, so we'll keep the method in this base class.
         *
         * @param category The UserInfo object with information about the category
         * whose RSS feed we'd like to generate
         * @þaram profile The profile we'd like to generate: RSS 0.90, RSS 1.0, RSS 2.0
         * or XML.
         * @return The url pointing to the rss feed of user
         * @see BlogInfo
         */
        function userRssLink( $userInfo, $profile = "" )
        {
			$rssLink = $this->getRssUrl();
            $rssLink .= "?blogId=".$this->_blogInfo->getId();
            
            if( $profile != "" )
            	$rssLink .= "&amp;profile=$profile";

			$rssLink .= "&amp;userId=".$userInfo->getId();
            return $rssLink;	
		}	

        /**
         * Returns the link of a category. Must be implemented by child classes to generate a valid URL.
         * This method has been deprecated as of pLog 1.0 so please use the method BaseRequestGenerator::categoryLink()
         * which takes an ArticleCategory object instead of an Article object since this method does not support
         * posts that have multiple categories.
         *
         * @param post The post from which we'll fetch the category and then generate the right link.
         * @return The url pointing to the page with only the posts belonging to that category.
         * @see Article
         * @see categoryLink
         * @deprecated
         */
        function postCategoryLink( $post )
        {
        	throw( new Exception( "This function must be implemented by child classes." ));
        }

        /**
         * Returns the link but given a category. Must be implemented by child classes to generate a valid URL.
         *
         * @see postCategoryLink
         * @see ArticleCategory
         * @param An ArticleCategory object containing the information regarding the category.
         * @return A string with the correct url pointing to the page that will show only the posts that belong
         * to the given category.
         */
        function categoryLink( $category )
        {
        	throw( new Exception( "This function must be implemented by child classes." ));
        }

        /**
         * Returns a link to only the articles of the user. Must be implemented by child classes to generate a valid URL.
         *
         * @param user The user whose posts we would like to see
         * @param category Optionally, we can specify an ArticleCategory object
         * @return A string containing the right url to only the posts of the user.
         * @see UserInfo
         * @see ArticleCategory
         */
        function postUserLink( $user, $category = null )
        {
        	throw( new Exception( "This function must be implemented by child classes." ));
        }

        /**
         * Returns the url of the host where the blog is running. Must be implemented by child classes to generate a valid URL.
         *
         * @return Returns the url where the blog is running.
         */
        function blogLink( $blogInfo = null )
        {
        	throw( new Exception( "This function must be implemented by child classes." ));
        }

        /**
         * Returns the url where the rss feed is running. Must be implemented by child classes to generate a valid URL.
         *
         * @param profile The profile whose link we'd like to return (rss090, rss10, rss20 or atom)
         * @param blogInfo A BlogInfo object containing information about the blog.
         * @return The url pointing to the rss feed of the journal.
         * @see BlogInfo
         */
        function rssLink( $profile = "", $blogInfo = null )
        {
        	throw( new Exception( "This function must be implemented by child classes." ));
        }	

        /**
         * Returns the url to reply to a given comment. Must be implemented by child classes to generate a valid URL.
         *
         * @param post An Article object with information about the post
         * @param commen A UserComment object containing information about the post we'd like to reply to.
         * @return The right url to reply to this comment.
         * @see UserComment
         * @see Article
         */
        function replyCommentLink( $post, $comment )
        {
        	throw( new Exception( "This function must be implemented by child classes." ));
        }

        /**
         * Manually adds the "show more" link in a post. Must be implemented by child classes to generate a valid URL.
         *
         * @param post The post we are going to cut.
         * @param maxWords Amount of words we'd like to allow.
         * @param linkText Text we are going to show.
         * @return The modified link.
         */
        function addShowMoreLink( $post, $maxWords, $linkText )
        {
        	throw( new Exception( "This function must be implemented by child classes." ));
        }

        /**
         * Returns the trackback link for a given post.
         *
         * @param post The post with the necessary information.
         * @return A string representing the rdf trackback link.
         */
        function postTrackbackLink( $post )
        {
        	$rdfHeader = '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                                   xmlns:dc="http://purl.org/dc/elements/1.1/"
                                   xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">';
            $trackbackLink = $this->getTrackbackUrl()."?id=".$post->getId();
			$postLink = $this->postLink($post);
			$topic = str_replace('-', '\-', $post->getTopic());
            $rdfBody = "<rdf:Description
                             rdf:about=\"".$postLink."\"
                             dc:identifier=\"".$postLink."\"
                             dc:title=\"".$topic."\"
                             trackback:ping=\"".$trackbackLink."\"/>";
            $rdfFooter = "</rdf:RDF>";

            return $rdfHeader.$rdfBody.$rdfFooter;
        }

        /**
         * generates an archive link given a date. Must be implemented by child classes to generate a valid URL.
         *
         * @param date A String in the format yyyymm
         * @return A valid archive link
         */      
        function getArchiveLink( $date )
        {
        	throw( new Exception( "This function must be implemented by child classes." ));
        }


        /**
         * Returns the link to the page showing the trackback statistics for a given post. Must be implemented by child classes to generate a valid URL.
         *
         * @param post The post with the information.
         * @return Returns a string with the valid link.
         */
        function postTrackbackStatsLink( $post )
        {
        	throw( new Exception( "This function must be implemented by child classes." ));
        }

        /**
         * Returns the link to an album. If the parameter is null or zero, then a link to the top level
		 * album will be returned. Must be implemented by child classes to generate a valid URL.
         *
         * @param album The GalleryAlbum object.
         */
        function albumLink( $album = null )
        {
        	throw( new Exception( "This function must be implemented by child classes." ));
        }
        
        /**
         * Given an album, generates a link to its parent. Must be implemented by child classes to generate
         * a valid URL.
         *
         * @param album The album
         */                        
        function parentAlbumLink( $album )
        {
			throw( new Exception( "This function must be implemented by child classes." ));
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
        	throw( new Exception( "This function must be implemented by child classes." ));
        }        

        /**
         * Returns the link to a resource. Must be implemented by child classes to generate a valid URL.
         *
         * @param album Generates the correct link to fetch a resource
         */
        function resourceLink( $resource )
        {
        	throw( new Exception( "This function must be implemented by child classes." ));
        }

        /**
         * Returns the link to a resource preview
         *
         * @param album Generates the correct link to fetch a resource preview
         */
        function resourcePreviewLink( $resource )
        {
            $blogId = ($resource->getOwnerId() ? $resource->getOwnerId() : $this->_blogInfo->getId());            
			$resourceLink = $this->getResourcesBaseUrl().$blogId."/previews/".rawurlencode($resource->getPreviewFileName());            
            return $resourceLink;
        }
		
        /**
         * Returns the link to a resource preview
         *
         * @param album Generates the correct link to fetch a resource preview
         */
        function resourceMediumSizePreviewLink( $resource )
        {
            $blogId = ($resource->getOwnerId() ? $resource->getOwnerId() : $this->_blogInfo->getId());            
			$resourceLink = $this->getResourcesBaseUrl().$blogId."/previews-med/".rawurlencode($resource->getMediumSizePreviewFileName());
            return $resourceLink;
		}

        /**
         * Returns the link to a resource
         *
         * @param resource Generates the correct link to fetch a resource
         */
        function resourceDownloadLink( $resource )
        {
            $blogId = ($resource->getOwnerId() ? $resource->getOwnerId() : $this->_blogInfo->getId());            
			$resourceLink = $this->getResourcesBaseUrl().$blogId."/".rawurlencode($resource->getOriginalSizeFileName());
            return $resourceLink;
        }

        /**
         * Returns the link to a resource preview with raw file name, for TinyMCE use only
         *
         * @param album Generates the correct link to fetch a resource preview
         */
        function rawResourcePreviewLink( $resource )
        {
            $blogId = ($resource->getOwnerId() ? $resource->getOwnerId() : $this->_blogInfo->getId());            
			$resourceLink = $this->getResourcesBaseUrl().$blogId."/previews/".$resource->getPreviewFileName();            
            return $resourceLink;
        }
		
        /**
         * Returns the link to a resource preview with raw file name, for TinyMCE use only
         *
         * @param album Generates the correct link to fetch a resource preview
         */
        function rawResourceMediumSizePreviewLink( $resource )
        {
            $blogId = ($resource->getOwnerId() ? $resource->getOwnerId() : $this->_blogInfo->getId());            
			$resourceLink = $this->getResourcesBaseUrl().$blogId."/previews-med/".$resource->getMediumSizePreviewFileName();
            return $resourceLink;
		}

        /**
         * Returns the link to a resource with raw file name, for TinyMCE use only
         *
         * @param resource Generates the correct link to fetch a resource
         */
        function rawResourceDownloadLink( $resource )
        {
            $blogId = ($resource->getOwnerId() ? $resource->getOwnerId() : $this->_blogInfo->getId());            
			$resourceLink = $this->getResourcesBaseUrl().$blogId."/".$resource->getOriginalSizeFileName();
            return $resourceLink;
        }
		
		/**
		 * whether we should generate valid xhtml requests or not
		 * (used for example when sending out messages, as some email clients will
		 * go to the wrong url when using xhtml message
		 *
		 * @param enable Whether to enable xhtml or not (enabled by default)
		 * @return Always true
		 */
		function setXHTML( $enabled = true )
		{
			$this->_xhtmlEnabled = $enabled;
		}
		
		/**
		 * whether xhtml mode is enabled or not
		 *
 		 * @return True if enabled or false otherwise
		 */
		function isXHTML()
		{
			return $this->_xhtmlEnabled;
		}
		
		/**
		 * generates a unique atom id for the entry. This is not as easy
		 * as it sounds, take a look http://diveintomark.org/archives/2004/05/28/howto-atom-id
		 *
		 * @param article An Article object
		 * @return A unique atom article id
		 */
		function getAtomUniqueId( $article )
		{
			  lt_include( PLOG_CLASS_PATH."class/net/url.class.php" );		
		      $config =& Config::getConfig();
		      $url = new Url($config->getValue( "base_url" ));
		      $articleDate = $article->getDateObject();
		      
		      // Timestamp::getDay() doesn't do two digits, so we'll do it here
		      $day = $articleDate->getDay();
		      if( $day < 10 ) $day = "0".$day;		      
		      
		      $date = $articleDate->getYear()."-".$articleDate->getMonth()."-".$day;
		      $tag = "tag:".$url->getHost().",".$date.":".$article->getId();
		      
		      return $tag;
		}		

        /**
         * get user profile picture link. It is not necessary for child classes to implement this method.
         *
         * @param blogInfo
         */
        function profileLink($blogInfo = null)
        {
        	if( $blogInfo == null ) {
				$blogInfo = $this->_blogInfo;
            }
			
            $ownerInfo = $blogInfo->getOwnerInfo();
            $pic = $ownerInfo->getPicture();
            if(!$pic){
                // show a default user picture
                return "imgs/no-user-picture.jpg";
            } else {
			    return $this->resourceLink($pic);
            }
        }
		
        
		/**
		 * generates the correct path to a file in the template folder, without having to worry
		 * whether the template was installed in /templates/ or in /templates/blog_X/. It is not necessary
		 * for child classes to implement this method.
		 *
		 * @param file
		 * @return A string
		 */
		function getTemplateFile( $file )
		{
			lt_include( PLOG_CLASS_PATH."class/file/file.class.php" );
			
		    // get the current template set
		    $blogSettings = $this->_blogInfo->getSettings();
		    $template = $blogSettings->getValue( "template" );
		    
		    // define this couple of things
        	$baseUrl = $this->getBaseUrl();
			$config =& Config::getConfig();
			$url = "$baseUrl/".File::expandPath( $config->getValue( "template_folder" ))."/";
		    		    
		    // is it a blog template?
		    $blogTemplates = $blogSettings->getValue( "blog_templates" );
		    
		    if( !is_array($blogTemplates ))
		        $url .= "$template/$file";
		    else {
		        if( in_array( $template, $blogTemplates ))
		          $url .= "blog_".$this->_blogInfo->getId()."/$template/$file";
		        else
		          $url .= "$template/$file";
		    }
		
            return $url;		    
		}		
		
		/**
		 * generates the correct path to a file in the template folder, without having to worry
		 * whether the template was installed in /templates/ or in /templates/blog_X/
		 * This is locale-aware version.
		 *
		 * @param file
		 * @return A string
		 */
		function getTemplateLocaledFile( $file )
		{
			// get the current template set
			$blogSettings = $this->_blogInfo->getSettings();
			$template = $blogSettings->getValue( "template" );
			$localeCode = $blogSettings->getValue( "locale" );

			// if this file has extension
			$parts = explode( ".", $file );
			if( count($parts) > 1 && !strstr($parts[count($parts) - 1], "/")) {
				$ext = array_pop( $parts );
				array_push ( $parts, $localeCode, $ext);
				$localedFile = implode( ".", $parts);
			} else {
				$localedFile = $file;
			}
			unset($parts);

			$baseUrl = $this->getBaseUrl();
			$url = "$baseUrl/templates/";

			$blogTemplates = $blogSettings->getValue( "blog_templates" );

			if( !is_array($blogTemplates) ) {
					$filePath = "$template";
			} else {
				if( in_array( $template, $blogTemplates ))
					$filePath = "blog_".$this->_blogInfo->getId()."/$template";
				else
					$filePath = "$template";
			}

			if( File::exists("$filePath/$localedFile"))
				$url .= "$filePath/$localedFile";
			else
				$url .= "$filePath/$file";

			return $url;
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
			throw( new Exception( "This function must be implemented by child classes." ));
		}
		
		/**
		 * Returns the page format for this URL generator
		 *
		 * @return A page suffix
		 */
		function getPageSuffix( $page = "" )
		{
			throw( new Exception( "This function must be implemented by child classes." ));		
		}		
    }
?>
