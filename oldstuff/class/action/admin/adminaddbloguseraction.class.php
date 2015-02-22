<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminnewbloguserview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/usernamevalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminbloguserslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/mail/emailservice.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Adds a user to the blog
     */
    class AdminAddBlogUserAction extends AdminAction 
	{

    	var $_sendNotification;
        var $_notificationText;
        var $_newUsername;
		var $_permissions;

    	function AdminAddBlogUserAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// data validation
			$this->registerFieldValidator( "newBlogUserName", new UsernameValidator());
			$this->registerFieldValidator( "sendNotification", new IntegerValidator(), true);
			$this->registerFieldValidator( "perm", new ArrayValidator( new IntegerValidator() ), true );
			$this->registerFieldValidator( "newBlogUserText", new StringValidator(), true );
						
			$this->_sendNotification = ($this->_request->getValue( "sendNotification" ) != "" );
			if( $this->_sendNotification )
				$this->registerFieldValidator( "newBlogUserText", new StringValidator());	

			$view = new AdminNewBlogUserView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_adding_user"));
			$this->setValidationErrorView( $view );
			
			$this->requirePermission( "add_blog_user" );
        }

        function sendNotificationEmail( &$userInfo )
        {
            // if everything went fine, we can now send the confirmation email
            // only if the user specified a valid email address
            if( $userInfo->getEmail() != "" ) {
            	// build an email message
                $message = new EmailMessage();
                $message->setBody( $this->_notificationText );
                $message->setSubject( $this->_locale->tr("notification_subject") );
                $message->addTo( $userInfo->getEmail());
                $message->setFrom( $this->_userInfo->getEmail());
                // and finally send it
                $emailService = new EmailService();
                $emailService->sendMessage( $message );
            }

            return true;
        }

        function perform()
        {
            $this->_notificationText = $this->_request->getValue( "newBlogUserText" );
        	$this->_newUsername = Textfilter::filterAllHTML($this->_request->getValue( "newBlogUserName" ));
			$this->_perms = $this->_request->getValue( "perm" );
		
        	// see if the user exists
            $users = new Users();
            $userInfo = $users->getUserInfoFromUsername( $this->_newUsername );
            if( !$userInfo ) {
            	$this->_view = new AdminNewBlogUserView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->pr("error_invalid_user"), $this->_newUsername );
				$this->_form->setFieldValidationStatus( "newBlogUserName", false );
                $this->setCommonData( true );

                return false;
            }
			$this->notifyEvent( EVENT_USER_LOADED, Array( "user" => &$userInfo ));			

            // get the permissions that this user will be granted
            $userPerms = new UserPermissions();
			if( is_array( $this->_perms )) {
				foreach( $this->_perms as $val => $permId ) {
	                $perm = new UserPermission( $userInfo->getId(), $this->_blogInfo->getId(), $permId );
	                $res = $userPerms->grantPermission( $perm );
				}
			}
			
			$this->notifyEvent( EVENT_PRE_USER_UPDATE, Array( "user" => &$userInfo ));
            if( !$res ) {
            	// there was an error adding the user to the blog
            	$this->_view = new AdminNewBlogUserView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->pr("error_adding_user", $userInfo->getUsername()));
                $this->setCommonData();

                return false;
            }
			$this->notifyEvent( EVENT_POST_USER_UPDATE, Array( "user" => &$userInfo ));

            // send a notification if enabled
            if( $this->_sendNotification ) {
            	$this->sendNotificationEmail( $userInfo );
            }

			if( $this->userHasPermission( "view_blog_users" ))  {
            	$this->_view = new AdminBlogUsersListView( $this->_blogInfo );
			}
			else {
				$this->_view = new AdminNewBlogUserView( $this->_blogInfo );
			}
				
            $this->_view->setSuccessMessage( $this->_locale->pr("user_added_to_blog_ok", $userInfo->getUsername()));
            $this->setCommonData();

            return true;
        }
        
    }
?>
