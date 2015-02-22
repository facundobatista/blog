<?php

    /**
     * \defgroup Security
     *
     * The Security module provides a basic implementation of a generic Pipeline to which we can 
     * register "filters", which wil carry out specific actions. Any of the filters can interrupt the
     * processing, depending on the logic of the filter. Please see the Pipeline class for more information.
     */


    
    lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
    lt_include( PLOG_CLASS_PATH."class/security/commentfilter.class.php" );
    
    /**
     * global array used to hold the list of filters that we're going to use in the pipeline.
     * Now again, more than ever, wish that PHP4 had support for static attributes at the class
     * level... 
     */
    $_pLogPipelineRegisteredFilters = array();
    Pipeline::registerFilter("CommentFilter");


    /**
     * \ingroup Security
     *
     * Implementation of a basic security framework based on a
     * pipeline. Every element of the pipeline implements a simple
     * security mechanism. When one of the filters in the pipeline
     * find a condition that is matched by the incoming request, the
     * request will be blocked and the processing of the pipeline will be stopped.
     *
     * As of pLog 1.0, plugins can also register new filters dynamically via the PluginBase::registerFilter(), which
     * eventually uses the static method Pipeline::registerFilter() Since it is static, this method is not restricted
     * to plugins and can be used by any other class at run time to add new filters.
     *
     * The out of the box implementation of the Pipeline comes with a filter that
     * implements a Bayesian filter for advanced spam protection. See the
     * BayesianFilter class for more information.
     */
    class Pipeline  
    {

        /**
         * HTTP request that will be used if the filter is doing
         * some content filtering
         */
        var $_httpRequest;

        /**
         * the BlogInfo object that has information about the blog
         * that is currently processing the incoming request
         */
        var $_blogInfo;

        /**
         * variable to hold the final result of executing the pipeline
         */
        var $_result;

        /**
         * Constructor
         *
         * @param httpRequest The HTTP request
         * @param blogInfo The BlogInfo object with information about the blog
         * that is currently executing this pipeline
         */
        function Pipeline( $httpRequest, $blogInfo = null )
        {
            $this->_httpRequest = $httpRequest;
            $this->_blogInfo    = $blogInfo;
        }
        
        /**
         * Method that takes care of registering the default filters
         * that should be run at the end of the pipeline.
         *
         * In 2.0, this will probably change to have a priority value
         * or something to order the filters
         *
         * This should be called after all other filters have been
         * registered
         * @static
         * @return Always true
         */
        function _registerPostDefaultFilters()
        {
            lt_include( PLOG_CLASS_PATH."class/security/bayesianfilter.class.php" );
            $this->registerFilter( "BayesianFilter" );
            return true;
        }
        
        /**
         * Static method that registers a filter externally
         *
         * @param filterClass A class that implements the PipelineFilter interface
         * @static
         * @return Always returns true.
         */
        function registerFilter( $filterClass )
        {
            global $_pLogPipelineRegisteredFilters;
            
            if( !is_array($_pLogPipelineRegisteredFilters))     // make sure that we have an array...
                $_pLogPipelineRegisteredFilters = Array();
                
            $_pLogPipelineRegisteredFilters["$filterClass"] =  $filterClass;
        }

        /**
         * Processes the pipeline, using the request and blogInfo
         * objects as given in the constructor.
         */
        function process()
        {
            lt_include( PLOG_CLASS_PATH . 'class/security/pipelinerequest.class.php' );
            lt_include( PLOG_CLASS_PATH . 'class/security/pipelineresult.class.php' );
            global $_pLogPipelineRegisteredFilters;        
            static $defaultFiltersRegistered = false;
            
            // check if the pipeline is enabled
            $config =& Config::getConfig();
            if( $config->getValue( "security_pipeline_enabled" ) == false ) {
                // pipeline is disabled, so everything's fine
                return new PipelineResult( true );
            }

                // any filters that should be at the end of the
                // pipeline (like the bayesian filter)
            if(!$defaultFiltersRegistered){
                $defaultFiltersRegistered = true;
                $this->_registerPostDefaultFilters();
            }

            // Assume that this will be successful
            $this->_result = new PipelineResult( true );

            $rejected = false;
            
            // if enabled, then check all the filters
            foreach( $_pLogPipelineRegisteredFilters as $filterClass ) {
                    // create an instance of the filter
                $pipelineRequest = new PipelineRequest( $this->_httpRequest,
                                                        $this->_blogInfo,
                                                        $rejected );
                $filter = new $filterClass( $pipelineRequest );
                    // and execute it...
                $result = $filter->filter();

                    // if there was an error, save it, and notify
                    // the following filters in the chain
                if( !$result->isValid()) { 
                    $rejected = true;
                    $this->_result = $result;
                }
            }
    
            return $this->_result ;
        }
    }
?>
