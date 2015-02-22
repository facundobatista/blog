<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminresourceslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminerrorview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresourcestorage.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * shows all the resources of a blog
     */
    class AdminResourcesAction extends AdminAction
    {

    	var $_albumId;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminResourcesAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// data validation
			$this->registerFieldValidator( "albumId", new IntegerValidator(), true );
			$view = new AdminResourcesListView( $this->_blogInfo, Array( "albumId" => 0 ));
			$view->setErrorMessage( $this->_locale->tr("error_incorrect_album_id"));
			$this->setValidationErrorView( $view );
			
			$this->requirePermission( "view_resources" );
        }

		function checkFolders()
		{
			$baseFolder = GalleryResourceStorage::getResourcesStorageFolder();
			$userFolder = GalleryResourceStorage::getUserFolder( $this->_blogInfo->getId());
			$previewsFolder = GalleryResourceStorage::getPreviewsFolder( $this->_blogInfo->getId());
			$folders = "$baseFolder<br/>$userFolder<br/>$previewsFolder";

			$message = "";

			// check if the storage folder exists and it is readable
			if( !GalleryResourceStorage::checkBaseStorageFolder() || 
						!GalleryResourceStorage::checkUserStorageFolder( $this->_blogInfo->getId()) ||
						!GalleryResourceStorage::checkPreviewsStorageFolder( $this->_blogInfo->getId()) ||
						!GalleryResourceStorage::checkMediumSizePreviewsStorageFolder( $this->_blogInfo->getId())) {
				$message = $this->_locale->pr("error_base_storage_folder_missing_or_unreadable", $folders);
			}

			return $message;
		}

        /**
         * Carries out the specified action
         */
        function perform()
        {
            $this->_albumId = $this->_request->getValue( "albumId", 0 );
			$this->_searchTerms = $this->_request->getFilteredValue( "searchTerms", new HtmlFilter());

			$errorMessage = $this->checkFolders();
			if( $errorMessage ) {
				// something happened, we leave it here...
				$this->_view = new AdminErrorView( $this->_blogInfo );
				$this->_view->setMessage( $errorMessage );
			}
			else {
				$this->_view = new AdminResourcesListView( $this->_blogInfo, Array( "albumId" => $this->_albumId,
																					"searchTerms" => $this->_searchTerms ));
			}

            $this->setCommonData();

            return true;
        }
    }
?>