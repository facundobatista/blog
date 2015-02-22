<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admineditresourcealbumview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminresourceslistview.class.php" );	
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Shows information about a resource
     */
    class AdminUpdateResourceAlbumAction extends AdminAction
    {

    	var $_albumId;
        var $_albumName;
        var $_albumDescription;
        var $_parentId;
        var $_showAlbum;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminUpdateResourceAlbumAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// data validation
			$this->registerFieldValidator( "albumId", new IntegerValidator());
			$this->registerFieldValidator( "albumName", new StringValidator());
			$this->registerFieldValidator( "parentId", new IntegerValidator());
			$this->registerFieldValidator( "albumDescription", new StringValidator(), true );
			$this->registerFieldValidator( "showAlbum", new IntegerValidator(), true );
			$view = new AdminEditResourceAlbumView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_updating_album" ));
			$this->setValidationErrorView( $view );
			
			$this->requirePermission( "update_album" );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
        	$this->_albumId = $this->_request->getValue( "albumId" );
        	$this->_parentId = $this->_request->getValue( "parentId" );
            $this->_albumName = Textfilter::filterAllHTML($this->_request->getValue( "albumName" ));
            $this->_albumDescription = Textfilter::filterAllHTML($this->_request->getValue( "albumDescription" ));
            $this->_showAlbum = ( $this->_request->getValue( "showAlbum" ) != "" );
		
            // fetch the albums for this blog
            $albums = new GalleryAlbums();
            $album = $albums->getAlbum( $this->_albumId, $this->_blogInfo->getId());
            
            if( $this->userHasPermission( "view_resources" ))
            	$this->_view = new AdminResourcesListView( $this->_blogInfo );
            else {
            	$this->_view = new AdminEditResourceAlbumView( $this->_blogInfo );
				$this->_view->setValue( "albumName", $this->_albumName );
				$this->_view->setValue( "albumDescription", $this->_albumDescription );
				$this->_view->setValue( "showAlbum", $this->_showAlbum );
				$this->_view->setValue( "parentId", $this->_parentId );
				$this->_view->setValue( "albumId", $this->_albumId );            	
        	}
            	
            if( !$album ) {
                $this->_view->setErrorMessage( $this->_locale->tr("error_updating_album" ) );
                $this->setCommonData();
                return false;
            }
            
            // update the fields in the object
            $album->setName( $this->_albumName );
            $album->setDescription( $this->_albumDescription );
            $album->setParentId( $this->_parentId );
            $album->setShowAlbum( $this->_showAlbum );            
			
			$this->notifyEvent( EVENT_PRE_ALBUM_UPDATE, Array( "album" => &$album ));
            // and update the data in the database
            if( !$albums->updateAlbum( $album )) {
                $this->_view->setErrorMessage( $this->_locale->tr("error_updating_album" ) );
            	$this->setCommonData();
                return false;
            }

            $this->_view->setSuccessMessage( $this->_locale->pr("album_updated_ok", $album->getName()));
			$this->notifyEvent( EVENT_POST_ALBUM_UPDATE, Array( "album" => &$album ));			
            $this->setCommonData();
			
			// clear the cache
			CacheControl::resetBlogCache( $this->_blogInfo->getId(), false );

            // better to return true if everything fine
            return true;
        }
    }
?>
