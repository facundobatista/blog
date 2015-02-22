<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Baba Buehler <baba@babaz.com>                               |
// |                                                                      |
// +----------------------------------------------------------------------+
//
// $Id: TimeZone.php,v 1.6 2004/05/16 12:48:06 pajoye Exp $
//
// Date_TimeZone Class
//

/**
 * \ingroup Data
 *
 * TimeZone representation class, along with time zone information data.
 *
 * TimeZone representation class, along with time zone information data.
 * The default timezone is set from the first valid timezone id found
 * in one of the following places, in this order: <br>
 * 1) global $_DATE_TIMEZONE_DEFAULT<br>
 * 2) system environment variable PHP_TZ<br>
 * 3) system environment variable TZ<br>
 * 4) the result of date('T')<br>
 * If no valid timezone id is found, the default timezone is set to 'UTC'.
 * You may also manually set the default timezone by passing a valid id to
 * Date_TimeZone::setDefault().<br>
 *
 * This class includes time zone data (from zoneinfo) in the form of a global array, $_DATE_TIMEZONE_DATA.
 *
 *
 * @author Baba Buehler <baba@babaz.com>
 * @access public
 * @version 1.0
 */
class Date_TimeZone
{
    /**
     * Time Zone ID of this time zone
     * @var string
     */
    var $id;
    /**
     * Long Name of this time zone (ie Central Standard Time)
     * @var string
     */
    var $longname;
    /**
     * Short Name of this time zone (ie CST)
     * @var string
     */
    var $shortname;
    /**
     * true if this time zone observes daylight savings time
     * @var boolean
     */
    var $hasdst;
    /**
     * DST Long Name of this time zone
     * @var string
     */
    var $dstlongname;
    /**
     * DST Short Name of this timezone
     * @var string
     */
    var $dstshortname;
    /**
     * offset, in milliseconds, of this timezone
     * @var int
     */
    var $offset;

    /**
     * System Default Time Zone
     * @var object Date_TimeZone
     */
    var $default;


    /**
     * Constructor
     *
     * Creates a new Date::TimeZone object, representing the time zone
     * specified in $id.  If the supplied ID is invalid, the created
     * time zone is UTC.
     *
     * @access public
     * @param string $id the time zone id
     * @return object Date_TimeZone the new Date_TimeZone object
     */
    function Date_TimeZone($id)
    {
        global $_DATE_TIMEZONE_DATA;
        if(Date_TimeZone::isValidID($id)) {
            $this->id = $id;
            $this->longname = $_DATE_TIMEZONE_DATA[$id]['longname'];
            $this->shortname = $_DATE_TIMEZONE_DATA[$id]['shortname'];
            $this->offset = $_DATE_TIMEZONE_DATA[$id]['offset'];
            if($_DATE_TIMEZONE_DATA[$id]['hasdst']) {
                $this->hasdst = true;
                $this->dstlongname = $_DATE_TIMEZONE_DATA[$id]['dstlongname'];
                $this->dstshortname = $_DATE_TIMEZONE_DATA[$id]['dstshortname'];
            } else {
                $this->hasdst = false;
                $this->dstlongname = $this->longname;
                $this->dstshortname = $this->shortname;
            }
        } else {
            $this->id = 'UTC';
            $this->longname = $_DATE_TIMEZONE_DATA[$this->id]['longname'];
            $this->shortname = $_DATE_TIMEZONE_DATA[$this->id]['shortname'];
            $this->hasdst = $_DATE_TIMEZONE_DATA[$this->id]['hasdst'];
            $this->offset = $_DATE_TIMEZONE_DATA[$this->id]['offset'];
        }
    }

    /**
     * Return a TimeZone object representing the system default time zone
     *
     * Return a TimeZone object representing the system default time zone,
     * which is initialized during the loading of TimeZone.php.
     *
     * @access public
     * @return object Date_TimeZone the default time zone
     */
    function getDefault()
    {
        global $_DATE_TIMEZONE_DEFAULT;
        return new Date_TimeZone($_DATE_TIMEZONE_DEFAULT);
    }

    /**
     * Sets the system default time zone to the time zone in $id
     *
     * Sets the system default time zone to the time zone in $id
     *
     * @access public
     * @param string $id the time zone id to use
     */
    function setDefault($id)
    {
        global $_DATE_TIMEZONE_DEFAULT;
        if(Date_TimeZone::isValidID($id)) {
            $_DATE_TIMEZONE_DEFAULT = $id;
        }
    }

