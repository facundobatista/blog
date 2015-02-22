<?php

    lt_include(PLOG_CLASS_PATH."class/data/validator/rules/rule.class.php");
	
	/**
	 * \ingroup Validator_Rules
	 *
	 * Implements a rule that checks if the input is a valid php array (used
	 * in cases when we'd like to validate if something coming from $_GET or $_POST
	 * is an array)
	 */
    class ArrayRule extends Rule
    {
        /**
         * Initializes the rule
         */
        function ArrayRule()
        {
            $this->Rule();
        }

        /**
         * Validates the data.
         *
         * @param value The array that we'd like to validate
         */
        function validate($value)
        {
			return( is_array($value));
        }
    }
?>