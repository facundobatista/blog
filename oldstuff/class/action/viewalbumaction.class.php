<?php


	lt_include( PLOG_CLASS_PATH."class/action/blogaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/blogview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/errorview.class.php" );

	define( "VIEW_ALBUMS_TEMPLATE", "albums" );
	define( "VIEW_ALBUM_TEMPLATE", "album" );

    /**
     * \ingroup Action
     * @private
     *
     * This class shows an album with resources
     */
	class ViewAlbumAction extends BlogAction
    {

        var $_albumId;

		function ViewAlbumAction( $actionInfo, $request )
        {
			$this->BlogAction( $actionInfo, $request );
			
			$this->registerFieldValidator( "albumId", new IntegerValidator(), true );
			$this->registerFieldValidator( "albumName", new StringValidator(), true );
			
			$this->setValidationErrorView( new ErrorView( $this->_blogInfo, "error_fetching_album" ));
        }

        function validate()
        {
			if( !parent::validate())
				return false;
	
        	$this->_albumId = $this->_request->getValue( "albumId", 0 );
			$this->_albumName = $this->_request->getValue( "albumName" );
			// get the page from the request
			$this->_page = View::getCurrentPageFromRequest();			
			
            return true;
        }

        function perform()
        {
			$browseRootAlbum = ( $this->_albumId == 0 && $this->_albumName == "" ); 
			
			// check which template we should use
            if( $browseRootAlbum )
				$template = VIEW_ALBUMS_TEMPLATE;
            else
				$template = VIEW_ALBUM_TEMPLATE;
            
			// initialize the view and check if it was cached
			$this->_view = new BlogView( $this->_blogInfo, 
			                             $template, 
										 SMARTY_VIEW_CACHE_CHECK,
										 Array( "albumId" => $this->_albumId, 
												"albumName" => $this->_albumName,
												"page" => $this->_page ));
			if( $this->_view->isCached()) {
				// nothing to do if it is cached!
				$this->setCommonData();
                return true;
			}
			
			lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresources.class.php" );
			lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );
			lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
			lt_include( PLOG_CLASS_PATH."class/data/pager/pager.class.php" );			
			
        	$galleryResources = new GalleryResources();
            $galleryAlbums = new GalleryAlbums();			

            // fetch the album we're trying to browse
            if( $browseRootAlbum ) {
            	// fetch only the first level albums
                $blogAlbums = $galleryAlbums->getChildAlbums( 0, $this->_blogInfo->getId(), true );
				if( count($blogAlbums) == 0 ) {
					$this->_view = new ErrorView( $this->_blogInfo );
					$this->_view->setValue( "message", "error_no_albums_defined" );
				}
				else {
					$this->notifyEvent( EVENT_ALBUMS_LOADED, Array( "albums" => &$blogAlbums ));
					$this->_view->setValue( "albums", $blogAlbums );
					$this->_view->setPageTitle( $this->_blogInfo->getBlog()." | ".$this->_locale->tr("albums"));
				}
            }
            else {
            	// the third parameter is telling _not_ to fetch all those albums that have
                // been disabled and are not to be shown in the page when browsing the album
				if( $this->_albumName ) {
					$album = $galleryAlbums->getAlbumByName( $this->_albumName, $this->_blogInfo->getId(), true, true );
				}
				else {
					$album = $galleryAlbums->getAlbum( $this->_albumId, $this->_blogInfo->getId(), true, true );
				}				
                // check if the album was correctly fetched
                if( !$album ) {
                	$this->_view = new ErrorView( $this->_blogInfo );
                    $this->_view->setValue( "message", "error_fetching_album" );
                    $this->setCommonData();

                    return false;
                }
				$this->notifyEvent( EVENT_ALBUM_LOADED, Array( "album" => &$blogAlbum ));				
                // put the album to the template
                $this->_view->setValue( "album", $album );
				// and set a page title
				$this->_view->setPageTitle( $this->_blogInfo->getBlog()." | ".$album->getName());
				
				// load the resources and build the pager
				$resources = $galleryResources->getUserResources( $this->_blogInfo->getId(),
																  $album->getId(),
																  GALLERY_RESOURCE_ANY,
																  "",
																  $this->_page,
																  DEFAULT_ITEMS_PER_PAGE );															
																  
				// total number of resources, used by the pager
				$numResources = $galleryResources->getNumUserResources( $this->_blogInfo->getId(),
																		$album->getId(),
																		GALLERY_RESOURCE_ANY );
				// build the pager
				$url = $this->_blogInfo->getBlogRequestGenerator();
				$pager = new Pager( $url->albumLink( $album ).$url->getPageSuffix(),
				                    $this->_page,
									$numResources,
									DEFAULT_ITEMS_PER_PAGE );
				// and pass everything back to the templates
				$this->_view->setValue( "pager", $pager );
				$this->_view->setValue( "resources", $resources );
            }

            // if all went fine, continue
            $this->setCommonData();
			
            // and return everything normal
            return true;
        }
    }
?>