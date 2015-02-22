<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresource.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/cachecontrol.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminresourceslistview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Massive changes resources and albums to another Album
     */
    class AdminChangeGalleryItemsAlbumAction extends AdminAction
    {

    	var $_resourceIds;
		var $_albumIds;
		var $_galleryAlbumId;
		
		var $_successMessage;
		var $_errorMessage;
		var $_totalOk;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminChangeGalleryItemsAlbumAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->_totalOk = 0;
			$this->_successMessage = "";
			$this->_errorMessage = "";
			
			// data validation
			$this->registerFieldValidator( "resourceIds", new ArrayValidator( new IntegerValidator()), true );
			$this->registerFieldValidator( "albumIds", new ArrayValidator( new IntegerValidator()), true );
			$this->registerFieldValidator( "galleryAlbumId", new IntegerValidator(), true );
			$view = new AdminResourcesListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_no_resources_selected"));
			$this->setValidationErrorView( $view );
        }
		
		function perform()
		{
			// create the view
			$this->_view = new AdminResourcesListView( $this->_blogInfo );

			// fetch the parameters
			$this->_resourceIds = $this->_request->getValue( "resourceIds" );
			$this->_albumIds = $this->_request->getValue( "albumIds" );
			$this->_galleryAlbumId = $this->_request->getValue( "galleryAlbumId" );

			// make sure that we're dealing with arrays!
			if( !is_array( $this->_resourceIds)) $this->_resourceIds = Array();
			if( !is_array( $this->_albumIds)) $this->_albumIds = Array();

			// update the items, if any
			$this->_updateAlbums();	
			$this->_updateResources();

			// put error and success messages (if any) into the view
			if( $this->_successMessage != "" ) $this->_view->setSuccessMessage( $this->_successMessage );
			if( $this->_errorMessage != "" ) $this->_view->setErrorMessage( $this->_errorMessage );
			$this->setCommonData();
			
			// clear the cache
			CacheControl::resetBlogCache( $this->_blogInfo->getId(), false );

            // better to return true if everything fine
            return true;		
		}

        /**
		 * updates resources from the list
         */
        function _updateResources()
        {
        	// Chanages the resource album field by selection
            $resources = new GalleryResources();

            foreach( $this->_resourceIds as $resourceId ) {
            	// get the resource
                $resource = $resources->getResource( $resourceId, $this->_blogInfo->getId());
				
				if( $resource ) {
					// fire the event
					$this->notifyEvent( EVENT_PRE_RESOURCE_UPDATE, Array( "resource" => &$resource ));
						
					// update the resource album
					$resource->setAlbumId( $this->_galleryAlbumId );
					$result = $resources->updateResource( $resource );
					
					if( !$result ) {
						$this->_errorMessage .= $this->_locale->pr("error_updating_resource", $resource->getFileName())."<br/>";
					}
					else {
						$this->_totalOk++;
						if( $this->_totalOk < 2 ) 
							$this->_successMessage .= $this->_locale->pr("resource_updated_ok", $resource->getFileName());
						else
							$this->_successMessage = $this->_locale->pr("resources_updated_ok", $this->_totalOk );
						// fire the post event
						$this->notifyEvent( EVENT_POST_RESOURCE_UPDATE, Array( "article" => &$post ));					
					}
				} 
				else {
					$this->_errorMessage .= $this->_locale->pr("error_updating_resource2", $resourceId )."<br/>";
				}
			}
        }

        /**
		 * update resources from the list
         */
        function _updateAlbums()
        {
        	// Chanages the album's parent album field by selection
        	$albums = new GalleryAlbums();

			foreach( $this->_albumIds as $albumId => $value ) {
               	// get the album
               	$album = $albums->getAlbum( $albumId, $this->_blogInfo->getId());
					
				if( $album ) 
				{
					// fire the event
					$this->notifyEvent( EVENT_PRE_ALBUM_UPDATE, Array( "album" => &$album ));

					// update the resource album
					$album->setParentId( $this->_galleryAlbumId );
					$result = $albums->updateAlbum( $album );
					
					if( !$result ) {
						$this->_errorMessage .= $this->_locale->pr("error_deleting_album", $album->getName())."<br/>";
					}
					else {
						$this->_totalOk++;
						if( $this->_totalOk < 2 ) 
							$this->_successMessage = $this->_locale->pr("album_updated_ok", $album->getName());
						else
							$this->_successMessage = $this->_locale->pr("albums_updated_ok", $this->_totalOk );
						// fire the post event
						$this->notifyEvent( EVENT_POST_ALBUM_UPDATE, Array( "album" => &$album ));	
					}
				} else {
					$this->_errorMessage .= $this->_locale->pr( "error_updating_album2", $albumId )."<br/>";
            	}
			}
        }
    }
?>
