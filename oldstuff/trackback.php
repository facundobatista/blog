<?php

    if (!defined( "PLOG_CLASS_PATH" )) {
        define( "PLOG_CLASS_PATH", dirname(__FILE__)."/");
    }
	
	include_once( PLOG_CLASS_PATH."class/bootstrap.php" );
	lt_include( PLOG_CLASS_PATH."class/net/http/httpvars.class.php" );

    //
	// set appender for the "trackback" logger to "file" in
	// config/logging.properties.php if you'd like this code to log some debug
	// messages to tmp/trackback.log.
    //
	
	$request = HttpVars::getRequest();
	$request["op"] = "AddTrackback";
	HttpVars::setRequest( $request );
	
	lt_include( PLOG_CLASS_PATH."index.php" );

?>
