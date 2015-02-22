<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admineditresourcealbumview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminresourceslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Shows information about a resource
     */
    class AdminEditResourceAlbumAction extends AdminAction
    {

    	var $_albumId;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminEditResourceAlbumAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			$this->registerFieldValidator( "albumId", new IntegerValidator());
			$view = new AdminResourcesListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_incorrect_album_id"));
			$this->setValidationErrorView( $view );
			
			$this->requirePermission( "update_album" );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
            // fetch the albums for this blog
			$this->_albumId = $this->_request->getValue( "albumId" );
            $albums = new GalleryAlbums();
            $album = $albums->getAlbum( $this->_albumId, $this->_blogInfo->getId());

            if( !$album ) {
            	$this->_view = new AdminResourcesListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_fetching_album"));
            }
            else {
				// and put everything into the template
            	$this->_view = new AdminEditResourceAlbumView( $this->_blogInfo );
				$this->_view->setValue( "albumName", $album->getName());
				$this->_view->setValue( "albumDescription", $album->getDescription());
				$this->_view->setValue( "showAlbum", $album->getShowAlbum());
				$this->_view->setValue( "parentId", $album->getParentId());
				$this->_view->setValue( "albumId", $album->getId());
            }

            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>
