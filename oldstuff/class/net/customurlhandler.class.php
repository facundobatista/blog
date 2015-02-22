<?php


	
	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	lt_include( PLOG_CLASS_PATH."class/net/linkformatmatcher.class.php" );
    lt_include( PLOG_CLASS_PATH."class/net/url.class.php" );	

	/**
	 * \ingroup Net
	 *
	 * Parses incoming URLs when "custom URLs" are enabled. It uses the LinkFormatMatcher
	 * to determine which *_link_format configuration setting matches the incoming URL, extracts
	 * the information from the URL according to the specified format and puts it back to the
	 * request so that the core classes can fetch the parameters.
	 *
	 * The use of this class is very specific and it should not be be called directly anyway.
	 *
	 * @see LinkFormatMatcher
	 */	
	class CustomUrlHandler 
	{
	
		var $_formats;
		var $_params;
		var $_vars;
		var $_includeFile;
		var $_format;
        var $_indexName;
	
		function CustomUrlHandler()
		{
			
			
			// we need to figure out the full server path so that we can generate proper
			// regexps for the url parsing. This should get rid of issues like
			// http://bugs.plogworld.net/view.php?id=369
			// The base url will be one or another depending
            $config =& Config::getConfig();
            if( $config->getValue( "subdomains_enabled" ))
                $url = new Url( $config->getValue( "subdomains_base_url" ));
            else
                $url = new Url( $config->getValue( "base_url" ));
            $path = $url->getPath();
			
			// intialize the array we're going to use
			$config =& Config::getConfig();
            $this->_indexName = $config->getValue("script_name");
			$this->_formats = Array( "permalink_format" => $path.$config->getValue( "permalink_format" ),
									 "category_link_format" => $path.$config->getValue( "category_link_format" ),
									 "blog_link_format" => $path.$config->getValue( "blog_link_format" ),
									 "archive_link_format" => $path.$config->getValue( "archive_link_format" ),
									 "user_posts_link_format" => $path.$config->getValue( "user_posts_link_format" ),
									 "post_trackbacks_link_format" => $path.$config->getValue( "post_trackbacks_link_format" ),
									 "template_link_format" => $path.$config->getValue( "template_link_format" ),
									 "album_link_format" => $path.$config->getValue( "album_link_format" ),
									 "resource_link_format" => $path.$config->getValue( "resource_link_format" ),
									 "resource_download_link_format" => $path.$config->getValue( "resource_download_link_format" ),
									 "resource_preview_link_format" => $path.$config->getValue( "resource_preview_link_format" ),
									 "resource_medium_size_preview_link_format" => $path.$config->getValue( "resource_medium_size_preview_link_format" ));
			// if the url did not match any of the current settings, then let's try to parse it as an old
			// "search engine friendly" url
			$this->_fallback = Array( "permalink_format" => $path."/post/{blogid}/{postid}",
									 "category_link_format" => $path."/category/{blogid}/{catid}",
									 "blog_link_format" => $path."/{blogid}$",
									 "archive_link_format" => $path."/archives/{blogid}/{year}/{month}/{day}",
									 "user_posts_link_format" => $path."/user/{blogid}/{userid}",
									 "post_trackbacks_link_format" => $path."/trackbacks/{blogid}/{postid}",
									 "template_link_format" => $path."/static/{blogid}/{templatename}",
									 "album_link_format" => $path."/album/{blogid}/{albumid}",
									 "resource_link_format" => $path."/resource/{blogid}/{resourceid}",
									 "resource_download_link_format" => $path."/get/{blogid}/{resourceid}",
									 "resource_preview_link_format" => "!INVALID_LIFETYPE_URL",  // this one does not exist
									 "resource_medium_size_preview_link_format" => "!INVALID_LIFETYPE_URL" ); // this one does not exist either
		}
		
		/**
		 * @private
		 */
		function getPageParameterValue( $requestUri )		
		{
			$config =& Config::getConfig();
			$pageSuffix = $config->getValue( "page_suffix_format" );
			$pageSuffixRegexp = str_replace( "{page}", "([0-9]*)", $pageSuffix );
			$pageSuffixRegexp = "/".str_replace( "/", "\/", $pageSuffixRegexp )."/";
			//print($pageSuffixRegexp."<br/>");			
			if( preg_match( $pageSuffixRegexp, $requestUri, $matches ))
				$page = $matches[1];
			else
				$page = "";
				
			//print("page = $page<br/>");
			
			return( $page );
		}
		
		/**
		 * @private
		 * Given a request uri/url, remove the suffix which is used for the paging if any		 
		 */
		function removePageSuffix( $requestUri )
		{
			$config =& Config::getConfig();
			$pageSuffix = $config->getValue( "page_suffix_format" );
			$pageSuffixRegexp = str_replace( "{page}", "[0-9]*", $pageSuffix );
			$pageSuffixRegexp = "/".str_replace( "/", "\/", $pageSuffixRegexp )."/";
			$requestUri = preg_replace( $pageSuffixRegexp, "", $requestUri );
			//print($pageSuffixRegexp." - ".$requestUri);
			
			return( $requestUri );		
		}
		
		function process( $requestUri )
		{
			// decode the string, since it seems that php will not do it for us in this case...
			$requestUri = urldecode( $requestUri);
	        // we should remove anything that comes after a '?' parameter, since we don't want to take
	        // HTTP GET parameters into account                        
            if(( $pos = strpos( $requestUri, '?' ))) {
	            // if so, remove everything including the question mark
	            $requestUri = substr( $requestUri, 0, $pos );
            }
			
			// once we're done with everything else, let's get the value page parameter and save it			
			$page = $this->getPageParameterValue( $requestUri );
			
			// and now we can remove remove the page suffix so that it doesn't interfere with the 
			// url parser
			$requestUri = $this->removePageSuffix( $requestUri );                  						
			
			// guess which format we're using...
			$m = new LinkFormatMatcher( $requestUri, $this->_formats );
			$this->_format = $m->identify();
			$this->_params = $m->getParameters();
			
			// if it didn't work out the first time, let's try with an additional url format
			if( !$this->_fillRequestParameters()) {
				$m = new LinkFormatMatcher( $requestUri, $this->_fallback );
				$this->_format = $m->identify();
				$this->_params = $m->getParameters();
				if(!$this->_fillRequestParameters())
                    return false;
			}
			
			// put the parameter back as a parameter
			$this->_params["page"] = $page;
			return( true );
		}
		
		/**
		 * @private
		 */
		function _fillRequestParameters()
		{
			// ...and then based on this, fill in the parameters in the request
			$matched = true;
			if( $this->_format == "permalink_format" ) {
				$this->_includeFile = $this->_indexName;
				if ( array_key_exists( "year", $this->_params ) )
				{
					$this->_params["date"] = $this->_params["year"];
					if( array_key_exists( "month", $this->_params ) )
					{
						$this->_params["date"] .= $this->_params["month"];
						if( array_key_exists( "day", $this->_params ) )
						{
							$this->_params["date"] .= $this->_params["day"];
							if ( array_key_exists( "hours", $this->_params ) )
							{
								$this->_params["date"] .= $this->_params["hours"];
								if( array_key_exists( "minutes", $this->_params ) )
									$this->_params["date"] .= $this->_params["minutes"];
							}
						}
					}
				}
				$this->_params["op"] = "ViewArticle";
				$this->_vars = Array( "postid" => "articleId",
							   "postname" => "articleName",
							   "blogid" => "blogId",
							   "blogname" => "blogName",
							   "userid" => "userId",
							   "username" => "userName",
							   "catid" => "postCategoryId",
							   "catname" => "postCategoryName",
							   "date" => "Date",
							   "blogowner" => "blogUserName" );
			}
			elseif( $this->_format == "blog_link_format" ) {
				$this->_includeFile = $this->_indexName;	
				$this->_params["op"] = "Default";
				$this->_vars = Array( "blogid" => "blogId",
							          "blogname" => "blogName",
									  "blogowner" => "blogUserName" );
			}
			elseif( $this->_format == "category_link_format" ) {
				$this->_includeFile = $this->_indexName;	
				$this->_params["op"] = "Default";
				$this->_vars = Array( "blogid" => "blogId",
							   "blogname" => "blogName",
							   "blogowner" => "blogUserName",							   
							   "catid" => "postCategoryId",
							   "catname" => "postCategoryName" );
			}
			elseif( $this->_format == "archive_link_format" ) {
				$this->_includeFile = $this->_indexName;	
				if ( array_key_exists( "year", $this->_params ) )
				{
					$this->_params["date"] = $this->_params["year"];
					if( array_key_exists( "month", $this->_params ) )
					{
						$this->_params["date"] .= $this->_params["month"];
						if( array_key_exists( "day", $this->_params ) )
						{
							$this->_params["date"] .= $this->_params["day"];
							if ( array_key_exists( "hours", $this->_params ) )
							{
								$this->_params["date"] .= $this->_params["hours"];
								if( array_key_exists( "minutes", $this->_params ) )
									$this->_params["date"] .= $this->_params["minutes"];
							}
						}
					}
				}
				$this->_params["op"] = "Default";
				$this->_vars = Array( "blogid" => "blogId",
							   "blogname" => "blogName",
							   "blogowner" => "blogUserName",							   
							   "date" => "Date" );
			}
			elseif( $this->_format == "user_posts_link_format" ) {
				$this->_includeFile = $this->_indexName;	
				if ( array_key_exists( "year", $this->_params ) )
				{
					$this->_params["date"] = $this->_params["year"];
					if( array_key_exists( "month", $this->_params ) )
					{
						$this->_params["date"] .= $this->_params["month"];
						if( array_key_exists( "day", $this->_params ) )
						{
							$this->_params["date"] .= $this->_params["day"];
							if ( array_key_exists( "hours", $this->_params ) )
							{
								$this->_params["date"] .= $this->_params["hours"];
								if( array_key_exists( "minutes", $this->_params ) )
									$this->_params["date"] .= $this->_params["minutes"];
							}
						}
					}
				}
				$this->_params["op"] = "Default";
				$this->_vars = Array( "blogid" => "blogId",
							   "blogname" => "blogName",
							   "blogowner" => "blogUserName",							   
							   "date" => "Date",
							   "userid" => "userId",
							   "catid" => "postCategoryId",
							   "catname" => "postCategoryName",
							   "username" => "userName" );
			}
			elseif( $this->_format == "post_trackbacks_link_format" ) {
				$this->_includeFile = $this->_indexName;	
				$this->_params["op"] = "Trackbacks";
				if ( array_key_exists( "year", $this->_params ) )
				{
					$this->_params["date"] = $this->_params["year"];
					if( array_key_exists( "month", $this->_params ) )
					{
						$this->_params["date"] .= $this->_params["month"];
						if( array_key_exists( "day", $this->_params ) )
						{
							$this->_params["date"] .= $this->_params["day"];
							if ( array_key_exists( "hours", $this->_params ) )
							{
								$this->_params["date"] .= $this->_params["hours"];
								if( array_key_exists( "minutes", $this->_params ) )
									$this->_params["date"] .= $this->_params["minutes"];
							}
						}
					}
				}
				$this->_vars = Array( "blogid" => "blogId",
							   "blogname" => "blogName",
							   "blogowner" => "blogUserName",							   
							   "postid" => "articleId",
							   "postname" => "articleName",
							   "userid" => "userId",
							   "username" => "userName",
							   "catid" => "postCategoryId",
							   "catname" => "postCategoryName",
							   "date" => "Date" );					   
			}
			elseif( $this->_format == "template_link_format" ) {
				$this->_includeFile = $this->_indexName;	
				$this->_params["op"] = "Template";
				$this->_vars = Array( "templatename" => "show",
							   "blogid" => "blogId",
							   "blogowner" => "blogUserName",							   
							   "blogname" => "blogName" );
			}
			elseif( $this->_format == "album_link_format" ) {
				$this->_includeFile = $this->_indexName;	
				$this->_params["op"] = "ViewAlbum";
				$this->_vars = Array( "blogid" => "blogId",
							   "blogname" => "blogName",
							   "albumid" => "albumId",
							   "blogowner" => "blogUserName",							   
							   "albumname" => "albumName" );
			}
			elseif( $this->_format == "resource_link_format" ) {
				$this->_includeFile = $this->_indexName;
				$this->_params["op"] = "ViewResource";
				$this->_vars = Array( "blogid" => "blogId",
							   "blogname" => "blogName",
							   "blogowner" => "blogUserName",							   
							   "albumid" => "albumId",
							   "albumname" => "albumName",
							   "resourceid" => "resId",
							   "resourcename" => "resource" );
			}
			elseif( $this->_format == "resource_download_link_format" ) {
				$this->_includeFile = "resserver.php";
				$this->_vars = Array( "blogid" => "blogId",
							   "blogname" => "blogName",
							   "blogowner" => "blogUserName",							   
							   "albumid" => "albumId",
							   "albumname" => "albumName",
							   "resourceid" => "resId",
							   "resourcename" => "resource" );
			}
			elseif( $this->_format == "resource_preview_link_format" ) {
				$this->_includeFile = "resserver.php";
				$this->_params["mode"] = "preview";
				$this->_vars = Array( "blogid" => "blogId",
							   "blogname" => "blogName",
							   "blogowner" => "blogUserName",							   
							   "albumid" => "albumId",
							   "albumname" => "albumName",
							   "resourceid" => "resId",
							   "resourcename" => "resource",
							   "mode" => "mode");
			}
			elseif( $this->_format == "resource_medium_size_preview_link_format" ) {
				$this->_includeFile = "resserver.php";
				$this->_params["mode"] = "medium";
				$this->_vars = Array( "blogid" => "blogId",
							   "blogname" => "blogName",
							   "blogowner" => "blogUserName",							   
							   "albumid" => "albumId",
							   "albumname" => "albumName",
							   "resourceid" => "resId",
							   "resourcename" => "resource",
							   "mode" => "mode");			
			}
			else {
				$this->_includeFile = $this->_indexName;
				$matched = false;
			}
			
			// this must be put in the _vars array so that client classes, when checking
			// for the parameters that were identified in the request, can also include
			// the page parameter
			$this->_vars["page"] = "page";
			
			return( $matched );
		}
		
		function getVars()
		{
			return $this->_vars;
		}
		
		function getParams()
		{
			return $this->_params;
		}
		
		function getIncludeFile()
		{
			return $this->_includeFile;
		}
	}
?>