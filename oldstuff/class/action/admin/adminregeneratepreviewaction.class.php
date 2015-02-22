<?php

lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
lt_include( PLOG_CLASS_PATH."class/view/admin/adminresourceslistview.class.php" );
lt_include( PLOG_CLASS_PATH."class/view/admin/admineditresourceview.class.php" );
lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
lt_include( PLOG_CLASS_PATH."class/gallery/resizers/galleryresizer.class.php" );
lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresources.class.php" );
lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresourcestorage.class.php" );

    /**
     * \ingroup Action
     * @private
     */ 
class AdminRegeneratePreviewAction extends AdminAction
{
        
    var $_resourceId;
        
    function AdminRegeneratePreviewAction( $actionInfo, $request ){
        $this->AdminAction( $actionInfo, $request );
            
        $this->registerFieldValidator( "resourceId", new IntegerValidator());
        $view = new AdminResourcesListView( $this->_blogInfo );
        $view->setErrorMessage( $this->_locale->tr("error_loading_resource"));
        $this->setValidationErrorView( $view );
    }

    function setCommonData($resource=null){
        if($resource){
            $this->_view->setValue( "resourceDescription", $resource->getDescription());
            $this->_view->setValue( "albumId", $resource->getAlbumId());
            $this->_view->setValue( "resource", $resource );
        }
        parent::setCommonData();
    }
    
	function perform(){
            // first of all, fetch the resource
        $this->_resourceId = $this->_request->getValue( "resourceId" );
        $resources = new GalleryResources();
        $resource = $resources->getResource( $this->_resourceId, $this->_blogInfo->getId());
            
            // check if it was loaded ok
        if( !$resource ) {
            $this->_view = new AdminResourcesListView( $this->_blogInfo );
            $this->_view->setErrorMessage( $this->_locale->tr("error_loading_resource"));
            $this->setCommonData();
            return false;
        }
            
            // if so, continue... first by checking if the resource is an image or not
            // because if not, then there is no point in generating a thumbnail of it!
        if( !$resource->isImage()) {
            $this->_view = new AdminEditResourceView( $this->_blogInfo );
            $this->_view->setErrorMessage( $this->_locale->tr("error_resource_is_not_an_image" ));
            $this->setCommonData($resource);
            return false;
        }

            // Get a helper to read the metadata
        $reader = $resource->getMetadataReader();
        if(!$reader){
            $this->_view = new AdminEditResourceView( $this->_blogInfo );
            $this->_view->setErrorMessage( $this->_locale->tr("error_updating_resource" ));
            $this->setCommonData($resource);
            return false;
        }

            // We know this is an image, and so can use the imagemetadatareader calls
        $imgHeight = $reader->getHeight();
        $imgWidth = $reader->getWidth();
        
        lt_include( PLOG_CLASS_PATH."class/gallery/resizers/gallerythumbnailgenerator.class.php" );
        lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresourcestorage.class.php" );
        lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
        $config =& Config::getConfig();

        $storage = new GalleryResourceStorage();
        
            // delete the small thumbnail
        $previewFile = GalleryResourceStorage::getPreviewsFolder($resource->getOwnerId()) .
            $resource->getPreviewFileName();
        if( File::isReadable($previewFile))
            File::delete($previewFile);

            // create the new small thumbnail
        $previewHeight = $config->getValue( "thumbnail_height",
                                            GALLERY_DEFAULT_THUMBNAIL_HEIGHT );
        $previewWidth  = $config->getValue( "thumbnail_width",
                                            GALLERY_DEFAULT_THUMBNAIL_WIDTH );
        $thumbHeight = ( $imgHeight > $previewHeight ? $previewHeight : $imgHeight );
        $thumbWidth = ( $imgWidth > $previewWidth ? $previewWidth : $imgWidth );

        $result = GalleryThumbnailGenerator::generateResourceThumbnail(
            $storage->getResourcePath($resource),
            $resource->getId(), $resource->getOwnerId(), $thumbHeight, $thumbWidth );
        if( !$result ) {
            $this->_view = new AdminEditResourceView( $this->_blogInfo );
            $this->_view->setErrorMessage( $this->_locale->tr("error_updating_resource" ));
            $this->setCommonData($resource);
            return false;
        }

            // delete the medium thumbnail
        $previewFile = GalleryResourceStorage::getMediumSizePreviewsFolder(
            $resource->getOwnerId()) . $resource->getMediumSizePreviewFileName();
        if( File::isReadable($previewFile))
            File::delete($previewFile);

            // create the new medium thumbnail
        $previewHeight = $config->getValue( "medium_size_thumbnail_height",
                                               GALLERY_DEFAULT_MEDIUM_SIZE_THUMBNAIL_HEIGHT );
        $previewWidth  = $config->getValue( "medium_size_thumbnail_width",
                                               GALLERY_DEFAULT_MEDIUM_SIZE_THUMBNAIL_WIDTH );
        $thumbHeight = ( $imgHeight > $previewHeight ? $previewHeight : $imgHeight );
        $thumbWidth = ( $imgWidth > $previewWidth ? $previewWidth : $imgWidth );
        $result = GalleryThumbnailGenerator::generateResourceMediumSizeThumbnail(
            $storage->getResourcePath($resource),
            $resource->getId(), $resource->getOwnerId(), $thumbHeight, $thumbWidth );
        
        if( !$result ) {
            $this->_view = new AdminEditResourceView( $this->_blogInfo );
            $this->_view->setErrorMessage( $this->_locale->tr("error_updating_resource" ));
            $this->setCommonData($resource);
            return false;
        }

            // set new preview format
        $resource->setThumbnailFormat(
            $config->getValue("thumbnail_format", THUMBNAIL_OUTPUT_FORMAT_SAME_AS_IMAGE));
        $resources->updateResource( $resource );
        
        $this->_view = new AdminEditResourceView( $this->_blogInfo );
        $this->_view->setSuccessMessage( $message = $this->_locale->tr("resource_preview_generated_ok" ));
        $this->setCommonData($resource);
            
        return true;
    }
}
?>