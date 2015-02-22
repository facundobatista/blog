<?php

	lt_include( PLOG_CLASS_PATH."class/gallery/data/galleryresourcebasemetadatareader.class.php" );

	/**
     * methods for fetching metadata from video resources
	 *
	 * \ingroup Gallery
     */
    class GalleryResourceVideoMetadataReader extends GalleryResourceBaseMetadataReader
    {
    	function GalleryResourceVideoMetadataReader( $resource )
        {
        	$this->GalleryResourceBaseMetadataReader( $resource );
        }

		/**
		 * @return the height in pixels of the video
		 */
        function getHeight()
        {
        	return $this->_metadata["video"]["resolution_y"];
        }

		/**
		 * @return the width in pixels of the video
		 */
        function getWidth()
        {
        	return $this->_metadata["video"]["resolution_x"];
        }

		/**
		 * @return the format of the video
		 */
        function getFormat()
        {
        	return $this->_metadata["video"]["dataformat"];
        }

		/**
		 * @return the bits per sample of the video
		 */
        function getBitsPerSample()
        {
        	return $this->_metadata["video"]["bits_per_sample"];
        }

		/**
		 * @return returns the codec that was used to generate the video
		 */
        function getVideoCodec()
        {
        	return $this->_metadata["video"]["codec"];
        }

		/**
		 * @return returns the name of the codec in which the audio was saved in the video
		 */
        function getAudioCodec()
        {
        	return $this->_metadata["audio"]["codec"];
        }

		/**
		 * @return returnsd the length in seconds of the video
		 */
        function getLength()
        {
        	return $this->_metadata["playtime_seconds"];
        }

		/**
		 * @return a nicely formatted string with the length of the video
		 */
        function getLengthString()
        {
        	return $this->_metadata["playtime_string"];
        }


    }
?>
