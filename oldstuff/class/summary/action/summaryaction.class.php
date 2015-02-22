<?php
	lt_include( PLOG_CLASS_PATH."class/action/action.class.php" );
	lt_include( PLOG_CLASS_PATH."class/summary/view/summaryview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/summary/view/summarycachedview.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/net/http/httpvars.class.php" );

	define('HTTP_ACCEPT_LANGUAGE_DETECTION', 0);

	/**
	 * Base action that all summary actions should extend
	 */
	class SummaryAction extends Action
	{
		
		var $_config;
		var $_locale;
		var $_userInfo;
		
		function SummaryAction( $actionInfo, $request )
		{
			$this->Action( $actionInfo, $request );
			$this->_config   =& Config::getConfig();
			$this->_locale   =& $this->_loadLocale();
			
			// load userinfo data if any
			$this->_userInfo = SessionManager::getUserInfoFromSession();
		}
		
		/**
		 * Loads the current locale. It works so that it tries to fetch the parameter "lang" from the
		 * request. If it's not available, then it will try to look for it in the session. If it is not
		 * there either, it will try to guess the most prefered language according to what the User Agent 
		 * included in the HTTP_ACCEPT_LANGUAGE string sent with the request. If none matches available 
		 * languages we have to use the value of "default_locale" and display the default language.
		 *
		 * @private
		 * @return Returns a reference to a Locale object
		 */
		function &_loadLocale()
		{
		    $requestLocale = $this->_request->getValue( "lang" );
		    $localeCode = "";
			$serverVars = HttpVars::getServer();

		    // check if there's something in the request... 
		    // if not, check the session or at least try to 
			// guess the apropriate language from the http_accept_language string
		    if( $requestLocale ) {
		        // check if it's a valid one
		        if( Locales::isValidLocale( $requestLocale )) {
		            $localeCode = $requestLocale;
		        }
		    }
		    else {
				$sessionLocale = SessionManager::getSessionValue( "summaryLang" );				
			    if( $sessionLocale ) {
		            $localeCode = $sessionLocale;
		        }
				elseif ( $this->_config->getValue( "use_http_accept_language_detection", HTTP_ACCEPT_LANGUAGE_DETECTION) == 1 )
				{
					$localeCode = $this->_matchHttpAcceptLanguages( $serverVars['HTTP_ACCEPT_LANGUAGE'] );
				}
		    }
		    
		    // check if the locale code is correct
			// and as a valid resort, use the default one if the locale ist not valid or 'false'
		    if( $localeCode === false || !Locales::isValidLocale( $localeCode ) ) {
		        $localeCode = $this->_config->getValue( "default_locale" );
		    }
		    
		    // now put whatever locale value back to the session
		    SessionManager::setSessionValue( "summaryLang", $localeCode );
		    
		    // load the correct locale
		    $locale =& Locales::getLocale( $localeCode );		    
		    
		    return( $locale );
		}
		
		/**
		 * Common things that should be performed by all SummaryAction classes
		 */
		function setCommonData( $copyFormValues = false )
		{
		    parent::setCommonData( $copyFormValues );
		    
		    $this->_view->setValue( "locale", $this->_locale );
		    $this->_view->setValue( "authuser", $this->_userInfo );
		}
		
		/**
		 * Tries to match the prefered language out of the http_accept_language string 
		 * with one of the available languages.
		 *
		 * @private
		 * @param httpAcceptLanguage the value of $_SERVER['HTTP_ACCEPT_LANGUAGE']
		 * @return Returns returns prefered language or false if no language matched.
		 */
        function _matchHttpAcceptLanguages(&$httpAcceptLanguage)
        {
			$acceptedLanguages = explode( ',', $httpAcceptLanguage );
			$availableLanguages = Locales::getAvailableLocales();
			$primaryLanguageMatch = '';

			// we iterate through the array of languages sent by the UA and test every single 
			// one against the array of available languages
			foreach($acceptedLanguages as $acceptedLang)
			{   
				// clean the string and strip it down to the language value (remove stuff like ";q=0.3")
				$acceptedLang = substr( $acceptedLang, 0, strcspn($acceptedLang, ';') );

				if (strlen($acceptedLang) > 2)
				{
					// cut to primary language
					$primaryAcceptedLang = substr($acceptedLang, 0, 2);
				}
				else
				{
					$primaryAcceptedLang = $acceptedLang;
				}
				
				// this is where we start to iterate through available languages
				foreach($availableLanguages as $availableLang)
				{ 	
					if( stristr($availableLang, $acceptedLang) !== false )
					{
						// we have a exact language match
						return $availableLang;
					}
					elseif ( stristr($availableLang, $primaryAcceptedLang) !== false && $primaryLanguageMatch == '')
					{
						// we found the first primary language match!
						$primaryLanguageMatch = $availableLang;
					}
				}
			} // foreach

			if ($primaryLanguageMatch != '')
			{
				return $primaryLanguageMatch;
			}
			else
			{
				return false;
			}
		}

	}
	
?>