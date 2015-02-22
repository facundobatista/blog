<?php

	/**
	 * \ingroup File
	 *
	 * given an array with strings and a folder on disk, this class will scan the folder and compare
	 * them with the ones in the array and return which ones from disk do NOT exist in the given array.
	 * In order to get the array with the elements that are new according to our first array, please
	 * use FileFinder::getNew()
	 * @see getNew
	 */
	class FileFinder 
	{
		var $_folder;
		var $_removed;
		var $_new;
		var $_recursive;

		function FileFinder( $folder = '.' )
		{
			$this->_folder = $folder;
			$this->_removed = Array();
			$this->_new = Array();
			$this->_recursive = true;
		}

		/**
		 * given a file, generates a key for it so that it can be checked whether
		 * it really belongs to the $currentList array or not. This is useful in case of when we're
		 * for example looking for new template files and we'd like to know whether locale_en_UK.php
		 * is alreasdy in the list or not... This class can be extended to reimplement getKeyForFile
		 * so that $this->getKeyForFile( "locale_en_UK.php" ) returns "en_UK" and so forth.
		 * By default is returns the same name but if null is returned for a particular file, this
		 * file will be ignored.
		 *
		 * @param fileName tha name of the file
		 * @return the key for the given file. If null is returned, this file will be ignored.
		 */
		function getKeyForFile( $fileName )
		{
			return( $fileName );
		}

		/**
 	 	 * uses Glob::glob() to find files that match the given format
	 	 * and compares the file names against the ones already available
		 * 
		 * @param currentList
		 * @param fileName
		 */
		function find( $currentList, $fileName = '*' )
		{
			lt_include( PLOG_CLASS_PATH.'class/misc/glob.class.php' );		
			$files = Glob::myGlob( $this->_folder, $fileName );
			
			// create an empty array if we got something else other than an array so that
			// we can avoid some ugly error messages!
			if( !is_array($currentList))
				$currentList = Array();

			// loop through the files...
			foreach( $files as $file ) {
				// get the key for the given file
				$key = $this->getKeyForFile( $file );
				if( $key != null && !in_array( $key, $currentList )) {
					// the file is new!
					$this->_new[] = $key;
				}
			}

			// and now see which files are new and which ones have been removed, by comparing
			// both arrays...

			return true;
		}

        function findBinary( $binary, $searchFolders ) {
            if( $searchFolders == null )
                $searchFolders = Array( $this->_folder );

			$found = false;
			$i = 0;
			while( !$found && $i < count($searchFolders)) {
				// get the current folder
				$currentFolder = $searchFolders[$i];
				// see if the file's there
				$fullPath = $currentFolder.$binary;
				if( File::isReadable( $fullPath )) 
					$found = true;
				else
					$i++;
			}
			
			if( $found ) 
				return $fullPath;
			else
				return "";
        }
	
		/**
		 * returns all the new items that are not in the list that was passed as a parameter to the
		 * FileFinder::find() method
		 *
		 * @return An array with the new items
		 */
		function getNew()
		{
			return( $this->_new );
		}
	}
?>
