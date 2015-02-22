<?php

	// the three different modes that a resource can be requested
	define( 'RESOURCE_VIEW_MODE_DEFAULT', '' );
	define( 'RESOURCE_VIEW_MODE_PREVIEW', 'preview' );
	define( 'RESOURCE_VIEW_MODE_MEDIUM', 'medium' );

    lt_include( PLOG_CLASS_PATH."class/action/action.class.php" );
    lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/redirectview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/net/http/subdomains.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresources.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/usernamevalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/domainvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/blognamevalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );	

    /**
     * \ingroup Action
     * @private
     */	
	class ResourceServerAction extends Action
	{
		var $_mode;
		var $_resource;
		var $_resId;
		var $_album;
		var $_config;
		var $_blogInfo;
	
		function ResourceServerAction( $actionInfo, $request )
		{
			$this->Action( $actionInfo, $request );

			// keep the session for later use
            $session = HttpVars::getSession();
        	$this->_session = $session['SessionInfo'];			
			
			$this->_config =& Config::getConfig();
			
			$this->registerFieldValidator( "resource", new StringValidator(), true );
			$this->registerFieldValidator( "resId", new IntegerValidator(), true );
			$this->registerFieldValidator( "albumId", new IntegerValidator(), true );
			$this->registerFieldValidator( "albumName", new StringValidator(), true );
			$this->registerFieldValidator( "blogId", new IntegerValidator(), true );
			$this->registerFieldValidator( "blogDomain", new DomainValidator(), true );
			$this->registerFieldValidator( "blogName", new BlogNameValidator(), true );
			$this->registerFieldValidator( "userId", new IntegerValidator(), true );
			$this->registerFieldValidator( "blogUserName", new UsernameValidator(), true );
			
			// since this class does not return HTML code but files, we cannot
			// return HTML so let's return 404 status code with a custom error message
			$view = new View();
			$view->addHeaderResponse( "HTTP/1.1 404 Not Found" );
			$view->addHeaderResponse( "Status: 404 Not Found" );
			$view->addHeaderResponse( "X-LifeType-Error: Invalid parameters" );
			$this->setValidationErrorView( $view );
		}
		
        /**
         * Fetches the information for this blog from the database since we are going to need it
         * almost everywhere.
         */
        function _getBlogInfo()
        {			
			// see if we're using subdomains
			$config =& Config::getConfig();
			if( $config->getValue( "subdomains_enabled" )) {
				$subdomainInfo = Subdomains::getSubdomainInfoFromRequest();

                if( !empty($subdomainInfo["blogdomain"]) && $this->_request->getValue( 'blogDomain' ) == "" ) {
                    $this->_request->setValue( 'blogDomain', $subdomainInfo["blogdomain"] );
                }
                if( !empty($subdomainInfo["username"]) && $this->_request->getValue( 'blogUserName' ) == "" ) {
                    $this->_request->setValue( 'blogUserName', $subdomainInfo["username"] );
                }
                if( !empty($subdomainInfo["blogname"]) && $this->_request->getValue( 'blogName' ) == "" ) {
                    $this->_request->setValue( 'blogName', $subdomainInfo["blogname"] );
                }
			}

    		$blogId = $this->_request->getValue( 'blogId' );
    		$blogName = $this->_request->getValue( 'blogName' );
    		$userId = $this->_request->getValue( 'userId' );
    		$userName = $this->_request->getValue( 'blogUserName' );
            $blogDomain = $this->_request->getValue( 'blogDomain' );
			
            // if there is a "blogId" parameter, it takes precedence over the
            // "user" parameter.
            if( !$blogId && !$blogName && !$blogDomain) {
            	// check if there was a user parameter
                if( !empty($userName) ) {
                	// if so, check to which blogs the user belongs
					lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
                	$users = new Users();
                 	$userInfo = $users->getUserInfoFromUsername( $userName );
                    // if the user exists and is valid...
                	if( $userInfo ) {
                    	$userBlogs = $users->getUsersBlogs( $userInfo->getId(), BLOG_STATUS_ACTIVE );
                        // check if he or she belogs to any blog. If he or she does, simply
                        // get the first one (any better rule for this?)
                    	if( !empty($userBlogs)) {						
	                		$blogId = $userBlogs[0]->getId();
                        } else{
                        	$blogId = $this->_config->getValue('default_blog_id');
                        }
                    } else{
                    	$blogId = $this->_config->getValue('default_blog_id');
                    }
                }
                else {
                    // if there is no user parameter, we take the blogId from the session
                    if( $this->_session->getValue('blogId') != '' ) {
                    	$blogId = $this->_session->getValue('blogId');
                    }
                    else {
                        // get the default blog id from the database
                        $blogId = $this->_config->getValue('default_blog_id');                        
                    }
                }
            }
			
            // fetch the BlogInfo object
            lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );			
            $blogs = new Blogs();
            if( $blogId ) {
                $this->_blogInfo = $blogs->getBlogInfo( $blogId );
            }
            else if($blogName){
                $this->_blogInfo = $blogs->getBlogInfoByName( $blogName );
            }
            else if($blogDomain){
                $this->_blogInfo = $blogs->getBlogInfoByDomain( $blogDomain );
            }
            else{
                $this->_blogInfo = false;
            }
        }
		
		function validate()
		{
			if( !parent::validate())
				return false;
			
			// before we do anything, let's find out the blogId and if there isn't any, quit
			$this->_getBlogInfo();
			if( $this->_blogInfo == false ) {
				// return 404 not found because the blog id is not correct!
				$this->_view = new View();
				$this->_view->addHeaderResponse( "HTTP/1.1 404 Not Found" );
				$this->_view->addHeaderResponse( "Status: 404 Not Found" );
				$this->_view->addHeaderResponse( "X-LifeType-Error: Blog $resId is not correct" );
				
				return false;			
			}

			// now if the blog id was correct, then we can proceed to get the rest of the parameters
			$this->_resName = $this->_request->getValue( "resource" );
			$this->_resId = $this->_request->getValue( "resId" );
			$this->_albumId = $this->_request->getValue( "albumId" );
			$this->_albumName = $this->_request->getValue( "albumName" );
			$this->_mode = $this->_request->getValue( "mode" );
			
			// check if we need to load the album to figure out the correct album id
			// because we got an album name instead of an album id
			if( !empty($this->_albumId) || !empty($this->_albumName)) {
				if( $this->_albumName ) {
					lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );
					$albums = new GalleryAlbums();
					$album = $albums->getAlbumByName( $this->_albumName );
					if( !$album ) {
						$this->_view = new View();
						$this->_view->addHeaderResponse( "HTTP/1.1 404 Not Found" );
						$this->_view->addHeaderResponse( "Status: 404 Not Found" );
						$this->_view->addHeaderResponse( "X-LifeType-Error: Album $albumId not found" );
						return false;
					}
					$this->_albumId = $album->getId();
				}
			}
			
			return true;
		}
		
		function perform()
		{
			// and fetch the resource
			$resources = new GalleryResources();
			if( $this->_resName ) {
				$resource = $resources->getResourceFile( $this->_blogInfo->getId(), $this->_resName );
			}
			else {
				$resource = $resources->getResource( $this->_resId, $this->_blogInfo->getId());
			}

			if( !$resource ) {
				// return 404 not found because the resource wasn't found
				$this->_view = new View();
				$this->_view->addHeaderResponse( "HTTP/1.1 404 Not Found" );
				$this->_view->addHeaderResponse( "Status: 404 Not Found" );
				$this->_view->addHeaderResponse( "X-LifeType-Error: Resource $this->_resId not found" );
				
				return false;
			}
			
			$url = $this->_blogInfo->getBlogRequestGenerator();
			switch( $this->_mode ) {
				case RESOURCE_VIEW_MODE_PREVIEW:
					$redirectUrl = $url->resourcePreviewLink( $resource );
					break;
				case RESOURCE_VIEW_MODE_MEDIUM:
					$redirectUrl = $url->resourceMediumSizePreviewLink( $resource );
					break;
				default:
					$redirectUrl = $url->resourceDownloadLink( $resource );
					break;
			}
			
			// generate the correct view with the resource data...			
			$this->_view = new RedirectView( $redirectUrl );
			
			return true;
		}
	}
?>