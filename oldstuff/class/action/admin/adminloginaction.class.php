<?php

	lt_include( PLOG_CLASS_PATH."class/action/action.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admindashboardview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admindefaultview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
    lt_include( PLOG_CLASS_PATH."class/net/http/session/sessioninfo.class.php" );
	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	lt_include( PLOG_CLASS_PATH."class/misc/version.class.php" );
	lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/usernamevalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminnewpostview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * When the user fills in the login form, we jump to this action which will show
     * another form when the user will choose to which of the blog he or she wants to
     * carry out administrative tasks.
     */
    class AdminLoginAction extends Action 
	{

    	var $_userName;
        var $_userPassword;
        var $_locale;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminLoginAction( $actionInfo, $request )
        {
        	$this->Action( $actionInfo, $request );

            $this->_config =& Config::getConfig();
            $this->_locale =& Locales::getLocale( $this->_config->getValue( "default_locale" ));

			// data validation
			$this->registerFieldValidator( "userName", new UsernameValidator());
			$this->registerFieldValidator( "userPassword", new StringValidator());
			$view = new AdminDefaultView();
			$view->setErrorMessage( $this->_locale->tr("error_incorrect_username_or_password"));
			$this->setValidationErrorView( $view );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
        	// get the parameters, which have already been validated
            $this->_userName     = Textfilter::filterAllHTML($this->_request->getValue( "userName" ));
            $this->_userPassword = $this->_request->getValue( "userPassword" );

			// create a plugin manager
			$pm =& PluginManager::getPluginManager();	
		
        	// try to authenticate the user
            $users = new Users();
            if( !$users->authenticateUser( $this->_userName, $this->_userPassword )) {
            	$this->_view = new AdminDefaultView();
                $this->_view->setErrorMessage( $this->_locale->tr("error_incorrect_username_or_password"));
				$this->setCommonData();
				
				$pm->notifyEvent( EVENT_LOGIN_FAILURE, Array( "user" => $this->_userName ));
                return false;
            }
			
            // if the user is correct, get and put his or her information in the session
            $userInfo = $users->getUserInfoFromUsername( $this->_userName );
			
			if( !$userInfo ) {
            	$this->_view = new AdminDefaultView();
                $this->_view->setErrorMessage( $this->_locale->tr("error_incorrect_username_or_password"));
				$this->setCommonData();
				
				$pm->notifyEvent( EVENT_LOGIN_FAILURE, Array( "user" => $this->_userName ));
                return false;
			}
			
			// check if the user has the "login_perm" permission and is allowed to log in
			if( !$userInfo->hasPermissionByName( "login_perm" )) {
            	$this->_view = new AdminDefaultView();
                $this->_view->setErrorMessage( $this->_locale->tr("error_cannot_login"));
				$this->setCommonData();
				
				$pm->notifyEvent( EVENT_LOGIN_FAILURE, Array( "user" => $this->_userName ));
                return false;
			}
			
            $pm->notifyEvent( EVENT_LOGIN_SUCCESS );							
			$pm->notifyEvent( EVENT_USER_LOADED, Array( "user" => &$userInfo, "from" => "Login" ));

            // get the list of blogs to which the user belongs
            $userBlogs = $users->getUsersBlogs( $userInfo->getId(), BLOG_STATUS_ACTIVE );

            // but if he or she does not belong to any yet, we quit
            if( empty($userBlogs)) {
            	$this->_view = new AdminDefaultView();
                $this->_view->setErrorMessage( $this->_locale->tr("error_dont_belong_to_any_blog"));
				$this->setCommonData();

                return false;
            }

            // We have to update the userInfo in session after we check all situations
            $session = HttpVars::getSession();
            $sessionInfo = $session["SessionInfo"];

            $sessionInfo->setValue( "userInfo", $userInfo );
            $session["SessionInfo"] = $sessionInfo;
            HttpVars::setSession( $session );
			
			$pm->notifyEvent( EVENT_BLOGS_LOADED, Array( "blogs" => &$userBlogs, "from" => "Login" ));			
			
			// check if we are skipping the dashboard
			if( $this->_config->getValue( "skip_dashboard" )) {
				// get the first blog that came
				$this->_blogInfo = $userBlogs[0];
				// set it in the session
            	$session = HttpVars::getSession();
            	$sessionInfo->setValue( "blogInfo", $this->_blogInfo );
            	$session["SessionInfo"] = $sessionInfo;
            	HttpVars::setSession( $session );			
            	// and then continue...
            	if( $userInfo->hasPermissionByName( "new_post", $this->_blogInfo->getId()))
					AdminController::setForwardAction( "newPost" );
				else
					AdminController::setForwardAction( "Manage" );
			}
			else {
				$this->_view = new AdminDashboardView( $userInfo, $userBlogs );	
			}
            // better to return true if everything's fine
            return true;
        }
    }
?>
