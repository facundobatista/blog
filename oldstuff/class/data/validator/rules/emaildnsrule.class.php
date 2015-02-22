<?php

    lt_include(PLOG_CLASS_PATH."class/data/validator/rules/rule.class.php");
    lt_include(PLOG_CLASS_PATH."class/net/dns.class.php");
    lt_include(PLOG_CLASS_PATH."class/net/http/httpvars.class.php");
    lt_include(PLOG_CLASS_PATH."class/config/config.class.php");
    lt_include(PLOG_CLASS_PATH."class/data/textfilter.class.php");

    define( "ERROR_RULE_EMAIL_DNS_SERVER_UNREACHABLE", "error_rule_email_dns_server_unreachable");
    define( "ERROR_RULE_EMAIL_DNS_SERVER_TEMP_FAIL", "error_rule_email_dns_server_temp_fail");
    define( "ERROR_RULE_EMAIL_DNS_NOT_PERMITTED", "error_rule_email_dns_not_permitted");
    define( "ERROR_RULE_EMAIL_DNS_BAD_DOMAIN", "error_rule_email_dns_bad_domain");

    /**
	 * \ingroup Validator_Rules
	 *
	 * Given an email address it will connect to the MX server listed for the given domain
	 * and check whether the given user name has a valid mailbox in the server. This operation
	 * is a bit costly concerning time, since it takes a while to carry out these operations.
	 *
	 * This class will set one of these errors:
	 *
	 * - ERROR_RULE_EMAIL_DNS_NOT_PERMITTED
	 * - ERROR_RULE_EMAIL_DNS_SERVER_UNREACHABLE
     */
    class EmailDnsRule extends Rule
    {
        /**
         * The constructor does nothing.
         */
        function EmailDnsRule()
        {
            $this->Rule();
        }

        /**
		 * Checks the given email address
         */
        function validate($value)
        {
            if (empty($value))
            {
                $this->_setError(false);
                return true;
            }

            list($userName, $domain) = explode("@", $value);

                // check input
            $clean_domain = Textfilter::domainize($domain);
            if($clean_domain != $domain){
                $this->_setError(ERROR_RULE_EMAIL_DNS_BAD_DOMAIN);
                return false;
            }

            $connectAddress          = $domain;

            if (!Dns::checkdnsrr($domain, "A"))
            {
                $this->_setError(ERROR_RULE_EMAIL_DNS_SERVER_UNREACHABLE);
                return false;
            }

            if (Dns::checkdnsrr($domain, "MX") && Dns::getmxrr($domain, $mxHosts))
            {
                $connectAddress = $mxHosts[0];
            }

            if ($connect = fsockopen($connectAddress, 25))
            {
                $greeting = fgets($connect, 1024);

                if (ereg("^220", $greeting))
                {
                    $server = &HttpVars::getServer();
                    fputs($connect, "HELO " . $server["HTTP_HOST"] . "\r\n");
                    $helo = fgets($connect, 1024);

                    $config =& Config::getConfig();
                    $lt_from = $config->getValue("post_notification_source_address");
                    if($lt_from == "")
                        $lt_from = $value;
                    
                    fputs($connect, "MAIL FROM: <" . $lt_from . ">\r\n");
                    $from = fgets($connect, 1024);

                    fputs($connect, "RCPT TO: <" . $value .">\r\n");
                    $to = fgets($connect, 1024);

                    fputs($connect, "QUIT\r\n");
                    fclose($connect);

                    if (!ereg("^250", $from) || !ereg ("^250", $to))
                    {
                        if(ereg("^4[0-9][0-9]", $helo) || ereg("^4[0-9][0-9]", $from) || ereg ("^4[0-9][0-9]", $to)){
                            $this->_setError(ERROR_RULE_EMAIL_DNS_SERVER_TEMP_FAIL);
                                // Note: see http://bugs.lifetype.net/view.php?id=718 to fix this
                            return true;
                        }
                        else{
                            $this->_setError(ERROR_RULE_EMAIL_DNS_NOT_PERMITTED);
                            return false;
                        }
                    }
                }
                else if(ereg("^4[0-9][0-9]", $greeting)){
                    $this->_setError(ERROR_RULE_EMAIL_DNS_SERVER_TEMP_FAIL);
                    return false;
                }
            }
            else
            {
                $this->_setError(ERROR_RULE_EMAIL_DNS_SERVER_UNREACHABLE);
                return false;
            }

            return true;
        }
    }
?>
