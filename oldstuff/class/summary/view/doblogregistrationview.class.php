<?php

	lt_include( PLOG_CLASS_PATH."class/summary/view/summaryview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/blogcategories.class.php" );
	lt_include( PLOG_CLASS_PATH."class/net/http/subdomains.class.php" );
	
	/**
	 * shows a list with all the locales so that users can choose, too
	 */
	class doBlogRegistrationView extends SummaryView
	{
	
		function doBlogRegistrationView()
		{
			$this->SummaryView( "registerstep2" );
		}
		
		function render()
		{
			// get the list of languages available, so that users can choose
			$locales = Locales::getLocales();
			$this->setValue( "locales", $locales );
            
            $config =& Config::getConfig();
            // assign default Local to template
            $this->setValue( "defaultLocale", $config->getValue("default_locale" ) );

			// get a list of blog categories, so we can let user to choose
			$blogCategories = new BlogCategories();
			$categories = $blogCategories->getBlogCategories();
			$this->setValue( "blogCategories", $categories );
			
			// enable or disable the drop-down list to select subdomains
			if( Subdomains::getSubdomainsEnabled()) {
				$this->setValue( "blogDomainsEnabled", true );
				$this->setValue( "blogAvailableDomains", Subdomains::getAvailableDomains());
			}
			
			// and render the rest of the contents of the view
			parent::render();
		}
	}
?>