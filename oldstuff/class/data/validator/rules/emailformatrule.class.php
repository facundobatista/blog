<?php

    lt_include(PLOG_CLASS_PATH."class/data/validator/rules/regexprule.class.php");

    define( "ERROR_RULE_EMAIL_FORMAT_WRONG", "error_rule_email_format_wrong");

    /**
	 * \ingroup Validator_Rules
	 *
	 * Given an email address, returns true if it looks like a valid email address (if it has the 
	 * valid format) If not, it will return ERROR_RULE_EMAIL_FORMAT_WRONG
     */
    class EmailFormatRule extends Rule
    {
	
		var $_email;
	
        /**
         * The constructor does nothing.
         */
        function EmailFormatRule()
        {
            $this->Rule();
        }

        /**
		 * Validates the format of the given email address
		 * Based on PEAR Validate (http://pear.php.net/package/Validate)
		 *
		 * @param value The email address whose format we are going to validate
		 * @return True if the address is a valid one or false otherwise
         */
        function validate($value)
        {
	        // the base regexp for address
			// I get these code from PEAR::Validate v0.64
	        $regex = '&^(?:                                               # recipient:
	         ("\s*(?:[^"\f\n\r\t\v\b\s]+\s*)+")|                          #1 quoted name
	         ([-\w!\#\$%\&\'*+~/^`|{}]+(?:\.[-\w!\#\$%\&\'*+~/^`|{}]+)*)) #2 OR dot-atom
	         @(((\[)?                     #3 domain, 4 as IPv4, 5 optionally bracketed
	         (?:(?:(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.){3}
	               (?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))))(?(5)\])|
	         ((?:[a-z0-9](?:[-a-z0-9]*[a-z0-9])?\.)*[a-z0-9](?:[-a-z0-9]*[a-z0-9])?)  #6 domain as hostname
	         \.((?:([^-])[-a-z]*[-a-z])?)) #7 ICANN domain names 
	         $&xi';
	
			if( preg_match($regex, $value) ){
                $this->_setError(false);
                return true;			
			} else {
                $this->_setError(ERROR_RULE_EMAIL_FORMAT_WRONG);
                return false;			
			}
        }
    }
?>