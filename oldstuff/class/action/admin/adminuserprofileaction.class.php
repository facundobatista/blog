<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminsiteuserslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admineditsiteuserview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );

    /**
     * \ingroup Action
     * @private
     *
	 * allows to edit the profile of a user
	 */
    class AdminUserProfileAction extends AdminAction 
	{

    	var $_userId;

    	function AdminUserProfileAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// data validation
			$this->registerFieldValidator( "userId", new IntegerValidator());
			$view = new AdminSiteUsersListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_invalid_user" ));
			$this->setValidationErrorView( $view );
			
			$this->requireAdminPermission( "update_user" );			
        }

        function perform()
        {
        	// load the user settings
			$this->_userId = $this->_request->getValue( "userId" );
            $users = new Users();
            $user  = $users->getUserInfoFromId( $this->_userId );

            // if no info could be fetched, shown an error and quit
            if( !$user ) {
            	$this->_view = new AdminSiteUsersListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_invalid_user"));
                $this->setCommonData();
                return false;
            }
			$this->notifyEvent( EVENT_USER_LOADED, Array( "user" => &$user ));

			// start the view and render it
			$this->_view = new AdminEditSiteUserView( $this->_blogInfo, $user );
            $this->setCommonData();

            return true;
        }
    }
?>