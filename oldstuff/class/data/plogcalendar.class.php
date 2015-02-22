<?php
    lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/bloginfo.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/Date.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/htmlcalendar.class.php" );
    lt_include( PLOG_CLASS_PATH.'class/data/timestamp.class.php' );
    lt_include( PLOG_CLASS_PATH."class/locale/locale.class.php" );
    lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );
    lt_include( PLOG_CLASS_PATH."class/net/requestgenerator.class.php" );

// artificially define min and maximum years - maybe it'd be better to query
// for min and max post dates?  // max is easy enough, but min is 0 a lot of the
// time, since draft posts are labelled with a 0 (which arguably shouldn't be the
// case anyway)
define("MIN_YEAR", 1980);
define("MAX_YEAR", 2030);

    /**    
    * \ingroup Data
    *
    * A derived class is required to reimplement some methods from the parent
    * Calendar class to add things like links to the days and so on.
    * This one also converts the original HtmlCalendar into Locale aware, respecting
    * things like the first day of the week and the abbreviations of the days of the week.
    *
    * User classes will rarely need to use this class.
    */
    class PlogCalendar extends Calendar 
	{
        
        var $_dayPosts;
        var $_blogInfo;
        var $rg;

        /**
         * Constructor.
         *
         * @param dayPosts An array indexed from 1 to as many days as the month where
         * every position tells how many posts were made that day.
         * @param localeCode A code specifying the locale we want to use. If empty, the default
         * one specified in the configuration file will be used.
         */
        function PlogCalendar( $dayPosts =  null, $blogInfo = null, $locale = null )
        {
            $this->Calendar();

            if( $locale == null ) {
                $config = Config::getConfig();
                //$locale = new Locale( $config->getValue( "default_locale" ));
                $locale = Locales::getLocale( $config->getValue( "default_locale" ));
            }

            $this->_dayPosts = $dayPosts;
            $this->_blogInfo = $blogInfo;

            // set the first day of the week according to our blogsetting
            $blogSettings =  $this->_blogInfo->getSettings();
            $this->startDay = $blogSettings->getValue( 'first_day_of_week', $locale->firstDayOfWeek() );
            //array_push( $this->monthsNames, $locale->tr("January"));
            $this->monthNames = $locale->getMonthNames();
            // abbreviations of the days of the week
            $this->dayNamesShort = $locale->getDayNamesShort();
            // full names of the days of the week
            $this->dayNames = $locale->getDayNames();

            $this->rg = RequestGenerator::getRequestGenerator( $blogInfo );
        }

        /**
         * Function overwritten from the base one, so that we can display links for the
         * next month and the previous month         
         *        
         * @see Calendar::getCalendarLink()
         */
        function getCalendarLink( $month, $year )
        {
            if($year < MIN_YEAR || $year >= MAX_YEAR)
                return "";

            if( $month < 10 ) $month = "0".$month;
            $date = "$year$month";

            $calendarLink = $this->rg->getArchiveLink( $date );


            return( $calendarLink );
        }

        /**
         * Function overwritten from the base one, so that we can display a link for the days
         * that have posts         
         *         
         * @see Calendar::getDateLink()
         */
        function getDateLink( $day, $month, $year )
        {
            if( $this->_dayPosts == null )
                return "";

            if( array_key_exists( $day, $this->_dayPosts) && $this->_dayPosts[$day] > 0 ) {
                if( $day < 10 ) $day = "0".$day;
                $date = "$year$month$day";

                $dateLink = $this->rg->getArchiveLink( $date );

                return( $dateLink );
            }

            return "";
        }
        
        /**
         * Return the HTML for a specified month
         *         
         * @see Calendar::getMonthView()
         */
        function getMonthView($month, $year)
        {
            $t = Timestamp::getBlogDate( $this->_blogInfo );
            return $this->getMonthHTML($month, $year, 1/*showYear*/, 
                                       $t->getTimestamp(DATE_FORMAT_UNIXTIME) );
        }


        
    }
?>
