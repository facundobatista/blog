<?php

	

    /**
     * Implementation of a very simple resource server.
     *
     * As a parameter, receives the name of a resource and it will take
     * care of fetching it.
     */
    if (!defined( "PLOG_CLASS_PATH" )) {
        define( "PLOG_CLASS_PATH", dirname(__FILE__)."/");
    }

	include_once( PLOG_CLASS_PATH."class/bootstrap.php" );
    lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );

	// the functionality of the old ressever.php file have been moved
	// to the action ResourceServerAction and ResourceServerView for better
	// integration with the reset of the framework. This file has been kept
	// here for compatibility reasons and it is still referenced in all urls
	// generated internally that point to resources so please DO NOT REMOVE IT!
	$_REQUEST["op"] = "ResourceServer";
	
	$config =& Config::getConfig();
	$indexPage = $config->getValue( "script_name", "index.php" );
	
	lt_include( PLOG_CLASS_PATH.$indexPage );
?>
