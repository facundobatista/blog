<?php

    lt_include( PLOG_CLASS_PATH."class/data/validator/rules/rule.class.php");

    define( "DEFAULT_RULE_CASE_SENSITIVE", true);
    define( "ERROR_RULE_REGEXP_NOT_MATCH", "error_rule_regexp_not_match");

    /**
	 * \ingroup Validator_Rules
	 *
	 * Implements a rule that checks whether a string matches the given regular expression, supporting
	 * both case sensitive and not sensitive.
     */
    class RegExpRule extends Rule
    {
        var $_regExp;
        var $_caseSensitive;

        /**
 	     * Builds the regular expression rule.
		 *
		 * @param regExp The regular expression against which we are going to be matching data.
		 * @param caseSensitive Whether the regular expression will be matched using the case
		 * sensitive mode or not.
         */
        function RegExpRule($regExp, $caseSensitive = DEFAULT_RULE_CASE_SENSITIVE)
        {
            $this->Rule();

            $this->_regExp        = $regExp;
            $this->_caseSensitive = $caseSensitive;
        }

        /**
		 * @return Returns the regular expression that is being used to validate data
         */
        function getRegExp()
        {
            return $this->_regExp;
        }

        /**
		 * @return Sets the regular expression that will be used to validate data
         */
        function setRegExp($regExp)
        {
            $this->_regExp = $regExp;
        }

        /**
         * @return Returns true if the case-sensitive mode is enabled or false otherwise
         */
        function isCaseSensitive()
        {
            return $this->_caseSensitive;
        }

        /**
         * Sets the case sensitive mode.
		 *
		 * @param caseSensitive True to activate the case-sensitive mode or false otherwise
         */
        function setCaseSensitive($caseSensitive = DEFAULT_RULE_CASE_SENSITIVE)
        {
            $this->_caseSensitive = $caseSensitive;
        }

        /**
 	 	 * Checks whether the given value matches the regular expression that was given as a parameter
 	     * to the constructor (or changed later on via the setRegExp method)
		 *
		 * @param value The string that will be validated
		 * @return Returns true if the string matches the regular expression or false otherwise
         */
        function validate($value)
        {
            if ($this->_caseSensitive && ereg($this->_regExp, $value))
            {
                $this->_setError(false);
                return true;
            }
            else if (!$this->_caseSensitive && eregi($this->_regExp, $value))
            {
                $this->_setError(false);
                return true;
            }
            else
            {
                $this->_setError(ERROR_RULE_REGEXP_NOT_MATCH);
                return false;
            }
        }
    }
?>
