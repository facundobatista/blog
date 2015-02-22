<?php

	/**
	 * \defgroup File_Unpacker
	 *
	 * This module implements support for easily unpacking files compressed with
	 * .zip, .tar.gz, .tar.bz2 and .rar provided that all the binary tools are available.
	 *
	 * The main Unpacker class is a proxy class that will take care of finding the right
	 * subclass according to the package type.
	 */

	
    lt_include( PLOG_CLASS_PATH."class/file/unpacker/baseunpacker.class.php" );
    lt_include( PLOG_CLASS_PATH."class/file/unpacker/targzunpacker.class.php" );
    lt_include( PLOG_CLASS_PATH."class/file/unpacker/zipunpacker.class.php" );
    lt_include( PLOG_CLASS_PATH."class/file/unpacker/tarbz2unpacker.class.php" );
    lt_include( PLOG_CLASS_PATH."class/file/unpacker/rarunpacker.class.php" );

	define( "UNPACKER_AUTODETECT", "detect" );
	define( "UNPACKER_TAR_GZ", "tar.gz" );
    define( "UNPACKER_TAR_BZ2", "tar.bz2" );
    define( "UNPACKER_ZIP", "zip" );
    define( "UNPACKER_RAR", "rar" );
    define( "UNPACKER_UNSUPPORTED", false );

	/**
	 * \ingroup File_Unpacker
	 *
     * Class that implements an object capable of unpacking several different
     * kinds of compressed files. It will take care of finding the right unpacker class for the
	 * job, call it and return a result from it, so <b>there is no need to create instances of 
	 * the other unpacker classes</b>.
	 *
	 * Example of usage:
	 * 
	 * <pre>
	 *  $unpacker = new Unpacker();
	 *  $unpacker->unpack( "/tmp/my_uploaded_file.tar.gz", "/tmp/results" );
	 * </pre>
	 * 
	 * More unpacker classes can be added if needed.
	 *
	 * @see BaseUnpacker
	 * @see TarGzUnpacker
	 * @see ZipUnpacker
	 * @see TarBz2Unpacker
	 * @see RarUnpacker
     */
	class Unpacker  
	{

    	var $_methods = Array( "tar.gz"  => "TarGzUnpacker",
                           "zip"     => "ZipUnpacker",
                           "tar.bz2" => "TarBz2Unpacker",
			   "rar" => "RarUnpacker"
                         );

        var $_method;

        var $_unpackerObj;

        /**
         * Creates an object of this class. The first parameter is the
         * name of the file while the second parameter is the method
         * we'd like to use. The class is able to to auto-detect
         * the file we're using, but we can still force one specific
         * unpacking method.
		 *
		 * @param method The method we'd like to use to unpack the file. It defaults to
		 * UNPACKER_AUTODETECT but it can also take any of the following values:
		 * <ul>
		 * <li>UNPACKER_TAR_GZ</li>
		 * <li>UNPACKER_TAR_GZ2</li>
		 * <li>UNPACKER_ZIP</li>
		 * <li>UNPACKER_RAR</li>
		 * </ul>
         */
    	function Unpacker( $method = UNPACKER_AUTODETECT )
        {
        	

        	$this->_method = $method;
        }

		/**
		 * finds the right unpacker class
		 * @private
		 */
        function _findUnpacker()
        {
        	if( $this->_method == UNPACKER_AUTODETECT ) {
            	$extArray = explode( ".", $this->_file );

                $ext = $extArray[count($extArray)-1];
                $ext2 = $extArray[count($extArray)-2].".".$ext;

                if( isset($this->_methods[$ext]))
                	$this->_method = $ext;
                elseif( isset($this->_methods[$ext2]))
                	$this->_method = $ext2;
                else
                	$this->_method = UNPACKER_UNSUPPORTED;
            }

            // create the object
            if( $this->_method != UNPACKER_UNSUPPORTED ) {
            	$this->_unpacker = new $this->_methods[$this->_method]();
                $result = true;
            }
            else
            	$result = false;

            return $result;
        }

        /**
         * Unpacks the file using the selected method to the destination
         * folder. The method used to unpack the file will be autodetected from the file type
		 * unless the parameter to the autoconstructor is different from UNPACKER_AUTODETECT
		 *
		 * @param file path to the file that we're going to unpack
		 * @param destFolder destination folder where files should be unpacked
		 * @return UNPACKER_UNSUPPORTED if the file cannot be unpacked with any of the known unpacker
		 * classe, true if successful or false if not.
         */
        function unpack( $file, $destFolder = "./" )
        {
        	// find the most suitable unpacker mechanism
            $this->_file = $file;

        	if( !$this->_findUnpacker())
            	return UNPACKER_UNSUPPORTED;

            // and then just do it
        	return $this->_unpacker->unpack( $file, $destFolder );
        }
    }
?>
