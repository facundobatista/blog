<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/blognamevalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminblogsettingsview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/domainvalidator.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/data/validator/rules/intrangerule.class.php" );
	lt_include( PLOG_CLASS_PATH."class/net/http/subdomains.class.php" );


    /**
     * \ingroup Action
     * @private
     *
     * Action that shows a form to change the settings of the current blog.
     */
    class AdminUpdateBlogSettingsAction extends AdminAction
	{
    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminUpdateBlogSettingsAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
        	// specific validator that does not allow an integer below 1
			$val = new IntegerValidator();
			$val->addRule( new IntRangeRule( 1, 99999999 ));
			$this->registerFieldValidator( "blogMaxMainPageItems", $val );			
			$this->registerFieldValidator( "blogNumCommentsPerPage", $val );
			// the rest of validators, as normal...
			$this->registerFieldValidator( "blogMaxRecentItems", new IntegerValidator());
			$this->registerFieldValidator( "blogName",  new BlogNameValidator());
			$this->registerFieldValidator( "blogLocale", new StringValidator());
			$this->registerFieldValidator( "blogTemplate", new StringValidator());
			$this->registerFieldValidator( "blogCategory", new IntegerValidator());
			$this->registerFieldValidator( "blogArticlesOrder", new IntegerValidator());
			$this->registerFieldValidator( "blogAbout", new StringValidator(), true );
			$this->registerFieldValidator( "blogShowMoreEnabled", new IntegerValidator(), true );
			$this->registerFieldValidator( "blogEnableHtmlarea", new IntegerValidator(), true );
			$this->registerFieldValidator( "blogEnablePullDownMenu", new IntegerValidator(), true );
			$this->registerFieldValidator( "blogCommentsEnabled", new IntegerValidator(), true );
			$this->registerFieldValidator( "blogCommentsOrder", new IntegerValidator() );
			$this->registerFieldValidator( "blogArticlesOrder", new IntegerValidator() );
			$this->registerFieldValidator( "blogShowFuturePosts", new IntegerValidator(), true );
			$this->registerFieldValidator( "blogFirstDayOfWeek", new IntegerValidator() );
			$this->registerFieldValidator( "blogTimeOffset", new IntegerValidator( true ) );
			$this->registerFieldValidator( "blogCategoriesOrder", new IntegerValidator() );
			$this->registerFieldValidator( "blogLinkCategoriesOrder", new IntegerValidator() );
			$this->registerFieldValidator( "blogEnableAutosaveDrafts", new IntegerValidator(), true );
			$this->registerFieldValidator( "blogShowInSummary", new IntegerValidator(), true );
			$this->registerFieldValidator( "blogSendNotification", new IntegerValidator(), true );
			$this->registerFieldValidator( "blogCommentOnlyRegisteredUsers", new IntegerValidator(), true );
            if( Subdomains::getSubdomainsEnabled()) {
                $this->registerFieldValidator( "blogSubDomain", new DomainValidator());
                $this->registerFieldValidator( "blogMainDomain", new DomainValidator());
            }       
			// set the view that we're going to use
			$view = new AdminBlogSettingsView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_updating_settings"));
			$this->setValidationErrorView( $view );
			
			$this->requirePermission( "update_blog" );
        }
        
		function validate()
		{
			$valid = parent::validate();
			
            // check to see whether we are going to save subdomain information
            if( Subdomains::getSubdomainsEnabled()) {

				// Translate a few characters to valid names, and remove the rest
                $mainDomain = Textfilter::domainize($this->_request->getValue( "blogMainDomain" ));
                if(!$mainDomain)
                    $mainDomain = "?";
                $subDomain = Textfilter::domainize($this->_request->getValue( "blogSubDomain" ));

                // get list of allowed domains
				$available_domains = Subdomains::getAvailableDomains();
				
                if($mainDomain == "?")
                    $this->blogDomain = $subDomain;
                else {
                    $this->blogDomain = $subDomain . "." . $mainDomain;
				}							

                // make sure the mainDomain parameter is one of the blogAvailableDomains and if not, 
				// force a validation error
                if( !Subdomains::isDomainAvailable( $mainDomain ) || !Subdomains::isValidDomainName( $subDomain )) {
					$valid = false;
					$this->_form->setFieldValidationStatus( "blogSubDomain", false );					
					$this->validationErrorProcessing();					
                }
				if( Subdomains::domainNameExists( $this->blogDomain, $this->_blogInfo->getId() )) {
					$valid = false;
					$this->_form->setFieldValidationStatus( "blogSubDomain", false );					
					$this->validationErrorProcessing();					
				}				
            }

			return( $valid );			
		}

        /**
         * Carries out the specified action
         */
        function perform()
        {
			$config =& Config::getConfig();	
	
        	// fetch the settings from the db and update them accordingly
            $blogs = new Blogs();
            $blogSettings = $this->_blogInfo->getSettings();
            $blogSettings->setValue( "locale", $this->_request->getValue( "blogLocale" ));
            $blogSettings->setValue( "show_posts_max", $this->_request->getValue( "blogMaxMainPageItems" ));
            $blogSettings->setValue( "recent_posts_max", $this->_request->getValue( "blogMaxRecentItems" ));
            $blogSettings->setValue( "time_offset", $this->_request->getValue( "blogTimeOffset" ));
			$blogSettings->setValue( "categories_order", $this->_request->getValue( "blogCategoriesOrder" ));
			$blogSettings->setValue( "link_categories_order", $this->_request->getValue( "blogLinkCategoriesOrder" ));
           	$blogSettings->setValue( "show_more_enabled",  Textfilter::checkboxToBoolean($this->_request->getValue( "blogShowMoreEnabled" )));
           	$blogSettings->setValue( "htmlarea_enabled", Textfilter::checkboxToBoolean($this->_request->getValue( "blogEnableHtmlarea" )));
           	$blogSettings->setValue( "pull_down_menu_enabled", Textfilter::checkboxToBoolean($this->_request->getValue( "blogEnablePullDownMenu" )));
           	$blogSettings->setValue( "comments_enabled", Textfilter::checkboxToBoolean($this->_request->getValue( "blogCommentsEnabled" )));
           	$blogSettings->setValue( "show_future_posts_in_calendar",  Textfilter::checkboxToBoolean($this->_request->getValue( "blogShowFuturePosts" )));
           	$blogSettings->setValue( "first_day_of_week",  $this->_request->getValue( "blogFirstDayOfWeek" ));
           	$blogSettings->setValue( "new_drafts_autosave_enabled", Textfilter::checkboxToBoolean($this->_request->getValue( "blogEnableAutosaveDrafts" )));
           	$blogSettings->setValue( "show_comments_max", $this->_request->getValue( "blogNumCommentsPerPage" ));
			//$blogSettings->setValue( "comment_only_auth_users", Textfilter::checkboxToBoolean($this->_request->getValue( "blogCommentOnlyRegisteredUsers" )));
            $blogSettings->setValue( "comments_order", $this->_request->getValue( "blogCommentsOrder" ));
            $blogSettings->setValue( "articles_order", $this->_request->getValue( "blogArticlesOrder" ));
            $blogSettings->setValue( "default_send_notification", $this->_request->getValue( "blogSendNotification" ));
            $this->_blogInfo->setAbout( Textfilter::filterAllHTML($this->_request->getValue( "blogAbout" )));
            $this->_blogInfo->setBlog( Textfilter::filterAllHTML($this->_request->getValue( "blogName" )));
            $this->_blogInfo->setSettings( $blogSettings );
            $this->_blogInfo->setTemplate( $this->_request->getValue( "blogTemplate" ));
			$this->_blogInfo->setBlogCategoryId( $this->_request->getValue( "blogCategory" ));
			$this->_blogInfo->setMangledBlogName( $this->_blogInfo->getBlog(), true );
			$this->_blogInfo->setShowInSummary( Textfilter::checkboxToBoolean( $this->_request->getValue( "blogShowInSummary" )));
			
            // check to see whether we are going to save subdomain information			
            if( Subdomains::getSubdomainsEnabled()) {
	
                // Translate a few characters to valid names, and remove the rest
                $mainDomain = Textfilter::domainize($this->_request->getValue( "blogMainDomain" ));
                if(!$mainDomain)
					$mainDomain = "?";
                $subDomain = Textfilter::domainize($this->_request->getValue( "blogSubDomain" ));
                if( !Subdomains::isDomainAvailable( $mainDomain )) {
                    $this->_view = new AdminBlogSettingsView( $this->_blogInfo );
                    $this->_view->setErrorMessage( $this->_locale->tr("error_updating_blog_domain"));
					$this->_form->setFieldValidationStatus( "blogMainDomain", false );
                    $this->setCommonData();
                    return false;
                }

				if( !Subdomains::isValidDomainName( $subDomain )) {
	                $this->_view = new AdminBlogSettingsView( $this->_blogInfo );
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

				$this->_blogInfo->setCustomDomain( $blog_domain );
            }			

            // and now update the settings in the database
            $blogs = new Blogs();

            // and now we can proceed...
			$this->notifyEvent( EVENT_PRE_BLOG_UPDATE, Array( "blog" => &$this->_blogInfo ));
			$blogs = new Blogs();
            if( !$blogs->updateBlog( $this->_blogInfo )) {
            	$this->_view = new AdminBlogSettingsView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_updating_settings"));
               	$this->setCommonData();

                return false;
            }

            $this->_session->setValue( "blogInfo", $this->_blogInfo );
            $this->saveSession();

			$this->notifyEvent( EVENT_POST_BLOG_UPDATE, Array( "blog" => &$this->_blogInfo ));
            $this->_view = new AdminBlogSettingsView( $this->_blogInfo );
            $this->_locale =& Locales::getLocale( $blogSettings->getValue( "locale" ));
            $this->_view->setSuccessMessage( $this->_locale->pr("blog_settings_updated_ok", $this->_blogInfo->getBlog()));
            $this->setCommonData();

			// clear the cache
			CacheControl::resetBlogCache( $this->_blogInfo->getId());

            // better to return true if everything fine
            return true;
        }
    }
?>
