<?php

    lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );

    if ( !defined("CHECK_CONFIG_REQUEST_MODE") )
        define( "CHECK_CONFIG_REQUEST_MODE", 0 );
    if ( !defined("NORMAL_REQUEST_MODE") )
        define( "NORMAL_REQUEST_MODE", 1 );
    if ( !defined("SEARCH_ENGINE_FRIENDLY_MODE") )
        define( "SEARCH_ENGINE_FRIENDLY_MODE", 2 );
    if ( !defined("MODREWRITE_MODE") )
        define( "MODREWRITE_MODE", 3 );
    if ( !defined("CUSTOM_REQUEST_MODE") )
        define( "CUSTOM_REQUEST_MODE", 4 );    

    /** 
     * \ingroup Net
     *
     * Request generators are a way to allow the templates to generate links in an abstract manner regarding of the
     * current settings. Each request generator defines its own format for its URLs, and by using request generators 
     * we can easily change the format of our URLs without the need to alter our templates 
     * (and therefore, without the need to hardcode URLs in our templates)
     *
     * This is the class that implements the factory pattern to obtain the correct request generator, given
     * a BlogInfo object and optionally, a request generator type. If not type is given, then
     * the class will check the value of the <b>request_format_mode</b> configuration parameter.
     *
     * The four types of request generators supported as of pLog 1.0 are:
     *
     * - NORMAL_REQUEST_MODE
     * - SEARCH_ENGINE_FRIENDLY_MODE
     * - MODREWRITE_MODE
     * - CUSTOM_REQUEST_MODE
     *
     * The preferred way to obtain a request generator is:
     *
     * <pre>
     *  $rg =& RequestGenerator::getRequestGenerator( $blogInfo );
     *  ...
     *  $blogLink = $rg->blogLink();
     * </pre>
     *
     * In order to force the factory class to generate a specific request generator:
     *
     * <pre>
     *  $rg =& RequestGenerator::getRequestGenerator( $blogInfo, CUSTOM_REQUEST_MODE );
     * </pre>
     * 
     * However, in cases when we have a BlogInfo available it is even better to use the BlogInfo::getBlogRequestGenerator()
     * method:
     *
     * <pre>
     *  $rg = $blogInfo->getBlogRequestGenerator();
     *  ...
     *  $blogLink = $rg->blogLink();
     * </pre>
     *
     * In order to check which methods are available to request generators, please see the documentation of the
     * RequestGenerator proxy class.     
     */
    class RequestGenerator  
    {	
		var $_mode;

        function getRequestGenerator( $blogInfo = null, $mode = CHECK_CONFIG_REQUEST_MODE )
        {
            // check the mode
            if( $mode == CHECK_CONFIG_REQUEST_MODE ) {
                $config =& Config::getConfig();
                $mode   = $config->getValue( "request_format_mode" );
            }

            $this->_mode  = $mode;

            // load the correct generator, while doing some nice dynamic loading...
            if( $this->_mode == SEARCH_ENGINE_FRIENDLY_MODE ) {
                lt_include( PLOG_CLASS_PATH."class/net/prettyrequestgenerator.class.php" );
                $rg = new PrettyRequestGenerator( $blogInfo );
            } 
            elseif( $this->_mode == MODREWRITE_MODE ) {
                lt_include( PLOG_CLASS_PATH."class/net/modrewriterequestgenerator.class.php" );
                $rg = new ModRewriteRequestGenerator( $blogInfo );
            } 
            elseif( $this->_mode == CUSTOM_REQUEST_MODE ) {
                lt_include( PLOG_CLASS_PATH."class/net/customrequestgenerator.class.php" );
                $rg = new CustomRequestGenerator( $blogInfo );                
            } 
            else {
                lt_include( PLOG_CLASS_PATH."class/net/rawrequestgenerator.class.php" );
                $rg = new RawRequestGenerator( $blogInfo );
            }

            return $rg;
        }
    }
?>
