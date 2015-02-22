<?php
	
	class SummaryMailer
	{
        /**
         * send confirm email to user.
         * user will activate his/her account according to this email
		 *
		 * @static
         */
        function sendConfirmationEmail( $userName ) 
		{
			lt_include( PLOG_CLASS_PATH."class/summary/mail/confirmemailmessage.class.php" );		
			lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
			lt_include( PLOG_CLASS_PATH."class/mail/emailservice.class.php" );			
			lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );

            $activeCode = SummaryMailer::generateActiveCode();

            // store the active code to the backend db in the properties field of user table
            $users = new Users();
            $userInfo = $users->getUserInfoFromUsername( $userName );
            $userInfo->setProperties(Array("activeCode"=>$activeCode));
            $users->updateUser($userInfo);


			$config =& Config::getConfig();
            $message = new ConfirmEmailMessage();
            $message->setFrom( $config->getValue( "post_notification_source_address" ));
            $message->addTo( $userInfo->getEmail());
            $locale =& Locales::getLocale();
            $message->setSubject( $locale->tr( "registration_default_subject" ));            
            $message->setUsername($userName);
            $message->setActiveCode($activeCode);

            // create active Link
            $base_url = $config->getValue("base_url");
            $message->setActiveLink($base_url."/summary.php?op=activeAccount&username="
                    .$userName."&activeCode=".$activeCode);
            $message->createBody();

            $service = new EmailService();
            $service->sendMessage( $message );
        }
		
        /**
         * generate a random active code based on current time
         * @return a string that random generated
         * @access private
		 * @static
         */
        function generateActiveCode()
		{
            srand((double)microtime() * 10000000);  
            $activeCode = md5(time() . rand(1, 10000000));
            return $activeCode;
        }
	}
?>