<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminbloguserslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/permissions.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Revokes the permissions some users might have in a given blog
     */
    class AdminDeleteBlogUserPermissionsAction extends AdminAction 
	{

    	var $_userIds;
		var $_op;

    	function AdminDeleteBlogUserPermissionsAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			$this->_op = $actionInfo->getActionParamValue();
			if( $this->_op == "deleteBlogUserPermissions" )
				$this->registerFieldValidator( "userId", new IntegerValidator());
			else
				$this->registerFieldValidator( "userIds", new ArrayValidator( new IntegerValidator()));
			$view = new AdminBlogUsersListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_no_users_selected" ));
			$this->setValidationErrorView( $view );
        }
		
		function perform()
		{
			if( $this->_op == "deleteBlogUserPermissions" ) {
				$this->_userId = $this->_request->getValue( "userId" );
				$this->_userIds = Array();
				$this->_userIds[] = $this->_userId;
			}
			else
				$this->_userIds = $this->_request->getValue( "userIds" );
				
			// perform the action itself...
			$this->_revokePermissions();
		}

        function _revokePermissions()
        {
        	// now that we have the list of users we'd like to remove
            // let's go through it and remove those that have been selected
            $users = new Users();
            $userPermissions = new UserPermissions();
			$successMessage = "";
			$errorMessage = "";
			$totalOk = 0;
			
			$perms = new Permissions();
			
            foreach( $this->_userIds as $userId ) {
            	$res = $userPermissions->revokePermissions( $userId, $this->_blogInfo->getId());
                $userInfo = $users->getUserInfoFromId( $userId );
                if( $res ) {
					$totalOk++;				
					if( $totalOk < 2 )
						$successMessage = $this->_locale->pr("user_removed_from_blog_ok", $userInfo->getUsername());
					else
						$successMessage = $this->_locale->pr("users_removed_from_blog_ok", $totalOk);
				}
                else {
					if( $userInfo )
						$errorMessage .= $this->_locale->pr("error_removing_user_from_blog", $userInfo->getUsername())."<br/>";
					else
						$errorMessage .= $this->_locale->pr("error_removing_user_from_blog2", $userId)."<br/>";
				}
            }

            $this->_view = new AdminBlogUsersListView( $this->_blogInfo );
            if( $successMessage != "" ) $this->_view->setSuccessMessage( $successMessage );
            if( $errorMessage != "" ) $this->_view->setErrorMessage( $errorMessage );			
            $this->setCommonData();

            return true;
        }
    }
?>