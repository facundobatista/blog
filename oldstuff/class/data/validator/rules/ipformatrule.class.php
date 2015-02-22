<?php

    lt_include(PLOG_CLASS_PATH."class/data/validator/rules/regexprule.class.php");

    define( "IP_FORMAT_RULE_REG_EXP", "^([0-9]{1,3})\\.([0-9]{1,3})\\.([0-9]{1,3})\\.([0-9]{1,3})$");
    define( "ERROR_RULE_IP_FORMAT_WRONG", "error_rule_ip_format_wrong");

    /**
	 * \ingroup Validator_Rules
	 *
	 * Returns true if a given IP address is in valid CIDR format (Classless Inter-Domain Routing) These addresses
	 * should have the following format: 
	 *
	 * <pre>xxx.yyy.zzz.www</pre>
	 *
	 * where each one of the elements is an integer between 0 and 255.
     */
    class IpFormatRule extends RegExpRule
    {
        /**
         * Initializes the rule
         */
        function IpFormatRule()
        {
            $this->RegExpRule(IP_FORMAT_RULE_REG_EXP, false);
        }

        /**
         * Retursn true if the given IP address is in valid CIDR format
         *
         * @param value the IP address to validate
         */
        function validate($value)
        {
            if (!ereg($this->_regExp, $value, $regs))
            {
                $this->_setError(ERROR_RULE_IP_FORMAT_WRONG);
                return false;
            }
            else if ($regs[1] > 255 || $regs[2] > 255 || $regs[3] > 255 || $regs[4] > 255)
            {
                $this->_setError(ERROR_RULE_IP_FORMAT_WRONG);
                return false;
            }
            else
            {
                $this->_setError(false);
                return true;
            }
        }
    }
?>