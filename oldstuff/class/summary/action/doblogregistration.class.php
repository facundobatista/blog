<?php

	lt_include( PLOG_CLASS_PATH."class/summary/action/registeraction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/blognamevalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );	 		
	lt_include( PLOG_CLASS_PATH."class/summary/view/doblogregistrationview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/summary/view/blogtemplatechooserview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/domainvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/net/http/subdomains.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );    

	/**
	 * registers a blog
	 */
    class doBlogRegistration extends RegisterAction 
	{
        
        function doBlogRegistration( $actionInfo, $request )
        {
	    	$this->RegisterAction( $actionInfo, $request );
	
			// input filters
			$this->_request->registerFilter( "blogName", new HtmlFilter());
	    	
	    	// data validation
	    	//$this->registerFieldValidator( "userId", new IntegerValidator());
	    	$this->registerFieldValidator( "blogName", new BlogNameValidator());
	    	$this->registerFieldValidator( "blogCategoryId", new IntegerValidator());
	    	$this->registerFieldValidator( "blogLocale", new StringValidator());
			$this->registerFieldValidator( "blogSubDomain", new StringValidator(), true );
			$this->registerFieldValidator( "blogMainDomain", new StringValidator(), true );
	    	$view = new doBlogRegistrationView();
	    	$view->setErrorMessage( $this->_locale->tr("register_error_creating_blog"));
	    	$this->setValidationErrorView( $view );   
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
				if( Subdomains::domainNameExists( $this->blogDomain )) {
					$valid = false;
					$this->_form->setFieldValidationStatus( "blogSubDomain", false );					
					$this->validationErrorProcessing();					
				}				
            }

			return( $valid );
		}

        function perform()
        {
            // create the new view and clean the cache
            $this->_view = new BlogTemplateChooserView();
            $this->setCommonData();

			// save data to the session
	    	SessionManager::setSessionValue( "blogName", $this->_request->getValue( "blogName" ));
	    	SessionManager::setSessionValue( "blogCategoryId", $this->_request->getValue( "blogCategoryId" ));
	    	SessionManager::setSessionValue( "blogLocale", $this->_request->getValue( "blogLocale" ));
	    	SessionManager::setSessionValue( "blogSubDomain", $this->_request->getValue( "blogSubDomain" ));
	    	SessionManager::setSessionValue( "blogMainDomain", $this->_request->getValue( "blogMainDomain" ));
			SessionManager::setSessionValue( "blogDomain", $this->blogDomain );

			return( true );
        }
    }
?>