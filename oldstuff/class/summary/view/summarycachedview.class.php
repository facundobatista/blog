<?php

	lt_include( PLOG_CLASS_PATH."class/view/view.class.php" );

	/**
	 * This class should extended SmartyView but SmartyView was designed to work with BlogInfo
	 * objects while the summary does not so we had to reimplement part of the functionality... This is
	 * ideal or optimal but it works so we will let it be for a while.
	 */
    class SummaryCachedView extends View
	{

        var $_templateName;
		var $_data;
		var $_template;
		var $_viewId;

        function SummaryCachedView( $templateName, $data )
        {
            $this->View();
            $this->_templateName = $templateName;
			$this->_data = $data;
			$this->_viewId = $this->generateCacheId();

			$this->generateTemplate();
        }

        function generateTemplate()
        {
            lt_include( PLOG_CLASS_PATH."class/template/templateservice.class.php" );

            $templateService = new TemplateService();
            $this->_template = $templateService->customTemplate( $this->_templateName, "summary", true );

			// set the summary custom time
			lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
			$config =& Config::getConfig();
			// set the summary cache lifetime, or zero if none
			$summaryTemplateLifetime = $config->getValue( "summary_template_cache_lifetime", 0 );
			if( $summaryTemplateLifetime > 0 ) {
				$this->_template->cache_lifetime = $summaryTemplateLifetime;
			}
        }

		/**
		 * generates a unique identifier for this view
		 *
		 * @private
		 */
		function generateCacheId()
		{
			$cacheId = "";
			foreach( $this->_data as $key => $value )
				$cacheId .= "$key=$value";
			$cacheId = md5($cacheId);

			return $cacheId;
		}

		function getBlogViewId()
		{
			return $this->_viewId;
		}

		function isCached()
		{
			$isCached = $this->_template->isCached( $this->_viewId );

			return $isCached;
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

		function sendUncachedOutput()
		{
			lt_include( PLOG_CLASS_PATH."class/xml/rssparser/rssparser.class.php" );

			$config =& Config::getConfig();
			$baseurl = $config->getValue( "base_url" );

			$this->_getLocale();
			$this->_params->setValue( "version", Version::getVersion());
			$this->_params->setValue( "locale", $this->_locale);
			$this->_params->setValue( "rss", new RssParser());
			$this->_params->setValue( "baseurl", $baseurl);
			$this->_params->setValue( "serviceName", $config->getValue( "summary_service_name" ));

            lt_include( PLOG_CLASS_PATH."class/summary/net/summaryrequestgenerator.class.php" );
            $this->_params->setValue( "url", new SummaryRequestGenerator());

            $this->_template->assign( $this->_params->getAsArray());
			print $this->_template->fetch( $this->_viewId );
		}

		function getCacheTimeSeconds()
		{
			$config =& Config::getConfig();

			$cacheTime = $config->getValue( "http_cache_lifetime", 1800 );

			if( $cacheTime == "" || !is_numeric($cacheTime))
				$cacheTime = 1; // [almost] no value, just one second of caching
			if( $cacheTime == -1 )
				$cacheTime = 788400000; // a veeery long time!
			if( $cacheTime > 788400000)
				$cacheTime = 788400000;

			return( $cacheTime );
		}

        function render()
        {
	       lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	       lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );

			// set the view character set based on the default locale
			$this->_getLocale();
			$this->setCharset( $this->_locale->getCharset());

			$sendOutput = true;

			// check if support for conditional HTTP requests is enabled
			$config =& Config::getConfig();
			if( $config->getValue( "template_http_cache_enabled" )) {
	            lt_include( PLOG_CLASS_PATH."class/net/http/httpcache.class.php" );
				// some debug information
				$timestamp = $this->_template->getCreationTimestamp();
				// and now send the correct headers
				if( HttpCache::httpConditional( $timestamp, $this->getCacheTimeSeconds()))
					$sendOutput = false;

				header( "Last-Modified: ".gmdate('D, d M Y H:i:s', $timestamp).' GMT');
			}
			else {
				$sendOutput = true;
			}

			if( $sendOutput ) {
				 View::render();
			     $this->sendUncachedOutput();
			}
        }
    }
?>
