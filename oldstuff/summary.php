<?php

    if (!defined( "PLOG_CLASS_PATH" )) {
        define( "PLOG_CLASS_PATH", dirname(__FILE__)."/");
    }

    include_once( PLOG_CLASS_PATH."class/bootstrap.php" );     
    lt_include( PLOG_CLASS_PATH."class/summary/controller/summarycontroller.class.php" );
    lt_include( PLOG_CLASS_PATH."class/net/http/httpvars.class.php" );
    lt_include( PLOG_CLASS_PATH."class/misc/version.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/usernamevalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/net/http/subdomains.class.php" );
	lt_include( PLOG_CLASS_PATH."class/net/http/session/sessionmanager.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/dao/userinfo.class.php" );	

	define( "SUMMARY_DEFAULT_BLOGS_PER_PAGE", 25 );
	
    //
    // a security check, or else people might forget to remove the wizard.php script
    //
    if( is_readable( "wizard.php")) {
    	lt_include( PLOG_CLASS_PATH."install/installation.class.php" );
    	Installation::check();
    }
    
	// check if the url is coming for a subdomain because if it is... we should
	// redirect to index.php because it is not coming to us!
	$config =& Config::getConfig();
	if( $config->getValue( "subdomains_enabled") && Subdomains::isSubdomainUrl()) {
    	$indexPage = $config->getValue( "script_name", "index.php" );
		lt_include( PLOG_CLASS_PATH.$indexPage );
		die();
	}
	
	// initialiaze the session
	SessionManager::init();		
	
    //// main part ////
    $controller = new SummaryController();
    $controller->process( HttpVars::getRequest());
?>
