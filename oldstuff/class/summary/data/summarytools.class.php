<?php

	
    lt_include( PLOG_CLASS_PATH."class/mail/emailmessage.class.php" );
    lt_include( PLOG_CLASS_PATH."class/mail/emailservice.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );

	class SummaryTools 
	{
		
		/**
		 * returns the url that will effectively allow users to enter a new
		 * password
		 */
		function calculatePasswordResetHash( $userInfo ) 
		{
			$string = $userInfo->getPassword().$userInfo->getEmail().$userInfo->getId();
			$requestHash = md5($string);
			
			return $requestHash;
		}
		
		/**
		 * sends the email with the request
		 * @private
		 */
		function sendResetEmail( $userInfo, $url )
		{
			// prepare the template
            $templateService = new TemplateService();
            $template = $templateService->Template( "resetpasswordemail", "summary" );
			$template->forceDisableTrimWhitespace = true;
            $template->assign( "locale", $this->_locale );
			$template->assign( "reseturl", $url );
			
			// render it and keep its contents
			$emailBody = $template->fetch();
			
            $message = new EmailMessage();
			$config =& Config::getConfig();
            $message->setFrom( $config->getValue( "post_notification_source_address" ));
            $message->addTo( $userInfo->getEmail());
			// get the default locale
			$locale =& Locales::getLocale();
            $message->setSubject( $locale->tr("password_reset_subject"));
            $message->setBody( $emailBody );

//			print_r($message);

            $service = new EmailService();
            return $service->sendMessage( $message );
		}
		
		function verifyRequest( $userNameHash, $requestHash )
		{		
			// make sure that the request is correct
			lt_include( PLOG_CLASS_PATH."class/database/db.class.php" );
			$users = new Users();

			$db =& Db::getDb();
			$prefix = Db::getPrefix();
			
			$query = "SELECT id, user, password, email, about, full_name, properties, 
					  site_admin, resource_picture_id, status
					  FROM {$prefix}users 
					  WHERE MD5(user) = '".Db::qstr($userNameHash)."'
					  AND status = ".USER_STATUS_ACTIVE;
										
			$result = $db->Execute( $query );
			if( !$result )
				return false;
				
			$row = $result->FetchRow();
			$userInfo = $users->mapRow( $row );
			
			// try to see if we can load the user...
			if( !$userInfo ) 
				return false;

			// and if so, validate the hash
			$originalRequestHash = SummaryTools::calculatePasswordResetHash( $userInfo );
			if( $requestHash != $originalRequestHash )
				return false;
				
			return $userInfo;
		}
	}
?>