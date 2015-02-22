<?php

    lt_include( PLOG_CLASS_PATH."class/file/unpacker/baseunpacker.class.php" );
    lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );

    /**
	 * \ingroup File_Unpacker
	 *
     * Unpacks .tar.gz files.
	 *
	 * It users the binaries "tar" and "gzip" to unpack the files. The location of these binaries
	 * is obtained from the config parameter "path_to_tar" (or /bin/tar if it does not exist) and
	 * "path_to_gzip" (or /bin/gzip if it does not exist)
	 *
     */
	class TarBz2Unpacker extends BaseUnpacker 
	{

    	function TarBz2Unpacker()
        {
        	$this->BaseUnpacker();
        }

        function unpack( $file, $destFolder )
        {
        	// get the paths where tar and gz are
            $config =& Config::getConfig();
            $tarPath = $config->getValue( "path_to_tar" );
            if( $tarPath == "" )
            	$tarPath = DEFAULT_TAR_PATH;

            $bzip2Path = $config->getValue( "path_to_bz2" );
            if( $bzip2Path == "" )
            	$bzip2Path = DEFAULT_BZIP2_PATH;

            // and now build the command
            //$file = escapeshellarg($file);
            //$destFolder = escapeshellarg($destFolder);

            //
            // :DANGER:
            // what if the user voluntarily sets the path of gzip and tar
            // to something else? we are doing no checks here to make sure that
            // the user is giving us a valid commnand so... how could we make
            // sure that it'll work?
            //
            $cmd = "$bzip2Path -dc $file | $tarPath xv -C $destFolder";

            $result = exec( $cmd, $output, $retval );

            //
            // :KLUDGE:
            // apparently, we should get something in $retval but there's nothing
            // to the only way I've found to check if the command finished
            // successfully was checking if the $output array is full or empty
            //

            return ($retval==0);
        }
    }
?>
