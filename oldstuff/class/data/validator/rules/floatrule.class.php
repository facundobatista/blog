<?php

    lt_include(PLOG_CLASS_PATH."class/data/validator/rules/regexprule.class.php");

    define( "FLOAT_RULE_REG_EXP", "^([+-]?[0-9][0-9.]*)|0$");
    define( "ERROR_RULE_FLOAT_FORMAT_WRONG", "error_rule_float_format_wrong");

    /**
	 * \ingroup Validator_Rules
	 *
	 * Checks if the given value is an integer, not only in type but also in format.
	 * It will return ERROR_RULE_INT_FORMAT_WRONG if the format is not correct
     */
    class FloatRule extends RegExpRule
    {
        /**
         * Initialize the rule
         */
        function FloatRule()
        {
            $this->RegExpRule(FLOAT_RULE_REG_EXP, false);
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
                $this->_setError(ERROR_RULE_FLOAT_FORMAT_WRONG);
                return false;
            }
        }
    }
?>