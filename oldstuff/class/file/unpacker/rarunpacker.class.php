<?php

    lt_include( PLOG_CLASS_PATH."class/file/unpacker/baseunpacker.class.php" );

	/**
	 * \ingroup File_Unpacker
	 *
	 * extends the BaseUnpacker class to implement support for RAR files
	 *
	 * It users the binary "unrar" and "gzip" to unpack the files. The location of these binaries
	 * is obtained from the config parameter "path_to_unrar" (or /usr/bin/unrar)
	 */
	class RarUnpacker extends BaseUnpacker
	{

    	function RarUnpacker()
        {
        	$this->BaseUnpacker();
        }

		/**
		 * @see BaseUnpacker::unpack()
		 */
        function unpack( $file, $destFolder )
        {
        	// get the paths where tar and gz are
            $config =& Config::getConfig();
            $unrarPath = $config->getValue( "path_to_unrar" );
            if( $unrarPath == "" )
            	$unrarPath = DEFAULT_UNRAR_PATH;

            $cmd = "$unrarPath x $file $destFolder";

            $result = exec( $cmd, $output, $retval );

            //
            // :KLUDGE:
            // apparently, we should get something in $retval but there's nothing
            // to the only way I've found to check if the command finished
            // successfully was checking if the $output array is full or empty
            //

            return ( $retval == 0 );
        }
     }
?>
