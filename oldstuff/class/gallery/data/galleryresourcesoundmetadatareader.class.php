<?php

	lt_include( PLOG_CLASS_PATH."class/gallery/data/galleryresourcebasemetadatareader.class.php" );

	/**
	 * \ingroup Gallery
	 *
     * methods for fetching metadata from sound resources
     */
    class GalleryResourceSoundMetadataReader extends GalleryResourceBaseMetadataReader
    {
    	function GalleryResourceSoundMetadataReader( $resource )
        {
        	$this->GalleryResourceBaseMetadataReader( $resource );
        }

		/**
		 * @return the number of channels of the audio file
		 */
        function getChannels()
        {
        	return $this->_metadata["audio"]["channels"];
        }

		/**
		 * @return the rate in Hz at which the audio was sampled
		 */
        function getSampleRate()
        {
        	return $this->_metadata["audio"]["sample_rate"];
        }

		/**
		 * @return the channel mode: mono or stereo
		 */
        function getChannelMode()
        {
        	return $this->_metadata["audio"]["channelmode"];
        }

		/**
		 * @return the format of the audio file
		 */
        function getFormat()
        {
        	return $this->_metadata["audio"]["dataformat"];
        }
		
		/**
		 * @return a nicer, human-readable string with the length of the file
		 */		
		function getLengthString()
		{
			return $this->_metadata["playtime_string"];
		}
    }

?>