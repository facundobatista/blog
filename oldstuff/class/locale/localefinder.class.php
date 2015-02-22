<?php

	lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );
	lt_include( PLOG_CLASS_PATH."class/file/finder/filefinder.class.php" );
	
	/**
	 * \ingroup Locale
	 *
 	 * updates the list of locales based on what we can find on disk. It will add new ones
	 * and remove old ones.
	 *
	 * @see FileFinder
	 */
	class LocaleFinder extends FileFinder
	{
		function LocaleFinder( $localeFolder = null )
		{
			if( $localeFolder == null )
				$localeFolder = Locale::getLocaleFolder();

			$this->_localeFolder = $localeFolder;
			
			$this->FileFinder( $this->_localeFolder );
		}
	
		/**
		 * reimplemented from FileFinder::getKeyForFile so that we can adapt locale filenames
	 	 * to look like keys from our array
		 *
		 * @see FileFinder::getKeyForFile()
		 */
		function getKeyForFile( $fileName )
		{
		      // this regexp allows for more permissive locale names so that people can 
		      // at least have things like locale_xx_YY_utf-8.php
			$result = preg_match( REGEXP_VALID_LOCALE, $fileName, $matches );
			
			// in case there is some crap in the locale/ folder, then ignore
			// it (that is, if the file name does not match our regexp!
			if( !$result ) 
				$fileKey = null;
			else
				$fileKey = $matches[1];
			
			return( $fileKey );
		}

		/**
		 * provides default parameters for the FileFinder::find() method
	 	 *
		 * @see FileFinder::find()
		 * @return An array with the new locales found in the disk
		 */
		function find()
		{
			// first find the new ones
			parent::find( Locales::getAvailableLocales(), "locale_*.php" );
			
			// and then return them, if any
			return( $this->getNew());
		}
	}
?>