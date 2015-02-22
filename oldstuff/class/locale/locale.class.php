<?php

    /**
	 * \defgroup Locale
	 *
	 * The Locale module is used for localization purposes. Its main class is the Locale class
	 * that is capable of loading very simple php files containing a big array of string identifiers
	 * and their translations. The main method of the class is the Locale::tr() that given a string
	 * identifier, will return the translated version.
	 *
	 * Locale files can also specify default encodings and very basic date and time formats (this will
	 * be improved in next version) At the moment it is also possible to use right-to-left languages
	 * even though there is none available yet.
	 *
	 * The Locales class is the preferred way to load translations from disk since it has
	 * caching mechanisms so that we don't have to load the data everytime from disk.
	 *
	 * Please see http://wiki.lifetype.net/index.php/Translating_LifeType for more information regading
	 * how to work with locale files in LifeType.
	 *
	 * @see Locale
	 * @see Locales
     */

	
    lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );

	define( "DEFAULT_LOCALE_FOLDER", PLOG_CLASS_PATH . "locale" );
	
	/**
	 * default locale format that will be used if none available
	 */
	define( "DEFAULT_LOCALE_FORMAT", "%d/%m/%Y %H:%M" );
	
	/**
	 * default encoding if the locale does not specify one
	 */
	define( "DEFAULT_ENCODING", "iso-8859-1" );
    
    /**
     * default direction for texts, if the locale does not specify one
     */
	define( "DEFAULT_DIRECTION", "ltr" );
	
    /**
     * default first day fot calendar
     */
	define( "DEFAULT_FIRST_DAY_OF_WEEK", 1 );

    /**
	 * \ingroup Locale
	 *
     * Class used to localize messages and things such as dates and numbers.
     *
     * To use this class, we will have to provide a file containing an array
     * of the form:
     *
     * <pre>
     * $messages["identifier"] = "Translated text"
     * </pre>
     *
     * The file will be loaded when creating this object and must be called following
     * the same scheme: locale_lang_COUNTRY (see constructor on locales namig schemes)
     *
     * When we want to translate a string, we will have to use its identifier, that will
     * be looked up in the array containing all the messages. If there is a message for that
     * identifier, it will be returned or a empty string otherwise.
     *
     * This class is extensively used throughout the templates to localize texts, dates
     * and numbers, being the formatDate function one of the most importants of this class.
     *
     * <b>IMPORTANT:</b> For performance reasons, it is recommended to use the Locales::getLocale
     * method instead of creating new Locale objects every time we need one. The getLocale methods
     * offers caching capabilities so that the file with the messages will not need to be fetched
     * every time from disk.
     * @see Locales::getLocale()
     */
	class Locale  
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
		function Locale( $code )
		{	
            $this->_code = $code;

            $this->_loadLocaleInfo();

            if( $this->_charset == "" )
                $this->_charset = DEFAULT_ENCODING;

			$this->_dataLoaded = false;
		}

        /**
         * @private
         */
		function _loadLocaleFile()
		{			
            $this->_defaultFolder = $this->getLocaleFolder();
  
			// load the blog locale
            $fileName = $this->_defaultFolder."/locale_".$this->_code.".php";
            if( File::isReadable( $fileName )){
                    // TODO: this function is only called once, right?
                include( $fileName );
            }
			if ( !isset($messages) || !is_array( $messages ) ) {
				$messages = array();
			}
			$this->_messages = $messages;
			
			// load the admin locale
			$fileName = $this->_defaultFolder."/admin/locale_".$this->_code.".php";
            if( File::isReadable( $fileName )){
                    // TODO: this function is only called once, right?
                include( $fileName );
            }
			if ( !isset($messages) || !is_array( $messages ) ) {
				$messages = array();
			}
			$this->_messages = array_merge( $this->_messages, $messages );
			
			$this->_dataLoaded = true;

            unset($messages);
		}
		
		/**
		 * @private
		 * Loads the locale file, extracts the information needed and frees the used memory
		 */
		function _loadLocaleInfo()
		{

            if( !is_array($this->_messages) ) {
                // load the locale into $this->_messages
                $this->_loadLocaleFile();
            }
			
			// get the info that we need
			$this->_description = isset($this->_messages["locale_description"]) ? $this->_messages["locale_description"] : "no description for " . $this->_code;
            $this->_charset = isset($this->_messages["encoding"]) ? $this->_messages["encoding"] : DEFAULT_ENCODING;
			$this->_direction = isset($this->_messages["direction"]) ? $this->_messages["direction"] : DEFAULT_DIRECTION;
			$this->_dateFormat = isset($this->_messages["date_format"]) ? $this->_messages["date_format"] : DEFAULT_LOCALE_FORMAT;
			$this->_firstDayOfWeek = isset($this->_messages["first_day_of_week"]) ? $this->_messages["first_day_of_week"] : DEFAULT_FIRST_DAY_OF_WEEK;
			
			unset( $this->_messages );
			
			$this->_messages = NULL;
			
			return( true );
		}

        /**
         * Returns the character encoding method used by the current locale file. It has to be a valid
         * character encoding, since it will be used in the header of the html file to tell the browser
         * which is the most suitable encoding that should be used.
         *
         * @return A valid character encoding method.
         */
        function getCharset()
        {
        	return $this->_charset;
        }
		
		/** 
		 * returns the direction in which this language is written.
		 * Possible values are, as with the html standard, "rtl" or "ltr"
		 *
		 */
		function getDirection()
		{
			$direction = $this->_direction;
			if( $direction != "rtl" )
				$direction = "ltr";
				
			return $direction;
		}

        /**
         * Returns an optional locale description string that can be included in the
         * locale file with the other texts.
         *
         * @return A string describing the locale file.
         */
        function getDescription()
        {
        	return( $this->_description );
        }

        /**
         * @static
         */
        function getLocaleFolder()
        {
            $config =& Config::getConfig();

            $localeFolder = $config->getValue( "locale_folder" );
            if( $localeFolder == "" || $localeFolder == "./locale")
            	$localeFolder = DEFAULT_LOCALE_FOLDER;

            return $localeFolder;
        }

        /**
         * Changes the locale to something else than what we chose in the first place when
         * creating the object.
         *
         * @param code follows the same format as in the constructor.
         */
		function setLocale( $code )
		{
			$this->_code = $code;

			$this->_loadLocaleFile();
		}

		/**
		 * returns all the strings
		 *
		 * @return An array containing all the strings that this locale supports
		 */
		function getStrings()
		{
			// load the file if it hadn't been loaded yet		
			if( !is_array($this->_messages))
				$this->_loadLocaleFile();
		
			return $this->_messages;
		}

        /**
         * Translates a string
         *
         * @param id Identifier of the message we would like to translate
         */
		function tr( $id )
		{
			// load the file if it hasn't been loaded yet
			if( !$this->_dataLoaded ) {
				$this->_loadLocaleFile();
            }

			isset( $this->_messages[$id] ) ? $string = $this->_messages[$id] : $string = $id;
			if( $string == "" ) $string = $id;

			return $string;
		}

        /**
         * Alias for getString
         * @see getString
         */
		function i18n( $id )
		{
			return $this->getString( $id );
		}

		/**
		 * calls printf on the translated string.
		 *
		 * Crappy Crappy! Since it only accepts three arguments... ;) Well, if we
		 * ever need more than three, I'll change it!
         * @private
		 */
		function pr( $id, $arg1 = null, $arg2 = null, $arg3 = null )
		{
			// first of all, we translate the string
			$str = $this->tr( $id );
			if( $arg1 == null )
				$result = $str;
			else if( $arg2 == null )
				$result = sprintf( $str, $arg1 );
			else if( $arg3 == null )
				$result = sprintf( $str, $arg1, $arg2 );
			else
				$result = sprintf( $str, $arg1, $arg2, $arg3 );

			return $result;
		}

        /**
         * Returns the complete code
         *
         * @return The Locale code
         */
        function getLocaleCode()
        {
        	return $this->_code;
        }

        /**
         * Returns the two-character language code
         *
         * @return The two-character language code
         */
		function getLanguageId()
		{
            $countryId = substr( $this->_code, 3, 5 ); 
            if ( strcmp( $countryId, "TW" ) == 0 || strcmp( $countryId, "CN") ==0 ) { 
                $langId = substr( $this->_code, 0, 2 ); 
                return $langId."-".$countryId;
            } 
            else { 
                return substr( $this->_code, 0, 2 ); 
            } 
		}

        /**
         * Returns the two-character country code
         *
         * @return The two-character country code.
         */
		function getCountryId()
		{
			return substr( $this->_code, 3, 5 );
		}

        /**
         * Returns the first day of the week, which also depends on the country
         *
         * @return Returns 0 for Sunday, 1 for Monday and so on...
         */
        function firstDayOfWeek()
        {
            return $this->_firstDayOfWeek;
        }

        /**
         * Returns all the months of the year
         *
         * @return Returns an array containing the names of the months, where the
         * first one is January.
         */
        function getMonthNames()
        {
			// load the file if it hadn't been loaded yet		
			if( !is_array($this->_messages))
				$this->_loadLocaleFile();
		
        	return $this->_messages["months"];
        }

        /**
         * Returns the days of the week
         *
         * @return Returns the names of the days of the week, where the first one is
         * Sunday.
         */
        function getDayNames()
        {
			// load the file if it hadn't been loaded yet		
			if( !is_array($this->_messages))
				$this->_loadLocaleFile();
		
        	return $this->_messages["days"];
        }

        /**
         * Returns the shorter version of the days of the week
         *
         * @return Returns an array with the days of the week abbreviated, where the first
         * one is Sunday.
         */
        function getDayNamesShort()
        {
			// load the file if it hadn't been loaded yet		
			if( !is_array($this->_messages))
				$this->_loadLocaleFile();		
		
        	return $this->_messages["daysshort"];
        }
		
		function _getOrdinal( $num )
		{
			// first we check the last two digits
			$last_two_digits = substr( $num, -2 );
			if( $last_two_digits == "11" )
				$value = $num."th";
			elseif( $last_two_digits == "12" )
				$value = $num."th";
			elseif( $last_two_digits == "13" )
				$value = $num."th";
			else {
				// we get the last digit
				$last_digit = substr( $num, -1 );
				
				if( $num < 10 )
					$num = $last_digit;
				
				if( $last_digit == "1" )
					$value = $num."st";
				elseif( $last_digit == "2" )
					$value = $num."nd";
				elseif( $last_digit == "3" )
					$value = $num."rd";
				else
					$value = $num."th";
			}
			
			return $value;
		}		
		
		/**
		 * Returns the day in an ordinal format, i.e. 1st, 2nd, 3rd, etc (in English)
         *
         * @return A string with the ordinal representation of the day.
		 */
		function getDateOrdinal( $date )
		{
			$dayOrdinal = $date;
			$last_digit = substr( $dayOrdinal, -1 );
			if( $dayOrdinal < 10 )
				$dayOrdinal = $last_digit;
			
			switch( $this->getLanguageId()) {
				case "es":
				case "ca":
					break;
				case "de":
				case "fi":
					$dayOrdinal .= "."; break;
				case "en":
				default:
					$dayOrdinal = $this->_getOrdinal( $date); break;
			}
			
			return $dayOrdinal;
		}	

		function getDayOrdinal( $t )
		{
			$dayOrdinal = $t->getDay();
			return $this->getDateOrdinal( $dayOrdinal );
		}	


        /**
		 * Formats the date of a Timestamp object according to the given format:
		 *
         * (compatible with PHP):<ul>
		 * <li>%a abbreviated weekday</li>
		 * <li>%A	complete weekday</li>
		 * <li>%b	abbreviated month</li>
		 * <li>%B	long month</li>
		 * <li>%d	day of the month, 2 digits with leading zero</li>
         * <li>%j   day of the month, numeric (without leading zero)</li>
		 * <li>%H	hours, in 24-h format</li>
		 * <li>%I	hours, in 12-h format (without leading zero)</li>
		 * <li>%p   returns 'am' or 'pm'</li>
		 * <li>%P   returns 'AM' or 'PM'</li>
		 * <li>%M	minutes</li>
		 * <li>%m	month number, from 00 to 12</li>
		 * <li>%S	seconds</li>
		 * <li>%y	2-digit year representation</li>
		 * <li>%Y	4-digit year representation</li>
		 * <li>%O   Difference to Greenwich time (GMT) in hours, format will be +0000</li>
		 * <li>%G   Difference to Greenwich time (GMT) in hours, format will be +00:00</li>
		 * <li>%%	the '%' character
         * </ul>
         * (these have been added by myself and are therefore incompatible with php)<ul>
         * <li>%T	"_day_ of _month_", where the day is in ordinal form and 'month' is the name of the month</li>
         * <li>%D	cardinal representation of the day</li>
         * </ul>
		 */
		function formatDate( $timeStamp, $format = null, $blog = null )
		{
			// load the file if it hadn't been loaded yet		
			if( !is_array($this->_messages))
				$this->_loadLocaleFile();	
		
			// if the user did not specify a format, let's use the default one
			if( $format == null )
				$format = $this->_dateFormat;
				
            // Get the unix time stamp 
			if( strtolower(get_class( $timeStamp )) == "timestamp") {
            	$time = $timeStamp->getTimestamp(DATE_FORMAT_UNIXTIME);
			}
			else {
				// we can assume it's a unix timestamp
				$time = $timeStamp;
				$timeStamp = new Timestamp( $time );
			}
			
            $timeZoneSec = date("Z", $time);
            if ( $blog ) {
                //
                // The blog was specified.  Use it to get the time offset
                //
                $timeDiff = 0;
                $blogSettings = $blog->getSettings();
                $timeDiff = $blogSettings->getValue( 'time_offset' );

                // The following line relies on the fact that the result will
                // be an int.
                $timeZoneSec += ( $timeDiff * 3600 );
            }				
				
			$text = $format;
				
			if( strpos( $text, "%a" ) !== FALSE ) {
	            $weekdayId = $timeStamp->getWeekdayId();
	            $weekday = $this->_messages["days"][$weekdayId];
	            if( !empty( $this->_messages["weekdaysshort"] ) )
	            	$shortWeekday = $this->_messages["weekdaysshort"][$weekdayId];
	            else
	            	$shortWeekday = function_exists('html_entity_decode') ? htmlentities(substr(html_entity_decode($weekday), 0, 3 )) : substr($weekday, 0, 3);	
				$text = str_replace( "%a", $shortWeekday, $text );
			}
			if( strpos( $text, "%A" ) !== FALSE ) {
	            $weekdayId = $timeStamp->getWeekdayId();	
				$text = str_replace( "%A", $this->_messages["days"][$weekdayId], $text );	
			}
			if( strpos( $text, "%b" ) !== FALSE ) {
	            $monthId    = (int)$timeStamp->getMonth();
	            $monthStr   = $this->_messages["months"][$monthId-1];
	            if( !empty( $this->_messages["monthsshort"] ) )
	            	$shortMonthStr = $this->_messages["monthsshort"][$monthId-1];
	            else
	            	$shortMonthStr = function_exists('html_entity_decode') ? htmlentities(substr(html_entity_decode($monthStr), 0, 3 )) : substr($monthStr, 0, 3);

				$text = str_replace( "%b", $shortMonthStr, $text );				
			}
			if( strpos( $text, "%B" ) !== FALSE ) {
	            $monthId = (int)$timeStamp->getMonth();
				$text = str_replace( "%B", $this->_messages["months"][$monthId-1], $text );				
			}	
			if( strpos( $text, "%d" ) !== FALSE ) {
				$text = str_replace( "%d", ($timeStamp->getDay() < 10) ? "0".$timeStamp->getDay() : $timeStamp->getDay(), $text );
			}
			if( strpos( $text, "%e" ) !== FALSE ) {
				$text = str_replace( "%e", intval($timeStamp->getDay()), $text );
			}
			if( strpos( $text, "%j" ) !== FALSE ) {
				$text = str_replace( "%j", $timeStamp->getDay(), $text );
			}
			if( strpos( $text, "%H" ) !== FALSE ) {
				$text = str_replace( "%H", $timeStamp->getHour(), $text );				
			}			
			if( strpos( $text, "%I" ) !== FALSE ) {
				$text = str_replace( "%I", ($timeStamp->getHour() != 0) ? ($timeStamp->getHour() > 12) ? $timeStamp->getHour()-12 : $timeStamp->getHour()+0 : 12, $text );	
			}
			if( strpos( $text, "%p" ) !== FALSE ) {
				$text = str_replace( "%p", $timeStamp->getHour() >= 12 ? "pm" : "am", $text );					
			}
			if( strpos( $text, "%P" ) !== FALSE ) {
				$text = str_replace( "%P", $timeStamp->getHour() >= 12 ? "PM" : "AM", $text );				
			}
			if( strpos( $text, "%M" ) !== FALSE ) {
				$text = str_replace( "%M",  $timeStamp->getMinutes(), $text );					
			}
			if( strpos( $text, "%m" ) !== FALSE ) {
				$text = str_replace( "%m", $timeStamp->getMonth(), $text );				
			}
			if( strpos( $text, "%S" ) !== FALSE ) {
				$text = str_replace( "%S", $timeStamp->getSeconds(), $text );					
			}
			if( strpos( $text, "%y" ) !== FALSE ) {
				$text = str_replace( "%y", substr($timeStamp->getYear(), 2, 4 ), $text );									
			}
			if( strpos( $text, "%Y" ) !== FALSE ) {
				$text = str_replace( "%Y", $timeStamp->getYear(), $text );				
			}
			if( strpos( $text, "%O" ) !== FALSE ) {
	            // Now convert the time zone seconds to hours and minutes
	            $timeZoneHours = intval( abs($timeZoneSec) / 3600 );
	            $timeZoneMins = intval(( abs($timeZoneSec) % 3600 ) / 60 );
	            $timeZoneDirection = ($timeZoneSec < 0 ) ? "-" : "+";				
				
				$text = str_replace( "%O", sprintf( "%s%02d%02d", $timeZoneDirection, $timeZoneHours, $timeZoneMins ), $text );				
			}					
			if( strpos( $text, "%G" ) !== FALSE ) {
	            // Now convert the time zone seconds to hours and minutes
	            $timeZoneHours = intval( abs($timeZoneSec) / 3600 );
	            $timeZoneMins = intval(( abs($timeZoneSec) % 3600 ) / 60 );
	            $timeZoneDirection = ($timeZoneSec < 0 ) ? "-" : "+";				
				
				$text = str_replace( "%G", sprintf( "%s%02d:%02d", $timeZoneDirection, $timeZoneHours, $timeZoneMins ), $text );				
			}
			if( strpos( $text, "%%" ) !== FALSE ) {
				$text = str_replace( "%%", "%", $text );				
			}
			if( strpos( $text, "%T" ) !== FALSE ) {
	            $monthId    = (int)$timeStamp->getMonth();
	            $monthStr   = $this->_messages["months"][$monthId-1];				
	
				$text = str_replace( "%T", $this->getDayOrdinal( $timeStamp )." ".$this->tr("of")." ".$monthStr, $text );				
			}			
			if( strpos( $text, "%D" ) !== FALSE ) {
				$text = str_replace( "%D", $this->getDayOrdinal( $timeStamp ), $text );				
			}
			
    		if ( $this->_code == 'fa_IR' )
    		{			
	    		lt_include( PLOG_CLASS_PATH."class/data/jalalicalendar.class.php" );
	            list( $jyear, $jmonth, $jday ) = JalaliCalendar::gregorian_to_jalali(gmdate( "Y", $time ), gmdate( "m", $time ), gmdate( "d", $time ));
	
				if( strpos( $text, "%q" ) !== FALSE ) {
		      		$text = str_replace( "%q", JalaliCalendar::Convertnumber2farsi($jyear), $text );				
				}			
				if( strpos( $text, "%w" ) !== FALSE ) {
		      		$text = str_replace( "%w", JalaliCalendar::Convertnumber2farsi($jmonth), $text );
				}			
				if( strpos( $text, "%o" ) !== FALSE ) {
		      		$text = str_replace( "%o", JalaliCalendar::Convertnumber2farsi($jday), $text );
				}			
				if( strpos( $text, "%R" ) !== FALSE ) {
		      		$text = str_replace( "%R", JalaliCalendar::monthname($jmonth), $text );
				}			
				if( strpos( $text, "%T" ) !== FALSE ) {
		      		$text = str_replace( "%T", JalaliCalendar::Convertnumber2farsi($timeStamp->getHour()), $text );					
				}			
				if( strpos( $text, "%U" ) !== FALSE ) {
		      		$text = str_replace( "%U", JalaliCalendar::Convertnumber2farsi($timeStamp->getMinutes()), $text );
				}
			}			

			return $text;
		}
		
        /**
		 * Formats the date of a Timestamp object according to the given format:
		 * This function assumes that the timestamp is local and it converts it
		 * to GMT before formatting.
		 *
         * (compatible with PHP):<ul>
		 * <li>%a abbreviated weekday</li>
		 * <li>%A	complete weekday</li>
		 * <li>%b	abbreviated month</li>
		 * <li>%B	long month</li>
		 * <li>%d	day of the month, 2 digits with leading zero</li>
         * <li>%j   day of the month, numeric (without leading zero)</li>
		 * <li>%H	hours, in 24-h format</li>
		 * <li>%I	hours, in 12-h format (without leading zero)</li>
		 * <li>%p   returns 'am' or 'pm'</li>
		 * <li>%P   returns 'AM' or 'PM'</li>
		 * <li>%M	minutes</li>
		 * <li>%m	month number, from 00 to 12</li>
		 * <li>%S	seconds</li>
		 * <li>%y	2-digit year representation</li>
		 * <li>%Y	4-digit year representation</li>
		 * <li>%O   Difference to Greenwich time (GMT) in hours (Will always be +0000)</li>
		 * <li>%G   Difference to Greenwich time (GMT) in hours (Will always be +00:00)</li>
		 * <li>%%	the '%' character
         * </ul>
         * (these have been added by myself and are therefore incompatible with php)<ul>
         * <li>%T	"_day_ of _month_", where the day is in ordinal form and 'month' is the name of the month</li>
         * <li>%D	cardinal representation of the day</li>
         * </ul>
		 */
        function formatDateGMT( $timeStamp, $format = null, $blog = null )
        {
	

			// load the file if it hadn't been loaded yet		
			if( !is_array($this->_messages))
				$this->_loadLocaleFile();	
		
			// if the user did not specify a format, let's use the default one
			if( $format == null )
				$format = $this->_dateFormat;
				
            // Get the unix time stamp 
			if( strtolower(get_class( $timeStamp )) == "timestamp") {
            	$time = $timeStamp->getTimestamp(DATE_FORMAT_UNIXTIME);
			}
			else {
				// we can assume it's a unix timestamp
				$time = $timeStamp;
				$timeStamp = new Timestamp( $time );
			}
            $timeZoneSec = date("Z", $time);
            if ( $blog ) {
                //
                // The blog was specified.  Use it to get the time offset
                //
                $timeDiff = 0;
                $blogSettings = $blog->getSettings();
                $timeDiff = $blogSettings->getValue( 'time_offset' );
                $timeDiff *= -1;
                
                if( $timeDiff > 0 )
                    $timeStamp->addSeconds( $timeDiff * 3600 );
                else
                    $timeStamp->subtractSeconds( $timeDiff * (-3600));
            }				
				
			$text = $format;				
				
			if( strpos( $text, "%a" )  !== FALSE ) {
            	$weekdayId = gmdate( "w", $time );
	            $weekday = $this->_messages["days"][$weekdayId];
	            if( !empty( $this->_messages["weekdaysshort"] ) )
	            	$shortWeekday = $this->_messages["weekdaysshort"][$weekdayId];
	            else
	            	$shortWeekday = function_exists('html_entity_decode') ? htmlentities(substr(html_entity_decode($weekday), 0, 3 )) : substr($weekday, 0, 3);
	
				$text = str_replace( "%a", $shortWeekday, $text );
			}
			if( strpos( $text, "%A" ) !== FALSE ) {
            	$weekdayId = gmdate( "w", $time );
				$text = str_replace( "%A", $this->_messages["days"][$weekdayId], $text );	
			}
			if( strpos( $text, "%b" ) !== FALSE ) {
            	$monthId = gmdate( "n", $time );
	            $monthStr   = $this->_messages["months"][$monthId-1];
	            if( !empty( $this->_messages["monthsshort"] ) )
	            	$shortMonthStr = $this->_messages["monthsshort"][$monthId-1];
	            else
	            	$shortMonthStr = function_exists('html_entity_decode') ? htmlentities(substr(html_entity_decode($monthStr), 0, 3 )) : substr($monthStr, 0, 3);

				$text = str_replace( "%b", $shortMonthStr, $text );				
			}
			if( strpos( $text, "%B" ) !== FALSE ) {
            	$monthId = gmdate( "n", $time );
				$text = str_replace( "%B", $this->_messages["months"][$monthId-1], $text );				
			}	
			if( strpos( $text, "%d" ) !== FALSE ) {
				$text = str_replace( "%d", gmdate( "d", $time ), $text );
			}
			if( strpos( $text, "%e" ) !== FALSE ) {
				$text = str_replace( "%e", intval(gmdate( "d", $time )), $text );
			}
			if( strpos( $text, "%j" ) !== FALSE ) {
				$text = str_replace( "%j", gmdate( "j", $time ), $text );
			}
			if( strpos( $text, "%H" ) !== FALSE ) {
				$text = str_replace( "%H", gmdate( "H", $time ), $text );				
			}			
			if( strpos( $text, "%I" ) !== FALSE ) {
				$text = str_replace( "%I", gmdate( "g", $time ), $text );	
			}
			if( strpos( $text, "%p" ) !== FALSE ) {
				$text = str_replace( "%p", gmdate( "a", $time ), $text );					
			}
			if( strpos( $text, "%P" ) !== FALSE ) {
				$text = str_replace( "%P", gmdate( "A", $time ), $text );				
			}
			if( strpos( $text, "%M" ) !== FALSE ) {
				$text = str_replace( "%M",  gmdate( "i", $time ), $text );					
			}
			if( strpos( $text, "%m") !== FALSE ) {
				$text = str_replace( "%m", gmdate( "m", $time ), $text );				
			}
			if( strpos( $text, "%S" ) !== FALSE ) {
				$text = str_replace( "%S", gmdate( "s", $time ), $text );					
			}
			if( strpos( $text, "%y" ) !== FALSE ) {
				$text = str_replace( "%y", gmdate( "y", $time ), $text );									
			}
			if( strpos( $text, "%Y" ) !== FALSE ) {
				$text = str_replace( "%Y", gmdate( "Y", $time ), $text );				
			}
			if( strpos( $text, "%O" ) !== FALSE ) {
				$text = str_replace( "%O", "+0000", $text );				
			}					
			if( strpos( $text, "%G" ) !== FALSE ) {
				$text = str_replace( "%G", "+00:00", $text );				
			}					
			if( strpos( $text, "%%" ) !== FALSE ) {
				$text = str_replace( "%%", "%", $text );
			}
			if( strpos( $text, "%T" ) !== FALSE ) {
	            $monthId    = (int)$timeStamp->getMonth();
	            $monthStr   = $this->_messages["months"][$monthId-1];				
	
				$text = str_replace( "%T", $this->getDateOrdinal( gmdate( "d", $time ))." ".$this->tr("of")." ".$monthStr, $text );				
			}			
			if( strpos( $text, "%D" ) !== FALSE ) {
				$text = str_replace( "%D", $this->getDateOrdinal( gmdate( "d", $time )), $text );
			}
			
    		if ( $this->_code == 'fa_IR' )
    		{			
	    		lt_include( PLOG_CLASS_PATH."class/data/jalalicalendar.class.php" );
	            list( $jyear, $jmonth, $jday ) = JalaliCalendar::gregorian_to_jalali(gmdate( "Y", $time ), gmdate( "m", $time ), gmdate( "d", $time ));
	
				if( strpos( $text, "%q" ) !== FALSE ) {
		      		$text = str_replace( "%q", JalaliCalendar::Convertnumber2farsi($jyear), $text );				
				}			
				if( strpos( $text, "%w" ) !== FALSE ) {
		      		$text = str_replace( "%w", JalaliCalendar::Convertnumber2farsi($jmonth), $text );
				}			
				if( strpos( $text, "%o" ) !== FALSE ) {
		      		$text = str_replace( "%o", JalaliCalendar::Convertnumber2farsi($jday), $text );
				}			
				if( strpos( $text, "%R" ) !== FALSE ) {
		      		$text = str_replace( "%R", JalaliCalendar::monthname($jmonth), $text );
				}			
				if( strpos( $text, "%T" ) !== FALSE ) {
		      		$text = str_replace( "%T", JalaliCalendar::Convertnumber2farsi(gmdate( "H", $time )), $text );
				}			
				if( strpos( $text, "%U" ) !== FALSE ) {
		      		$text = str_replace( "%U", JalaliCalendar::Convertnumber2farsi(gmdate( "i", $time )), $text );
				}
			}			

			return $text;
        }
        
        /**
         * Formats a date as an RFC 822 date timestamp.
         * @see formatDate
         */
        function formatDateAsRFC822( $timestamp, $blog = null )
        {
            $locale = $this->getLocaleCode();
            $this->setLocale( "en_UK" );
            $rfc822Date = $this->formatDate( $timestamp, "%a, %d %b %Y %H:%M:%S %O", $blog );
            $this->setLocale( $locale );
            
            return( $rfc822Date );
        }
		
		/**
		 * merges two locales
		 */
		function mergeLocale( $locale )
		{
            if(isset($locale)){
                // load the file if it hadn't been loaded yet		
			    if( !is_array($this->_messages))
				    $this->_loadLocaleFile();		
		
			    $this->_messages = array_merge( $this->_messages, $locale->getStrings());
                return true;
			}
            else{
                return false;
            }
		}
	}
?>