<?php

	lt_include( PLOG_CLASS_PATH."class/data/validator/validator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/rules/emailformatrule.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/rules/emaildnsrule.class.php" );    

    /**
     * \ingroup Validator
     * 
     * Implements validation of email addresses. If check_email_address_validity is set to
     * enabled, it will also check whether the email address is valid in the given email server
     * by adding an EmailDnsRule to the list of rules to validate.
     *
     * @see EmailFormatRule
     * @see EmailDnsRule
     */
    class EmailValidator extends Validator
    {
        /**
         * The constructor only initializes the validator and depending on the 
         * value of check_email_address_validity, it will also add a EmailDnsRule
         * rule to check for the validity of the mailbox in the given email server
         */
        function EmailValidator()
        {
            $this->Validator();

            $this->addRule(new EmailFormatRule());
            
            $config =& Config::getConfig();

            if ( $config->getValue( "check_email_address_validity" )) {
                $this->addRule(new EmailDnsRule());
            }
        }
    }
?>
