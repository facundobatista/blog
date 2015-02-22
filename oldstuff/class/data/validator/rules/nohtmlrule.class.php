<?php

    lt_include( PLOG_CLASS_PATH."class/data/validator/rules/rule.class.php");
    lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php");

    /**
	 * \ingroup Validator_Rules
	 *
	 * Checks for HTML in the string
	 */
    class NoHtmlRule extends Rule
    {
        /**
		 * Validates that the given string doesn't contain any HTML/javascript
		 *
		 * @param value The string to validate
		 * @return True if there isn't any HTML in the string or false otherwise
         */
        function validate($value)
        {
            $filtered = Textfilter::filterAllHtml($value);
            return ($filtered == trim($value));
        }
    }
?>
