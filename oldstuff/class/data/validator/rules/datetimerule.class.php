<?php

    lt_include(PLOG_CLASS_PATH."class/data/validator/rules/rule.class.php");

    define( "ERROR_RULE_DATE_FORMAT_WRONG", "error_rule_date_format_wrong");

    /**
     * \ingroup Validator_Rules
     *
     * Validates if a date is valid or not
     */
    class DateTimeRule extends Rule
    {
        var $_format;

        /**
         * Initializes the rule
         */
        function DateTimeRule( $format = '')
        {
            $this->Rule();

			$this->_format = $format;
        }

        /**
         * Returns true if the value is not empty or false otherwise. If empty,
         * the error ERROR_RULE_VALUE_IS_EMPTY will be set.
         *
         * @param value the string that we'd like to validate
         * @return true if successful or false otherwise
         */
        function validate( $value )
        {
            if( DateTimeRule::_datetime( $value, $this->_format ) ) {
				return true;         
            }
            else {
                $this->_setError( ERROR_RULE_DATE_FORMAT_WRONG );
                return false;
            }
        }

	    function _datetime( $date, $format = '' )
	    {
	        if (strtolower($format) == 'rfc822_compliant') {
	            $preg = '&^(?:(Mon|Tue|Wed|Thu|Fri|Sat|Sun),) \s+
	                    (?:(\d{2})?) \s+
	                    (?:(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)?) \s+
	                    (?:(\d{2}(\d{2})?)?) \s+
	                    (?:(\d{2}?)):(?:(\d{2}?))(:(?:(\d{2}?)))? \s+
	                    (?:[+-]\d{4}|UT|GMT|EST|EDT|CST|CDT|MST|MDT|PST|PDT|[A-IK-Za-ik-z])$&xi';
	
	            if (!preg_match($preg, $date, $matches)) {
	                return false;
	            }
	
	            $year   = (int)$matches[4];
	            $months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
	                            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
	            $month  = array_keys($months, $matches[3]);
	            $month  = (int)$month[0]+1;
	            $day    = (int)$matches[2];
	            $weekday= $matches[1];
	            $hour   = (int)$matches[6];
	            $minute = (int)$matches[7];
	            isset($matches[9]) ? $second = (int)$matches[9] : $second = 0;
	
	            if ((strlen($year) != 4)        ||
	                ($day    > 31   || $day < 1)||
	                ($hour   > 23)  ||
	                ($minute > 59)  ||
	                ($second > 59)) {
	                    return false;
	            }
	        } else {
	            $date_len = strlen($format);
	            for ($i = 0; $i < $date_len; $i++) {
	                $c = $format{$i};
	                if ($c == '%') {
	                    $next = $format{$i + 1};
	                    switch ($next) {
	                        case 'j':
	                        case 'd':
	                            if ($next == 'j') {
	                                $day = (int)DateTimeRule::_substr($date, 1, 2);
	                            } else {
	                                $day = (int)DateTimeRule::_substr($date, 0, 2);
	                            }
	                            if ($day < 1 || $day > 31) {
	                                return false;
	                            }
	                            break;
	                        case 'm':
	                        case 'n':
	                            if ($next == 'm') {
	                                $month = (int)DateTimeRule::_substr($date, 0, 2);
	                            } else {
	                                $month = (int)DateTimeRule::_substr($date, 1, 2);
	                            }
	                            if ($month < 1 || $month > 12) {
	                                return false;
	                            }
	                            break;
	                        case 'Y':
	                        case 'y':
	                            if ($next == 'Y') {
	                                $year = DateTimeRule::_substr($date, 4);
	                                $year = (int)$year?$year:'';
	                            } else {
	                                $year = (int)(substr(date('Y'), 0, 2) .
	                                              DateTimeRule::_substr($date, 2));
	                            }
	                            if (strlen($year) != 4 || $year < 0 || $year > 9999) {
	                                return false;
	                            }
	                            break;
	                        case 'g':
	                        case 'h':
	                            if ($next == 'g') {
	                                $hour = DateTimeRule::_substr($date, 1, 2);
	                            } else {
	                                $hour = DateTimeRule::_substr($date, 2);
	                            }
	                            if (!preg_match('/^\d+$/', $hour) || $hour < 0 || $hour > 12) {
	                                return false;
	                            }
	                            break;
	                        case 'G':
	                        case 'H':
	                            if ($next == 'G') {
	                                $hour = DateTimeRule::_substr($date, 1, 2);
	                            } else {
	                                $hour = DateTimeRule::_substr($date, 2);
	                            }
	                            if (!preg_match('/^\d+$/', $hour) || $hour < 0 || $hour > 24) {
	                                return false;
	                            }
	                            break;
	                        case 's':
	                        case 'i':
	                            $t = DateTimeRule::_substr($date, 2);
	                            if (!preg_match('/^\d+$/', $t) || $t < 0 || $t > 59) {
	                                return false;
	                            }
	                            break;
	                        default:
	                            trigger_error("Not supported char `$next' after % in offset " . ($i+2), E_USER_WARNING);
	                    }
	                    $i++;
	                } else {
	                    //literal
	                    if (DateTimeRule::_substr($date, 1) != $c) {
	                        return false;
	                    }
	                }
	            }
	        }
	        // there is remaing data, we don't want it
	        if (strlen($date) && (strtolower($format) != 'rfc822_compliant')) {
	            return false;
	        }
	
	        if (isset($day) && isset($month) && isset($year)) {
	            if (!checkdate($month, $day, $year)) {
	                return false;
	            }
	
	            if (strtolower($format) == 'rfc822_compliant') {
	                if ($weekday != date("D", mktime(0, 0, 0, $month, $day, $year))) {
	                    return false;
	                }
	            }
	        }
	
	        return true;
	    }
	
	    function _substr(&$date, $num, $opt = false)
	    {
	        if ($opt && strlen($date) >= $opt && preg_match('/^[0-9]{'.$opt.'}/', $date, $m)) {
	            $ret = $m[0];
	        } else {
	            $ret = substr($date, 0, $num);
	        }
	        $date = substr($date, strlen($ret));
	        return $ret;
	    }
    }
?>