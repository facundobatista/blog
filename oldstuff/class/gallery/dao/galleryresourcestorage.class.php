<?php

	
    lt_include( PLOG_CLASS_PATH."class/file/fileupload.class.php" );
    lt_include( PLOG_CLASS_PATH."class/file/file.class.php" );
    lt_include( PLOG_CLASS_PATH."class/file/fileuploads.class.php" );
    lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/galleryconstants.php" );

	define( "RESOURCE_STORAGE_STORE_COPY", 1 );
	define( "RESOURCE_STORAGE_STORE_MOVE", 2 );

    /**
	 * \ingroup Gallery
	 *
     * Takes care of dealing with resource files on disk. This class hides all the intricacies of storing
	 * files in disk, and how they are found and handled later on. 
     */
    class GalleryResourceStorage 
    {

    	function GalleryResourceStorage()
        {
        	
        }
		
		/**
		 * Returns the path to the folder where resources are stored. This method can 
		 * be used as static.
		 *
		 * @static
		 * @return Returns a string containing the base path to the resource storage folder.
		 */
		function getResourcesStorageFolder()
		{
			$config =& Config::getConfig();
			$resourcesStorageFolder = $config->getValue( "resources_folder" );
			
			// just in case...
			if( $resourcesStorageFolder == "" )
				$resourcesStorageFolder = DEFAULT_RESOURCES_STORAGE_FOLDER;
				
			// append a forward slash to the folder if we forgot about it...
			if( $resourcesStorageFolder[strlen($resourcesStorageFolder)-1] != '/')
				$resourcesStorageFolder .= "/";
				
			// if relative path, like e.g. "./gallery/"...
			if (strpos($resourcesStorageFolder,'.') === 0)
			    $resourcesStorageFolder = rtrim( PLOG_CLASS_PATH ,'/').ltrim($resourcesStorageFolder,'.');
			
			return $resourcesStorageFolder;
		}
		
        /**
         * @private
         */
        function _checkBaseStorageFolder()
        {
			$baseFolder = GalleryResourceStorage::getResourcesStorageFolder();
			if( $baseFolder[strlen($baseFolder)-1] == "/") {
        	   $baseFolder = substr($baseFolder,0,strlen($baseFolder)-1);
            }			
						
        	if( !File::isDir( $baseFolder )) {
            	// folder does not exist, so we should try to create it
                if( !File::createDir( $baseFolder, 0755 )) {
                	throw( new Exception( "Could not create storage folder for resources: ".$baseFolder));
					return false;
                    //die();
                }				
            }

            if( !File::isReadable( $baseFolder )) {
            	throw( new Exception( $baseFolder." storage folder exists but it is not readable!" ));
				return false;
                //die();
            }

            return true;
        }
		
		/**
		 * a nicer function that the one above. And it is also meant to be used
		 * by external classes
		 *
		 * @static
		 * @return Returns true if the base storage folder has been created and
		 * it is readable.
		 */
        function checkBaseStorageFolder()
        {
			return GalleryResourceStorage::_checkBaseStorageFolder();
        }		
				
        /**
         * @private
         */
        function getUserFolder( $ownerId )
        {
        	return GalleryResourceStorage::getResourcesStorageFolder().$ownerId."/";
        }

        /**
         * @public
         */
        function getPreviewsFolder( $ownerId )
        {
        	return GalleryResourceStorage::getResourcesStorageFolder().$ownerId."/previews/";
        }
		
        /**
         * @public
         */
        function getMediumSizePreviewsFolder( $ownerId )
        {
        	return GalleryResourceStorage::getResourcesStorageFolder().$ownerId."/previews-med/";
        }		

        /**
         * @private
         */
        function _checkUserStorageFolder( $ownerId )
        {
			$baseFolder = GalleryResourceStorage::getResourcesStorageFolder();
			if( $baseFolder[strlen($baseFolder)-1] == "/") {
        	   $baseFolder = substr($baseFolder,0,strlen($baseFolder)-1);
            }			

        	$userFolder = GalleryResourceStorage::getUserFolder( $ownerId );
        	if( $userFolder[strlen($userFolder)-1] == "/") {
        	   $userFolder = substr($userFolder,0,strlen($userFolder)-1);
            }
			
        	if( !File::isDir( $userFolder )) {
            	// folder does not exist, so we should try to create it
                if( !File::createDir( $userFolder, 0755 )) {
                	throw( new Exception( "Could not create user storage folder for resources: ".$userFolder ));
					return false;
                    //die();
                }
            }

            if( !File::isReadable( $userFolder )) {
            	//throw( new Exception( $userFolder." user storage folder exists but it is not readable!" ));
				return false;
                //die();
            }
			
            return true;
        }
		
		function checkUserStorageFolder( $ownerId )
		{
			return GalleryResourceStorage::_checkUserStorageFolder( $ownerId );
		}

        /**
         * @public
         */
        function checkPreviewsStorageFolder( $ownerId )
        {
            $previewsFolder = GalleryResourceStorage::getPreviewsFolder( $ownerId );
        	if( $previewsFolder[strlen($previewsFolder)-1] == "/") {
        	   $previewsFolder = substr($previewsFolder,0,strlen($previewsFolder)-1);
            }

        	if( !File::isDir( $previewsFolder )) {
            	// folder does not exist, so we should try to create it
                if( !File::createDir( $previewsFolder, 0755 )) {
                	throw( new Exception( "Could not create user storage folder for previews: ".$previewsFolder ));
                    //die();
					return false;
                }				
            }

            if( !File::isReadable( $previewsFolder )) {
            	throw( new Exception( $previewsFolder." user previews storage folder exists but it is not readable!" ));
                //die();
				return false;
            }

            return true;
        }
		
        /**
         * @public
         */
        function checkMediumSizePreviewsStorageFolder( $ownerId )
        {
            $previewsFolder = GalleryResourceStorage::getMediumSizePreviewsFolder( $ownerId );
        	if( $previewsFolder[strlen($previewsFolder)-1] == "/") {
        	   $previewsFolder = substr($previewsFolder,0,strlen($previewsFolder)-1);
            }

        	if( !File::isDir( $previewsFolder )) {
            	// folder does not exist, so we should try to create it
                if( !File::createDir( $previewsFolder, 0755 )) {
                	throw( new Exception( "Could not create user storage folder for medium size previews: ".$previewsFolder ));
                    //die();
					return false;
                }
				
            }

            if( !File::isReadable( $previewsFolder )) {
            	throw( new Exception( $previewsFolder." user previews storage folder exists but it is not readable!" ));
                //die();
				return false;
            }

            return true;
        }		

        /**
         * stores a new resource in disk
         *
         * @param ownerId The id of the owner of this file
         * @param albumId The album id to which the
         * @param upload a FileUpload object with information about the
         * uploaded file
         */
        function storeUpload( $resourceId, $ownerId, $upload )
        {
        	// check that the folders exist
            if( !$this->_checkBaseStorageFolder())
				return false;
            if( !$this->_checkUserStorageFolder( $ownerId ))
				return false;

            // new name for the file
            $filePath = $this->getUserFolder( $ownerId );
			
            // move the file to the temporaray folder first
            $config =& Config::getConfig();
            $tmpFolder = $config->getValue( "temp_folder" );

                // if relative path, like e.g. "./tmp/"...
            if(strpos($tmpFolder, '.') === 0)
                $tmpFolder = rtrim(PLOG_CLASS_PATH, '/') . ltrim($tmpFolder, '.');

			// we don't need the parameter in the constructor though it is necessary 
			// according to the signature of the method
            $uploads = new FileUploads( null );
            $result = $uploads->processFile( $upload, $tmpFolder );

            if( $result < 0 ) {
            	return $result;
            }

			$origFile = $tmpFolder."/".basename( $upload->getTmpName() );

			//do not use storeFile method because I have change filename in $tmpFolder.
			//$destFile = $this->storeFile( $resourceId, $ownerId, $origFile, RESOURCE_STORAGE_STORE_MOVE );
			//$destFile use $filePath and $fileName generated above.
			//$destFile = $filePath.$fileName;

			if( $config->getValue( "resources_naming_rule" ) == "encoded_file_name" ) {
				$fileName = $upload->getFileName();
	            // new name for the file
	            $fileParts = explode( ".", $fileName);
	            $fileExt = strtolower($fileParts[count($fileParts)-1]);
				$destFile = $filePath.$ownerId."-".$resourceId.".".$fileExt;
			}
			else {
				$destFile = $filePath.stripslashes($upload->getFileName());
			}

			// first of all, check if the file is readable and if not, quit	
			if( !File::isReadable($origFile)) {
				return false;
			}

			$res = File::rename( $origFile, $destFile );
            
			if( !$res ) {
            	return false;
			}			
			// check that the permissions are correct
			File::chMod( $destFile, 0755 );

            return $destFile;
        }

	/**
	 * the method above works only with files that have been uploaded while
	 * this one works with files that are anywhere. It will take care of copying
	 * the file to the right destination folder and so on
	 *
         * @param ownerId The id of the owner of this file
         * @param albumId The album id to which the
         * @param fileName full path and name to the file that we're trying to store
	 */
	function storeFile( $resourceId, $ownerId, $fileName, $mode = RESOURCE_STORAGE_STORE_COPY )
	{
        	// check that the folders exist
            if( !$this->_checkBaseStorageFolder())
				return false;
            if( !$this->_checkUserStorageFolder( $ownerId ))
				return false;

            // new name for the file
            $fileParts = explode( ".", $fileName);
            $fileExt = strtolower($fileParts[count($fileParts)-1]);

            //$destFile = "$ownerId-$resourceId.$fileExt";
			$config =& Config::getConfig();

			// first of all, remove the resource file itself
            $filePath = $this->getUserFolder( $ownerId );
			if( $config->getValue( "resources_naming_rule" ) == "encoded_file_name" )
				$destFile = $ownerId."-".$resourceId.".".$fileExt;
			else
				$destFile = basename( $fileName );

            $destPath = $this->getUserFolder( $ownerId );

			// first of all, check if the file is readable and if not, quit	
			if( !File::isReadable($fileName)) {
				return false;
			}

			$destFile = $destPath.$destFile;
			if( $mode == RESOURCE_STORAGE_STORE_COPY )
            	$res = File::copy( $fileName, $destFile );
			else 
				$res = File::rename( $fileName, $destFile );
            
			if( !$res ) {
            	return false;
			}			
			// check that the permissions are correct
			File::chMod( $destFile, 0755 );

            return $destFile;
		}

        /**
         * removes a file from disk
         *
         * @param resource A GalleryResource object, representing the resource
         * we'd like to delete.
         * @return Returns ok if file was successfully deleted ok or false otherwise.
         */
        function remove( $resource )
        {
        	if( $resource ) {
				$config =& Config::getConfig();

				// first of all, remove the resource file itself
                $filePath = $this->getUserFolder( $resource->getOwnerId());
				if( $config->getValue( "resources_naming_rule" ) == "encoded_file_name" )
					$fullName = $filePath.$resource->getEncodedFileName();
				else
					$fullName = $filePath.$resource->getFileName();

				if( File::isReadable( $fullName)) {
					$result = File::delete( $fullName );
				} else {
					$result = false;
				}
				
				// and now if preview images are available, remove them too!
				if( $resource->hasPreview()) {
					// delete the small thumbnail
					$previewFile = $this->getPreviewsFolder( $resource->getOwnerId()).$resource->getPreviewFileName();
					if( File::isReadable( $previewFile ))
						File::delete( $previewFile );
						
					// and the medium-sized thumbnail
					$medPreviewFile = $this->getMediumSizePreviewsFolder( $resource->getOwnerId()).$resource->getMediumSizePreviewFileName();
					if( File::isReadable( $medPreviewFile ))
						File::delete( $medPreviewFile );
				}
            }
            else
            	$result = false;

            return $result;
        }
		
		/**
		 * returns the path of a GalleryResource object within the storage area
		 *
		 * @param $resource A GalleryResource object
		 * @return A string containing the path to the file relative to the storage area.
		 */
		function getResourcePath( $resource )
		{
			$config =& Config::getConfig();

			// first of all, remove the resource file itself
            $filePath = $this->getUserFolder( $resource->getOwnerId());
			if( $config->getValue( "resources_naming_rule" ) == "encoded_file_name" )
				$fileName = $resource->getEncodedFileName();
			else
				$fileName = $resource->getFileName();

			$resourcePath = $this->getUserFolder( $resource->getOwnerId()).$fileName;
			return $resourcePath;
		}
		
		
		/**
		 * Moves a file from one owner folder to another if the owner was modified.
		 *
		 * @param prevVersion A GalleryResource object
		 * @param resource A GalleryResource object
		 * @return True if successfull or if no move were needed.
		 */
		function moveFile( $prevVersion, $resource )
		{
			$prevOwnerId = $prevVersion->getOwnerId();
			$newOwnerId = $resource->getOwnerId();

			if ($newOwnerId == $prevOwnerId) {
				// No need to move
				return true;
			}

			// check that the folders exist
			if( !$this->_checkUserStorageFolder( $prevOwnerId ))
			return false;
			if( !$this->_checkUserStorageFolder( $newOwnerId ))
			return false;

			// move the main file
			$fileName = $this->getUserFolder( $prevOwnerId ) . $resource->getFileName();
			$destFile = $this->getUserFolder( $newOwnerId ) . $resource->getFileName();			
			$res = File::rename( $fileName, $destFile );
			if( !$res ) 
				return false;
				
			// check that the permissions are correct
			File::chMod( $destFile, 0755 );

			// move the preview and the medium preview, but only if available
			if( $resource->hasPreview()) {
				$fileName = $this->getPreviewsFolder( $prevOwnerId ) . $resource->getPreviewFileName();
				$destFile = $this->getPreviewsFolder( $newOwnerId ) . $resource->getPreviewFileName();			
				$res = File::rename( $fileName, $destFile );
				if( !$res ) 
					return false;
				
				// check that the permissions are correct
				File::chMod( $destFile, 0755 );

				// move the medium size preview
				$fileName = $this->getMediumSizePreviewsFolder( $prevOwnerId ) . $resource->getMediumSizePreviewFileName();
				$destFile = $this->getMediumSizePreviewsFolder( $newOwnerId ) . $resource->getMediumSizePreviewFileName();			
				$res = File::rename( $fileName, $destFile );
			
				if( !$res ) 
					return false;

				// check that the permissions are correct
				File::chMod( $destFile, 0755 );
			}

			return true;		
		}
    }   
?>
