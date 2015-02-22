<?php

	if (!defined( "PLOG_CLASS_PATH" )) {
    	define( "PLOG_CLASS_PATH", dirname(__FILE__)."/");
    }

	include_once( PLOG_CLASS_PATH."class/bootstrap.php" );
	lt_include( PLOG_CLASS_PATH."class/net/xmlrpc/xmlrpcserver.class.php" );
	
	$server = new XmlRpcServer();

?>
