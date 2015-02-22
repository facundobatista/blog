<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admindefaultview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/redirectview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/net/http/httpvars.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Logs the user out, by removing all the information from the session.
     */
    class AdminLogoutAction extends AdminAction 
	{

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminLogoutAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
            $config =& Config::getConfig();        	
			
			$this->notifyEvent( EVENT_PRE_LOGOUT );

            // remove all the information from the session
            $session = HttpVars::getSession();
            $session["SessionInfo"] = null;
            unset($session["SessionInfo"]);
            $session = Array();
            HttpVars::setSession( $session );
            session_destroy();
            
            $destinationUrl = $config->getValue( "logout_destination_url", "" );
            if( $destinationUrl == "" ) {
	            // and pass the locale to the template
	        	$this->_view = new AdminDefaultView();            
	            $locale =& Locales::getLocale( $config->getValue( "default_locale" ));
	            $url = $this->_blogInfo->getBlogRequestGenerator();
	            $blogTitle = $this->_blogInfo->getBlog();
				$logoutMessage = $this->_locale->tr("logout_message")."<br/>".$locale->pr("logout_message_2", $url->blogLink(), $blogTitle);
				$this->_view->setSuccessMessage( $logoutMessage );
			}
			else {
				// nothing else to do, just redirect the browser once we've cleaned up the session
				$this->_view = new RedirectView( $destinationUrl );				
			}
		
			
			$this->notifyEvent( EVENT_POST_LOGOUT );

            // better to return true if everything fine
            return true;
        }
    }
?>
