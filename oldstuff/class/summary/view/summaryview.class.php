<?php

	lt_include( PLOG_CLASS_PATH."class/view/view.class.php" );
    lt_include( PLOG_CLASS_PATH."class/template/templateservice.class.php" );
	lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );
	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );

    class SummaryView extends View
	{

        var $_templateName;
        var $_templateFolder;

        function SummaryView( $templateName, $templateFolder = "summary" )
        {
            $this->View();
            $this->_templateName = $templateName;
            $this->_templateFolder = $templateFolder;
        }

        /**
         * @private
         */
        function _getLocale()
        {
            // load the Locale object from the view context or initialize it now
            if( $this->_params->keyExists( "locale" )) {
                $this->_locale = $this->_params->getValue( "locale" );
            }
            else {
                $config =& Config::getConfig();
                $this->_locale =& Locales::getLocale( $config->getValue("default_locale" ));
            }
        }

        function render()
        {
			// fetch the baseurl
			$config =& Config::getConfig();
			$baseurl = $config->getValue( "base_url" );

			// load the locale
			$this->_getLocale();
			// set the view character set based on the locale
			$this->setCharset( $this->_locale->getCharset());

			parent::render();

            $templateService = new TemplateService();
            $template = $templateService->customTemplate( $this->_templateName, $this->_templateFolder );

            $this->_params->setValue( "locale", $this->_locale );
			$this->_params->setValue( "baseurl", $baseurl);
			$this->_params->setValue( "serviceName", $config->getValue( "summary_service_name" ));			
            lt_include( PLOG_CLASS_PATH."class/summary/net/summaryrequestgenerator.class.php" );
            $this->_params->setValue( "url", new SummaryRequestGenerator());

            $template->assign( $this->_params->getAsArray());
            print $template->fetch();
        }
    }
?>