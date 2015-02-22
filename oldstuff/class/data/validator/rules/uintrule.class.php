<?php

    lt_include(PLOG_CLASS_PATH."class/data/validator/rules/regexprule.class.php");

    define( "UINT_RULE_REG_EXP", "^([0-9]+)$");
    define( "ERROR_RULE_UINT_FORMAT_WRONG", "error_rule_uint_format_wrong");

    /**
     * \ingroup Validator_Rules
     *
     * Validates that the given value is an unsigned integer or not. If unsuccessful, the error
     * ERROR_RULE_UINT_FORMAT_WRONG will be set.
     */
    class UIntRule extends RegExpRule
    {
        /**
         * Initialize the rule
         */
        function UIntRule()
        {
            $this->RegExpRule(UINT_RULE_REG_EXP, false);
        }

        /**
         * Checks whether the given value is an unsigned integer
         *
         * @return True if successful or false otherwise. If unsuccessful, the error
         * ERROR_RULE_UINT_FORMAT_WRONG will be set.
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
                $this->_setError(ERROR_RULE_UINT_FORMAT_WRONG);
                return false;
            }
        }
    }
?>
