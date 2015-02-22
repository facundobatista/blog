<?php

	lt_include( PLOG_CLASS_PATH."class/summary/view/summarycachedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/template/templateservice.class.php" );
	lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );
	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );

    class SummaryRssView extends SummaryCachedView
	{

        var $_profile;

        function SummaryRssView( $profile, $data = Array())
        {
			if( $profile == "" || $profile == "blogs_" ) {
				$config =& Config::getConfig();
				$profile .= $config->getValue( "default_rss_profile", "rss090" );
			}
            $this->SummaryCachedView( $profile, $data );			
				
            $this->_profile = $profile;
			
			$this->setContentType( "text/xml" );
        }
		
        function generateTemplate()
        {
            $templateService = new TemplateService();
            $this->_template = $templateService->customTemplate( $this->_templateName, "summary/rss", true );
            
            // by default, these compiled files would be saved and cached to tmp/summary/rss but that's too
            // many folders so we'll overwrite the default settings set by TemplateService::customTemplate()
            // and save the files to tmp/summary/ with the other summary pages
			$config =& Config::getConfig();
			$templateTmpFolder = $config->getValue( "temp_folder" ).'/summary';
			$this->_template->cache_dir = $templateTmpFolder;
			$this->_template->compile_dir = $templateTmpFolder;

                // set the summary custom time
            lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
            $config =& Config::getConfig();
                // set the summary cache lifetime, or zero if none
            $summaryTemplateLifetime = $config->getValue( "summary_template_cache_lifetime", 0 );
            if( $summaryTemplateLifetime > 0 ) {
                $this->_template->cache_lifetime = $summaryTemplateLifetime;
            }
        }		

        function sendUncachedOutput()
        {
            //$templateService = new TemplateService();
            //$template = $templateService->Template( $this->_profile, "summary/rss" );           

            $config =& Config::getConfig();
            $this->_locale =& Locales::getLocale( $config->getValue("default_locale" ));
            $this->_params->setValue( "version", Version::getVersion());
            $this->_params->setValue( "locale", $this->_locale );
			$this->_params->setValue( "serviceName", $config->getValue( "summary_service_name" ));
            $this->_template->assign( $this->_params->getAsArray());
            print $this->_template->fetch( $this->_viewId );
        }
    }
?>