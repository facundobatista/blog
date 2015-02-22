<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresources.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresourcequotas.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/pager/pager.class.php" );
	
	define( "ROOT_ALBUM_ID", 0 );
	
    /**
     * \ingroup View
     * @private
     *	
	 * lists the resources
	 */
	class AdminResourcesListView extends AdminTemplatedView
	{
		var $_albumId;
		var $_page;
		var $_resourceType;
		var $_viewParams;
	
		function AdminResourcesListView( $blogInfo, $params = Array())
		{
			$this->AdminTemplatedView( $blogInfo, "resources" );
			
			$this->_viewParams = $params;
		}
		
		function render()
		{
			
			// fetch and save the albumId parameter in the request, if not available as a
			// constructor parameter
			isset( $this->_viewParams["albumId"] ) ? $this->_albumId = $this->_viewParams["albumId"] : $this->_albumId = null;
				
			if( $this->_albumId == null )
				$this->_albumId = $this->getSessionValue( "albumId", ROOT_ALBUM_ID );

			// in case we didn't get any album id at all!
			if( $this->_albumId == "" )
				$this->_albumId = ROOT_ALBUM_ID;
				
			// search terms
			$this->_searchTerms = "";
			if( isset( $this->_viewParams['searchTerms'] ))
				$this->_searchTerms = $this->_viewParams['searchTerms'];
				
			$this->setSessionValue( "albumId", $this->_albumId );

			$this->_resourceType = GALLERY_RESOURCE_ANY;

			// base url for the pager so that it can be changed by parent classes
			// such as AdminSimpleResourcesListView
			if ( !isset( $this->_pagerUrl ) )
				$this->_pagerUrl = "?op=resources&amp;albumId=".$this->_albumId."&amp;page=";
		
		
			// get the page from the request
			$this->_page = $this->getCurrentPageFromRequest();
			
			$albums = Array();

	        // and the current album
        	$galleryAlbums = new GalleryAlbums();
			$galleryResources = new GalleryResources();

			$numResources = 0;
			if( $this->_albumId > ROOT_ALBUM_ID && $this->_page > 0 ) {			
        	    $album = $galleryAlbums->getAlbum( $this->_albumId, $this->_blogInfo->getId());
				if( !$album || empty($album) ) {
					$this->_albumId = ROOT_ALBUM_ID;
				}
				else {				
					// total number of resources in this album, used by the pager
					$numResources = $galleryResources->getNumUserResources( $this->_blogInfo->getId(),
					                                                        $this->_albumId,
					                                                        $this->_resourceType,
																			$this->_searchTerms );					
					
					// create and export the pager
					$pager = new Pager( $this->_pagerUrl, $this->_page, $numResources, DEFAULT_ITEMS_PER_PAGE );
					if( isset( $this->_viewParams["gotoLastPage"] )) {
						$pager->setCurPage( $pager->getEndPage());
						$this->_page = $pager->getEndPage();
					}
					
					// resources for this page
					$resources = $galleryResources->getUserResources( $this->_blogInfo->getId(),
					                                                  $this->_albumId,
					                                                  $this->_resourceType,
																	  $this->_searchTerms,
					                                                  $this->_page,
					                                                  DEFAULT_ITEMS_PER_PAGE );
																	  

				}
			}
			else {
				$album = null;
				// if we're at the root album but search terms, still call GalleryResources::getUserResources
				if( $this->_searchTerms ) {
					// total number of resources for the pager
					$numResources = $galleryResources->getNumUserResources( $this->_blogInfo->getId(),
					                                                        GALLERY_NO_ALBUM,
					                                                        $this->_resourceType,
																			$this->_searchTerms );																			
					
					// load the resources matching the given string from *all* albums
					$resources = $galleryResources->getUserResources( $this->_blogInfo->getId(),
					                                                  GALLERY_NO_ALBUM,
					                                                  $this->_resourceType,
																	  $this->_searchTerms,
					                                                  $this->_page,
					                                                  DEFAULT_ITEMS_PER_PAGE );																			
				}
				else {
					$albums = $galleryAlbums->getChildAlbums( $this->_albumId, $this->_blogInfo->getId(), $this->_searchTerms );
					$resources = Array();
				}
				// create and export the pager
				$pager = new Pager( $this->_pagerUrl, $this->_page, $numResources, DEFAULT_ITEMS_PER_PAGE );				
			}
			
			// get a list with the nested albums
			$userAlbums = $galleryAlbums->getNestedAlbumList( $this->_blogInfo->getId());
			
			// event about the albums we just loaded
			$this->notifyEvent( EVENT_ALBUMS_LOADED, Array( "albums" => &$userAlbums ));
			
			$this->setValue( "albumsList", $userAlbums );

			// fetch some statistics and continue
			$quotaUsage = GalleryResourceQuotas::getBlogResourceQuotaUsage( $this->_blogInfo->getId());
			$totalResources = $galleryResources->getNumUserResources( $this->_blogInfo->getId());
			$this->setValue( "quotausage", $quotaUsage );
			$this->setValue( "totalresources", $totalResources );

			// and now export info about the albums and so on but only 
			// if we're browsing the first page only (albums do not appear anymore after the first page)
            $this->setValue( "album", $album );
			if( $this->_albumId > ROOT_ALBUM_ID && $this->_page < 2 ) {
				$this->setValue( "albums", $album->getChildren());
			}
			else {
				$this->setValue( "albums", $albums );
			}
	        
			// event about the resources
			$this->notifyEvent( EVENT_RESOURCES_LOADED, Array ( "resources" => &$resources ));
			$this->setValue( "resources", $resources );

			$this->setValue( "pager", $pager );	
			$this->setValue( "searchTerms", $this->_searchTerms );
			
			parent::render();
		}
	}
?>