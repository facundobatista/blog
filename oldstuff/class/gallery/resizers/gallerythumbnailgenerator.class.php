<?php	
	
	class GalleryThumbnailGenerator 
	{
		
		/**
		 * generates the thumbnail of a file that we have just added.
		 *
		 * @param resFile the resource file from which we're trying to generate the
		 * thubmail.
		 * @param fileName The name and path of the new thumbnail we're going to create
		 * @param previewHeight
		 * @param previewWidth		
		 * @static
		 */
		function generateResourceThumbnail( $resFile, $resourceId, $ownerId,
			                                $previewHeight = GALLERY_DEFAULT_THUMBNAIL_HEIGHT,
                                            $previewWidth = GALLERY_DEFAULT_THUMBNAIL_WIDTH )	
		{
			// get some configuration settings regarding the size of the
			// thumbnails, and also the default format for thumbnails
			lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );			
			$config =& Config::getConfig();
			$previewKeepAspectRatio = $config->getValue( "thumbnails_keep_aspect_ratio" );
			
			// and start the resizing process
			lt_include( PLOG_CLASS_PATH."class/gallery/resizers/galleryresizer.class.php" );			
			$resizer = new GalleryResizer( $resFile );
			lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresourcestorage.class.php" );			
			GalleryResourceStorage::checkPreviewsStorageFolder( $ownerId );
			lt_include( PLOG_CLASS_PATH."class/file/file.class.php" );
			$outFile = GalleryResourceStorage::getPreviewsFolder( $ownerId ).File::basename($resFile);
			
			// and finally, we can generate the preview!
			$result = $resizer->generate( $outFile, $previewWidth, $previewHeight, $previewKeepAspectRatio );
			
			return $result;
		}
		
		/**
		 * generates the medium-sized thumbnail of a file that we have just added
		 *
		 * @param resFile the resource file from which we're trying to generate the
		 * thubmail.
		 * @param fileName The name and path of the new thumbnail we're going to create
		 * @param previewHeight
		 * @param previewWidth		
		 * @static
		 */
		function generateResourceMediumSizeThumbnail( $resFile, $resourceId, $ownerId, 
													  $previewHeight = GALLERY_DEFAULT_MEDIUM_SIZE_THUMBNAIL_HEIGHT,				
			                                          $previewWidth = GALLERY_DEFAULT_MEDIUM_SIZE_THUMBNAIL_WIDTH )
		{
			// get some configuration settings regarding the size of the
			// thumbnails, and also the default format for thumbnails
			lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );			
			$config =& Config::getConfig();
			$previewKeepAspectRatio = $config->getValue( "thumbnails_keep_aspect_ratio" );
						
			// and start the resizing process
		    lt_include( PLOG_CLASS_PATH."class/gallery/resizers/galleryresizer.class.php" );			
			$resizer = new GalleryResizer( $resFile );
		    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresourcestorage.class.php" );			
			GalleryResourceStorage::checkMediumSizePreviewsStorageFolder( $ownerId );
			lt_include( PLOG_CLASS_PATH."class/file/file.class.php" );
			$outFile = GalleryResourceStorage::getMediumSizePreviewsFolder( $ownerId ).File::basename( $resFile );
			
			// and finally, we can generate the preview!
			$result = $resizer->generate( $outFile, $previewWidth, $previewHeight, $previewKeepAspectRatio );
			
			return $result;
		}
		
		/**
		 * generates the final version of an image
		 *
		 * @param resFile the resource file from which we're trying to generate the
		 * thubmail.
		 * @param fileName The name and path of the new thumbnail we're going to create
		 * @param previewHeight
		 * @param previewWidth
		 * 
		 * @static
		 */
		function generateResourceFinalSizeThumbnail( $resFile, $resourceId, $ownerId,
                                       			     $previewHeight = 0,				
                                                     $previewWidth = 0 )
			
		{
			// get some configuration settings regarding the size of the
			// thumbnails, and also the default format for thumbnails
			lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );			
			$config =& Config::getConfig();
			$previewKeepAspectRatio = $config->getValue( "thumbnails_keep_aspect_ratio" );

			// and start the resizing process
		    lt_include( PLOG_CLASS_PATH."class/gallery/resizers/galleryresizer.class.php" );			
			$resizer = new GalleryResizer( $resFile );
		    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresourcestorage.class.php" );			
			GalleryResourceStorage::checkUserStorageFolder( $ownerId );
			lt_include( PLOG_CLASS_PATH."class/file/file.class.php" );
			$outFile = GalleryResourceStorage::getUserFolder( $ownerId ).File::basename( $resFile );
			
			// and finally, we can generate the preview!
			$result = $resizer->generate( $outFile, $previewWidth, $previewHeight, $previewKeepAspectRatio );
			
			return $result;
		}				
	}
?>