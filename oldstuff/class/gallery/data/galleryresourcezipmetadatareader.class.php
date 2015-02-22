<?php

	lt_include( PLOG_CLASS_PATH."class/gallery/data/galleryresourcebasemetadatareader.class.php" );

	/**
	 * \ingroup Gallery
	 *
     * methods for fetching metadata from zip file resources
     */
    class GalleryResourceZipMetadataReader extends GalleryResourceBaseMetadataReader
    {
    	function GalleryResourceZipMetadataReader( $resource )
        {
        	$this->GalleryResourceBaseMetadataReader( $resource );
        }

		/**
		 * returns the total compressed size of this file
		 *
		 * @return the size in bytes
		 */
        function getCompressedSize()
        {
        	return $this->_metadata["zip"]["compressed_size"];
        }

		/**
		 * returns the total uncompressed size of the files in the package
		 *
		 * @return the size in bytes
		 */
        function getUncompressedSize()
        {
        	return  $this->_metadata["zip"]["uncompressed_size"];
        }
		
		/**
		 * returns the total compressed size of this file, in a nicer, human-readable format
		 *
		 * @return a string representing the uncompressed size
		 */		
        function getRoundedUncompressedSize()
        {
        	return  StringUtils::formatSize($this->_metadata["zip"]["uncompressed_size"]);
        }		

		/**
		 * @return returns the number of files in the package
		 */
        function getTotalFiles()
        {
        	return $this->_metadata["zip"]["entries_count"];
        }

		/**
		 * returns the compression method that was used to generate this file
		 *
		 * @return a string representing the compression method
		 */
        function getCompressionMethod()
        {
        	return $this->_metadata["zip"]["copmression_method"];
        }

		/**
		 * returns the compression speed
		 *
		 * @return a string representing the compression speed
		 */
        function getCompressionSpeed()
        {
        	return $this->_metadata["zip"]["compression_speed"];
        }
    }
?>
