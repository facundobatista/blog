<?php

    lt_include( PLOG_CLASS_PATH."class/data/validator/rules/rule.class.php");

    define( "ERROR_RULE_INTEGER_TOO_SMALL", "error_rule_integer_too_small");
    define( "ERROR_RULE_INTEGER_TOO_LARGE", "error_rule_integer_too_large");

    /**
     * \ingroup Validator_Rules
     *
     * Given two values that will be used as lower and upper boundaries of the range, 
     * it will validate whether the given value falls within the range.
     *
     * It will set the errors ERROR_RULE_INTEGER_TOO_SMALL or ERROR_RULE_INTEGER_TOO_LARGE in case the
     * validation is not successful.
     */
    class IntRangeRule extends Rule
    {
        var $_minValue;
        var $_maxValue;

        /**
         * Initializes the rule
         *
         * @param minValue the lower boundary of the range
         * @param maxValue the upper boundary of the range
         */
        function IntRangeRule($minValue, $maxValue)
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
            //$len = strlen($value);
            $intValue = (int)$value;

            if ($intValue < $this->_minValue)
            {
                $this->_setError(ERROR_RULE_INTEGER_TOO_SMALL);
                return false;
            }
            else if ($this->_maxValue != 0 && $intValue > $this->_maxValue)
            {
                $this->_setError(ERROR_RULE_INTEGER_TOO_LARGE);
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