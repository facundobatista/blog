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
     * Deletes resources and albums from the blog
     */
    class AdminDeleteGalleryItemsAction extends AdminAction
    {

    	var $_resourceIds;
		var $_albumIds;
		
		var $_successMessage;
		var $_errorMessage;
		var $_totalOk;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminDeleteGalleryItemsAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->_totalOk = 0;
			$this->_successMessage = "";
			$this->_errorMessage = "";
			
			// data validation
			$this->registerFieldValidator( "resourceIds", new ArrayValidator( new IntegerValidator()), true );
			$this->registerFieldValidator( "albumIds", new ArrayValidator( new IntegerValidator()), true );
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

			// make sure that we're dealing with arrays!
			if( !is_array( $this->_resourceIds)) $this->_resourceIds = Array();
			if( !is_array( $this->_albumIds)) $this->_albumIds = Array();

			// remove the items, if any
			$this->_deleteAlbums();	
			$this->_deleteResources();

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
		 * deletes resources from the list
         */
        function _deleteResources()
        {
        	// load the resource
        	$resources = new GalleryResources();

	        // loop through the array of resource ids and
        	// remove them all
           	 foreach( $this->_resourceIds as $resourceId => $value ) {
            		// fetch the resource first, to get some info about it
                	$resource = $resources->getResource( $resourceId, $this->_blogInfo->getId());
					
					if( !$resource ) {
						$this->_errorMessage .= $this->_locale->pr("error_deleting_resource2", $resourceId )."<br/>";
					}
					else {
						$this->notifyEvent( EVENT_PRE_RESOURCE_DELETE, Array( "resource" => &$resource ));
						
						// and now remove it
						$res = $resources->deleteResource( $resourceId, $this->_blogInfo->getId());
						if( $res ) {
							$this->_totalOk++;
							if( $this->_totalOk > 1 )
								$this->_successMessage = $this->_locale->pr("items_deleted_ok", $this->_totalOk );
							else
								$this->_successMessage .= $this->_locale->pr("item_deleted_ok", $resource->getFileName());
							$this->notifyEvent( EVENT_POST_RESOURCE_DELETE, Array( "resource" => &$resource ));
						}
						else 
							$this->_errorMessage .= $this->_locale->pr("error_deleting_resource", $resource->getFileName())."<br/>";
					}
				}

		return true;
         }

        /**
		 * deletes resources from the list
         */
        function _deleteAlbums()
        {
        	$albums = new GalleryAlbums();

	        // loop through the array of resource ids and
        	// remove them all
           	 foreach( $this->_albumIds as $albumId => $value ) {
            		// fetch the resource first, to get some info about it
                	$album = $albums->getAlbum( $albumId, $this->_blogInfo->getId());
					
					if( !$album ) {
						$this->_errorMessage .= $this->_locale->pr( "error_deleting_album2", $albumId )."<br/>";
					}
					else {
						if( $album->getNumChildren() > 0 || $album->getNumResources() > 0 ) {
							$this->_errorMessage .= $this->_locale->pr("error_album_has_children", $album->getName()."<br/>");
						}
						else {
							$this->notifyEvent( EVENT_PRE_ALBUM_DELETE, Array( "album" => &$album ));
						
							// and now remove it
							$res = $albums->deleteAlbum( $albumId, $this->_blogInfo->getId());
							if( $res ) {
								$this->_totalOk++;
								if( $this->_totalOk > 1 )
									$this->_successMessage = $this->_locale->pr("items_deleted_ok", $this->_totalOk );
								else
									$this->_successMessage = $this->_locale->pr("item_deleted_ok", $album->getName());
							
								$this->notifyEvent( EVENT_POST_ALBUM_DELETE, Array( "album" => &$album ));
							}
							else 
								$this->_errorMessage .= $this->_locale->pr("error_deleting_album", $album->getName())."<br/>";
						}
					}
            	}

		return true;
         }
    }
?>
