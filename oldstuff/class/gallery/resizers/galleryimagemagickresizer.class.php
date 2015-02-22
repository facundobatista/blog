<?php

	lt_include( PLOG_CLASS_PATH."class/gallery/resizers/galleryabstractresizer.class.php" );
    lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	
	define( "DEFAULT_PATH_TO_CONVERT", "/usr/bin/convert" );

	/**
	 *
	 * \ingroup Gallery_resizer
	 *
     * Back end class for generating thumbnails with ImageMagick. It requires the tool
	 * 'convert' to be installed somewhere in the filesystem. The exact location is determined
	 * via the config setting "path_to_convert", but it will default to <b>/usr/bin/convert</b>
	 * if the setting does not exist.
     */
	class GalleryImageMagickResizer extends GalleryAbstractResizer 
	{

    	/**
         * Constructor.
         */
    	function GalleryImageMagickResizer( $image, $outputMethod )
        {
        	$this->GalleryAbstractResizer( $image, $outputMethod );
        }

		/**
		 * @see GalleryResizer::generate
		 */
        function generate( $outFile, $width, $height )
        {
        	// get the path to the convert tool
            $config =& Config::getConfig();
            $convertPath = $config->getValue( "path_to_convert", DEFAULT_PATH_TO_CONVERT );
                // run the command
            $command = $convertPath." -geometry ".$width."x".$height." \"".$this->_image."\" \"".$outFile."\"";

            $cmdOutput = system($command, $retval);

                // check if there was an error creating the thubmnail
            if($cmdOutput === FALSE || $retval )
            	return false;

                // depending on the default file creation settings in some hosts, files created via
                // ImageMagick may not be readable by the web server
            File::chMod( $outFile, 0644 );
            
            return $outFile;
        }
    }
?>
