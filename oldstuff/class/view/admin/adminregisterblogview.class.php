<?php

	lt_include( PLOG_CLASS_PATH."class/view/view.class.php" );
	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/templatesets/templatesets.class.php" );
	lt_include( PLOG_CLASS_PATH."class/net/http/subdomains.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/blogcategories.class.php" );
	
	class AdminRegisterBlogView extends View
	{
		var $_userInfo;
	
		function AdminRegisterBlogView( $userInfo )
		{
			$this->View();
			
			$this->_userInfo = $userInfo;
		}
		
		function setUserInfo( $user )
		{
			// whatever...
		}
		
		function render()
		{
			// set the view character set based on the default locale
            $config =& Config::getConfig();
            $locale =& Locales::getLocale( $config->getValue( "default_locale" ));			
			$this->setCharset( $locale->getCharset());		
		
			parent::render();
            $ts = new TemplateService();
        	$template = $ts->AdminTemplate( "registerblog" );
        	// assign a few values that have been loaded locally
            $this->setValue( "defaultLocale",
                             $config->getValue( "default_locale" ));
            $this->setValue( "locale", $locale );
            $this->setValue( "user", $this->_userInfo );
            $this->setValue( "locales", Locales::getLocales());

            // only do blog_domain stuff if subdomains are enabled
            // Don't waste time here, as well as be less confusing by
            // not showing the option to users who can't use it
            if( Subdomains::getSubdomainsEnabled()) {
				$available_domains = Subdomains::getAvailableDomains();
                
                $subdomain = "";
                $maindomain = "";

                if($available_domains){
                    foreach($available_domains as $avdomain){
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


            $ts = new TemplateSets();
            $this->setValue( "templates", $ts->getGlobalTemplateSets());
            
            // and the blog categories
            $blogCategories = new BlogCategories();
            $this->setValue( "blogcategories", $blogCategories->getBlogCategories());
            
            // assign all the values
            $template->assign( $this->_params->getAsArray());

            // and send the results
            print $template->fetch();		
		}
	}
?>