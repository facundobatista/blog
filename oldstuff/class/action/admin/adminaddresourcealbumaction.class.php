<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminnewalbumview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminresourceslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/cachecontrol.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Adds a new album
     */
    class AdminAddResourceAlbumAction extends AdminAction
    {

    	var $_albumName;
        var $_albumDescription;
        var $_parentId;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminAddResourceAlbumAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// validation stuff
			$this->registerFieldValidator( "albumName", new StringValidator());
			$this->registerFieldValidator( "parentId", new IntegerValidator());
			$this->registerFieldValidator( "albumDescription", new StringValidator(), true);
			$this->setValidationErrorView( new AdminNewAlbumView( $this->_blogInfo ));
			
			$this->requirePermission( "add_album" );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
			// fetch our data
        	$this->_albumName = Textfilter::filterAllHTML($this->_request->getValue( "albumName" ));
            $this->_albumDescription = Textfilter::filterAllHTML($this->_request->getValue( "albumDescription" ));
            $this->_parentId = $this->_request->getValue( "parentId" );			
            $showAlbum = $this->_request->getValue("showAlbum") ? 1 : 0;
            
			// create the album
        	$albums = new GalleryAlbums();
			$t = new Timestamp();
			$album = new GalleryAlbum( $this->_blogInfo->getId(), $this->_albumName, 
			                           $this->_albumDescription, 
									   GALLERY_RESOURCE_PREVIEW_AVAILABLE,
									   $this->_parentId, 
									   $t->getTimestamp(),
									   Array(),
									   $showAlbum);
									   
			$this->notifyEvent( EVENT_PRE_ALBUM_ADD, Array( "album" => &$album ));
			// and add it to the database
            $result = $albums->addAlbum( $album );
            
            if( $this->userHasPermission( "view_resources" ))
           		$this->_view = new AdminResourcesListView( $this->_blogInfo, Array( "albumId" => $this->_parentId, "gotoLastPage" => true ));
           	else
           		$this->_view = new AdminNewAlbumView( $this->_blogInfo );
           	
            if( $result ) {
                $this->_view->setSuccessMessage( $this->_locale->pr( "album_added_ok", $album->getName()));
				$this->notifyEvent( EVENT_POST_ALBUM_ADD, Array( "album" => &$album ));								
				// clear the cache if everything went fine
				CacheControl::resetBlogCache( $this->_blogInfo->getId(), false );
            }
            else {
                $this->_view->setErrorMessage( $this->_locale->tr("error_adding_album" ) );
            }
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>
