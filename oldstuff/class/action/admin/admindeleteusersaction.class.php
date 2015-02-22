<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminsiteuserslistview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
	 * disables users from the site (it doesn not actually remove them!!)
	 */
	class AdminDeleteUsersAction extends AdminAction
	{

    	var $_userIds;
    	var $_op;

    	function AdminDeleteUsersAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			// set up the data validation stuff
        	$this->_op = $actionInfo->getActionParamValue();
        	if( $this->_op == "deleteUsers" )
        		$this->registerFieldValidator( "userIds", new ArrayValidator( new IntegerValidator()));
        	else
        		$this->registerFieldValidator( "userId", new IntegerValidator());
        	$view = new AdminSiteUsersListView( $this->_blogInfo );
        	$view->setErrorMessage( $this->_locale->tr("error_no_users_selected"));
        	$this->setValidationErrorView( $view );

			$this->requirePermission( "update_user" );
        }

        function perform()
        {
        	if( $this->_op == "deleteUser" ) {
        		$userId = $this->_request->getValue( "userId" );
        		$this->_userIds = Array();
        		$this->_userIds[] = $userId;
        	}
        	else {
        		$this->_userIds = $this->_request->getValue( "userIds" );
			}

        	$this->_disableUsers();
        }

		/**
		 * @private
		 */
        function _disableUsers()
        {
            $errorMessage = "";
            $successMessage = "";
            $totalOk = 0;

            $users = new Users();
            // go user by user to remove them
            foreach( $this->_userIds as $userId ) {
            	// get some info about the user
                $userInfo = $users->getUserInfoFromId( $userId );
                if( !$userInfo ) {
                	$errorMessage .= $this->_locale->pr("error_invalid_user2", $userId )."<br/>";
                }
                else {
					$this->notifyEvent( EVENT_PRE_USER_DELETE, Array( "user" => &$userInfo ));
					$userInfo->setStatus( USER_STATUS_DISABLED );
                	if( !$users->updateUser( $userInfo ))
                    	$errorMessage .= $this->_locale->pr("error_deleting_user", $userInfo->getUsername())."<br/>";
                    else {
                    	$totalOk++;					
                    	if( $totalOk < 2 )
                    		$successMessage = $this->_locale->pr( "user_deleted_ok", $userInfo->getUsername());
                    	else
                    		$successMessage = $this->_locale->pr( "users_deleted_ok", $totalOk );

						// notify of the post delete event
						$this->notifyEvent( EVENT_POST_USER_DELETE, Array( "user" => &$userInfo ));
					}
                }
            }

			// prepare the view
            $this->_view = new AdminSiteUsersListView( $this->_blogInfo );
            if( $errorMessage != "" ) $this->_view->setErrorMessage( $errorMessage );
            if( $successMessage != "" ) $this->_view->setSuccessMessage( $successMessage );
            $this->setCommonData();

            return false;
        }

    }
?>
