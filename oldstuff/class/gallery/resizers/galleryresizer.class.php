<?php

	/**
	 * \defgroup Gallery_resizer
	 *
	 * Please use the proxy class GalleryResizer that should be used to generate thumbnails, instead of using directly
	 * classes such as GalleryGdResizer or GalleryImageMagickResizer. This class will check from the 
	 * configuration the values of the parameters <b>thumbnail_method</b> and <b>thumbnail_format</b>
	 * and load the appropiate thumbnail generator class.
	 *
	 * Example usage:
	 *
	 * <pre>
	 *  $generator = new GalleryResizer( "/tmp/image.jpg" );
	 *  if( $generator->generate( "/tmp/image_thum.jpg", 120, 120 ))
	 *     print("Thumbnail generated ok!" );
	 *  else
	 *     print( "There was an error generating the thumbnail" ); 
	 * </pre>
     */

	lt_include( PLOG_CLASS_PATH."class/gallery/resizers/gallerynullresizer.class.php" );
	lt_include( PLOG_CLASS_PATH."class/gallery/resizers/galleryimagemagickresizer.class.php" );
	lt_include( PLOG_CLASS_PATH."class/gallery/resizers/gallerygdresizer.class.php" );
	lt_include( PLOG_CLASS_PATH."class/gallery/galleryconstants.php" );

	/**
	 * \ingroup Gallery_resizer
	 *
     * Takes care of generating, storing and retrieving thumbnails.
     *
     * Supports several methods for the generation of thumbnails and can support as
     * many as needed. It also supports caching of thumbnails so that they don't have
     * to be generated after each request.
	 */
	class GalleryResizer  
	{

    	/**
    	 * This is the array used to know which are the supported
         * thumbnail generator methods. To add another one, we only
         * have to create our own class extending the AbstractThumbnailGenerator
         * class and implement the methods given there.
         * The key of the array is the 'generatorMethod' parameter of the class
         * constructor and the value assigned to the key is the name of the
         * class that is going to generate the thumbnail.
         */
    	var $_methods = Array(
        	"default"     => "GalleryGdResizer",
        	"gd"          => "GalleryGdResizer",
            "null"        => "GalleryNullResizer",
            "imagemagick" => "GalleryImagemagickResizer"
        );

        /**
         * The name of the image file
         * @private
         */
        var $_image;

        /**
         * The name of the method we are going to use to generate the thumbnail
         * @private
         */
        var $_generatorMethod;

        /**
         * Constructor. Creates a Thumbnail object from the given image file.
         *
         * @param image An Image object
         * @param generatorMethod Optional parameter specifying which
         */
        function GalleryResizer( $image )
        {
        	
			
            // keep these things for later
        	$this->_image = $image;

            // fetch some needed values from the configuration file, such as
            // the format we'd like to use or the backend that will finally generate
            // the thubmnail
            $config =& Config::getConfig();

            // the backend generator
            $this->_generatorMethod = $config->getValue( "thumbnail_method" );
            if( $this->_generatorMethod == "" )
            	$this->_generatorMethod = DEFAULT_GENERATOR_METHOD;
            // the preferred output format
            $this->_defaultOutputFormat = $config->getValue( "thumbnail_format" );
            if( $this->_defaultOutputFormat == "" )
            	$this->_defaultOutputFormat = THUMBNAIL_OUTPUT_FORMAT_PNG;
        }
		
		/**
		 * returns the format that will be used/has been used to generate a thumbnail.
		 *
		 * @return Returns one of:
		 * - THUMBNAIL_OUTPUT_FORMAT_SAME_AS_IMAGE
		 * - THUMBNAIL_OUTPUT_FORMAT_JPG
		 * - THUMBNAIL_OUTPUT_FORMAT_PNG
		 * - THUMBNAIL_OUTPUT_FORMAT_GIF
		 */
		function getThumbnailFormat()
		{
			$config =& Config::getConfig();
			return $config->getValue( "thumbnail_format" );
		}
		
        /**
         * Generates a thumbnail.
         *
		 * @param outFile
         * @param width The width of the thumbnail
         * @param height The height of the thumbnail
		 * @param keepAspectRatio whether thumbnails should keep their aspect ratio (even though the final size
		 * 	might be somehow bigger than the value of the $height or $width parameter)
         * @return the path to the thumbnail that was generated or empty if error
         */
        function generate( $outFile, $width = GALLERY_DEFAULT_THUMBNAIL_WIDTH, $height = GALLERY_DEFAULT_THUMBNAIL_HEIGHT, $keepAspectRatio = true )
        {
            if( $width == "" || $width < 0 )
            	$width = GALLERY_DEFAULT_THUMBNAIL_WIDTH;

            if( $height == "" || $height < 0 )
            	$height = GALLERY_DEFAULT_THUMBNAIL_HEIGHT;

            // we can get into this 'else' if the image was not stored *or* we do not
            // wish to use the cached version

            // if it does not exist, we'll have to generate it...
        	// find out which class is going to do to the job for us
            $generatorClassName = $this->_methods[$this->_generatorMethod];
            $generatorClassObject = new $generatorClassName( $this->_image, $this->_defaultOutputFormat );
			
			if( $this->_defaultOutputFormat != THUMBNAIL_OUTPUT_FORMAT_SAME_AS_IMAGE ) {
				$fileParts = explode( ".", $outFile );
				array_pop( $fileParts );
				$fileNoExt = implode( ".", $fileParts );
				$outFile = $fileNoExt.".".$this->_defaultOutputFormat;
			}

			$generatorClassObject->setKeepAspectRatio( $keepAspectRatio );
            $imgThumb = $generatorClassObject->generate( $outFile, $width, $height );
			
            return $imgThumb;
        }
    }
?>
