<?php

	lt_include( PLOG_CLASS_PATH."class/gallery/resizers/galleryabstractresizer.class.php" );

	/**
	 * \ingroup Gallery_resizer
	 *
     * This class is a thumbnail generator that does not generate thumbnails :) The purpose
     * of it (it has to have one, right? :)) is that if none of the other methods are
     * available (gd, imagemagick) at least we can "simulate" thumbnails by using the
     * with and height attributes of the img tag. In order to do that, this class
     * will not calculate the reduced image but simply return it as it... Well, at least it'll
     * be the fastest thumbnail method around ;)
     */
	class GalleryNullResizer extends GalleryAbstractResizer 
	{

        /**
         * Constructor.
         * @see GalleryAbstractResizer
         */
        function GalleryNullResizer( $image, $outputMethod )
        {
        	$this->GalleryAbstractResizer( $image, $outputMethod );
        }

        /**
		 * @see GalleryResizer::generate()
         */
        function generate( $outFile, $width, $height )
        {
        	return $this->_image;
        }
    }
?>
