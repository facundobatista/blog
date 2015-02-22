<?php

	/**
	 * This script handles the registration process.
	 */

    if (!defined( "PLOG_CLASS_PATH" )) {
        define( "PLOG_CLASS_PATH", dirname(__FILE__)."/");
    }

    include_once( PLOG_CLASS_PATH."class/bootstrap.php" );
	lt_include( PLOG_CLASS_PATH."class/summary/controller/registrationcontroller.class.php" );
    lt_include( PLOG_CLASS_PATH."class/misc/version.class.php" );    		
    lt_include( PLOG_CLASS_PATH."class/net/http/httpvars.class.php" );    
	
	// initialiaze the session
	SessionManager::init();	

	// and the registration/sequential controller
	$r = new RegistrationController();
	$r->process( HttpVars::getRequest());	
?>