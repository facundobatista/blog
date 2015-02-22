<?php

	 
	 lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	 lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );
     lt_include( PLOG_CLASS_PATH."class/data/Date.class.php" );

    /**
     * \ingroup Data
     *
     * This class allows to deal with all sorts of different date formats, as many as the PEAR::Date class
     * supports (Timestamp started as an independent class but later on due to requirements it was developed
     * as an extension for PEAR::Date, maintaining the previous interface for compatibility reasons)
     *
     * PEAR::Date has its own documentation available here: http://pear.php.net/package/Date/docs/1.4.2/Date/Date.html
     * 
     * The formats supported by PEAR::Date are:
     *
     * - DATE_FORMAT_ISO (YYYY-MM-DD HH:MM:SS)
     * - DATE_FORMAT_ISO_BASIC (YYYYMMSSTHHMMSS(Z|(+/-)HHMM)?)
     * - DATE_FORMAT_ISO_EXTENDED (YYYY-MM-SSTHH:MM:SS(Z|(+/-)HH:MM)?)
     * - DATE_FORMAT_ISO_EXTENDED_MICROTIME (YYYY-MM-SSTHH:MM:SS(.S*)?(Z|(+/-)HH:MM)?)
     * - DATE_FORMAT_TIMESTAMP (YYYYMMDDHHMMSS)
     * - DATE_FORMAT_UNIXTIME (seconds since the unix epoch)
     *
     * Use the static method Timestamp::parseDate() in order to use other date formats which are not directly
     * supported by this class.
     *
     * @see Date
     * @see Timestamp::parseDate()
     */
	class Timestamp extends Date 
	{
		/*
		 * Creates a Timestamp object
		 * If $timestamp is empty or not specified, creates a timestamp
		 * taking the current time
		 *
		 * @param timestamp a valid SQL timestamp (DATE_FORMAT_TIMESTAMP / YYYYMMDDHHMMSS)
		 */
		function Timestamp( $timestamp = null )
		{
            //if( $timestamp == "" ) $timestamp = null;
            $this->Date( $timestamp );
		}

        /**
         * @deprecated Use setDate instead
         */
		function setTime( $timestamp, $format = DATE_FORMAT_ISO )
		{
        	$this->setDate( $timestamp, $format );
		}

		/**
         * @deprecated Use getDate instead
         */
		function getTimestamp( $format = DATE_FORMAT_TIMESTAMP )
		{
			return $this->getDate( $format );
		}


        /**
         * Returns the century corresponding to the given date. If the current year is '2003', then the
         * century will be '2000', not 21.
         *
         * @return An integer value representing the current century.
         * @deprecated
         */
		function getCentury()
		{
			//return $this->_century;
            throw( new Exception("not implemented?"));
            die();
		}

        /**
         * Returns only the minutes specified by the current date.
         *
         * @return The minutes specified by the current date.
         */
		function getMinutes()
		{
			return $this->getMinute();
		}

        /**
         * Returns only the seconds specified by the current date.
         *
         * @return The seconds specified by the current date.
         */
		function getSeconds()
		{
			return $this->getSecond();
		}

		/**
	 	 * Returns the name of the month using the current locale.
         *
         * @return A string with the name of the month.
	 	 */
		function getMonthString()
		{
            throw( new Exception("Timestamp::getMonthString not implemented. Use Locale::formatDate instead!"));
            die();
		}

        /**
         * @return Returns the current month, as a value from "00" to "12"
         */
        function getMonth()
        {
        	// call the parent getMonth() method
        	$month = Date::getMonth();
            if( $month < 10 && $month[0] != "0" )
            	$month = "0".$month;

            return $month;
        }

        /**
         * Sets a new value for the minutes in this timestamp
         *
         * @param newMinutes the new value for the minutes
         */
        function setMinutes( $newMinutes )
        {
        	$this->setMinute( $newMinutes );
        }

		/**
		 * Instead of returning a string it will just return 0 for sunday, 1 for monday,
		 * 2 for tuesday, 3 for wednesday and so on...
         * @deprecated
         * @private
		 */
		function getWeekdayId()
		{
            return $this->getDayOfWeek();
		}

        /**
         * @private
         */
		function getNextMonthAndYear()
		{
			if( $this->_month == 12 ) {
				$month = "01";
				$year  = $this->_year+1;
			}
			else {
				$month = $this->_month+1;
				if( $month < 10 )
					$month = "0".$month;
				$year = $this->_year;
			}

			return $year.$month;
		}

        /**
         * @private
         */
		function setNextMonthAndYear()
		{
			$result = $this->getNextMonthAndYear();

			$this->setYear( substr( $result, 0, 4 ));
			$this->setMonth( substr( $result, 4, 2 ));

			$this->_calculateFields();
		}

        /**
         * @private
         */
		function getPrevMonthAndYear()
		{
			if( $this->_month == 01 ) {
				$month = 12;
				$year = $this->_year-1;
			}
			else {
				$month = $this->_month - 1;
				if( $month < 10 )
					$month = "0".$month;
				$year = $this->_year;
			}

			return $year.$month;
		}

        /**
         * @private
         */
		function setPrevMonthAndYear()
		{
			$result = $this->getPrevMonthAndYear();

			$this->setYear( substr( $result, 0, 4 ));
			$this->setMonth( substr( $result, 4, 2 ));

			$this->_calculateFields();
		}

        /**
         * Returns the UNIX timestamp for the given date.
         *
         * @return An integer specifying the unix timestamp for the given date.
         */
        function getUnixDate()
        {
        	return $this->getDate( DATE_FORMAT_UNIXTIME );
        }

        /**
         * Returns the date formatted in ISO 8601
         *
         * @return A string with the date in format ISO 8601
         */
        function getIsoDate()
        {
        	return $this->getDate( DATE_FORMAT_ISO );
        }
        
        /**
         * returns the date formatted according to the W3 specifications
         * @see http://www.w3.org/TR/NOTE-datetime
         */
        function getW3Date()
        {
			return $this->getDate( DATE_FORMAT_ISO_EXTENDED );
        }

        /**
         * Static method that returns a timestamp after applying a time
         * difference to it.
         *
         * @static
         * @param timeStamp The original ISO timestamp
         * @param timeDiff The time difference that we'd like to apply to the
         * original timestamp
         */
        function getDateWithOffset( $timeStamp, $timeDiff )
        {
            if( $timeDiff != 0 ) {
            	$t = new Timestamp( $timeStamp );
                //
                // we can't use the addSeconds method with a negative offset
                // so we have to check wether the offset is positive or negative
                // and then use the correct one...
                //
                if( $timeDiff > 0 )
                	$t->addSeconds( $timeDiff * 3600 );
                else
                	$t->subtractSeconds( $timeDiff * (-3600));

                $date = $t->getTimestamp();
            }
            else {
            	$date = $timeStamp;
            }

            return $date;
        }
        
        /**
         * Equivalent to Timestamp::getDateWithOffset but instead of returning a date,
         * it returns a Timestamp object with the given time difference applied to the starting
         * timestamp
         *
         * @param timeStamp
         * @param timeDiff
         * @see getDateWithOffset
         * @return A Timestamp object
         */
        function getTimestampWithOffset( $timeStamp, $timeDiff )
        {
            return( new Timestamp( Timestamp::getDateWithOffset( $timeStamp, $timeDiff )));
        }
        
        /**
         * @static
         * returns a Timestamp object with the blog time difference already
         * applied, if needed
         *
         * @param blog either a blog id or a BlogInfo object
         * @param timestamp 
         * @return A Timestamp object with time difference applied, if needed
         * @see BlogInfo
         */
        function getBlogDate( $blog, $timestamp = null )
        {
	       //
	       // how's this for function overloading??
	       // I know it's quite hackish, but it's a bit of a pain that
	       // we need to define two different functions depending on whether
	       // we're getting an object or an integer!
	       //
	       if( is_object( $blog )) {
		        $blogSettings = $blog->getSettings();
		   		$timeDifference = $blogSettings->getValue( "time_offset" );
	   	   } 
	   	   else {
		   	   	lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
		   	    $blogs = new Blogs();
		   	    $blogInfo = $blogs->getBlogInfoById( $blog );
		   	    if( !$blogInfo )
		   	    	$timeDifference = 0;
		   	    else {
			   		$blogSettings = $blogInfo->getSettings();
			   		$timeDifference = $blogSettings->getValue( "time_offset" );    
		   	    }
	       }
	       
	       // generate the date with the correct time difference applied
	       $t = new Timestamp();	       
	       $t->setDate( Timestamp::getDateWithOffset( $t->getDate(), $timeDifference ), DATE_FORMAT_TIMESTAMP );
	       
	       return $t;
    	}
    	
    	/**
    	 * stupid function that returns an array with all the hours
    	 *
    	 * @return an array
    	 */
    	function getAllHours()
    	{
            $hours = Array( "00", "01", "02", "03", "04", "05", "06", "07", "08",
                            "09", "10", "11", "12", "13", "14", "15", "16", "17",
                            "18", "19", "20", "21", "22", "23" );	    	
                            
            return $hours;
    	}
    	
    	/**
    	 * stupid function that returns an array with... the minutes!
    	 *
    	 * @return an array
    	 */
    	function getAllMinutes()
    	{
            $minutes = Array( "00", "01", "02", "03", "04", "05", "06", "07", "08", "09",
                              "10", "11", "12", "13", "14", "15", "16", "17", "18", "19",
                              "20", "21", "22", "23", "24", "25", "26", "27", "28", "29",
                              "30", "31", "32", "33", "34", "35", "36", "37", "38", "39",
                              "40", "41", "42", "43", "44", "45", "46", "47", "48", "49",
                              "50", "51", "52", "53", "54", "55", "56", "57", "58", "59" );	    	
                              
            return $minutes;
    	}
    	
    	/**
    	 * returns the current time as a mysql timestamp
    	 *
    	 * @static
    	 */
    	function getNowTimestamp()
    	{
    		$t = new Timestamp();
    		return( $t->getTimestamp());
    	}
    	
    	/**
    	 * returns an array with a range of years
    	 *
    	 * @param an array
    	 */
    	function getYears( $minYear = 1900, $maxYear = 2099 )
    	{
	    	return( range( $minYear, $maxYear ));	
    	}
    	
		/**
		 * Given a string with a date in whatever format, returns a valid Timestamp object
		 * The date will be parsed via PHP's strtotime()
		 * (http://fi2.php.net/manual/en/function.strtotime.php)
		 *
		 * @param dateString a string representing a date.
		 * @return a valid Timestamp object
		 * @static
		 */
        function parseDate( $dateString )
        {
            // parse the date via strtotime
            $timestamp = strtotime( $dateString );            
            
            // convert the unix timestamp to a Timestamp object
            $date = new Timestamp( $timestamp );
            
            // and return the whole thing
            return( $date );
        }    	 
	}
?>