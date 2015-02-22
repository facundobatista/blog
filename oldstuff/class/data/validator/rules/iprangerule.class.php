<?php

    lt_include( PLOG_CLASS_PATH."class/data/validator/rules/rule.class.php");

    define( "ERROR_RULE_IP_NOT_IN_RANGE", "error_rule_ip_not_in_range");

    /**
     * \ingroup Validator_Rules
     *
     * This rule returns true if the given IP address is within a certain range. Use the constructor
     * or the setRange() method for setting the right range. The range should be given in a submask format,
     * so for example 1.2.3.4 would be within the range 1.2.3.255
     *
     * It will set the error flag ERROR_RULE_IP_NOT_IN_RANGE error if the address is not in the
     * given range.
     */
    class IpRangeRule extends Rule
    {
        var $_range;

        /**
         * Initializes the rule with the given range
         *
         * @param range The range.
         */
        function IpRangeRule($range)
        {
            $this->Rule();
            $this->_range = $range;
        }

        /**
         * Sets a different range than the one given in the constructor
         *
         * @param range the new range
         */
        function setRange($range)
        {
            $this->_range = $range;
        }

        /**
         * Returns the current range being used for the calculations
         *
         * @return A string representing the range
         */
        function getRange()
        {
            return $this->_range;
        }

        /**
         * Returns true if the address is within the given range or false otherwise. It will
         * also set the error ERROR_RULE_IP_NOT_IN_RANGE
         *
         * @param value The IP address to validate
         * @return True if within range or false otherwise
         */
        function validate($value)
        {
            $counter = 0;
            $range   = explode("/", $this->_range);

            if ($range[1] < 32)
            {
                $maskBits  = $range[1];
                $hostBits  = 32 - $maskBits;
                $hostCount = pow(2, $hostBits) - 1;
                $ipStart   = ip2long($range[0]);
                $ipEnd     = $ipStart + $hostCount;

                if ((ip2long($value) > $ipStart) && (ip2long($value) < $ipEnd))
                {
                    $this->_setError(false);
                    return true;
                }
            }
            elseif (ip2long($value) == ip2long($range[0]))
            {
                $this->_setError(false);
                return true;
            }

            $this->_setError(ERROR_RULE_IP_NOT_IN_RANGE);
            return false;
        }
    }
?>