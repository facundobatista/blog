<?php

	/**
     * \ingroup Misc	
	 *
	 * This class is a bunch of helper methods that will calculate the MD5 of the files within
	 * the given list of folders, generate and write a PHP array to disk and compare a reference
	 * array with pre-calculated MD5 hashes of files against the current MD5 hashes of those files 
	 * in disk.
	 * 
	 * This is not a general purpose class so if you're confused about its description
	 * it probably is not what you're looking for.
	 */
	class IntegrityChecker
	{
		
		/**
		 * Given an array with folder paths, generate another associative array where the key is the path and file
		 * name and the value is the MD5 hash of the contents of the file.
		 *
		 * @param directories Array containing the paths of the directories that should be checked. If no parameter is specified
		 * it defaults to "class", "templates/admin", "templates/wizard", "templates/rss", "templates/summary", "templates/default",
		 * all of them under the PLOG_CLASS_PATH base folder
		 * @param recursive Whether to recursively process the given folders
		 * @param includeDirs Determines whether the folder names and paths should be included in the output array, defaults to 'false'
		 * @param ignore Array containing file patterns that will be ignored and not included in the output array
		 * @return An associative array
		 */
		function generateMD5Folder( $directories = Array( "class", "templates/admin", "templates/wizard", "templates/rss", "templates/summary", "templates/default" ), $recursive = true, $includeDirs = false, $ignore = Array( "*.svn", ".DS_Store", "test" )) 
		{
			$result = Array();
			foreach( $directories as $directory ) {
				$result = array_merge( $result, IntegrityChecker::directoryToMD5Array( $directory, $recursive, $includeDirs, $ignore ));
			}
			
			return( $result );
		}

		/**
		 * Generates an MD5 array, optinally recursively, of the given single folder. 
		 *
		 * @param directory
		 * @param recursive
		 * @param includeDirs
		 * @param ignore
		 * @return An associative array
		 * @see IntegrityChecker::generateMD5Folder()
		 */
		function directoryToMD5Array( $directory, $recursive = true, $includeDirs = false, $ignore = Array( "*.svn" )) 
		{
			$array_items = array();
			if ($handle = opendir($directory)) {
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != ".." && !IntegrityChecker::canIgnore( $file, $ignore )) {
						if (is_dir($directory. "/" . $file)) {
							if($recursive) {
								$array_items = array_merge($array_items, IntegrityChecker::directoryToMD5Array($directory. "/" . $file, $recursive, $includeDirs, $ignore ));
							}
							if( $includeDirs ) {
								$file = $directory . "/" . $file;
								$array_items[preg_replace("/\/\//si", "/", $file)] = 0;
							}
						} 
						else {
							$file = $directory . "/" . $file;
							$array_items[preg_replace("/\/\//si", "/", $file)] = md5(file_get_contents(preg_replace("/\/\//si", "/", $file)));
						}
					}
				}
				closedir($handle);
			}
			return $array_items;
		}
		
		/**
		 * @return Returns true if the given file matches any of the patterns in the $ignore array
		 * @param file
		 * @param ignore
		 * @private
		 */
		function canIgnore( $file, $ignore )
		{
			lt_include( PLOG_CLASS_PATH."class/misc/glob.class.php" );			
			
			$result = false;
			foreach( $ignore as $pattern ) {
				if( Glob::fnmatch( $pattern, $file )) {
					$result = true;
					break;					
				}
			}
			
			return( $result );
		}
		
		/**
		 * Writes the list of files and MD5 hashes to the destination file. If no destination file is given
		 * PLOG_CLASS_PATH/install/files.properties.php is used
		 *
		 * @param folders Array containing the names of the folders to include
		 * @param dest Name of the destination file
		 */
		function writeMD5ListToFile( $folders = Array( "class", "templates/admin", "templates/wizard", "templates/rss", "templates/summary", "templates/default" ), $dest = "" )
		{
			lt_include( PLOG_CLASS_PATH."class/file/file.class.php" );
			
			if( $dest == "" ) 
				$dest = PLOG_CLASS_PATH."install/files.properties.php"; 
			
			$currentData = IntegrityChecker::generateMD5Folder( $folders );
			
			// open the file and write the headers
			$file = new File( $dest );
			$file->open( "w+" );
			$file->write( "<?php\n" );
			$file->write( "\$data = Array(\n");
			
			$line = 1;
			foreach( $currentData as $f => $md5 ) {
				$file->write( "\"$f\" => \"$md5\"");
				if( $line < count($currentData ))
					$file->write( "," );
				$file->write( "\n" );
				$line++;
			}
			
			// write the footer and close the file
			$file->write( ");\n");
			$file->write( "?>" );
			$file->close();
			
			return( true );
		}
		
		/**
		 * Given the $reference reference array and a list of folders, this method
		 * will calculate the MD5 hashes of the given folders and compare them against the ones
		 * in the reference array. 
		 *
		 * @param reference
		 * @param folders
		 * @return An associative array containing the name of the files whose MD5 hash does not match
		 * the current one
		 */
		function checkIntegrity( $reference, $folders = Array( "class", "templates/admin", "templates/wizard", "templates/rss", "templates/summary", "templates/default" ) )
		{
			$currentData = IntegrityChecker::generateMD5Folder( $folders );
			
			foreach( $currentData as $file => $md5 ) {
				if( isset( $reference[$file] )) {
					if( $reference[$file] == $md5 )
						unset( $reference[$file]);
				}
			}
			
			return( $reference );
		}
	}
?>