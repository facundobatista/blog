<?php

	

	/**
	 * \ingroup Gallery_resizer
	 *
     * Abstract class for generating thumbnails. It actually provides no methods but only
     * the skeleton of the functions that should be implemented by the generators
     * themselves.
     */
	class GalleryAbstractResizer
	{

    	var $_image;
    	var $_outputMethod;
		var $_keepAspectRatio;

        /**
         * Constructor.
         *
         */
        function GalleryAbstractResizer( $image, $outputMethod )
        {	
            $this->_image  = $image;
            $this->_outputMethod = $outputMethod;
			$this->_keepAspectRatio = true;
        }

		/** 
		 * Informs the resizer to keep the aspect ratio of the image when resizing, although
		 * this value may be ignored by the resizer and it depends on the implementation.
		 *
		 * @param keepAspectRatio Whether to keep the aspect ratio
		 */
		function setKeepAspectRatio( $keepAspectRatio )
		{
			$this->_keepAspectRatio = $keepAspectRatio;
		}

        /**
         * Generates the thumbnail
         * Uses the values set in the constructor regarding the width, height and output format
         *
         * @return Returns a the path to the thumbnail that was generated, or empty if error
		 * @see GalleryResizer::generate()
         */
        function generate( $outFile, $width, $height )
        {
        	throw( new Exception( "This function can't be called and must be implemented by child classes!" ));
            die();
        }
    }
?>
