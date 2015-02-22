<?php

    /**
     * @package admin
     */


	lt_include( PLOG_CLASS_PATH."class/view/view.class.php" );
    lt_include( PLOG_CLASS_PATH."class/template/templateservice.class.php" );
    lt_include( PLOG_CLASS_PATH."class/template/template.class.php" );
	lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );
	lt_include( PLOG_CLASS_PATH."class/misc/version.class.php" );

    // name of the template we are going to use for this view
    define( "DEFAULTADMIN_TEMPLATE", "default" );

    /**
     * \ingroup View
     * @private
     *	
     * Default view
     */
    class AdminDefaultView extends View 
    {

    	/**
         * This initializes the class, but normally we'll only have to initialize the parent
         */
        function AdminDefaultView()
        {
        	$this->View();
        }

        /**
         * Renders the view. It simply gets all the parameters we've been adding to it
         * and puts them in the context of the template renderer so that they can be accessed
         * as normal parameters from within the template
         */
        function render()
        {
			// set the view character set based on the default locale
            $config =& Config::getConfig();
            $locale =& Locales::getLocale( $config->getValue( "default_locale" ));
            $this->setValue( 'version', Version::getVersion());            	
			$this->setCharset( $locale->getCharset());
		
			parent::render();
					
        	// to find the template we need, we can use the TemplateService
            $ts = new TemplateService();
        	$template = $ts->Template( DEFAULTADMIN_TEMPLATE, "admin" );
            $this->setValue( "locale", $locale );
            // assign all the values
            $template->assign( $this->_params->getAsArray());
			
            // and send the results
            print $template->fetch();
        }
    }
?>
