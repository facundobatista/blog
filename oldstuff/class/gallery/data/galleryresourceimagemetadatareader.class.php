<?php

	lt_include( PLOG_CLASS_PATH."class/gallery/data/galleryresourcebasemetadatareader.class.php" );

	/**
	 * \ingroup Gallery
     * 
	 * Retrieves information from images, regardless of the type of image.
     */
    class GalleryResourceImageMetadataReader extends GalleryResourceBaseMetadataReader
    {
		/**
		 * constructor
		 *
		 * @param resource A GalleryResource object
		 */
    	function GalleryResourceImageMetadataReader( $resource )
        {
        	$this->GalleryResourceBaseMetadataReader( $resource );
        }

		/**
		 * returns the height of the image
		 *
		 * @return the height of the image in pixels
		 */
        function getHeight()
        {
        	return $this->_metadata["video"]["resolution_y"];
        }
		
		/**
		 * returns the width of the image
		 *
		 * @return the width of the image in pixels
		 */
        function getWidth()
        {
        	return $this->_metadata["video"]["resolution_x"];
        }

		/**
		 * returns the exact format of the image
		 *
		 * @return a string representing the format of the image
		 */
        function getFormat()
        {
        	return $this->_metadata["video"]["dataformat"];
        }

		/**
		 * @return the number of bits per sample of the image
		 */
        function getBitsPerSample()
        {
        	return $this->_metadata["video"]["bits_per_sample"];
        }
    }
?>
