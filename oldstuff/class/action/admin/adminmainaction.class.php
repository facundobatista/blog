<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Sample action on how to develop our own actions.
     *
     * Please also refer to SampleView.class.php for more information
     */
    class AdminMainAction extends AdminAction 
	{

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminMainAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
        }

        /**
         * Validate if everything is correct
         */
        function validate()
        {
	        // first of all, check if we have a valid blog id
	        $this->_blogId = $this->_request->getValue( "blogId" );
	        $intVal = new IntegerValidator();
            if( $this->_blogId == "" || !$intVal->validate( $this->_blogId ) ) {
                $this->_blogId = ""; // clear invalid data
	            lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
				
				// check if the user really belongs to one or more blogs and if not, quit
				$users = new Users();
				$userBlogs = $users->getUsersBlogs( $this->_userInfo->getId(), BLOG_STATUS_ACTIVE );
				if( count($userBlogs) == 0 ) {
	                lt_include( PLOG_CLASS_PATH."class/view/admin/adminsimpleerrorview.class.php" );	
					$this->_view = new AdminSimpleErrorView();
					$this->_view->setValue( "message", $this->_locale->tr("error_dont_belong_to_any_blog" ));
					
					return false;
				}
				
				// if everything went fine, then we can continue...
                lt_include( PLOG_CLASS_PATH."class/view/admin/admindashboardview.class.php" );	

				$this->_view = new AdminDashboardView( $this->_userInfo, $userBlogs );
				return false;
            }

			// load the blog
            lt_include( PLOG_CLASS_PATH . "class/dao/blogs.class.php" );
			$blogs = new Blogs();
            $this->_blogInfo = $blogs->getBlogInfo( $this->_blogId );
			 
			// check if the blog really exists
			if( !$this->_blogInfo ) {
	            lt_include( PLOG_CLASS_PATH."class/view/admin/adminsimpleerrorview.class.php" );	

				$this->_view = new AdminSimpleErrorView();
				$this->_view->setValue( "message", $this->_locale->tr("error_incorrect_blog_id" ));
				
				return false;		 			 
			}
			
			// if so, check that it is active
			if( $this->_blogInfo->getStatus() != BLOG_STATUS_ACTIVE ) {
	            lt_include( PLOG_CLASS_PATH."class/view/admin/adminsimpleerrorview.class.php" );					
				$this->_view = new AdminSimpleErrorView();
				$this->_view->setValue( "message", $this->_locale->tr("error_incorrect_blog_id" ));
				
				return false;				
			}

            // if the blog identifier is valid, now we should now check if the user belongs
            // to that blog so that we know for sure that nobody has tried to forge the
            // parameter in the meantime			 
            $userPermissions = new UserPermissions();
			$blogUserPermissions = $userPermissions->getUserPermissions( $this->_userInfo->getId(), $this->_blogInfo->getId());
			if( (!$blogUserPermissions) && ($this->_blogInfo->getOwnerId() != $this->_userInfo->getId())) {
	            lt_include( PLOG_CLASS_PATH."class/view/admin/adminsimpleerrorview.class.php" );	

				$this->_view = new AdminSimpleErrorView();
				$this->_view->setValue( "message", $this->_locale->tr("error_no_permissions" ));
				
				return false;		 
			}

            // if all correct, we can now set the blogInfo object in the session for later
            // use
            $this->_session->setValue( "blogInfo", $this->_blogInfo );
            $session = HttpVars::getSession();
            $session["SessionInfo"] = $this->_session;
            $session["SessionInfo"]->setValue( "blogId", $this->_blogInfo->getId() );
            HttpVars::setSession( $session );

            return true;
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
			// we don't have to worry about much more here, we can let the
			// $this->_nextAction action take care of everytyhing now...
			// If $this->_nextAction is null, we use "newPost" as default nextAction
			lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );
	        $this->_nextAction = $this->_request->getFilteredValue( "action", new HtmlFilter() );

			if ( $this->_nextAction && AdminController::checkActionExist( $this->_nextAction ) ) {
				AdminController::setForwardAction( $this->_nextAction );
		    } else {
			    if( $this->userHasPermission( "new_post" ))
					AdminController::setForwardAction( "newPost" );
				else
					AdminController::setForwardAction( "Manage" );
			}
			
            // better to return true if everything fine
            return true;
        }
    }
?>
