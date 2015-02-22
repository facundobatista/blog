<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminsiteblogslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admineditsiteblogview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/blognamevalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/usernamevalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/domainvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows a form to change the settings of the current blog.
     */
    class AdminUpdateEditBlogAction extends AdminAction 
	{

    	var $_blogLocale;
        var $_editBlogId;
        var $_blogOwner;
        var $_blogTemplate;
		var $_blogTimeOffset;
		var $_blogUsers;
		var $_blogQuota;
		var $_blogName;
		var $_blogStatus;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminUpdateEditBlogAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// data validation
			$this->registerFieldValidator( "blogUsers", new ArrayValidator( new IntegerValidator()), true );
			$this->registerFieldValidator( "blogName", new BlogNameValidator());
			$this->registerFieldValidator( "blogId", new IntegerValidator());
			$this->registerFieldValidator( "blogStatus", new IntegerValidator());
			$this->registerFieldValidator( "blogLocale", new StringValidator());
			$this->registerFieldValidator( "blogTemplate", new StringValidator());
			$this->registerFieldValidator( "blogResourcesQuota", new IntegerValidator(), true );
			$this->registerFieldValidator( "userId", new IntegerValidator() );
			$this->registerFieldValidator( "userName", new UsernameValidator());
			$this->registerFieldValidator( "blogTimeOffset", new IntegerValidator( true ) );
			if( Subdomains::getSubdomainsEnabled()) {
				$this->registerFieldValidator( "blogSubDomain", new DomainValidator());
				$this->registerFieldValidator( "blogMainDomain", new DomainValidator());			
			}			
			$view = new AdminEditSiteBlogView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_updating_blog_settings2" ));
			$this->setValidationErrorView( $view );
			
			$this->requireAdminPermission( "update_site_blog" );			
        }
		
        /**
         * Carries out the specified action
         */
        function perform()
        {
            // fetch the values from the form which have already been validated
            $this->_blogName = Textfilter::filterAllHTML($this->_request->getValue( "blogName" ));
            $this->_blogLocale = $this->_request->getValue( "blogLocale" );
            $this->_blogTemplate = $this->_request->getValue( "blogTemplate" );
            $this->_editBlogId = $this->_request->getValue( "blogId" );
            $this->_blogTimeOffset = $this->_request->getValue( "blogTimeOffset" );
            $this->_blogQuota = $this->_request->getValue( "blogResourcesQuota" );
            $this->_blogUsers = $this->_request->getValue( "blogUsers" );
            $this->_blogStatus = $this->_request->getValue( "blogStatus" );
			$this->_blogOwner = $this->_request->getValue( "userId" );
			
            // get the blog we're trying to update
            $blogs = new Blogs();
            $blogInfo = $blogs->getBlogInfo( $this->_editBlogId );
            if( !$blogInfo ) {
                $this->_view = new AdminSiteBlogsListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_fetching_blog"));
                $this->setCommonData();

                return false;
            }

            $this->notifyEvent( EVENT_BLOG_LOADED, Array( "blog" => &$blogInfo ));

            // make sure that the user we'd like to set as owner exists
            $users = new Users();
            $userInfo = $users->getUserInfoFromId( $this->_blogOwner );
            if( !$userInfo ) {
                $this->_view = new AdminSiteBlogsListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_incorrect_blog_owner"));
                $this->setCommonData();
                return false;
            }
			
			$this->notifyEvent( EVENT_USER_LOADED, Array( "user" => &$userInfo ));

			// set the different settings
            $blogSettings = $blogInfo->getSettings();
            $blogSettings->setValue( "locale", $this->_blogLocale );
            $blogSettings->setValue( "template", $this->_blogTemplate );
            $blogSettings->setValue( "time_offset", $this->_blogTimeOffset );
            $blogInfo->setSettings( $blogSettings );
			$blogInfo->setResourcesQuota( $this->_blogQuota );
			$blogInfo->setBlog( $this->_blogName );
            $blogInfo->setOwner( $this->_blogOwner );
			$blogInfo->setStatus( $this->_blogStatus );
            $blogInfo->setMangledBlogName( $blogInfo->getBlog(), true );

            // check to see whether we are going to save subdomain information			
            if( Subdomains::getSubdomainsEnabled()) {
	
                // Translate a few characters to valid names, and remove the rest
                $mainDomain = Textfilter::domainize($this->_request->getValue( "blogMainDomain" ));
                if(!$mainDomain)
					$mainDomain = "?";
                $subDomain = Textfilter::domainize($this->_request->getValue( "blogSubDomain" ));



                if( !Subdomains::isDomainAvailable( $mainDomain )) {
                    $this->_view = new AdminEditSiteBlogView( $this->_blogInfo );
                    $this->_view->setErrorMessage( $this->_locale->tr("error_updating_blog_subdomain"));
					$this->_form->setFieldValidationStatus( "blogMainDomain", false );
                    $this->setCommonData();
                    return false;
                }

				if( !Subdomains::isValidDomainName( $subDomain )) {
	                $this->_view = new AdminEditSiteBlogView( $this->_blogInfo );
	                $this->_view->setErrorMessage( $this->_locale->tr("error_updating_blog_subdomain"));
					$this->_form->setFieldValidationStatus( "blogSubDomain", false );	
	                $this->setCommonData();
	                return false;
				}

                if($mainDomain == "?"){
					$blog_domain = $subDomain;
                }
                else{
					$blog_domain = $subDomain . "." . $mainDomain;
                }

				$blogInfo->setCustomDomain( $blog_domain );
            }       
            
			$this->notifyEvent( EVENT_PRE_BLOG_UPDATE, Array( "blog" => &$blogInfo ));
            if( !$blogs->updateBlog( $blogInfo )) {
            	$this->_view = new AdminSiteBlogsListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->pr( "error_updating_blog_settings", $blogInfo->getBlog()));
               	$this->setCommonData();
                return false;
            }

			$this->notifyEvent( EVENT_POST_BLOG_UPDATE, Array( "blog" => &$blogInfo ));

            // do it again, baby :)))
            if( $this->_blogInfo->getId() == $blogInfo->getId()) {
            	$this->_blogInfo->setSettings( $blogSettings );
            	$this->_session->setValue( "blogInfo", $this->_blogInfo );
            	$this->saveSession();
            }

            // if everything went fine, we can show a nice message
            $this->_view = new AdminSiteBlogsListView( $this->_blogInfo );
            $this->_view->setSuccessMessage( $this->_locale->pr( "edit_blog_settings_updated_ok", $blogInfo->getBlog()));
            $this->setCommonData();

            // clear the cache
            CacheControl::resetBlogCache( $blogInfo->getId());

            // better to return true if everything fine
            return true;
        }
    }
?>