    /**
     * Tests if given id is represented in the $_DATE_TIMEZONE_DATA time zone data
     *
     * Tests if given id is represented in the $_DATE_TIMEZONE_DATA time zone data
     *
     * @access public
     * @param string $id the id to test
     * @return boolean true if the supplied ID is valid
     */
    function isValidID($id)
    {
        global $_DATE_TIMEZONE_DATA;
        if(isset($_DATE_TIMEZONE_DATA[$id])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Is this time zone equal to another
     *
     * Tests to see if this time zone is equal (ids match)
     * to a given Date_TimeZone object.
     *
     * @access public
     * @param object Date_TimeZone $tz the timezone to test
     * @return boolean true if this time zone is equal to the supplied time zone
     */
    function isEqual($tz)
    {
        if(strcasecmp($this->id, $tz->id) == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Is this time zone equivalent to another
     *
     * Tests to see if this time zone is equivalent to
     * a given time zone object.  Equivalence in this context
     * is defined by the two time zones having an equal raw
     * offset and an equal setting of "hasdst".  This is not true
     * equivalence, as the two time zones may have different rules
     * for the observance of DST, but this implementation does not
     * know DST rules.
     *
     * @access public
     * @param object Date_TimeZone $tz the timezone object to test
     * @return boolean true if this time zone is equivalent to the supplied time zone
     */
    function isEquivalent($tz)
    {
        if($this->offset == $tz->offset && $this->hasdst == $tz->hasdst) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns true if this zone observes daylight savings time
     *
     * Returns true if this zone observes daylight savings time
     *
     * @access public
     * @return boolean true if this time zone has DST
     */
    function hasDaylightTime()
    {
        return $this->hasdst;
    }

    /**
     * Is the given date/time in DST for this time zone
     *
     * Attempts to determine if a given Date object represents a date/time
     * that is in DST for this time zone.  WARNINGS: this basically attempts to
     * "trick" the system into telling us if we're in DST for a given time zone.
     * This uses putenv() which may not work in safe mode, and relies on unix time
     * which is only valid for dates from 1970 to ~2038.  This relies on the
     * underlying OS calls, so it may not work on Windows or on a system where
     * zoneinfo is not installed or configured properly.
     *
     * @access public
     * @param object Date $date the date/time to test
     * @return boolean true if this date is in DST for this time zone
     */
    function inDaylightTime($date)
    {
        $env_tz = "";
        if(getenv("TZ")) {
            $env_tz = getenv("TZ");
        }
        putenv("TZ=".$this->id);
        $ltime = localtime($date->getTime(), true);
        putenv("TZ=".$env_tz);
        return $ltime['tm_isdst'];
    }

    /**
     * Get the DST offset for this time zone
     *
     * Returns the DST offset of this time zone, in milliseconds,
     * if the zone observes DST, zero otherwise.  Currently the
     * DST offset is hard-coded to one hour.
     *
     * @access public
     * @return int the DST offset, in milliseconds or zero if the zone does not observe DST
     */
    function getDSTSavings()
    {
        if($this->hasdst) {
            return 3600000;
        } else {
            return 0;
        }
    }

    /**
     * Get the DST-corrected offset to UTC for the given date
     *
     * Attempts to get the offset to UTC for a given date/time, taking into
     * account daylight savings time, if the time zone observes it and if
     * it is in effect.  Please see the WARNINGS on Date::TimeZone::inDaylightTime().
     *
     *
     * @access public
     * @param object Date $date the Date to test
     * @return int the corrected offset to UTC in milliseconds
     */
    function getOffset($date)
    {
        if($this->inDaylightTime($date)) {
            return $this->offset + $this->getDSTSavings();
        } else {
            return $this->offset;
        }
    }

    /**
     * Returns the list of valid time zone id strings
     *
     * Returns the list of valid time zone id strings
     *
     * @access public
     * @return mixed an array of strings with the valid time zone IDs
     */
    function getAvailableIDs()
    {
        global $_DATE_TIMEZONE_DATA;
        return array_keys($_DATE_TIMEZONE_DATA);
    }

    /**
     * Returns the id for this time zone
     *
     * Returns the time zone id  for this time zone, i.e. "America/Chicago"
     *
     * @access public
     * @return string the id
     */
    function getID()
    {
        return $this->id;
    }

    /**
     * Returns the long name for this time zone
     *
     * Returns the long name for this time zone,
     * i.e. "Central Standard Time"
     *
     * @access public
     * @return string the long name
     */
    function getLongName()
    {
        return $this->longname;
    }

    /**
     * Returns the short name for this time zone
     *
     * Returns the short name for this time zone, i.e. "CST"
     *
     * @access public
     * @return string the short name
     */
    function getShortName()
    {
        return $this->shortname;
    }

    /**
     * Returns the DST long name for this time zone
     *
     * Returns the DST long name for this time zone, i.e. "Central Daylight Time"
     *
     * @access public
     * @return string the daylight savings time long name
     */
    function getDSTLongName()
    {
        return $this->dstlongname;
    }

    /**
     * Returns the DST short name for this time zone
     *
     * Returns the DST short name for this time zone, i.e. "CDT"
     *
     * @access public
     * @return string the daylight savings time short name
     */
    function getDSTShortName()
    {
        return $this->dstshortname;
    }

    /**
     * Returns the raw (non-DST-corrected) offset from UTC/GMT for this time zone
     *
     * Returns the raw (non-DST-corrected) offset from UTC/GMT for this time zone
     *
     * @access public
     * @return int the offset, in milliseconds
     */
    function getRawOffset()
    {
        return $this->offset;
    }

} // Date_TimeZone


//
// Time Zone Data
//  offset is in miliseconds
//
$GLOBALS['_DATE_TIMEZONE_DATA'] = array(
        'UTC' => array(
        'offset' => 0,
        'longname' => "Coordinated Universal Time",
        'shortname' => 'UTC',
        'hasdst' => false )
);


Date_TimeZone::setDefault('UTC');
//
// END
?>
