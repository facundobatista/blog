<?php

    lt_include( PLOG_CLASS_PATH."class/data/validator/rules/rule.class.php");

    define( "ERROR_RULE_STRING_TOO_SMALL", "error_rule_string_too_small");
    define( "ERROR_RULE_STRING_TOO_LARGE", "error_rule_string_too_large");

    /**
     * \ingroup Validator_Rules
     *
     * Given two values that will be used as lower and upper boundaries of the string length, 
     * validates if the length of the given string is between the limits.
     *
     * It will set the errors ERROR_RULE_STRING_TOO_SMALL or ERROR_RULE_STRING_TOO_LARGE in case the
     * validation is not successful.
     */
    class StringRangeRule extends Rule
    {
        var $_minValue;
        var $_maxValue;

        /**
         * Initializes the rule
         *
         * @param minValue the lower boundary of the range
         * @param maxValue the upper boundary of the range
         */
        function StringRangeRule($minValue, $maxValue)
        {
            $this->Rule();

            $this->_minValue = $minValue;
            $this->_maxValue = $maxValue;
        }

        /**
         * @return the lower boundary of the range
         */
        function getMinValue()
        {
            return $this->_minValue;
        }

        /**
         * sets the lower boundary of the range
         *
         * @param minValue the minimum value
         */
        function setMinValue($minValue)
        {
            $this->_minValue = $minValue;
        }

        /**
         * @return the upper boundary of the range
         */
        function getMaxValue()
        {
            return $this->_maxValue;
        }

        /**
         * sets the lower boundary of the range
         *
         * @param minValue the minimum value
         */
        function setMaxValue($maxValue)
        {
            $this->_maxValue = $maxValue;
        }

        /**
         * validates that the given value is within the two boundaries previously specified
         *
         * @param value The value to validate
         * @return True if successful or false otherwise
         */
        function validate($value)
        {
            $len = strlen($value);

            if ($len < $this->_minValue)
            {
                $this->_setError(ERROR_RULE_STRING_TOO_SMALL);
                return false;
            }
            else if ($this->_maxValue != 0 && $len > $this->_maxValue)
            {
                $this->_setError(ERROR_RULE_STRING_TOO_LARGE);
                return false;
            }
            else
            {
                $this->_setError(false);
                return true;
            }
        }
    }
?>