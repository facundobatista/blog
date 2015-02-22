<?php

    lt_include(PLOG_CLASS_PATH."class/data/validator/rules/rule.class.php");

    define( "DEFAULT_RULE_CASE_SENSITIVE", true);
    define( "ERROR_RULE_VALUES_NOT_EQUAL", "error_rule_values_not_equal");

    /**
	 * \ingroup Validator_Rules
	 *
	 * checks whether an item is equal to another via the binary operator '=='.
	 * Use the setEqualValue() method to set the first value and call the validate()
	 * method with the second value.
     */
    class EqualRule extends Rule
    {
        var $_equalValue;

        /**
         * The constructor does nothing.
         */
        function EqualRule($equalValue)
        {
            $this->Rule();
            $this->_equalValue = $equalValue;
        }

        /**
         * @return returns the value we're comparing
         */
        function getEqualValue()
        {
            return $this->_equalValue;
        }

        /**
         * Sets the value we're comparing to
		 *
		 * @param equalValue the valure we're comparing to
         */
        function setEqualValue($equalValue)
        {
            $this->_equalValue = $equalValue;
        }

        /**
		 * Returns true if the two given values are equal. It will return ERROR_RULE_VALUES_NOT_EQUAL
		 * if they're not
         */
        function validate($value)
        {
            if ($this->_equalValue == $value)
            {
                $this->_setError(false);
                return true;
            }
            else
            {
                $this->_setError(ERROR_RULE_VALUES_NOT_EQUAL);
                return false;
            }
        }
    }
?>