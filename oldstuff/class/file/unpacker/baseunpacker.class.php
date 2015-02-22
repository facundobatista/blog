<?php

    // default paths where 'tar', 'gzip', 'bzip2', 'unrar', 'unzip' can be found... 
    // this should be true for at least linux-based machines (this is where they are
    // located in my gentoo box)
    define( "DEFAULT_TAR_PATH", "/bin/tar" );
    define( "DEFAULT_GZIP_PATH", "/bin/gzip" );
    define( "DEFAULT_BZIP2_PATH", "/bin/bzip2" );
    define( "DEFAULT_UNRAR_PATH", "/usr/bin/unrar" );
    define( "DEFAULT_UNZIP_PATH", "/usr/bin/unzip" );	

	/**
	 * \ingroup File_Unpacker
	 * 
	 * this is the interface that classes wishing to provide additional methods
	 * for unpacking files must implement. Of course PHP4 does not support native
	 * interface and so we have to resort to this kind of tricks but probably you
	 * already know the drill :)
	 */
	class BaseUnpacker  
	{

    	function BaseUnpacker()
        {
        	
        }

		/**
		 * method that implements the logic for unpacking files ofa  certain type
		 *
		 * @param file The file that we'd like to unpack
		 * @param destFolder the destination folder
		 * @return true if successful or false otherwise
		 */
        function unpack( $file, $destFolder )
        {
        	throw( new Exception( "This method must be implemented by child classes!" ));

            die();
        }
    }

?>
