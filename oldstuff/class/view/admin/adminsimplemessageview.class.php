<?php

	lt_include( PLOG_CLASS_PATH."class/view/view.class.php" );
	lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );

	define( "ADMINSIMPLEMESSAGE_TEMPLATE", "simplemessage" );

    /**
     * \ingroup View
     * @private
     *	
     * The SimpleMessageView class shows messages, but the template does not
     * include the header or the footer.
     */
    class AdminSimpleMessageView extends View 
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
            $template = $ts->Template( ADMINSIMPLEMESSAGE_TEMPLATE, "admin" );
            $this->setValue( "locale", $locale );

			
            // and pass the values to the template
            $template->assign( $this->_params->getAsArray());
			
            // finally, send the results
            print $template->fetch();
        }
    }
?>
