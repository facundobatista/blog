<?php

    lt_include(PLOG_CLASS_PATH."class/data/validator/rules/rule.class.php");

    define( "ERROR_RULE_VALUE_IS_EMPTY", "error_rule_value_is_empty");

    /**
     * \ingroup Validator_Rules
     *
     * Validates if a string is empty or not
     */
    class NonEmptyRule extends Rule
    {
        /**
         * Initializes the rule
         */
        function NonEmptyRule()
        {
            $this->Rule();
        }

        /**
         * Returns true if the value is not empty or false otherwise. If empty,
         * the error ERROR_RULE_VALUE_IS_EMPTY will be set.
         *
         * @param value the string that we'd like to validate
         * @return true if successful or false otherwise
         */
        function validate($value)
        {
            if( $value == null || $value == "" || trim($value) == "" ) {
                $this->_setError( ERROR_RULE_VALUE_IS_EMPTY );
                return false;            
            }
            else {
                return true;
            }
        }
    }
?>