<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminresourceslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresource.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Deletes a resource from the blog
     */
    class AdminDeleteResourceAlbumAction extends AdminAction
    {

    	var $_albumIds;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminDeleteResourceAlbumAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			$this->registerFieldValidator( "albumId", new IntegerValidator());
			$view = new AdminResourcesListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_no_resources_selected"));
			$this->setValidationErrorView( $view );
        }
		
		/**
		 * carries out the specified album
		 */
		function perform()
		{
			// get the album id
			$albumId = $this->_request->getValue( "albumId" );
			
        	// load the resource
        	$albums = new GalleryAlbums();
			
			// create the view
			$this->_view = new AdminResourcesListView( $this->_blogInfo );			

           	// fetch the resource albumm first, to get some info about it
            $album = $albums->getAlbum( $albumId, $this->_blogInfo->getId(), false );
			if( !$album ) {
				$this->_view->setErrorMessage( $this->_locale->tr("error_fetching_album" ));
				$this->setCommonData();
				return false;
			}
			
			// notify of the "pre" delete event
			$this->notifyEvent( EVENT_PRE_ALBUM_DELETE, Array( "album" => &$album ));

            //
            // this album cannot be deleted if either:
            //   1) we have resources under it
            //   2) we have child albums under it
            //
            if( $album->getNumChildren() > 0 || $album->getNumResources() > 0 ) {
              	$this->_view->setErrorMessage( $this->_locale->pr("error_album_has_children", $album->getName()));
            }
            else {
               	// otherwise, we can go ahead and remove it
           		if( $albums->deleteAlbum( $albumId, $this->_blogInfo->getId())) {
					$this->_view->setSuccessMessage( $this->_locale->pr("album_deleted_ok", $album->getName()));
					$this->notifyEvent( EVENT_POST_ALBUM_DELETE, Array( "album" => &$album ));						
					// clear the cache
					CacheControl::resetBlogCache( $this->_blogInfo->getId(), false );
				}
               	else {
               		$this->_view->setErrorMessage( $this->_locale->pr("error_deleting_album", $album->getName()));
				}
            }

            // return the view
            $this->setCommonData();

            // better to return true if everything fine
            return true;			
		}
    }
?>
