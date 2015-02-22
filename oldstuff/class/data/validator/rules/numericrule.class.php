<?php

    lt_include( PLOG_CLASS_PATH."class/data/validator/rules/regexprule.class.php");

    define( "NUMERIC_RULE_REG_EXP", "^[0-9]+$");
    define( "ERROR_RULE_NUMERIC_FORMAT_WRONG", "error_rule_numeric_format_wrong");

    /**
     * \ingroup Validator_Rules
     *
     * Via a regular expression, validates whether the given string is a valid numeric value.
     */
    class NumericRule extends RegExpRule
    {
        /**
         * Initializes the rule
         */
        function NumericRule()
        {
            $this->RegExpRule(NUMERIC_RULE_REG_EXP, false);
        }

        /**
         * Returns true if the rule is successful or false otherwise. It will set the error
         * ERROR_RULE_NUMERIC_FORMAT_WRONG in case the validation is unsuccessful.
         *
         * @param value The value that should be checked
         * @return true if successful or false otherwise.
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
                $this->_setError(ERROR_RULE_NUMERIC_FORMAT_WRONG);
                return false;
            }
        }
    }
?>