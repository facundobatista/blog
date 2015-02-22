<?php

    /**
     * \defgroup File
	 *
	 * The File module provides some classes to deal with files. The File class
	 * is the most useful once, since provides OOP wrappers around some PHP
	 * functions that work with files.
     */


	 
	 lt_include( PLOG_CLASS_PATH."class/misc/glob.class.php" );

	 define( "FILE_DEFAULT_DIRECTORY_CREATION_MODE", 0700 );

    /**
	 * \ingroup File
	 *
	 * Encapsulation of a class to manage files. It is basically a wrapper
	 * to some of the php functions.
	 * http://www.php.net/manual/en/ref.filesystem.php
	 */
	 class File  
	 {

		 var $_fileName;
		 var $_handler;
		 var $_mode;
		 
		 /**
		  * Creates an object of this type, but the file is not automaticaly 
		  * opened
		  *
		  * @param fileName The name of the file
		  */
		 function File( $fileName )
		 {
			 
			 
			 $this->_fileName = $fileName;
		 }
		 
		 /**
		  * Opens the file in the specified mode
		  * http://www.php.net/manual/en/function.fopen.php
		  * Mode by default is 'r' (read only)
		  * Returns 'false' if operation failed
		  *
		  * @param mode The mode in which the file is opened
		  * @return false if the operation failed
		  */
		 function open( $mode = "r" )
		 {
			 $this->_handler = fopen( $this->_fileName, $mode );
			 
			 $this->_mode = $mode;
			 
			 return $this->_handler;
		 }
		 
		 /**
		  * Closes the stream currently being held by this object
		  *
		  * @return nothing
		  */
		 function close()
		 {
			 fclose( $this->_handler );
		 }
		 
		 /**
		  * Reads the whole file and put it into an array, where every position
		  * of the array is a line of the file (new-line characters not
		  * included)
		  *
		  * @return An array where every position is a line from the file.
		  */
		 function readFile()
		 {
			 $contents = Array();
			 
			 $contents = file( $this->_fileName );
			 
			 for( $i = 0, $elements = count( $contents ); $i < $elements; $i++ )
				 $contents[$i] = trim( $contents[$i] );
			 
			 return $contents;
		 }
		 
		 /**
		  * Reads bytes from the currently opened file
		  *
		  * @param size Amount of bytes we'd like to read from the file. It is 
		  * set to 4096 by default.
		  * @return Returns the read contents
		  */
		 function read( $size = 4096 )
		 {
			 return( fread( $this->_handler, $size ));
		 }
		 
		 /**
		  * checks whether we've reached the end of file
		  *
		  * @return True if we reached the end of the file or false otherwise
		  */
		 function eof()
		 {
			 return feof( $this->_handler );
		 }
		 
		 /**
		  * Writes data to disk
		  *
		  * @param data The data that we'd like to write to disk
		  * @return returns the number of bytes written, or false otherwise
		  */
		 function write( $data )
		 {
			 return fwrite( $this->_handler, $data );
		 }
		 
		 /**
		  * truncates the currently opened file to a given length
		  *
		  * @param length Lenght at which we'd like to truncate the file
		  * @return true if successful or false otherwise
		  */
		 function truncate( $length = 0 )
		 {
			 return ftruncate( $this->_handler, $length );
		 }
		 
		 /**
		  * Writes an array of text lines to the file.
		  *
		  * @param lines The array with the text.
		  * @return Returns true if successful or false otherwise.
		  */
		 function writeLines( $lines )
		 {
			 // truncate the file to remove the old contents
			 $this->truncate();
			 
			 foreach( $lines as $line ) {
				 //print("read: \"".htmlentities($line)."\"<br/>");
				 if( !$this->write( $line, strlen($line))) {
					 return false;
				 }
				 /*else
                	print("written: \"".htmlentities($line)."\"<br/>");*/
			 }
			 
			 return true;
		 }
		 
		 /**
		  * Returns true wether the file is a directory. See
		  * http://fi.php.net/manual/en/function.is-dir.php for more details.
		  *
		  * @param file The filename we're trying to check. If omitted, the
		  * current file will be used (note that this function can be used as
		  * static as long as the file parameter is provided)
		  * @return Returns true if the file is a directory.
		  */
		 function isDir( $file = null )
		 {
			 if( $file == null )
				 $file = $this->_fileName;
			 
			 return is_dir( $file );
		 }
		 
		 /**
		  * Returns true if the file is writable by the current user.
		  * See http://fi.php.net/manual/en/function.is-writable.php for more 
		  * details.
		  *
		  * @param file The filename we're trying to check. If omitted, the
		  * current file will be used (note that this function can be used as
		  * static as long as the file parameter is provided)
		  * @return Returns true if the file is writable, or false otherwise.
		  */
		 function isWritable( $file = null )
		 {
			 if( $file == null )
				 $file = $this->_fileName;
			 
			 return is_writable( $file );
		 }
		 
		 /**
		  * returns true if the file is readable. Can be used as static if a
		  * filename is provided
		  *
		  * @param if provided, this method can be used as an static method and
		  * it will check for the readability status of the file
		  * @return true if readable or false otherwise
		  */
		 function isReadable( $file = null )
		 {
			 if( $file == null )
				 $file = $this->_fileName;
				 
			clearstatcache();
			 
			 return is_readable( $file );
		 }
		 
		 /**
		  * removes a file. Can be used as static if a filename is provided. 
		  * Otherwise it will remove the file that was given in the constructor
		  *
		  * @param optionally, name of the file to delete
		  * @return True if successful or false otherwise
		  */
		 function delete( $file = null )
		 {
			 if( $file == null )
				 $file = $this->_fileName;

            if( !File::isReadable( $file ))
                return false;
			 
			 if( File::isDir( $file ))
				 $result = @rmdir( $file );
			 else
				 $result = @unlink( $file );
			 
			 return $result;
		 }
		 
		 /**
		  * removes a directory, optinally in a recursive fashion
		  *
		  * @param dirName
		  * @param recursive Whether to recurse through all subdirectories that
		  * are within the given one and remove them.
		  * @param onlyFiles If the recursive mode is enabled, setting this to 'true' will
		  * force the method to only remove files but not folders. The directory will not be
		  * removed but all the files included it in (and all subdirectories) will be.
		  * @param excludedFiles If some files or directories should not be removed (like .htaccess) they can be added
		  * to this array. This operation is case sensitive!
		  * @return True if successful or false otherwise
		  * @static
		  */
		 function deleteDir( $dirName, $recursive = false, $onlyFiles = false, $excludedFiles = array('') )
		 {
			// if the directory can't be read, then quit with an error
			if( !File::isReadable( $dirName ) || !File::exists( $dirName )) {
				return false;
			}
		 
			// if it's not a directory, let's get out of here and transfer flow
			// to the right place...
			if( !File::isDir( $dirName )) {
				return File::delete( $dirName );
			}
				
			// Glob::myGlob is easier to use than Glob::glob, specially when 
			// we're relying on the native version... This improved version 
			// will automatically ignore things like "." and ".." for us, 
			// making it much easier!
			$files = Glob::myGlob( $dirName, "*" );
			foreach( $files as $file ) 
			{
				// check if the filename is in the list of files we must not delete
				if( File::isDir( $file ) && array_search(basename( $file ), $excludedFiles) === false) {
					// perform a recursive call if we were allowed to do so
					if( $recursive ) {
						File::deleteDir( $file, $recursive, $onlyFiles, $excludedFiles );
					}
				}

				// check if the filename is in the list of files we must not delete
				if(array_search(basename( $file ), $excludedFiles) === false) {
				    // File::delete can remove empty folders as well as files
				    if( File::isReadable( $file )) {
					    File::delete( $file );
					}
				}
			}
			
			// finally, remove the top-level folder but only in case we
			// are supposed to!
			if( !$onlyFiles ) {
    			File::delete( $dirName );
    		}
			
			return true;
		 }
		 
		 /**
       	  * Creates a new folder. If the folder name is /a/b/c and neither 
       	  * /a or /a/b exist, this method will take care of creating the 
       	  * whole folder structure automatically.
		  *
		  * @static
		  * @param dirName The name of the new folder
		  * @param mode Attributes that will be given to the folder
		  * @return Returns true if no problem or false otherwise.
		  */
		 function createDir( $dirName, 
		                     $mode = FILE_DEFAULT_DIRECTORY_CREATION_MODE )
		 {
             if(File::exists($dirName)) return true;

             if(substr($dirName, strlen($dirName)-1) == "/" ){
                 $dirName = substr($dirName, 0,strlen($dirName)-1);
             }

             // for example, we will create dir "/a/b/c"
             // $firstPart = "/a/b"
             $firstPart = substr($dirName,0,strrpos($dirName, "/" ));           

             if(file_exists($firstPart)){
                 if(!mkdir($dirName,$mode)) return false;
			     chmod( $dirName, $mode );
             } else {
                 File::createDir($firstPart,$mode);
                 if(!mkdir($dirName,$mode)) return false;
			     chmod( $dirName, $mode );
         	}
			 
			 return true;
		 }
		
		 /**
		  * Change the current directory
		  *
		  * @param dir
		  */
		 function chDir( $dir )
		 {
			 return( chdir( $dir ));
		 }
		 
		 /**
		  * returns a temporary filename in a pseudo-random manner
		  *
		  * @return a temporary name
		  */
		 function getTempName()
		 {
			 return md5(microtime());
		 }
		 
		 /**
		  * Returns the size of the file.
		  *
		  * @param string fileName An optional parameter specifying the name 
		  * of the file. If omitted, we will use the file that we have 
		  * currently opened. Please note that this function can
		  * be used as static if a file name is specified.
		  * @return An integer specifying the size of the file.
		  */
		 function getSize( $fileName = null )
		 {
			 if( $fileName == null )
				 $fileName = $this->_fileName;
			 
			 $size = filesize( $fileName );
			 if( !$size )
				 return -1;
			 else
				 return $size;
		 }
		 
         /**
          * renames a file
          *
          * http://www.php.net/manual/en/function.rename.php
          *
          * This function can be used as static if inFile and outFile are both 
          * not empty. if outFile is empty, then the internal file of the
          * current object will be used as the input file and the first 
          * parameter of this method will become the destination file name.
          *
          * @param inFile Original file
          * @param outFile Destination file.
          * @return Returns true if file was renamed ok or false otherwise.
          */
          function rename( $inFile, $outFile = null )
          {
              // check how many parameters we have
              if( $outFile == null ) {
                  $outFile = $inFile;
                  $inFile  = $this->_fileName;
              }

              // Checkt the $inFile and $outFile are the same file or not
              if ( realpath( dirname( $inFile ) ) == realpath( dirname( $outFile ) ) &&
                   basename( $inFile ) == basename( $outFile ) )
                  return true;
                              
              // In order to work around the bug in php versions older
              // than 4.3.3, where rename will not work across different
              // partitions, this will be a copy and delete of the original file
            
              // copy the file to the new location
              if (!copy($inFile, $outFile)) {
                  // The copy failed, return false
                  return false;
              }
            
              // Now delete the old file
              // NOTICE, we are not checking the error here.  It is possible 
              // the the original file will remain and the copy will exist.
              //
              // One way to potentially fix this is to look at the result of
              // unlink, and then delete the copy if unlink returned FALSE, 
              // but this call to unlink could just as easily fail
              unlink( $inFile );
            
              return true;;
          }
		 
		 /**
		  * copies a file from one place to another.
		  * This method is always static
		  *
		  * @param inFile
		  * @param destFile
		  * @return True if successful or false otherwise
		  * @static
		  */
		 function copy( $inFile, $outFile )
		 {
			 return @copy( $inFile, $outFile );
		 }
		 
		 /**
		  * changes the permissions of a file, via PHP's chmod() function
		  *
		  * @param inFile The name of the file whose mode we'd like to change
		  * @param mode The new mode, or if none provided, it will default 
		  * to 0644
		  * @return true if successful or false otherwise 
		  * @static
		  */
		 function chMod( $inFile, $mode = 0644 )
		 {
			 return chmod( $inFile, $mode );
		 }
		 
		 /**
		  * returns true if the file exists.
		  *
		  * Can be used as an static method if a file name is provided as a
		  *  parameter
		  * @param fileName optinally, name of the file whose existance we'd
		  * like to check
		  * @return true if successful or false otherwise
		  */
		 function exists( $fileName = null ) 
		 {
			 if( $fileName == null )
				 $fileName = $this->_fileName;
				 
			clearstatcache();
			 
			 return file_exists( $fileName );
		 } 

         /** 
          * returns true if the file could be touched
          *
          * Can be used to create a file or to reset the timestamp.
          * @return true if successful or false otherwise
          * @see PHP Function touch()
          *
          */
         function touch( $fileName = null )
         {
             if( $fileName == null )
                 return false;

             return touch($fileName);
         }

         /** 
          * returns the basename of a file
          *
          * @return basename of the file
          * @see PHP Function basename()
          *
          */
         function basename( $fileName = null )
         {
             if( $fileName == null )
                 return false;

             $basename = preg_replace( '/^.+[\\\\\/]/', '', $fileName );

             return $basename;
         }

		/**
		 * "Cleans" up a path by removing all references to relative paths. It works in a way similar to 
		 * PHP's own realpath() but it does not perform any kind of check to make sure that the path
		 * really exists in disk
		 *
		 * @param path
		 * @return String
		 * @static
		 */
		function expandPath($path)
		{
			$oldP = "";
			$newP = $path;
			while( $newP != $oldP) {
				$oldP = $newP;
				$newP = preg_replace("/([^\/]+\/)([^\/]+\/)(\.\.\/)/ms","$1",$newP);
			}
			$result = str_replace("//","",str_replace("./","",$newP));

			if($result[strlen($result)-1] == "/" )
				$result = substr( $result, 0, strlen($result)-1);

			return( $result );
		}
	 }
?>
