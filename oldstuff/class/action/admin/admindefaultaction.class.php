<?php

	lt_include( PLOG_CLASS_PATH."class/action/action.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admindefaultview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/net/http/httpvars.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Sample action on how to develop our own actions.
     *
     * Please also refer to SampleView.class.php for more information
     */
    class AdminDefaultAction extends Action 
    {

    	var $_blogInfo;
        var $_userInfo;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminDefaultAction( $actionInfo, $request )
        {
        	$this->Action( $actionInfo, $request );
        }

        function sessionInfoAvailable()
        {
        	$session = HttpVars::getSession();

            if( isset($session["SessionInfo"])) {
            	$sessionInfo = $session["SessionInfo"];
                $this->_blogInfo    = $sessionInfo->getValue( "blogInfo" );
                $this->_userInfo    = $sessionInfo->getValue( "userInfo" );
                if( empty($this->_blogInfo) || empty($this->_userInfo) )
                	return false;
                else
                	return true;
            }
            else
            	return false;
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
        	// initialize the view, but we first check if there is session information avaible
            // since in that case we will not make the user choose enter user and pass again, but
            // show the main menu page straight away
			
            if( $this->sessionInfoAvailable()) {
            	AdminController::setForwardAction( "emptyAction" );
				// launch the event since we have all the info we need
				$pm =& PluginManager::getPluginManager();
				$pm->setBlogInfo( $this->_blogInfo );
				$pm->setUserInfo( $this->_userInfo );
            }
            else {
        		$this->_view = new AdminDefaultView();
            }

            // better to return true if everything fine
            return true;
        }
    }
?>
