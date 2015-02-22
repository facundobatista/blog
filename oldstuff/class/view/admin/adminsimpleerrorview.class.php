<?php

	lt_include( PLOG_CLASS_PATH."class/view/view.class.php" );
	lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );

	define( "ADMINSIMPLEERROR_TEMPLATE", "simpleerror" );

    /**
     * \ingroup View
     * @private
     *	
     * The ErrorView class takes care of showing error messages
     */
    class AdminSimpleErrorView extends View 
	{

		function AdminSimpleErrorView()
        {
        	$this->View();
        }

        function render()
        {
			// set the view character set based on the default locale
            $config =& Config::getConfig();
            $locale =& Locales::getLocale( $config->getValue( "default_locale" ));
            $this->setValue( 'version', Version::getVersion());            
			$this->setCharset( $locale->getCharset());
		
			parent::render();		
		
            // load the contents into the template context
            $ts = new TemplateService();
            $template = $ts->Template( ADMINSIMPLEERROR_TEMPLATE, "admin" );
            $this->setValue( "locale", $locale );
            // finally pass the values to the templates            
            $template->assign( $this->_params->getAsArray());
			
            // finally, send the results
            print $template->fetch();
        }
    }
?>
