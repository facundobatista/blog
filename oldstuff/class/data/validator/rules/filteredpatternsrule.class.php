<?php

    lt_include( PLOG_CLASS_PATH."class/data/validator/rules/rule.class.php");
    
    define( "DEFAULT_PATTERN_CASE_SENSITIVE", true);

    /**
	 * \ingroup Validator_Rules
	 *
	 * Given an array with regular expressions, validates that the string passed
	 * as a parameter via the validate() method does not match any of the patterns.
     */
    class FilteredPatternsRule extends Rule
    {
		var $_filteredPatterns;
		
        /**
 		 * Constructor.
 	 	 *
 		 * @param filteredPatterns Array containing the regular expressions to check, but please do not use the full format
 	     * (i.e. if the pattern is "/^abc/" please do not specify the leading and trailing forward slashes as they will be
 		 * automatically added later.
		 * @caseSensitive Whether to treat patterns in a case-sensitive fashion or not.
         */
        function FilteredPatternsRule( $filteredPatterns, $caseSensitive = DEFAULT_PATTERN_CASE_SENSITIVE )
        {
            $this->Rule();

			$this->_caseSensitive = $caseSensitive;
			$this->_filteredPatterns = $filteredPatterns;
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
        function setCaseSensitive( $caseSensitive = DEFAULT_PATTERN_CASE_SENSITIVE )
        {
            $this->_caseSensitive = $caseSensitive;
        }

        /**
		 * Validates that none of the pattern matches the given string
		 *
		 * @param value The string to be checked
		 * @return True if the string does not match any of the patterns or false otherwise
         */
        function validate($value)
        {
			foreach( $this->_filteredPatterns as $filteredPattern ) {
				$regexp = "/^$filteredPattern\$/";
				if( !$this->_caseSensitive )
					$regexp .= "i";
					
				$res = preg_match( $regexp, $value );
				if( $res > 0 )
					return false;	// no need to keep searching					
			}
			
			return true;
        }
    }
?>
