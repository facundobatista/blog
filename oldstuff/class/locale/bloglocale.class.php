<?php

    /**
	 * \ingroup Locale
	 *
	 * @see Locale
	 * @see Locales
     */

    lt_include( PLOG_CLASS_PATH."class/locale/locale.class.php" );

	class BlogLocale extends Locale  
	{

		var $_defaultFolder;
		var $_code;	// our ISO locale code, eg. es_ES, en_UK, etc
        var $_cache;
		var $_messages;
        var $_charset;
		var $_description;
		var $_dateFormat;
		var $_firstDayOfWeek;
		var $_dataLoaded;

        /**
         * Constructor.
         *
         * @param $code Code follows the Java naming scheme: language_COUNTRY, so
         * for example if we want to have the texts translated in the English spoken
         * in the UK, we'd have to use en_UK as the code. The two digits country
         * code and language code are ISO standards and can be found in
         * http://userpage.chemie.fu-berlin.de/diverse/doc/ISO_3166.html (country codes) and
         * http://userpage.chemie.fu-berlin.de/diverse/doc/ISO_639.html (language codes)
         */
		function BlogLocale( $code )
		{
			$this->Locale( $code );
		}

        /**
         * @private
         */
		function _loadLocaleFile()
		{
            $this->_defaultFolder = $this->getLocaleFolder();
  
            $fileName = $this->_defaultFolder."/locale_".$this->_code.".php";

            if( File::isReadable( $fileName )){
                    // TODO: check to see that this is only called once??
                include( $fileName );
            }

			// The following is just to handle the case where there isn't
			// a valid local file.
			if ( !isset($messages) || !is_array( $messages ) ) {
				$messages = array();
			}
			$this->_messages = $messages;
			
			$this->_dataLoaded = true;

            unset($messages);
		}
	}
?>