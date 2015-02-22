<?php

    if (!defined( "PLOG_CLASS_PATH" )) {
        define( "PLOG_CLASS_PATH", dirname(__FILE__)."/");
    }

	include_once( PLOG_CLASS_PATH."class/bootstrap.php" );
    lt_include( PLOG_CLASS_PATH."class/net/http/httpvars.class.php" );
    lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	lt_include( PLOG_CLASS_PATH."class/net/request.class.php" );
	lt_include( PLOG_CLASS_PATH."class/net/requestgenerator.class.php" );

    /**
     * Implements an error handler for 401, 403 and 404 errors so that
     * if the user types a user name after the url, and Apache complains,
     * thanks to the ErrorDocument directive this handler will receive the
     * request and we will be able to perform a redirectiom
     *
     * To get this to work, we need a provider which allows to use
     * .htaccess files in their accounts and at the same time, allows
     * to have ErrorDocument directives in the .htaccess file.
     *
     * This should be the content of the file:
     *
     * ErrorDocument 401 /plog/error.php
     * ErrorDocument 403 /plog/error.php
     * ErrorDocument 404 /plog/error.php
     *
     * If LifeType is running somewhere else other than /plog/, then that
     * should be changed since an absolute URL is required.
     */
	$config =& Config::getConfig(); 

	if( $config->getValue( "request_format_mode" ) == SEARCH_ENGINE_FRIENDLY_MODE ) {
		lt_include( PLOG_CLASS_PATH."class/net/prettyrequestparser.class.php" );
		$server = HttpVars::getServer();
		$parser = new PrettyRequestParser( "default", $server["REQUEST_URI"]);
		$result = $parser->parse();
		HttpVars::setRequest( $result );
		lt_include( PLOG_CLASS_PATH."index.php" );		
	}
	else {
		lt_include( PLOG_CLASS_PATH."blog.php" );
	}
?>
