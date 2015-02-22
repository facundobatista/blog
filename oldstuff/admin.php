<?php
    // please enable the line below if you are having memory issues
    //ini_set('memory_limit', "16M");

    if (!defined( "PLOG_CLASS_PATH" )) {
        define( "PLOG_CLASS_PATH", dirname(__FILE__)."/");
    }

    include_once( PLOG_CLASS_PATH."class/bootstrap.php" );
    lt_include( PLOG_CLASS_PATH."class/controller/admincontroller.class.php" );
    lt_include( PLOG_CLASS_PATH."class/net/http/session/sessionmanager.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/userinfo.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/bloginfo.class.php" );
    lt_include( PLOG_CLASS_PATH."class/plugin/pluginmanager.class.php" );
    lt_include( PLOG_CLASS_PATH.'class/template/templatesets/templatesets.class.php' );

    ini_set("arg_seperator.output", "&amp;");
    ini_set("magic_quotes_runtime", 0 );

    // start gathering statistics
    Info::startMetrics();

    //
    // a security check, or else people might forget to remove the wizard.php script
    //
    if( File::isReadable( "wizard.php")) {
		lt_include( PLOG_CLASS_PATH."install/installation.class.php" );
		Installation::check();
    }

    // initialize the session
    SessionManager::init();

    $controller = new AdminController();

    // load the plugins, this needs to be done *before* we call the
    // Controller::process() method, as some of the plugins _might_
    // add new actions to the controller
    $pluginManager =& PluginManager::getPluginManager();
    $pluginManager->loadPlugins( "admin" );

    // give control to the, ehem, controller :)
    $controller->process( HttpVars::getRequest());
	
    // log statistics, only for debugging purposes
    //Info::logMetrics();
?>
