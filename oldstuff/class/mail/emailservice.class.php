<?php

	/**
	 * \defgroup Mail
	 *
	 * Module that allows to easily send email messages from within pLog. The actual email sending
	 * is carried out via the PHPMailer package (http://phpmailer.sourceforge.net/) and the classes of
	 * this module are just commodity wrappers around PHPMailer.
	 * 
	 * The EmailMessage class is an abstract representation of an email message, while the EmailService
	 * class takes care of sending out the messages.	
	 */

	
    lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
    lt_include( PLOG_CLASS_PATH."class/mail/emailmessage.class.php" );
    lt_include( PLOG_CLASS_PATH."class/mail/phpmailer/class.phpmailer.php" );

    /**
     * \ingroup Mail
     *
     * Provides services to send emails via PHPs built-in smtp capabilities.
     *
     * This service can be enabled or disabled by using the "email_service_enabled" from
     * the configuration file. It also requires PHP to be built with the smtp handling
     * libraries and those must be enabled.
     *
	 * An example of how to send an email message is as follows:
	 *
	 * <pre>
	 *  $message = new EmailMessage();
	 *  $message->addTo( "address1@domain.com" );
	 *  $message->addFrom( "myself@myself.com" );
	 *  $message->setSubject( "This is a sample message" );
	 *  $message->setBody( "this is the body of the message" );
	 *  $service = new EmailService();
	 *  if( $service->sendMessage( $message ))
	 *    print( "message sent ok!" );
	 *  else
	 *    print( "error sending message" );
	 * </pre>
	 *
	 * The EmailService class hides all the details about how the message is sent, the mechanism, etc.
	 * It wil in fact use certain configuration parameters such as:
	 *
	 * - email_service_enabled to determine whether emails should be sent at all.
	 * - email_service_type can be one of these four values:
	 *   # php (requires PHP's mail() function)
	 *   # smtp (see below)
	 *   # qmail
	 *   # sendmail
	 *
	 * If email_service_type is set as <b>smtp</b>, the following settings are also required:
	 *
	 * - smtp_host name of the host used for sending the messages
	 * - smtp_port port where the SMTP is listening, 25 by default
	 * - smtp_use_authentication Wether we should perform some basic authentication agains the host
	 * 
	 * If smtp_use_authentication is set to yes, the following settings will determine the right 
	 * username and password to use:
	 *	 
	 * - smtp_username
	 * - smtp_password
	 */
    class EmailService  
    {

    	var $_config;
		var $_lastErrorMessage;

    	/**
         * Constructor
         */
		function EmailService()
        {
            $this->_config =& Config::getConfig();
			$this->_lastErrorMessage = "";
        }

        /**
         * Sends the given message.
         *
         * @param message Object from the EmailMessage class that encapsulates all the different fields
         * an email can have (quite basic, though)
         * @return Returns true if operation was successful or false otherwise.
         */
        function sendMessage( $message )
        {
        	// quit if the service has been disabled
        	if( !$this->_config->getValue( "email_service_enabled" ))
            	return false;

			$this->_lastErrorMessage = "";

            // create a phpmailer object
            $mail = new PHPMailer();

			// need to set PluginDir if we use lt_include()
			$mail->PluginDir  = PLOG_CLASS_PATH."class/mail/phpmailer/";

            // set a few fields
            $mail->ContentType  = $message->getMimeType();
            $mail->From     = $message->getFrom();
            $mail->FromName = $message->getFromName();
            $mail->Subject  = $message->getSubject();
            $mail->Body     = $message->getBody();
            // set the destination addresses
            foreach( $message->getTo() as $to )
            	$mail->AddAddress( $to );
            foreach( $message->getCc() as $cc )
            	$mail->AddCC( $cc );
            foreach( $message->getBcc() as $bcc )
            	$mail->AddBCC( $bcc );
            	
            // set the character set of the message
            $mail->CharSet = $message->getCharset();

			// set the language for error reporting
			$mail->SetLanguage( 'en', PLOG_CLASS_PATH."class/mail/phpmailer/language/" );			

            //
            // phpmailer supports
            //    php built-in mail() function
            //    qmail
            //    sendmail, using the $SENDMAIL variable
            //    smtp
            // If using smtp, we need to provide a valid smtp server, and maybe a username
            // and password if supported.
            $mailServiceType = $this->_config->getValue( "email_service_type" );

            if( $mailServiceType == "php" ) {
            	$mail->IsMail();
            }
            elseif( $mailServiceType == "qmail" ) {
            	$mail->IsQmail();
            }
            elseif( $mailServiceType == "sendmail" ) {
            	$mail->IsSendmail();
            }
            elseif( $mailServiceType == "smtp" ) {
            	$mail->IsSMTP();

                $useAuthentication = $this->_config->getValue( "smtp_use_authentication" );

                // check if we should use authentication
                if( $useAuthentication ) {
                	$smtpUsername = $this->_config->getValue( "smtp_username" );
                    $smtpPassword = $this->_config->getValue( "smtp_password" );
                    if( $smtpUsername == "" || $smtpPassword == "" ) {
                    	// this is a severe error
                        throw( new Exception( "Please provide a username and a password if you wish to use SMTP authentication" ));
                        $mail->SMTPAuth = false;
                    }
                    else {
                    	// if the checks went ok, set the authentication
                        $mail->SMTPAuth = true;
                        $mail->Username = $smtpUsername;
                        $mail->Password = $smtpPassword;
                    }
                }
            	else {
                	$mail->SMTPAuth = false;
            	}

                // set the server
                $smtpHost = $this->_config->getValue( "smtp_host" );
                $smtpPort = $this->_config->getValue( "smtp_port" );
                if( $smtpPort == "" )
                	$smtpPort = 25;

                if( $smtpHost == "" ) {
                	throw( new Exception( "You should specify an SMTP server in order to send emails." ));
                    return false;
                }
                else {
                	$mail->Host = $smtpHost;
                    $mail->Port = $smtpPort;
                }
            }
            else {
            	// if none of the above is the right one, then let's use
                // php's very own mail() as the fallback plan
            	$mail->IsMail();
                throw( new Exception( "Unrecognized value of the email_service_type setting. Reverting to PHP built-in mail() functionality" ));
            }

            // we have set up everything, send the mail
            $result = $mail->Send();

			if( !$result )
				$this->_lastErrorMessage = $mail->ErrorInfo;
				
			return( $result );
        }

		/** 
		 * Returns the last error message
		 *
		 * @return A string containing the last error message, if any
		 */
		function getLastErrorMessage()
		{
			return( $this->_lastErrorMessage );
		}
    }
?>
