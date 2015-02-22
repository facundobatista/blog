<?php

    lt_include(PLOG_CLASS_PATH."class/data/validator/rules/regexprule.class.php");

    define( "INT_RULE_REG_EXP", "^([+-]?[1-9][0-9]*)|0$");
    define( "ERROR_RULE_INT_FORMAT_WRONG", "error_rule_int_format_wrong");

    /**
	 * \ingroup Validator_Rules
	 *
	 * Checks if the given value is an integer, not only in type but also in format.
	 * It will return ERROR_RULE_INT_FORMAT_WRONG if the format is not correct
     */
    class IntRule extends RegExpRule
    {
        /**
         * Initialize the rule
         */
        function IntRule()
        {
            $this->RegExpRule(INT_RULE_REG_EXP, false);
        }

        /**
		 * Returns true if the given value is an integer, or false otherwise. In case of error
		 * it will also set the error code to ERROR_RULE_INT_FORMAT_WRONG
         */
        function validate($value)
        {
            if (parent::validate($value))
            {
                $this->_setError(false);
                return true;
            }
            else
            {
                $this->_setError(ERROR_RULE_INT_FORMAT_WRONG);
                return false;
            }
        }
    }
?>