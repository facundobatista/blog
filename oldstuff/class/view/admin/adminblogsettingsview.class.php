<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/templatesets/templatesets.class.php" );
	lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/blogcategories.class.php" );
	lt_include( PLOG_CLASS_PATH."class/net/http/subdomains.class.php" );
	
    /**
     * \ingroup View
     * @private
     *
	 * displays the blog settings
	 */
	class AdminBlogSettingsView extends AdminTemplatedView
	{
	
		function AdminBlogSettingsView( $blogInfo )
		{
			$this->AdminTemplatedView( $blogInfo, "blogsettings" );
			
			$config =& Config::getConfig();			
			
            $blogSettings = $blogInfo->getSettings();
			$this->setValue( "blogAbout", $this->_blogInfo->getAbout( false ));
			$this->setValue( "blogName", $this->_blogInfo->getBlog());
            $this->setValue( "blogLocale", $blogSettings->getValue( "locale" ));
			$this->setValue( "blogMaxRecentItems", $blogSettings->getValue( "recent_posts_max" ));
			$this->setValue( "blogMaxMainPageItems", $blogSettings->getValue( "show_posts_max" ));
			$this->setValue( "blogTemplate", $blogSettings->getValue( "template" ));
			$this->setValue( "blogTimeOffset", $blogSettings->getValue( "time_offset" ));
			$this->setValue( "blogCategoriesOrder", $blogSettings->getValue( "categories_order" ));
			$this->setValue( "blogLinkCategoriesOrder", $blogSettings->getValue( "link_categories_order" ));
			$this->setValue( "blogShowMoreEnabled", $blogSettings->getValue( "show_more_enabled" ));
			$this->setValue( "blogEnableHtmlarea", $blogSettings->getValue( "htmlarea_enabled" ));
			$this->setValue( "blogEnablePullDownMenu", $blogSettings->getValue( "pull_down_menu_enabled" ));
			$this->setValue( "blogCommentsEnabled", $blogSettings->getValue( "comments_enabled" ));
			$this->setValue( "blogShowFuturePosts", $blogSettings->getValue( "show_future_posts_in_calendar" ));
			$locale =& Locales::getLocale( $blogSettings->getValue( "locale" ) );
			$this->setValue( "blogFirstDayOfWeek", $blogSettings->getValue( "first_day_of_week", $locale->firstDayOfWeek() ));
			$this->setValue( "blogEnableAutosaveDrafts", $blogSettings->getValue( "new_drafts_autosave_enabled" ));
			$this->setValue( "blogCommentsOrder", $blogSettings->getValue( "comments_order" ));
			$this->setValue( "blogArticlesOrder", $blogSettings->getValue( "articles_order" ));
			$this->setValue( "blogCategory", $this->_blogInfo->getBlogCategoryId());
			$this->setValue( "blogShowInSummary", $this->_blogInfo->getShowInSummary());
			$this->setValue( "blogSendNotification", $blogSettings->getValue( "default_send_notification" ));
			$this->setValue( "blogCommentOnlyRegisteredUsers", $blogSettings->getValue( "comment_only_auth_users" ));
			$this->setValue( "blogNumCommentsPerPage", $blogSettings->getValue( "show_comments_max", $config->getValue( "show_comments_max" )));
			
            // only do blog_domain stuff if subdomains are enabled
            // Don't waste time here, as well as be less confusing by
            // not showing the option to users who can't use it
            if( Subdomains::getSubdomainsEnabled()) {
                $domain = $this->_blogInfo->getCustomDomain();

				$available_domains = Subdomains::getAvailableDomains();
                
                // default to any domain, this will be overwritten
                // if the domain is found in the available_domains array
                $subdomain = $domain;
                $maindomain = "?";
                
                foreach($available_domains as $avdomain){
	                // search to see if domain suffix is on
	                // the available_domain list.
	                $found = strpos($domain, $avdomain);
	                if($found !== FALSE && $found == (strlen($domain) - strlen($avdomain))){
	                $subdomain = substr($domain, 0, $found-1);
	                $maindomain = $avdomain;
	                break;
	                }
                }

                // pass the domain information to the view
                $this->setValue( "blogSubDomain", $subdomain );
                $this->setValue( "blogMainDomain", $maindomain );
                $this->setValue( "blogAvailableDomains", $available_domains );
                $this->setValue( "blogDomainsEnabled", 1 );
            }			
		}
		
		function render()
		{
            $this->setValue( "blogsettings", $this->_blogInfo->getSettings());

            $ts = new TemplateSets();
            $templates = $ts->getBlogTemplateSets( $this->_blogInfo->getId(), true );
            $this->setValue( "templates", $templates);
			// loading all the locales from disk is a pretty heavy task but doing so, we'll get
			// nice Locale object with things like the encoding, the description, etc... which looks
			// waaaay nicer than just showing the locale code
            $this->setValue( "locales", Locales::getLocales());
			
			// set the blog categories
			$categories = new BlogCategories();
			$this->setValue( "categories", $categories->getBlogCategories());			
			
			parent::render();
		}
	}
?>