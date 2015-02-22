<?php
	
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresource.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/stringutils.class.php" );

    /**
	 * \ingroup Gallery
	 *
     * acts as some kind of decorator adding getter methods for getting
     * some information straight from the metadata field of the resource object,
     * without having to overload the basic GalleryResource object with thousands
     * of accessor methods for every single property we might need.
     *
     * Using this method, we can have a ImageMetadataReader which would provide
     * methods like getHeight, getWidth, getBpp etc, and another SoundMetadataReader
     * with methods like getFrequency, etc.
	 *
	 * The right way to use this class is:
	 * <pre>
	 *   $reader = $resource->getResourceMetadataReader();
	 *   if( $resource->isImage()) {
	 *      print( "image size: ".$reader->getHeight()."x".$reader->getWidth()." pixels" );
	 *   }
	 * </pre>
	 *
	 * The method GalleryResource::getResourceMetadataReader() will return the right
	 * metadata reader class so that we can check the properties of the file. We can either query
	 * the methods GalleryResource::isImage(), GalleryResource::isVideo(), etc to know which properties
	 * we can check, or use PHP's function for checking of which type a class is.
     */
	class GalleryResourceBaseMetadataReader 
    {
    	var $_metadata;

    	function GalleryResourceBaseMetadataReader( $resource )
        {
        	

            $this->_metadata = $resource->getMetadata();
        }

        function getMD5File()
        {
        	return $this->_metadata["md5_file"];
        }

        function getMD5Data()
        {
        	return $this->_metadata["md5_data"];
        }

        function getFileSize()
        {
        	return $this->_metadata["filesize"];
        }

        function getFileFormat()
        {
        	return $this->_metadata["fileformat"];
        }
		
		function getRoundedSize()
		{
			return StringUtils::formatSize( $this->getFileSize());
		}		
    }
?>
