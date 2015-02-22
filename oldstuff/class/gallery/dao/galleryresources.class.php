<?php

	/**
	 * \defgroup Gallery
	 *
	 * The pLog Gallery module encapsulates all the logic necessary for:
	 *
	 * - Dealing with files and their places in disk
	 * - Dealing with albums, which are virtual groups of disks
	 * - Automatic generation of thumbnails and medium-sized thumbnails, according to our configuration
	 * - Automatic extraction of metadata from a set of supported formats. This is achieved
	 * via the getID3 library.
	 */

	lt_include( PLOG_CLASS_PATH."class/dao/model.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresource.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/galleryconstants.php" );
	lt_include( PLOG_CLASS_PATH.'class/dao/daocacheconstants.properties.php' );

    /**
	 * \ingroup Gallery
	 * 
     * database access for GalleryResource objects. Provides methods for adding, retrieving, updating and removing
	 * resources from the database
	 *
	 * @see Model
	 * @see GalleryResource 
     */
    class GalleryResources extends Model
    {
    	var $albums;

    	/**
         * maps extensions to resource types
         */
       var $_extensionToType = Array(
       	"jpg" => GALLERY_RESOURCE_IMAGE,
        "jpeg" => GALLERY_RESOURCE_IMAGE,
        "png" => GALLERY_RESOURCE_IMAGE,
        "gif" => GALLERY_RESOURCE_IMAGE,
        "bmp" => GALLERY_RESOURCE_IMAGE,
        "mp3" => GALLERY_RESOURCE_SOUND,
		"mp2" => GALLERY_RESOURCE_SOUND,
        "wav" => GALLERY_RESOURCE_SOUND,
        "au" => GALLERY_RESOURCE_SOUND,
		"aac" => GALLERY_RESOURCE_SOUND,
		"mp4" => GALLERY_RESOURCE_SOUND,
		"m4a" => GALLERY_RESOURCE_SOUND,
		"aac" => GALLERY_RESOURCE_SOUND,
		"m4p" => GALLERY_RESOURCE_SOUND,
        "wma" => GALLERY_RESOURCE_SOUND,
		"ogg" => GALLERY_RESOURCE_SOUND,
		"mod" => GALLERY_RESOURCE_SOUND,
		"mid" => GALLERY_RESOURCE_SOUND,
		"midi" => GALLERY_RESOURCE_SOUND,
        "avi" => GALLERY_RESOURCE_VIDEO,
		"mpg" => GALLERY_RESOURCE_VIDEO,
		"mpeg" => GALLERY_RESOURCE_VIDEO,
        "wmv" => GALLERY_RESOURCE_VIDEO,
		//"asf" => GALLERY_RESOURCE_VIDEO,
        "mov" => GALLERY_RESOURCE_VIDEO,
        "divx" => GALLERY_RESOURCE_VIDEO,
		"rm" => GALLERY_RESOURCE_VIDEO,
		"swf" => GALLERY_RESOURCE_VIDEO,
		"flv" => GALLERY_RESOURCE_VIDEO,
		"qt" => GALLERY_RESOURCE_VIDEO,
        "pdf" => GALLERY_RESOURCE_DOCUMENT,
        "zip" => GALLERY_RESOURCE_ZIP
       );

    	function GalleryResources()
        {
        	$this->Model();            
            $this->table = $this->getPrefix()."gallery_resources";
        }

        /**
         * Fetches GalleryResource objects from the database
         *
         * @param resourceId The id of the resource we'd like to fetch
		 * @param ownerId Optional, the id of the owner
		 * @param albumId Optional, the id of the album to which this resoruce should belong
         * @return Returns a GalleryResource object representing the resource
         */
        function getResource( $resourceId, $ownerId = -1, $albumId = -1 )
        {
        	$resource = $this->get( "id", $resourceId, CACHE_RESOURCES );

            if( !$resource )            
            	return false;
            if( !$this->check( $resource, $ownerId, $albumId ))
            	return false;

			return $resource;
        }
		
		/**
		 * given a resource id, tries to find the next one in the sequence
		 *
		 * @param resource A GalleryResource object that represents the resource whose next
		 * object we'd like to load
         * @return Returns a GalleryResource object representing the next resource, or false
		 * if there was no next resource
         */		 
		function getNextResource( $resource )
		{		
			$prefix = $this->getPrefix();
			$albumId = $resource->getAlbumId();
			$date = $resource->getDate();
			$id = $resource->getId();
			$query = "SELECT id, owner_id, album_id, description,
        	                 date, flags, resource_type, file_path, file_name,
        	                 metadata, thumbnail_format, properties, file_size
        	          FROM {$prefix}gallery_resources 
			          WHERE album_id = '$albumId' AND date >= '$date' AND id > $id
					  ORDER BY date ASC,id ASC LIMIT 1";

			$result = $this->Execute( $query );
			
			if( !$result )
				return false;
			if( $result->RecordCount() == 0 ){
                $result->Close();
				return false;
            }
				
			$row = $result->FetchRow();
            $result->Close();
			$resource = $this->mapRow( $row );
			
			$this->_cache->setData( $resource->getId(), CACHE_RESOURCES, $resource );
			$this->_cache->setData( $resource->getFileName(), CACHE_RESOURCES_BY_NAME, $resource );			
			
			return $resource;
		}

		/**
		 * given a resource id, tries to find the previus one in the sequence
		 *
		 * @param resource A GalleryResource object that represents the resource whose next
		 * object we'd like to load
         * @return Returns a GalleryResource object representing the previous resource, or false
		 * if there was no previous resource
         */		 		
		function getPreviousResource( $resource )
		{
			$prefix = $this->getPrefix();
			$albumId = $resource->getAlbumId();
			$date = $resource->getDate();
			$id = $resource->getId();
			$query = "SELECT id, owner_id, album_id, description,
        	                 date, flags, resource_type, file_path, file_name,
        	                 metadata, thumbnail_format, properties, file_size
        	          FROM {$prefix}gallery_resources 
			          WHERE album_id = '$albumId' AND date <= '$date' AND id < $id
					  ORDER BY date DESC,id DESC LIMIT 1";

			$result = $this->Execute( $query );
			
			if( !$result )
				return false;
			if( $result->RecordCount() == 0 ){
                $result->Close();
				return false;
            }
				
			$row = $result->FetchRow();
            $result->Close();
            $resource = $this->mapRow( $row );
			
			$this->_cache->setData( $resource->getId(), CACHE_RESOURCES, $resource );
			$this->_cache->setData( $resource->getFileName(), CACHE_RESOURCES_BY_NAME, $resource );			
			
			return $resource;		
		}

        /**
         * Returns all the resources that belong to a blog
         *
         * @param blogId The blog to which the resources belong, use -1 to indicate any blog/owner
         * @param albumId Filters by album
		 * @param page
		 * @param itemsPerPage
		 * @param searchTerms
         * @return Returns an array of GalleryResource objects with all
         * the resources that match the given conditions, or empty
         * if none could be found.
         */
        function getUserResources( $ownerId, 
                                   $albumId = GALLERY_NO_ALBUM, 
                                   $resourceType = GALLERY_RESOURCE_ANY,
								   $searchTerms = "",
                                   $page = DEFAULT_PAGING_ENABLED, 
                                   $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
        {

            $resources = Array();
            $query = "SELECT id FROM ".$this->getPrefix()."gallery_resources WHERE "; 
            if( $ownerId != -1 )
                $query .= " owner_id = '".Db::qstr($ownerId)."' AND";
            if( $albumId != GALLERY_NO_ALBUM )
                $query .= " album_id = '".Db::qstr($albumId)."' AND";
            if( $resourceType != GALLERY_RESOURCE_ANY )
                $query .= " resource_type = '".Db::qstr($resourceType)."' AND";
            if( $searchTerms != "" )
                $query .= " (".$this->getSearchConditions( $searchTerms ).")";
            
                // just in case if for any reason the string ends with "AND"
            $query = trim( $query );
            if( substr( $query, -3, 3 ) == "AND" )
                $query = substr( $query, 0, strlen( $query ) - 3 );
            
            $result = $this->Execute( $query, $page, $itemsPerPage );
            if( !$result )
                return $resources;
            
            while( $row = $result->FetchRow()) {
                    // use the primary key to retrieve the items via the cache
                $resources[] = $resource = $this->get( "id", $row["id"], CACHE_RESOURCES );
            }
            
            return $resources;
        }
        
        /**
         * @private
         */
        function check( $resource, 
                        $ownerId = -1, 
                        $albumId = GALLERY_NO_ALBUM, 
                        $resourceType = GALLERY_RESOURCE_ANY )
        {
        	if( $ownerId != -1 && $ownerId != '_all_' ) {
        		if( $resource->getOwnerId() != $ownerId ) {
        			return false;
				}
        	}
        	if( $albumId != GALLERY_NO_ALBUM ) {
           		if( $resource->getAlbumId() != $albumId ) {
           			return false;
				}
           	}
           	if( $resourceType != GALLERY_RESOURCE_ANY ) {		
           		if( $resource->getResourceType() != $resourceType )
           			return false;
           	}
			
           	return( true );
        }
		
		/**
		 * returns the number of items given certain conditions
		 *
		 * @param ownerId The id of the user whose amount of albums we'd like to check
		 * @param albumId Optional, the id of the album, in case we'd only like to know the number of resources in a certain album.
		 * use the constant GALLERY_NO_ALBUM to disable this parameter
		 * @param resourceType An additional filter parameter, so that we can only count a certain type of resources.
		 * Defaults to the constant GALLERY_RESOURCE_ANY
		 * @param searchTerms
		 * @see getUserResources
		 * @return the total number of items
		 */
		function getNumUserResources( $ownerId, $albumId = GALLERY_NO_ALBUM, $resourceType = GALLERY_RESOURCE_ANY, $searchTerms = "" )
		{
			$prefix = $this->getPrefix();
			$table  = "{$prefix}gallery_resources";
			
			$cond = "";
			if( $ownerId != -1 )
				$cond = "owner_id = '".Db::qstr( $ownerId )."'";
			else
				$cond = "owner_id = owner_id ";
			
            if( $albumId > GALLERY_NO_ALBUM )
            	$cond .= "AND album_id = '".Db::qstr($albumId)."'";
			if( $resourceType > GALLERY_RESOURCE_ANY )
				$cond .= " AND resource_type = '".Db::qstr($resourceType)."'";
			if( $searchTerms != "" ) {
				$searchParams = $this->getSearchConditions( $searchTerms );
				$cond .= " AND (".$searchParams.")";
			}

                // return the number of items
			return( $this->getNumItems( $table, $cond ));
		}

		/**
		 * Adds a row related to a resource to the database. You should usually use
		 * GalleryResources::addResource() or GalleryResources::addResourceFromDisk(), which are more
		 * suitable and will do most of the job for you.
		 *
		 * @param ownerId
		 * @param albumId
		 * @param description
		 * @param flags
		 * @param resourceType
		 * @param filePath
		 * @param fileName
		 * @param metadata
		 * @return a valid resource id or false otherwise
		 * @see addResource
		 */
		function addResourceToDatabase( $ownerId, $albumId, $description, $flags, $resourceType, 
											$filePath, $fileName, $metadata )
		{
			// prepare the metadata to be stored in the db
			$fileSize = $metadata["filesize"];
			$serMetadata = Db::qstr( serialize($metadata));
			// get the correct thumbnail format
			lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
			$config =& Config::getConfig();
			$thumbnailFormat = $config->getValue( "thumbnail_format" );
			// prepare some other stuff
			lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );			
			$tf = new Textfilter();
			$normalizedDescription = $tf->normalizeText( $description );
			$properties = serialize( array() );
			
			// check if there already is a file with the same name stored
			$duplicated = $this->isDuplicatedFileName( $fileName );

			// finally put the query together and execute it
			$query = "INSERT INTO ".$this->getPrefix()."gallery_resources(
						  owner_id, album_id, description, flags, resource_type,
						  file_path, file_name, file_size, metadata, thumbnail_format, normalized_description, properties) 
						  VALUES (
						  $ownerId, $albumId, '".Db::qstr($description)."', $flags, $resourceType,
						  '$filePath', '".Db::qstr($fileName)."', '$fileSize', '$serMetadata', '$thumbnailFormat',
				  '".Db::qstr($normalizedDescription)."', '$properties');";
						  
			$result = $this->Execute( $query );

			// check the return result
			if( !$result )
				return GALLERY_ERROR_ADDING_RESOURCE;		

			// get the id that was given to the record
			$resourceId = $this->_db->Insert_ID();

			// check if we have two resources with the same filename now
			// check if there already exists a file with the same name
			//
			// if that's the case, then we should rename the one we just
			// added with some random prefix, to make it different from the
			// other one...
			if( $duplicated ) {
				$query = "UPDATE ".$this->getPrefix()."gallery_resources
						  SET file_name = '$resourceId-$fileName'
						  WHERE id = $resourceId";

				$this->Execute( $query );
			}
			
			// clear our own caches
            $this->_cache->removeData( $resourceId, CACHE_RESOURCES );
			$this->_cache->removeData( $ownerId, CACHE_RESOURCES_USER );
			$this->_cache->removeData( $fileName, CACHE_RESOURCES_BY_NAME );			
		
			return $resourceId;	
		}	
		
		/**
		 * @private
		 * @param fileName
		 * @param metadata
		 */
		function _getResourceType( $fileName, &$metadata )
		{
  			// find out the right resource type based on the extension
			// get the resource type
			$fileParts = explode( ".", $fileName );
			$fileExt = strtolower($fileParts[count($fileParts)-1]);
			
			//asf need special working
			if ("asf" == $fileExt ){			 
                if (!($metadata["audio"]["codec"]))                            
                    $resourceType = GALLERY_RESOURCE_SOUND;
                else 
                    $resourceType = GALLERY_RESOURCE_VIDEO;
             }           
 		     else {
    			if( array_key_exists( $fileExt, $this->_extensionToType ))
	   			     $resourceType = $this->_extensionToType[ $fileExt ];
		  	   else
				    $resourceType = GALLERY_RESOURCE_UNKNOWN;
            }
            
            return( $resourceType );					
		}

        /**
         * adds a resource to the database. This method requires a FileUpload parameter and it
		 * will take care of processing the upload file and so on. If the file is already in disk and we'd
		 * like to add it, please check GalleryResources::addResourceFromDisk()
		 * This method will also take care of extracting the metadata from the file and generating the
		 * thumbnail in the required format, according to our configuration.
		 *
		 * @param ownerId
		 * @param albumId
		 * @param description
		 * @param upload A FileUpload object
		 * @see FileUpload
		 * @see GalleryResources::addResourceFromDisk()
		 * @return It will return one of the following constants:
		 * - GALLERY_ERROR_RESOURCE_TOO_BIG
		 * - GALLERY_ERROR_RESOURCE_FORBIDDEN_EXTENSION
		 * - GALLERY_ERROR_QUOTA_EXCEEDED
		 * - GALLERY_ERROR_ADDING_RESOURCE
		 * - GALLERY_ERROR_UPLOADS_NOT_ENABLED
		 * or the identifier of the resource that was just added if the operation succeeded.
         */
        function addResource( $ownerId, $albumId, $description, $upload )
        {
            // check if quotas are enabled, and if this file would make us go
            // over the quota
            lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresourcequotas.class.php" );            
            if( GalleryResourceQuotas::isBlogOverResourceQuota( $ownerId, $upload->getSize())) {
                return GALLERY_ERROR_QUOTA_EXCEEDED;
            }
			
            // first of all, validate the file using the
            // upload validator class. It can return
            // UPLOAD_VALIDATOR_ERROR_UPLOAD_TOO_BIG (-1)
            // or
            // UPLOAD_VALIDATOR_ERROR_FORBIDDEN_EXTENSION (-2)
            // in case the file is not valid.
            lt_include( PLOG_CLASS_PATH."class/data/validator/uploadvalidator.class.php" );            
            $uploadValidator = new UploadValidator();
            $error = $uploadValidator->validate( $upload );
            if( $error < 0 )
                return $error;
            
            // get the metadata
            lt_include( PLOG_CLASS_PATH."class/gallery/getid3/getid3.php" );            
            $getId3 = new GetID3();
            $metadata = $getId3->analyze( $upload->getTmpName());

            // nifty helper method from the getid3 package
            getid3_lib::CopyTagsToComments($metadata);

            $resourceType = $this->_getResourceType( $upload->getFileName(), $metadata );
            
            // set the flags
            $flags = 0;
            if( $resourceType == GALLERY_RESOURCE_IMAGE )
                $flags = $flags|GALLERY_RESOURCE_PREVIEW_AVAILABLE;
                
            $info = $this->_filterMetadata( $metadata, $resourceType );  
      		
            // add the record to the database
            $fileName = $upload->getFileName();
			$duplicated = $this->isDuplicatedFilename( $fileName );
			$filePath = "";
			$resourceId = $this->addResourceToDatabase( $ownerId, $albumId, $description, $flags, $resourceType, $filePath, $fileName, $info );
			if( !$resourceId )
				return false;
								
			if( $duplicated ) {
				$upload->setFileName( $resourceId."-".$upload->getFileName());
			}
			
            // and finally move the file to the right place in disk
            // move the file to disk
		    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresourcestorage.class.php" );            
            $storage = new GalleryResourceStorage();
            $resFile = $storage->storeUpload( $resourceId, $ownerId, $upload );
            
            // if the file cannot be read, we will also remove the record from the
            // database so that we don't screw up
            $fileReadable = File::isReadable( $resFile );
			
            if( !$resFile || $resFile < 0 || !$fileReadable ) {
                // if something went wrong, we should not keep the record in the db
                $query = "DELETE FROM ".$this->getPrefix()."gallery_resources WHERE id = $resourceId";
                $this->Execute( $query );
                return $resFile;
            }

			lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );			
			$albums = new GalleryAlbums();
			$album = $albums->getAlbum( $albumId );
			$album->setNumResources( $album->getNumResources() + 1 );
			$albums->updateAlbum( $album );			
			
            // and finally, we can generate the thumbnail only if the file is an image, of course :)
            if( $resourceType == GALLERY_RESOURCE_IMAGE ) {
            	lt_include( PLOG_CLASS_PATH."class/gallery/resizers/gallerythumbnailgenerator.class.php" );

				lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
				$config =& Config::getConfig();
            	
            	$imgWidth = $info["video"]["resolution_x"];
                $imgHeight = $info["video"]["resolution_y"];

				$previewHeight = $config->getValue( "thumbnail_height", GALLERY_DEFAULT_THUMBNAIL_HEIGHT );
				$previewWidth  = $config->getValue( "thumbnail_width", GALLERY_DEFAULT_THUMBNAIL_WIDTH );
                $thumbHeight = ((!$imgHeight || ($imgHeight > $previewHeight)) ?
                                $previewHeight : $imgHeight); 	
                $thumbWidth = ((!$imgWidth || ($imgWidth > $previewWidth)) ?
                                $previewWidth : $imgWidth); 	
                GalleryThumbnailGenerator::generateResourceThumbnail( $resFile, $resourceId, $ownerId, $thumbHeight, $thumbWidth );                

				$medPreviewHeight = $config->getValue( "medium_size_thumbnail_height", GALLERY_DEFAULT_MEDIUM_SIZE_THUMBNAIL_HEIGHT );
				$medPreviewWidth  = $config->getValue( "medium_size_thumbnail_width", GALLERY_DEFAULT_MEDIUM_SIZE_THUMBNAIL_WIDTH );
				$thumbHeight = ( $imgHeight > $medPreviewHeight ? $medPreviewHeight : $imgHeight );
				$thumbWidth = ( $imgWidth > $medPreviewWidth ? $medPreviewWidth : $imgWidth );				
				GalleryThumbnailGenerator::generateResourceMediumSizeThumbnail( $resFile, $resourceId, $ownerId, $thumbHeight, $thumbWidth );

				// call this method only if the settings are right and the image is bigger than the final size(s)
				$finalPreviewHeight = $config->getValue( "final_size_thumbnail_height", 0 );
				$finalPreviewWidth  = $config->getValue( "final_size_thumbnail_width", 0 );
				
				if( $finalPreviewHeight > 0 )
					if( $imgHeight < $finalPreviewHeight )
						$finalPreviewHeight = $imgHeight;
						
				if( $finalPreviewWidth > 0 )
					if( $imgWidth < $finalPreviewWidth )
						$finalPreviewWidth = $imgWidth;
				
				if( $finalPreviewHeight != 0 && $finalPreviewWidth != 0 ) {
					GalleryThumbnailGenerator::generateResourceFinalSizeThumbnail( $resFile, $resourceId, $ownerId, $finalPreviewHeight, $finalPreviewWidth );
					// we have to recalculate the metadata because the image could be different... This is a bit cumbersome
					// and repeats code. We know, thanks.
					$getId3 = new GetID3();
					$metadata = $getId3->analyze( $resFile );
					getid3_lib::CopyTagsToComments($metadata);            
					$info = $this->_filterMetadata( $metadata, $resourceType );
					// and finally update the resource again		
					$resource = $this->getResource( $resourceId );
					$resource->setMetadata( $info );			
					$this->updateResource( $resource );					
				}
            }
			
            // return the id of the resource we just added
            return $resourceId;
        }
        
        /**
         * @private
         * Returns an array with only those bits of metadata as generate by getid3 that
         * we'd like to keep, instead of one huge array
         *
         * @param metadata
         * @param resourceType
         */
        function _filterMetadata( &$metadata, $resourceType ) 
        {
            $info = Array();
			if( isset( $metadata["md5_file"] ))
				$info["md5_file"] = $metadata["md5_file"];
			else
				$info["md5_file"] = "";

			if( isset( $metadata["md5_data"] ))
				$info["md5_data"] = $metadata["md5_data"];
			else
				$info["md5_data"] = "";
			
			if( isset( $metadata["filesize"] ))
				$info["filesize"]= $metadata["filesize"];
			else
				$info["filesize"] = 0;
				
			if( isset( $metadata["fileformat"] ))
				$info["fileformat"] = $metadata["fileformat"]; 			
			else
				$metadata["fileformat"] = "";

			if( isset( $metadata["comments"] ))
				$info["comments"] = $metadata["comments"];
			else
				$info["comments"] = 0;
                        
            if($resourceType == GALLERY_RESOURCE_IMAGE){
				if( isset( $metadata["video"] )) $info["video"] = $metadata["video"];
				if( isset( $metadata["jpg"] )) {
					$info["jpg"]["exif"]["FILE"] = $metadata["jpg"]["exif"]["FILE"];
					$info["jpg"]["exif"]["COMPUTED"] = $metadata["jpg"]["exif"]["COMPUTED"];
					if(isset( $metadata["jpg"]["exif"]["IFD0"] )) $info["jpg"]["exif"]["IFD0"] = $metadata["jpg"]["exif"]["IFD0"];
					$metadata["jpg"]["exif"]["EXIF"]["MakerNote"] = "";
					$info["jpg"]["exif"]["EXIF"] = $metadata["jpg"]["exif"]["EXIF"];
				}
             }  
             else  if($resourceType == GALLERY_RESOURCE_SOUND){
                $info["audio"] = $metadata["audio"];
                $info["playtime_string"] = $metadata["playtime_string"];
                $info["playtime_seconds"] = $metadata["playtime_seconds"];
             }   
             else  if($resourceType == GALLERY_RESOURCE_VIDEO){
                $info["video"] = $metadata["video"];
                $info["audio"] = $metadata["audio"];
                $info["playtime_seconds"] = $metadata["playtime_seconds"];                
                $info["playtime_string"] = $metadata["playtime_string"];                
             }
             else if( $resourceType == GALLERY_RESOURCE_ZIP ) {
                $info["zip"]["compressed_size"] = $metadata["zip"]["compressed_size"];
                $info["zip"]["uncompressed_size"] = $metadata["zip"]["uncompressed_size"];
                $info["zip"]["entries_count"] = $metadata["zip"]["entries_count"];
                $info["zip"]["compression_method"] = $metadata["zip"]["compression_method"];
                $info["zip"]["compression_speed"] = $metadata["zip"]["compression_speed"];
             }
             
             return( $info );            
        }
        
        /**
         * adds a resource to the gallery when the resource is already stored on disk, instead of
         * it coming from an upload as it usually happens. This method is better than 
		 * GalleryResources::addResource() when instead of dealing with uploaded files, the file
		 * is already in disk and all that is left to do is to add it to the database.
         *
         * @param ownerId
         * @param albumId
         * @param description
         * @param fullFilePath The real path where the file is stored. This is expected to be
		 * its final and permanent destination
		 * @return It will return one of the following constants:
		 * - GALLERY_ERROR_RESOURCE_TOO_BIG
		 * - GALLERY_ERROR_RESOURCE_FORBIDDEN_EXTENSION
		 * - GALLERY_ERROR_QUOTA_EXCEEDED
		 * - GALLERY_ERROR_ADDING_RESOURCE
		 * - GALLERY_ERROR_UPLOADS_NOT_ENABLED
		 * or the identifier of the resource that was just added if the operation succeeded.
         */
        function addResourceFromDisk( $ownerId, $albumId, $description, $fullFilePath )
        {
            // check if quotas are enabled, and if this file would make us go
            // over the quota
            lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresourcequotas.class.php" );            
            if( GalleryResourceQuotas::isBlogOverResourceQuota( $ownerId, File::getSize( $fullFilePath ))) {
                return GALLERY_ERROR_QUOTA_EXCEEDED;
            }
            
            // get the metadata
            lt_include( PLOG_CLASS_PATH."class/gallery/getid3/getid3.php" );            
            $getId3 = new GetID3();
            $metadata = $getId3->analyze( $fullFilePath );
            // nifty helper method from the getid3 package
            getid3_lib::CopyTagsToComments($metadata);                      
    
            $resourceType = $this->_getResourceType( $fullFilePath, $metadata );
            $info = $this->_filterMetadata( $metadata, $resourceType );            		
			    
            // set the flags
            $flags = 0;
            if( $resourceType == GALLERY_RESOURCE_IMAGE )
                $flags = $flags|GALLERY_RESOURCE_PREVIEW_AVAILABLE;
    
            // add the record to the database
            $fileName = basename( $fullFilePath );
			$duplicated = $this->isDuplicatedFilename( $fileName );
            $filePath = "";
        
			$resourceId = $this->addResourceToDatabase( $ownerId, $albumId, $description, $flags, $resourceType, $filePath, $fileName, $info );
            if( !$resourceId )
                return false;

			if( $duplicated ) {
				//
				// :TODO:
				// ugly...
				//
				$newFilePath = dirname( $fullFilePath)."/".$resourceId."-".basename( $fullFilePath );				
				File::rename( $fullFilePath, $newFilePath );
				$fullFilePath = $newFilePath;
			}
    
            // and finally move the file to the right place in disk
            // move the file to disk
		    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresourcestorage.class.php" );            
            $storage = new GalleryResourceStorage();
            $resFile = $storage->storeFile( $resourceId, 
			                                $ownerId, 
											$fullFilePath,
                                            RESOURCE_STORAGE_STORE_MOVE );
            
            // if the file cannot be read, we will also remove the record from the
            // database so that we don't screw up
            $fileReadable = File::isReadable( $resFile );
            if( !$resFile || $resFile < 0 || !$fileReadable ) {
                // if something went wrong, we should not keep the record in the db
                $query = "DELETE FROM ".$this->getPrefix()."gallery_resources
                          WHERE id = $resourceId";
    
                $this->Execute( $query );
                
                return $resFile;
            }
    
            // and finally, we can generate the thumbnail only if the file is an image, of course :)
            if( $resourceType == GALLERY_RESOURCE_IMAGE ) {
            	lt_include( PLOG_CLASS_PATH."class/gallery/resizers/gallerythumbnailgenerator.class.php" );            
                GalleryThumbnailGenerator::generateResourceThumbnail( $resFile, $resourceId, $ownerId );
				GalleryThumbnailGenerator::generateResourceMediumSizeThumbnail( $resFile, $resourceId, $ownerId );
				// call this method only if the settings are right
				lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
				$config =& Config::getConfig();
				$previewHeight = $config->getValue( "final_size_thumbnail_height", 0 );
				$previewWidth  = $config->getValue( "final_size_thumbnail_width", 0 );				
				if( $previewHeight != 0 && $previewWidth != 0 ) {
					GalleryThumbnailGenerator::generateResourceFinalSizeThumbnail( $resFile, $resourceId, $ownerId );
					// we have to recalculate the metadata because the image could be different... This is a bit cumbersome
					// and repeats code. We know, thanks.
					$getId3 = new GetID3();
					$metadata = $getId3->analyze( $resFile );
					getid3_lib::CopyTagsToComments($metadata);            
					$info = $this->_filterMetadata( $metadata, $resourceType );
					// and finally update the resource again	
					$resource = $this->getResource( $resourceId );
					$resource->setMetadata( $info );			
					$this->updateResource( $resource );					
				}
			}
            
            // return the id of the resource we just added
            return $resourceId;        
        }

        /**
         * retrieves a resource, given its filename and its owner
         *
         * @param ownerId
         * @param fileName
         * @return Returns a GalleryResource object containing the given resource
         * or false if the resource doesn't exist.
         */
        function getResourceFile( $ownerId, $fileName, $albumId = -1 )
        {
        	$resource = $this->get( "file_name", $fileName, CACHE_RESOURCES_BY_NAME );
			if( !$resource )
				return false;
				
        	if( $resource->getOwnerId() != $ownerId )
        		return false;
        	if( $albumId != -1 )
        		if( $resource->getAlbumId() != $albumId )
        			return false;
        			
            return( $resource );
        }

        /**
         * updates a resource in the database.
         *
         * @param resource A GalleryResource object with the information of the
         * resource we'd like to update.
		 * @return Returns true if successful or false otherwise
         */
        function updateResource( $resource ) 
        {
			// loads the previous version of this object
			$prevVersion = $this->getResource( $resource->getId());				
		
        	if( $result = $this->update( $resource )) {
        		// if update ok, reset the caches
				$this->_cache->removeData( $resource->getId(), CACHE_RESOURCES );
				$this->_cache->removeData( $resource->getOwnerId(), CACHE_RESOURCES_USER );
				$this->_cache->removeData( $resource->getFileName(), CACHE_RESOURCES_BY_NAME );
				// and update the album counters... must substract 1 from the previous album
				// and must add one to the new album
				lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );
				$albums = new GalleryAlbums();
				$album = $resource->getAlbum();
				$album->setNumResources( $album->getNumResources() + 1 );
				$albums->updateAlbum( $album );
				
				// update the counters of the previous album
				$prevAlbum = $prevVersion->getAlbum();
				$prevAlbum->setNumResources( $prevAlbum->getNumResources() - 1 );
				$albums->updateAlbum( $prevAlbum );
				
				// was the the owner id changed, compared to the old version? because if it was, then
				// we also need to move the file and the thumbnails
				if( $resource->getOwnerId() != $prevVersion->getOwnerId()) {
					lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresourcestorage.class.php" );
					$storage = new GalleryResourceStorage();
					if (!$storage->moveFile( $prevVersion, $resource )) {
						return false;
					}
				}
        	}
        	
        	return( $result );
        }

        /**
         * removes a resource from the database and disk
         *
         * @param resourceId The identifier of the resource we'd like to remove
         * @param ownerId Identifier of the owner of the resource. Optional.
         * @return Returns true if resource deleted ok or false otherwise.
         */
        function deleteResource( $resourceId, $ownerId = -1 )
        {
        	// first, get information about the resource
            $resource = $this->getResource( $resourceId, $ownerId );
            
            if( empty( $resource ) )
            	return false;
 
 	        if( $ownerId > -1 )
	         	if( $resource->getOwnerId() != $ownerId )
	           		return false;             	
        	
            $this->delete( "id", $resourceId );
	        $this->_cache->removeData( $resource->getId(), CACHE_RESOURCES );
			$this->_cache->removeData( $resource->getOwnerId(), CACHE_RESOURCES_USER );
			$this->_cache->removeData( $resource->getFileName(), CACHE_RESOURCES_BY_NAME );
			// update the counters
			lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );
			$albums = new GalleryAlbums();
			$album = $resource->getAlbum();
			$album->setNumResources( $album->getNumResources() - 1 );
			$albums->updateAlbum( $album );		
	        // proceed and remove the file from disk
			lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresourcestorage.class.php" );
        	$storage = new GalleryResourceStorage();
            return $storage->remove( $resource );            	
        }

        /**
         * Removes all the resource from the given ownerId
         *
         * @param ownerId The blog identifier
         */
        function deleteUserResources( $ownerId )
        {
            $userResources = $this->getUserResources( $ownerId, 
                                   					  GALLERY_NO_ALBUM, 
                                   					  GALLERY_RESOURCE_ANY,
								   					  "",
                                   					  -1);

            // remove resources belong to the owner one by one
            foreach( $userResources as $resource ) {
                $this->deleteResource( $resource->getId(), $resource->getOwnerId() );
            }

            return true;
        }

		/**
		 * returns true if the given filename already exists in the db
		 *
		 * @param fileName
		 * @return true if the filename already exists or false otherwise
		 */
        function isDuplicatedFilename( $fileName )
        {
        	$query = "SELECT COUNT(id) AS total FROM ".$this->getPrefix()."gallery_resources
                      WHERE file_name = '".Db::qstr($fileName)."'";

            $result = $this->Execute( $query );

            $row = $result->FetchRow();

            if( $row["total"] == 0 )
            	$result = false;
            else
            	$result = true;

			return( $result );
        }
		
		/**
		 * @see Model::getSearchConditions()
		 */
		function getSearchConditions( $searchTerms )
		{
			$query = "file_name LIKE '%".Db::qstr( $searchTerms )."%'";
			
			// search the text via the existing FULLTEXT index
			$db =& Db::getDb();
			if( $db->isFullTextSupported()) {			
				$query .= " OR MATCH(normalized_description) AGAINST ('".Db::qstr($searchTerms)."')";	
			}
			else {
				$query .= " OR normalized_description LIKE '%".Db::qstr( $searchTerms )."%'";
			}
			
			return( $query );
		}
		
        /**
         * @private
         */
        function mapRow( $row )
        {
        	$res = new GalleryResource( $row["owner_id"],
                                        $row["album_id"],
                                        $row["description"],
                                        $row["flags"],
                                        $row["resource_type"],
                                        $row["file_path"],
                                        $row["file_name"],
                                        unserialize($row["metadata"]),
                                        $row["date"],
										$row["thumbnail_format"],
										unserialize($row["properties"]),
                                        $row["id"] );

			$res->setFileSize( $row["file_size"] );

             return $res;
        }
    }
?>
