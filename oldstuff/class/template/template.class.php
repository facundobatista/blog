<?php

	/**
	 * \defgroup Template
	 *
	 * The Template module provides classes for dealing with templates, which are built on
	 * top of Smarty. 
	 *
	 * It also provides method for managing the cache, template validation sandboxes and for
	 * dealing with template sets.
	 *
	 * @see TemplateService
	 * @see Template
	 * @see TemplateSets
	 * @see Menu
	 * @see MenuRenderer
	 */

    lt_include( PLOG_CLASS_PATH.'class/template/smarty/Smarty.class.php' );
    lt_include( PLOG_CLASS_PATH.'class/config/config.class.php' );
    lt_include( PLOG_CLASS_PATH.'class/file/file.class.php' );

	// template load order constants
	define( "TEMPLATE_LOAD_ORDER_DEFAULT_FIRST", 1 );	
	define( "TEMPLATE_LOAD_ORDER_USER_FIRST", 2 );
	
	// name of the folder where the default template is stored
	define( "DEFAULT_TEMPLATE_FOLDER", "default" );
	
	// Smarty dynamic block function
	function smarty_block_dynamic($param, $content, &$smarty) {
    	return $content;
	}	

    /**
     * \ingroup Template
     * 
     * Wrapper around the Smarty class, inspired by the article
     * http://zend.com/zend/tut/tutorial-stump.php
     *
     * This class provides additional methods and initial values for the original Smarty
     * class, and reimplements the methods Smarty::fetch() and Smarty::display() so that they do 
     * not need an extra parameter.
     *
     * It is not recommended to create instances of this class directly but instead, use the factory
     * TemplateService which is able to generate different types of Template objects with some pre-set
     * values. The TemplateService class can also deal with cached and non-cached templates.
     *
     * @see TemplateService
     * @see CachedTemplate
     */
    class Template extends Smarty 
    {

        var $_templateFile = null;
        
        // logger object
        var $log = null;

		// whether to use the template load order settings
		var $useTemplateLoadOrder = false;
		
		/**
		 * Do not load the trim_whitespace plugin
		 * @public
		 */
		var $forceDisableTrimWhitespace;

        /**
         * Constructor. 
         *
         * @param templateFile Complete path to the template file we are going to render
         */
        function Template( $templateFile )
        {
            // create the Smarty object and set the security values
            $this->Smarty();
            $this->caching = false;
            //$this->cache_lifetime =  $cacheLifetime;
            $config =& Config::getConfig();
            $this->cache_dir    = $config->getValue( 'temp_folder' );

            $this->_templateFile = $templateFile;

			// we want to load the plugin by default...
            $this->forceDisableTrimWhitespace = false;

            // enable the security settings
            $this->php_handling = false;
            	
            $this->security = (boolean)!$config->getValue( 'allow_php_code_in_templates', false );
            //$this->security = true;
			$this->secure_dir = Array( './imgs', './templates/admin', './templates/' );

            // default folders
            $this->compile_dir  = $config->getValue( 'temp_folder' );
            $this->template_dir = array( '.', $config->getValue( 'template_folder' ) );
            $this->config_dir   = $config->getValue( 'template_folder' );
            $this->compile_check = $config->getValue( 'template_compile_check', true );
            // this helps if php is running in 'safe_mode'
            $this->use_sub_dirs = false;

			// register dynamic block for every template instance
			$this->register_block('dynamic', 'smarty_block_dynamic', false);			
        }

	    /**
	     * called for included templates
	 	 * This has been reimplemented from Smarty::_smarty_include() so that we can define a set
	     * of locations where template files can be located if the specified path and file do not exist.
	     *
		 * @see Smarty::_smarty_include
		 * @private
	     */
	    function _smarty_include($params)
	    {
			if( $this->useTemplateLoadOrder ) {
				$config =& Config::getConfig();
				$defaultTemplateFile = $config->getValue( "template_folder")."/".DEFAULT_TEMPLATE_FOLDER."/".basename( $params['smarty_include_tpl_file'] );
				//print( "load order = ".$config->getValue( "template_load_order" ));				
				if( $config->getValue( "template_load_order" ) == TEMPLATE_LOAD_ORDER_DEFAULT_FIRST ) {
					// if the 'default' one should be included first, then check if it is available and if
					// it is, go ahead. If it isn't then we'll just display an error
					if( File::isReadable( $defaultTemplateFile )) {
						$params['smarty_include_tpl_file'] = $defaultTemplateFile;
					}
				}
				else {
					// include the user's template unless it is not available, in which case we'll use the 
					// default one
					$readable = false;
					foreach( $this->template_dir as $templateDir ) {						
						if( File::isReadable( $templateDir."/".$params['smarty_include_tpl_file'] )) {
							$readable = true;
							break;
						}
					}
					if( !$readable ) {
						// if the file wasn't found in any of the template folders, then we should be using the default one
						$params['smarty_include_tpl_file'] = $defaultTemplateFile;					
					}
				}
			}
			
			Smarty::_smarty_include( $params );
	    }

        /**
         * By default templates are searched in the folder specified by the
         * template_folder configuration setting, but we can force Smarty to
         * look for those templates somewhere else. This method is obviously to be
         * used *before* rendering the template ;)
         *
         * @param templateFolder The new path where we'd like to search for templates
         * @return Returns always true.
         */
        function setTemplateDir( $templateDir )
        {
            $this->template_dir = array( '.', $templateDir );

            return true;
        }

        /**
         * Returns the name of the template file
         *
         * @return The name of the template file
		 *
		 * :TODO: 
		 * This code could do with some refactoring, its' pretty similar to what we've got in Template::_smarty_include()
         */
        function getTemplateFile()
        {			
			if( $this->useTemplateLoadOrder ) {
				$config =& Config::getConfig();
				$defaultTemplateFile = $config->getValue( "template_folder")."/".DEFAULT_TEMPLATE_FOLDER."/".basename( $this->_templateFile );
				if( $config->getValue( "template_load_order" ) == TEMPLATE_LOAD_ORDER_DEFAULT_FIRST ) {
					if( File::isReadable( $defaultTemplateFile )) {
						$this->_templateFile = $defaultTemplateFile;
					}
				}
				else {					
					$readable = false;
					foreach( $this->template_dir as $templateDir ) {						
						if( File::isReadable( $templateDir."/".$this->_templateFile )) {
							$readable = true;
							break;
						}
					}
					if( !$readable ) {
						// if the file wasn't found in any of the template folders, then we should be using the default one
						$this->_templateFile = $defaultTemplateFile;
					}
				}				
			}
			
            return $this->_templateFile;
        }

		/**
		 * Load all the required smarty filters
		 */
		function loadFilters()
		{
			$config =& Config::getConfig();
			if( $config->getValue( 'trim_whitespace_output' ) && !$this->forceDisableTrimWhitespace )
				$this->load_filter( 'output', 'trimwhitespace' );
		}

        /**
         * Renders the template and returns the contents as an string
         *
         * @return The result as an string
         */
        function fetch()
        {
			$this->loadFilters();
            return Smarty::fetch( $this->getTemplateFile());
        }

        /**
         * Displays the result of rendering the template
         *
         * @return I don't know :)
         */
        function display()
        {
			$this->loadFilters();	
            return Smarty::display( $this->getTemplateFile());
        }
        
        /**
         * the Template object is by default not cached
         *
         * @param viewId Not used
         * @return always false
         */
        function isCached( $viewId )
        {
            return false;
        }
    }
?>