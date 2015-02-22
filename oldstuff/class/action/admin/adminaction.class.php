<?php

	lt_include( PLOG_CLASS_PATH."class/action/action.class.php" );
    lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );
    lt_include( PLOG_CLASS_PATH."class/net/http/httpvars.class.php" );
    lt_include( PLOG_CLASS_PATH."class/template/templateservice.class.php" );
    lt_include( PLOG_CLASS_PATH."class/misc/version.class.php" );
	lt_include( PLOG_CLASS_PATH."class/plugin/pluginmanager.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/cachecontrol.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/admindefaultview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
    lt_include( PLOG_CLASS_PATH."class/security/pipeline.class.php" );
	
	/**
	 * @see AdminAction::requirePermission()
	 */
	define( "ADMIN_PERMISSION", 1 );
	define( "BLOG_PERMISSION", 2 );

    /**
     * \ingroup Action
     *
     * In the same way BlogAction sets some predefined information to be available for every action
     * for the public side of the blog, this one does the same but for the administrative interface.
     * So far it fetches information from the session, such as the UserInfo and the BlogInfo objects
     * so that they are available for every template.
     *
     * This is the most basic action for the admin interface and it allows all users to see them. If you
     * need to create an action that can only be accessed by users with certain privileges,
     * please use BlogOwnerAdminAction and SiteAdminAction.
     *
     * @see BlogOwnerAdminAction
     * @see SiteAdminAction
     */
    class AdminAction extends Action 
	{

    	var $_blogInfo;
        var $_userInfo;
        var $_session;
        var $_config;
        var $_locale;
		var $_pm;
		var $_userBlogs;
		var $_permissions;

        /**
         * Constructor.
         *
         * @param actionInfo An ActionInfo object as provided by the constroller
         * @param request A valid HTTP request
         */
        function AdminAction( $actionInfo, $request )
        {
        	$this->Action( $actionInfo, $request );

			// permission stuff
			$this->_permissions = Array();
	
            // get information about the session
            $session = HttpVars::getSession();
            $this->_session = $session["SessionInfo"];

            $this->_config  =& Config::getConfig();

            // get the information about the user and quit if we don't have it...
            $this->_getUserInfo();
            if( empty( $this->_userInfo ) ) {
            	header( "HTTP/1.0 403 Forbidden" );
                print($this->mustAuthenticatePage());
                die();
            }

            // do the same with the information about the blog
            $this->_getBlogInfo();
            if( empty( $this->_blogInfo )) {
            	if( $this->_actionInfo->getActionParamValue() != "blogSelect" &&
            	    $this->_actionInfo->getActionParamValue() != "registerBlog" &&
            	    $this->_actionInfo->getActionParamValue() != "finishRegisterBlog" ) {
                	header( "HTTP/1.0 403 Forbidden" );
                	print($this->mustAuthenticatePage());
                    die();
                }
            }
			
			// prepare the plugin manager in case we'd like to throw events
			$this->_pm =& PluginManager::getPluginManager();			
			
			// fetch the site locale
            $this->_locale =& $this->getLocale();

			$users =& new Users();
            $this->_userBlogs = $users->getUsersBlogs( $this->_userInfo->getId(), BLOG_STATUS_ACTIVE );            
			// in case we're in "admin mode" (where administrators can log into anybody's blog), we should also
			// display the current blog in the drop-down list on the top left corner, if only to make it clear to
			// the user that this is another completely different blog
            if( !empty( $this->_blogInfo ) && $this->_blogInfo->getOwnerId() != $this->_userInfo->getId() &&  $this->_userInfo->isSiteAdmin() ) {
				$find = false;
            	foreach( $this->_userBlogs as $userBlog ) {
					if( $userBlog->getId() == $this->_blogInfo->getId() ) {
						$find = true;
						break;
			        }
	            }
	            
			    if( !$find ) {
			        $this->_userBlogs[] = $this->_blogInfo;
			    }
            }            

            //
            // security stuff
            //
            if(!empty($this->_blogInfo)){
                $pipeline = new Pipeline($request, $this->_blogInfo);
                $result = $pipeline->process();
                    //
                    // if the pipeline blocked the request, then we have
                    // to let the user know
                if(!$result->isValid()){
                    if(!$result->hasView()){
                            // use the default view
                        lt_include(PLOG_CLASS_PATH."class/view/admin/adminerrorview.class.php");
                        $message = $this->_locale->tr('error_you_have_been_blocked').'<br/><br/>';
                        $message .= $result->getErrorMessage();
                        $this->_view = new AdminErrorView($this->_blogInfo);
                        $this->_view->setMessage($message);
                    }
                    else{
                            // if the filter that forced the processing to stop provided
                            // its own view, then use it				
                        $this->_view = $result->getView();
                    }
                    $this->setCommonData();
                    $this->_view->render();
                    
                    die();
                }
            }
        }

        /**
         * Retrieves the blogInfo object from the session
         * @private
         */
        function _getBlogInfo()
        {
            $session = HttpVars::getSession();
            $sessionInfo = $session["SessionInfo"];

            $this->_blogInfo = $sessionInfo->getValue( "blogInfo" );
        }

        /**
         * Retrieves the userInfo object from the session
         * @private
         */
        function _getUserInfo()
        {
            $session = HttpVars::getSession();
            $sessionInfo = $session["SessionInfo"];
            $this->_userInfo = $sessionInfo->getValue("userInfo");
        }

        /**
         * sets the default locale, in case we want to send localized messages to the user.
         * @private
         */
        function &getLocale()
        {
        	// don't like this so much...
        	if( !empty( $this->_blogInfo ) ) {
        		$this->_blogSettings = $this->_blogInfo->getSettings();
            	//$locale =& Locales::getLocale( $this->_blogSettings->getValue("locale"));
				$locale =& $this->_blogInfo->getLocale();
            }
            else {
            	$locale =& Locales::getLocale( $this->_config->getValue("default_locale"));
            }
			
			return $locale;
        }

        /**
         * Adds some common data to the view. this function must be manually called once
         * we've set up a view.
         *
         * @param copyFormValues
         * @see Action::setCommonData()
         */
        function setCommonData( $copyFormValues = false )
        {	
			parent::setCommonData( $copyFormValues );

			// initialiaze plugins
			$this->_pm->setBlogInfo( $this->_blogInfo );
			$this->_pm->setUserInfo( $this->_userInfo );
			$this->_pm->getPlugins();			
			
        	$this->_view->setValue( "user", $this->_userInfo );
        	$this->_view->setValue( "userBlogs", $this->_userBlogs);
			$this->_view->setUserInfo( $this->_userInfo );
            $this->_view->setValue( "blog", $this->_blogInfo );
            if( $this->_blogInfo )
            	$this->_view->setValue( "blogsettings", $this->_blogInfo->getSettings());
            $this->_view->setValue( "op", $this->_actionInfo->_actionParamValue );
			$this->_view->setValue( "locale", $this->_locale );
			$this->_view->setValue( "config", $this->_config );
        }

        /**
         * Saves the session data
         * @private
         */
        function saveSession()
        {
        	if( !empty( $this->_blogInfo ) )
        		$this->_session->setValue( "blogId", $this->_blogInfo->getId() );
        	if( !empty( $this->_userInfo ) )
            	$this->_session->setValue( "userInfo", $this->_userInfo );
        	//$_SESSION["SessionInfo"] = $this->_session;
            $session = HttpVars::getSession();
            $session["SessionInfo"] = $this->_session;
            HttpVars::setSession( $session );
        }

        /**
         * Generates a page which shows an "access forbidden" message, prompting the user to
         * authenticate first using the login page.
         * @private
         */
        function mustAuthenticatePage()
        {
			$locale = $this->getLocale();		
			$config =& Config::getConfig();			
			$destinationUrl = $config->getValue( "logout_destination_url", "" );
            if( $destinationUrl == "" ) {
				$view = new AdminDefaultView();
			}
			else {
				// nothing else to do, just redirect the browser once we've cleaned up the session
				lt_include( PLOG_CLASS_PATH."class/view/redirectview.class.php" );				
				$view = new RedirectView( $destinationUrl );							
			}
			$view->setErrorMessage( $locale->tr("error_access_forbidden" ));			
			
			return $view->render();
        }
		
		/**
		 * centralized way of throwing events, it also adds some useful information so that
		 * child classes do not have to do it
		 *
		 * @param eventType
		 * @param params
		 *
		 * @see PluginManager::notifyEvent()
		 */
		function notifyEvent( $eventType, $params = Array())
		{
			$params[ "from" ] = $this->_actionInfo->getActionParamValue();
			$params[ "request" ] = $this->_request;
			
			return $this->_pm->notifyEvent( $eventType, $params );
		}
		
		/**
		 * Returns true if the user has the requested permission (in the given mode)
		 * or false otherwise
		 *
		 * @param permName Name of the permission
		 * @param mode Either BLOG_PERMISSION or ADMIN_PERMISSION, depending on whether
		 * we're checking the user's permissions in this blog or an admin permission
		 */
		function userHasPermission( $permName, $mode = BLOG_PERMISSION )
		{			
			// check for the permission, whether the user is the blog owner or
			// whether the user is a site administrator
			$hasPermission = false;
			if( $mode == BLOG_PERMISSION ) {
		    	$hasPermission = ( 
		    		$this->_userInfo->hasPermissionByName( $permName, $this->_blogInfo->getId()) ||
		    		$this->_blogInfo->getOwnerId() == $this->_userInfo->getId() ||
					$this->_userInfo->hasPermissionByName( "edit_blog_admin_mode", 0 )
		    	);
			}
			else {				
		    	$hasPermission = ( $this->_userInfo->hasPermissionByName( $permName, 0 ));
			}
			
			return( $hasPermission );
		}
		
		/**
		 * tbd
		 */
		function canPerform()
		{
			foreach( $this->getRequiredPermissions() as $permData ) {
				if( !$this->userHasPermission( $permData["perm"], $permData["mode"] ))
					return( false );
			}
			
			return( true );
		}
		
		/**
		 * This method should be called by action classes to specify
		 * what kind of permission is required to execute the current action.
		 *
		 * @param perm The name of the permission, given as a string
		 * @param mode Either ADMIN_PERMISSION if the permission is an admin permission
		 * or BLOG_PERMISSION if the permission is a blog permission		 
		 */
		function requirePermission( $perm, $mode = BLOG_PERMISSION )
		{
			$this->_permissions[] = Array( "perm" => $perm, "mode" => $mode );
		}
		
		/**
		 * Informs the action that the given admin permission is required
		 *
		 * @param perm An admin permission
		 * @see requireAdminPermission
		 */
		function requireAdminPermission( $perm )
		{
			$this->_permissions[] = Array( "perm" => $perm, "mode" => ADMIN_PERMISSION );
		}		
		
		/**
		 * tbd
		 */
		function getRequiredPermissions()
		{
			return( $this->_permissions );
		}		
    }
?>