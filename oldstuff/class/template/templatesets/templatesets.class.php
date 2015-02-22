<?php

	
	lt_include( PLOG_CLASS_PATH."class/template/templatesets/templateset.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/templatesets/templatesetstorage.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/templateservice.class.php" );

	/**
	 * \ingroup Template
	 * 
	 * Takes care of retrieving information about template sets. By using this class
	 * we can know which template sets are globally available, or which blog-specific templates
	 * a blog has.
	 *
	 * The relation between this class, TemplateService and TemplateSetStorage is that TemplateService
	 * takes care of generating the right object (CachedTemplate, PluginTemplate, Template) when
	 * needed for rendering template pages. TemplateSets deals with the logical information (does it have an
	 * screenshot? Is it a local template?) and TemplateStorage deals with adding
	 * templates, removing them from disk, etc.
	 */
	class TemplateSets 
	{

		function TemplateSets()
		{
			
		}

		/**
		 * Returns all the template sets that are available for a given blog, including global
		 * templates by default. 
		 *
		 * @param blogId The id of the blog
		 * @param includeGlobal Whether global templates should be included, or else only
		 * blog-specific templates will be returned.
		 * @return An array of TemplateSet objects
		 */
		function getBlogTemplateSets( $blogId, $includeGlobal = true )
		{
			$blogTemplates = $this->getBlogTemplates( $blogId );

			if( $includeGlobal )
				$templateSets = $this->getGlobalTemplateSets();
			else
				$templateSets = Array();

			// now loop through the list and create the TemplateSet objects
			foreach( $blogTemplates as $template ) {
				$templateSet = new TemplateSet( $template, TEMPLATE_SET_BLOG_SPECIFIC, $blogId );
				array_push( $templateSets, $templateSet );
			}

			return $templateSets;
		}

		/**
		 * Returns all the global template sets that are available to all blogs
		 *
		 * @return returns an array of TemplateSet objects with all the global templates
		 */
		function getGlobalTemplateSets()
		{
			// get the list of global templates
			$globalTemplates = $this->getGlobalTemplates();

			// now loop through the list and create the TemplateSet objects
			$templateSets = Array();
			foreach( $globalTemplates as $template ) {
				$templateSet = new TemplateSet( $template, TEMPLATE_SET_GLOBAL );
				array_push( $templateSets, $templateSet );
			}

			return $templateSets;
		}


        /**
         * Returns an array that contains the name of all the 'global' templates
         * available.
         *
         * @return Returns an array of strings with the name of the template sets
         */
        function getGlobalTemplates()
        {
        	$config =& Config::getConfig();

            $templates = $config->getValue( "templates" );

            if( !is_array($templates))
                $templates = Array();

            return $templates;
        }

        /**
         * Returns a list with all the custom templates available *only*
         * to this blog.
         *
         * @return An array of string containing the identifiers of the template
         * sets available for the given blog
         */
        function getBlogTemplates( $blogId )
        {
        	lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );        
        	$blogs = new Blogs();
            $blogInfo = $blogs->getBlogInfo( $blogId );
            $blogSettings = $blogInfo->getSettings();

            $templates = $blogSettings->getValue( "blog_templates" );

			if( $templates == "" || !$templates )
				$templates = Array();

            return $templates;
        }

        /**
         * Returns an array with all the global templates and the custom ones
         * from the given blog.
         *
         * @return Array
         * @see Config::getGlobalTemplates
         * @see Config::getBlogTemplates
         */
        function getAllTemplates( $blogId )
        {
        	$globalTemplates = $this->getGlobalTemplates();
            $blogTemplates   = $this->getBlogTemplates( $blogId );

            $allTemplates = Array();
            foreach( $globalTemplates as $template )
            	array_push( $allTemplates, $template );
            foreach( $blogTemplates as $template )
            	array_push( $allTemplates, $template );

            return $allTemplates;
        }

		/**
		 * Returns a TemplateSet object with information about the template. If there are a local
		 * template and a global template with the same template id, the global template will <b>ALWAYS</b>
		 * have more priority!
		 *
		 * @param templateName the name of the template
		 * @param blogId 0 if the template is global or the blog id otherwise
		 * @return a TemplateSet object, or 'null' if the template does not exist at all
		 */
		function getTemplateSet( $templateName, $blogId = 0 )
		{
			if( $this->isTemplate( $templateName ))
				$templateSet = new TemplateSet( $templateName, TEMPLATE_SET_GLOBAL, 0 );
			elseif( $blogId && $this->isBlogTemplate( $templateName, $blogId ))
				$templateSet = new TemplateSet( $templateName, TEMPLATE_SET_BLOG_SPECIFIC, $blogId );
			else {
				$templateSet = null;
			}

			return $templateSet;
		}


        /**
         * Returns true if the template is a valid global template set, or false otherwise.
         *
         * @param templateName The name of the template.
         */
        function isTemplate( $templateName )
        {
        	$config =& Config::getConfig();
        	$templates = $config->getValue( "templates" );

        	if( !is_array($templates))
        		return false;

            return in_array( $templateName, $templates );
        }

        /**
         * Returns true if the template is a valid blog template set, or false otherwise.
         *
         * @param templateName The name of the template.
         * @param blogId The identifier of the blog we'd like to check
         */
        function isBlogTemplate( $templateName, $blogId )
        {
        	lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );        
        	$blogs = new Blogs();
        	$blog = $blogs->getBlogInfo( $blogId );
            if(!$blog)
                return false;
            
            $blogSettings = $blog->getSettings( $blogId );

            // get the array with the template names stored in the settings
            $blogTemplates = $blogSettings->getValue( "blog_templates" );
            if( empty($blogTemplates) || $blogTemplates == false )
            	return false;

            return in_array( $templateName, $blogTemplates );
        }

        /**
         * returns the default template set that has been configured in this site
         *
         * @return A TemplateSet object
         */
        function getDefaultTemplateSet()
        {
	     	$config =& Config::getConfig();
	     	$default_template_name = $config->getValue("default_template");
            $default_template = $this->getTemplateSet($default_template_name);
            if(!$default_template)
                $default_template = $this->getTemplateSet("standard");
            return $default_template;
        }
	}
?>