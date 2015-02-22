<?php

    lt_include( PLOG_CLASS_PATH."class/file/unpacker/baseunpacker.class.php" );
    lt_include( PLOG_CLASS_PATH."class/file/unpacker/pclzip.lib.php" );	

	/**
	 * \ingroup File_Unpacker
	 * 
	 * unpacks files with ZIP. It can also use native PHP code with the
	 * PclZip library, set "unzip_use_native_version" to true if the configuration table
	 * for enabling that option.
	 *
	 * It users the binary "zip" to unpack the files. Its location is obtained from the
	 * config parameter "path_to_unzip" or /usr/bin/unzip if it does not exist
	 *
	 * @see Unpacker
	 * @see BaseUnpacker
	 */
	class ZipUnpacker extends BaseUnpacker 
	{

    	function ZipUnpacker()
        {
        	$this->BaseUnpacker();
        }
		
		/** 
		 * uses a native php library that is capable of dealing with .zip
		 * files without needing to call external commands
		 *
		 * @param file
		 * @param destFolder
		 * @return true if successful or false otherwise
		 */
		function unpackNative( $file, $destFolder )
		{
            $z = new PclZip($file);
            return $z->extract( $destFolder );
		}

		/**
		 * @see BaseUnpacker::unpack()
		 */
        function unpack( $file, $destFolder )
        {
        	// get the paths where tar and gz are
            $config =& Config::getConfig();
			
			if( $config->getValue( "unzip_use_native_version" )) {
				return $this->unpackNative( $file, $destFolder );
			}
			else {
				$unzipPath = $config->getValue( "path_to_unzip" );
				if( $unzipPath == "" )
					$unzipPath = DEFAULT_UNZIP_PATH;

				$cmd = "$unzipPath -o $file -d $destFolder";

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
     }
?>