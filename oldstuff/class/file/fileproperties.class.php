<?php

	lt_include( PLOG_CLASS_PATH."class/config/properties.class.php" );
	lt_include( PLOG_CLASS_PATH."class/file/file.class.php" );

	/**
	 * \ingroup File
	 *
	 * Extends the class Properties so that it can read the values
	 * from a file, which has the format 'name = value'. This class is not used
	 * anywhere anymore but has been left for compatibility reasons.
	 */
	class FileProperties extends Properties 
	{

		var $_file;
		var $_contents;

		// regexps used to match the strings that are comments
		var $_commentRegExps = Array( 1 => "/^\/\/\s*(.*)/i",
		                              2 => "/^#\s*(.*)/i",
					      3 => "/^\/\*(.*)\*\//i" );

		/**
		 * Constructor of the class
		 *
		 * @param fileName Name of the file where data will be loaded from.
		 */
		function FileProperties( $fileName )
		{
			$this->Properties();

			$this->_file = new File( $fileName );

			$this->_load();

			$this->_process();
		}

		/**
		 * Loads the contents from the file, parses them and puts them
		 * into values
		 *
		 * @private
		 */
		function _load()
		{
			// open and load the contents of the file
			$this->_file->open( "r" );

			$this->_contents = $this->_file->readFile();
		}

		/**
		 * @private
		 */
		function _removeBlanks( $string, $where = 1 )
		{
			if( $where == 1 ) { // remove from the beginning
				$string = preg_replace( "/^( +)/", "", $string );
			}
			if( $where == 2 ) { // remove from the end
				$string = preg_replace( "/( +)$/", "", $string );
			}

			return $string;
		}

		/**
		 * Returns true if the specified line is a comment or not
		 *
		 * @private
		 */
		function _isComment( $line )
		{
			foreach( $this->_commentRegExps as $regexp ) {
				if( preg_match( $regexp, $line ))
					return true;
			}

			return false;
		}

		/**
		 * @private
		 */
		function _isNumber( $string )
		{
			return is_numeric( $string );
		}

		/**
		 * Parses the lines of the file.
		 * The following characters are considered to be comments, and can only
		 * appear in the beginning of the line:
		 * //, #
		 *
		 * The rest of the lines must either be empty or have the format
		 * key = value
		 *
		 * @private
		 */
		function _process()
		{
			foreach( $this->_contents as $line ) {
				// remove blank spaces from the beginning, if any
				$line = $this->_removeBlanks( $line );

				// if not empty, we deal with it
				if( $line != "" ) {
					// if line starts with any of the characters indicating
					// coments, we ignore it
					if( !$this->_isComment( $line )) {
						// we have to split the line now
						$parts = explode( "=", $line, 2 );
						// eliminate any trailing blank spaces
						$parts[0] = trim( $parts[0] );
						$parts[1] = trim( $parts[1] );
						$parts[1] = $this->_removeBlanks( $parts[1] );

						// detect if it is a number or not
						if( $this->_isNumber( $parts[1] )) {
							$value = (int)$parts[1];
							$parts[1] = $value;
						}

						// special handling for the boolean types
						if( $parts[1] == "true" )
							$parts[1] = true;
						elseif ( $parts[1] == "false" )
							$parts[1] = false;

						// and now we save the name and the value
						$this->setValue( $parts[0], $parts[1] );
					}
				}
			}
		}
	}
?>
