<?php
	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );

	class SummaryUserCreationView extends SummaryView
	{
		function SummaryUserCreationView()
		{
			$this->SummaryView( "registerstep1" );
		}

		function render()
		{
    		$config =& Config::getConfig();
    		// check whether we should also display one of those authentication images
    		if( $config->getValue( "use_captcha_auth" )) {
    			// generate a file with the captcha class
    			lt_include( PLOG_CLASS_PATH."class/data/captcha/captcha.class.php" );
    			$captcha = new Captcha();
    			$captchaFile = $captcha->generate();
    			// and then build a full url based on it...
    			$url = $config->getValue( "base_url" )."/".$captchaFile;
    			$this->setValue( "userAuthImgPath", $url );
    			$this->setValue( "useCaptchaAuth", true );
    		}
    		else {
    			$this->setValue( "useCaptchaAuth", false );
    		}

			parent::render();
		}
	}
?>