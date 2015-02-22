<?php

	lt_include( PLOG_CLASS_PATH."class/view/view.class.php" );
    lt_include( PLOG_CLASS_PATH."class/template/templateservice.class.php" );
    lt_include( PLOG_CLASS_PATH."class/template/template.class.php" );
	lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );

	define( "ADMINLOGIN_TEMPLATE", "chooseblog" );

    /**
     * \ingroup View
     * @private
     *	
     * Generates the view shown just after filling in the login form
     */
    class AdminLoginView extends View 
	{

    	/**
         * This initializes the class, but normally we'll only have to initialize the parent
         *
         * It gets the BlogSettings object since we need to know a few things about the blog we're
         * rendering before doing this.
         */
        function AdminLoginView()
        {
        	$this->View();
        	// Add the non cache headers to avoid proxy cache this page.
			$this->addHeaderResponse( 'Cache-Control: no-cache, must-revalidate' );
			$this->addHeaderResponse( 'Last-Modified: ' . gmdate( "D, d M Y H:i:s" ) . ' GMT' );
			$this->addHeaderResponse( 'Expires: now' );
			$this->addHeaderResponse( 'Pragma: no-cache' );
        }

        /**
         * Renders the view. It simply gets all the parameters we've been adding to it
         * and puts them in the context of the template renderer so that they can be accessed
         * as normal parameters from within the template
         */
        function render()
        {
			parent::render();		
		
        	// to find the template we need, we can use the TemplateService
            $ts = new TemplateService();
        	$template = $ts->AdminTemplate( ADMINLOGIN_TEMPLATE );
            // load the default locale
            $config =& Config::getConfig();
            $locale =& Locales::getLocale( $config->getValue( "default_locale" ));
            $this->setValue( "locale", $locale );
            // assign all the values
            $template->assign( $this->_params->getAsArray());

            // and send the results
            print $template->fetch();
        }
    }
?>
