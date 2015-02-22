<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminsiteuserslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/cachecontrol.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Implements bulk changes of users
     */
    class AdminChangeUserStatusAction extends AdminAction 
	{

        var $_userIds;
        var $_userStatus;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminChangeUserStatusAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			$this->registerFieldValidator( "userIds", new ArrayValidator( new IntegerValidator()));
			$this->registerFieldValidator( "userStatus", new IntegerValidator() );
			$view = new AdminSiteUsersListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_incorrect_user"));
			$this->setValidationErrorView( $view );
			
			$this->requireAdminPermission( "update_user" );	
        }

        /**
         * Carries out the specified action
         */
        function _changeUserStatus()
        {
        	// Chanages the post status field by selection
            $users = new Users();
            $errorMessage = "";
			$successMessage = "";
			$totalOk = 0;
			
            foreach( $this->_userIds as $userId ) {
            	// get the post
                $user = $users->getUserInfoFromId( $userId );
				
				if( $user ) {
					// fire the event
					$this->notifyEvent( EVENT_PRE_USER_UPDATE, Array( "user" => &$user ));
					
					// update the post status
					$user->setStatus( $this->_userStatus );
					$result = $users->updateUser( $user );
					
					if( !$result ) {
						$errorMessage .= $this->_locale->pr("error_updating_user", $user->getUsername())."<br/>";
					}
					else {
						$totalOk++;
						if( $totalOk < 2 ) 
							$successMessage .= $this->_locale->pr("user_updated_ok", $user->getUsername())."<br/>";
						else
							$successMessage = $this->_locale->pr("users_updated_ok", $totalOk );
						// fire the post event
						$this->notifyEvent( EVENT_POST_BLOG_UPDATE, Array( "user" => &$user ));					
					}
				}
				else {
					$errorMessage .= $this->_locale->pr( "eror_updating_user2", $userId )."<br/>";
				}
            }
			
			$this->_view = new AdminSiteUsersListView( $this->_blogInfo );
			if( $errorMessage != "" ) 
				$this->_view->setErrorMessage( $errorMessage );
			if( $successMessage != "" )
				$this->_view->setSuccessMessage( $successMessage );
				
			$this->setCommonData();
			
            return true;
        }
		
		function perform()
		{
			// prepare the parameters.. If there's only one category id, then add it to
			// an array.
			$this->_userIds = $this->_request->getValue( "userIds" );
			$this->_userStatus = $this->_request->getValue( "userStatus" );
				
			$this->_changeUserStatus();
		}
    }
?>