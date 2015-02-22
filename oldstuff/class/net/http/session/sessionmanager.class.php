<?php

	
    lt_include( PLOG_CLASS_PATH."class/net/url.class.php" );
    lt_include( PLOG_CLASS_PATH."class/net/http/httpvars.class.php" );
	lt_include( PLOG_CLASS_PATH."class/net/http/session/sessioninfo.class.php" );
	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	
	$__sessionManagerInitialized = false;
	
	/**
	 * \ingroup Net_HTTP
	 *
	 * Class that deals with session, initializing, session cookies, etc. It also takes care of setting
	 * the session folder paths, by calling the method SessionManager::setSessionSavePath().
	 */
	class SessionManager 
	{
	
		/**
		 * static method that takes care of initializing a session if there is none yet. It will also call
		 * setSessionSavePath() in order to tell PHP where sessions should be read and stored.
		 *
		 * @return nothing
		 * @static
		 * @see SessionInfo
		 * @see SessionManager::setSessionSavePath()
		 */
		function init()
		{
			global $__sessionManagerInitialized;

			if( !$__sessionManagerInitialized )
			{
				// this needs to be done before anything else
				SessionManager::setSessionSavePath();
			
				// this needs to be done before the session is started
	            $sessionPath   = SessionManager::setSessionCookiePath();
				//$sessionPath = "/";
	            $sessionDomain = SessionManager::setSessionCookieDomain();
			
				session_cache_limiter( "" );
				// Use base_url as part of session name to support multiple lifetype installations in the one site
				$config =& Config::getConfig();
				$baseURL = $config->getValue( "base_url" );
	            $sessionName = "LT" . preg_replace("/[^a-zA-Z0-9]/", "", $baseURL);
	            session_name( $sessionName );
	
	            session_set_cookie_params(0, $sessionPath, $sessionDomain);
				session_start();
	
				//
				// if there is no session object, we better create one
				//
				$session = HttpVars::getSession();
				if( !array_key_exists("SessionInfo", $session) || empty($session["SessionInfo"]) ) {
					$session["SessionInfo"] = new SessionInfo();
					HttpVars::setSession( $session );
				}
				
				// inform the other methods that the session manager has already been intialized
				$__sessionManagerInitialized = true;
			}
		}
		
		/**
		 * @private
		 * @static
		 * Sets the session cookie path to the right path
		 * @return nothing
		 */
		function setSessionCookiePath()
		{
			// get the current base url and fetch it dynamically according to the values
			// returned by Apache/PHP if not set yet in the config settings
			$config =& Config::getConfig();
			$scriptUrl = $config->getValue( "base_url" );
			if( $scriptUrl == "" )
				$scriptUrl = HttpVars::getBaseUrl();
				
			$url = new Url( $scriptUrl );
			$path = dirname($url->getPath());

			if( $path == "" || $path == "\\" ) 
				$path = "/";			
		
			return $path;
		}
		
		/**
		 * @private
		 * @static
		 * Sets the right domain for the cookie
		 * @return nothing
		 */
		function setSessionCookieDomain()
		{
            $scriptUrl = HttpVars::getBaseUrl();
							
			$url = new Url( $scriptUrl );
			$domain = preg_replace("/^www\./", ".", $url->getHost());
			
			// this won't work for top level domains and domains such as
			// 'localhost' or internal domains for obvious security reasons...
			// See comments in http://fi.php.net/manual/en/function.session-set-cookie-params.php
			if( count(explode('.', $domain)) > 1 ) {
				return $domain;				
			}
		}
		
		/**
		 * Sets the folder where sessions should be saved, in case we'd like to save
		 * them somewhere else. This class will check the config parameter <b>session_save_path</b>: if
		 * it's not empty, it will use its value as the name of the folder where sessions should be saved, and it
		 * will also take care of creating the folder if it does not exist. If the folder exists but it cannot
		 * be read, it will throw an exception and quit (because this is a big issue)
		 * If the value of this parameter is empty, it will not do anything and use PHP's default settings for this.
		 *
		 * @static
		 */
		function setSessionSavePath()
		{
			$config =& Config::getConfig();
			$sessionFolder = $config->getValue( "session_save_path" );
			// do we need to do anything if we are using the default
			// session path?  PHP defaults to /tmp/, so there isn't
			// anything to do
			if( isset( $sessionFolder )) {
				if( !File::exists( $sessionFolder )) {
					// create folder with only user permissions
					// since we want to protect the session data
					if( !File::createDir( $sessionFolder, 0700 )) {
						throw( new Exception( "Sessions should be " .
							"saved in $sessionFolder but it " .
							"doesn't exist and I can't create it!" ));
						die();
					}
				}

				// check if the folder is accessible
				if( !File::isReadable( $sessionFolder ) ||
					!File::isWritable( $sessionFolder )) {
					if( !File::chMod( $sessionFolder, 0700 )) {
						throw( new Exception( "Sessions should be " .
							"saved in $sessionFolder but it is " . 
							"not accessible!" ));
						die();
					}
				}
				// if everything ok, we can continue...
				session_save_path( $sessionFolder );
			}
			
			return true;
		}
		
		/**
		 * return the value of a parameter from the session
		 *
		 * @param key The name of the parameter
		 * @return The value assigned to this key
		 */
		function getSessionValue( $key )
		{
		    // switch that informs whether the session manager has already been initialized or not
		    global $__sessionManagerInitialized;
		    
		    // check if the session manager has already been initialized
		    if( !$__sessionManagerInitialized ) {
		        throw( new Exception( "SessionManager::init() must be called before SessionManager::getSessionValue()" ));
		        die();
		    }
		    
		    // get the session and SessionInfo object
		    $session = HttpVars::getSession();
		    $sessionInfo = $session["SessionInfo"];
		    
		    // and now return the value
		    return( $sessionInfo->getValue( $key ));
		}
		
		/**
		 * sets a value in the session
		 *
		 * @param key the key assigned to this vlaue
		 * @param value The value assigned
		 * @return always true
		 */
		function setSessionValue( $key, $value )
		{
		    // switch that informs whether the session manager has already been initialized or not
		    global $__sessionManagerInitialized;

		    // check if the session manager has already been initialized
		    if( !$__sessionManagerInitialized ) {
		        throw( new Exception( "SessionManager::init() must be called before SessionManager::getSessionValue()" ));
		        die();
		    }
		    
		    // get the session and SessionInfo object
		    $session = HttpVars::getSession();
		    $sessionInfo = $session["SessionInfo"];
		    // set the value and save it to the session
		    $sessionInfo->setValue( $key, $value );
		    $session["SessionInfo"] = $sessionInfo;
		    HttpVars::setSession( $session );
		    
		    return true;
		}

		/**
		 * Retrieve the UserInfo object from the session, if any
		 *
		 * @return A UserInfo object if found, or false otherwise
		 */
		function getUserInfoFromSession()
		{
	        $session = HttpVars::getSession();
	        $sessionInfo = $session["SessionInfo"];
	        $userInfo = $sessionInfo->getValue("userInfo");
			if( !is_object( $userInfo ))
				return false;
			
			return( $userInfo );
		}
	}
?>