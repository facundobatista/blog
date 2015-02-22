<?php


    lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
    lt_include( PLOG_CLASS_PATH."class/misc/glob.class.php" );
    lt_include( PLOG_CLASS_PATH."class/file/file.class.php" );
    lt_include( PLOG_CLASS_PATH."class/template/templatesets/templatesets.class.php" );

    define( "BLOG_BASE_TEMPLATE_FOLDER", "blog_" );

	/**
	 * \ingroup Template
	 *
	 * deals with template sets files in disk. The relation between this class,
	 * TemplateService and TemplateSets is that TemplateService takes care of generating
	 * the right object (CachedTemplate, PluginTemplate, Template) when needed for rendering
	 * template pages. TemplateSets deals with the logical information (does it have an
	 * screenshot? Is it a local template?) and TemplateStorage deals with adding
	 * templates, removing them from disk, etc.
	 *
	 * Class and API users should not need to use this class directly.
	 */
	class TemplateSetStorage
	{

		/**
		 * Constructor
		 */
		function TemplateSetStorage()
		{

		}

        /**
         * Returns the path to where the given template should be. If blogId == 0,
         * then the template will be considered global or else it will be considered
         * as a custom template for the given blog.
         *
         * @return Returns a string with the path to the folder.
         */
        function getTemplateFolder( $templateName, $blogId = 0 )
        {
            $config =& Config::getConfig();
            $baseFolder = $config->getValue( "template_folder" );
            if( $baseFolder[strlen($baseFolder)-1] != "/" )
            	$baseFolder = $baseFolder."/";

        	if( $blogId == 0 ) {
            	$templateFolder = $baseFolder.$templateName."/";
            }
            else {
            	$templateFolder = $baseFolder.BLOG_BASE_TEMPLATE_FOLDER."$blogId/".$templateName."/";
            }

            return $templateFolder;
        }

		/**
		 * returns the base folder where templates are stored (templates/) by default
		 *
		 * @return The folder where templates are stored, according to our configuration
		 * @static
		 */
        function getBaseTemplateFolder()
        {
        	$config =& Config::getConfig();

            $basePath = $config->getValue( "template_folder" );

            return $basePath;
        }

		/**
		 * returns the path where the given blog is storing its local templates
		 *
		 * @param blogId the id of the blog
		 * @return The template in disk where templates are being stored
		 * @static
		 */
        function getBlogBaseTemplateFolder( $blogId )
        {
            $config =& Config::getConfig();
            $baseFolder = $config->getValue( "template_folder" );
            if( $baseFolder[strlen($baseFolder)-1] != "/" )
            	$baseFolder = $baseFolder."/";

            $templateFolder = $baseFolder.BLOG_BASE_TEMPLATE_FOLDER."$blogId/";

            return $templateFolder;
        }

        /**
         * Recursively removes a folder from disk.
         *
         * @return True if successful or false otherwise.
         */
        function _removeFolder( $folderName )
        {
		// if the folder does not even exist, let's not even bother trying... It
		// could be that it was manually removed by the user or something!
		if( File::exists( $folderName ))
			$result = File::deleteDir( $folderName, true );
		else
			$result = true;

		return( $result );
        }

		/**
		 * @private
		 */
        function _removeTemplateFromArray( $array, $key )
        {
            $resultArray = Array();
            foreach( $array as $elem ) {
            	if( $elem != $key )
                	array_push( $resultArray, $elem );
            }

            return $resultArray;
        }

        /**
         * Removes a global template
		 *
		 * @param templateName The name of the template that we'd like to remove
         */
        function removeGlobalTemplate( $templateName )
        {
        	$config =& Config::getConfig();

			// check if the template really exists
            $availableTemplates = $config->getValue( "templates" );
			if( !in_array( $templateName, $availableTemplates ))
				return false;

            // remove the folder from disk
            $templateFolder = $this->getTemplateFolder( $templateName );
            $result = $this->_removeFolder( $templateFolder );
			if( !$result )
				return false;

            // remove the entry for that template from the configuration
            $newTemplateList = $this->_removeTemplateFromArray( $availableTemplates, $templateName );

            // update the list of templates
            $config->saveValue( "templates", $newTemplateList );

            return true;
        }

        /**
         * Removes a blog specific template.
		 *
		 * @param templateName The name of the blog-specific template that we'd like to remove
		 * @param blogId The id of the blog whose blog we'd like to remove
		 * @return true if sucessful or false otherwise
         */
        function removeBlogTemplate( $templateName, $blogId )
        {
        	$config =& Config::getConfig();

            // get the settings of this blog
        	$blogs = new Blogs();
            $blog = $blogs->getBlogInfo( $blogId );
            $blogSettings = $blog->getSettings();

            // looks like the blog doesn't exist, so let's not bother...
            if( empty($blogSettings) )
            	return false;

			// check if the template really exists
            $blogTemplates = $blogSettings->getValue( "blog_templates" );
			if( !in_array($templateName, $blogTemplates ))
				return false;

            // remove the folder where the template is
            $templateFolder = $this->getTemplateFolder( $templateName, $blogId );
            $result = $this->_removeFolder( $templateFolder );
            if( !$result )
            	return false;

            // remove the entry for that template from the configuration
            $newTemplateList = $this->_removeTemplateFromArray( $blogTemplates, $templateName );

            // finally, save the settings
            $blogSettings->setValue( "blog_templates", $newTemplateList );
            $blog->setSettings( $blogSettings );
            $blogs->updateBlog( $blog );

            return true;
        }

        /**
         * Removes a template from the templates folder. If blogId == 0, then we will
         * look for it as a global template, or else we will look for the template
         * in the folder of the blog.
		 *
		 * @param templateName
		 * @param blogId
		 * @see TemplateSetStorage::removeGlobalTemplate
		 * @see TemplateSetStorage::removeBlogTemplate
         *
         * @return Returns true if removed successfully or false otherwise.
         */
        function removeTemplate( $templateName, $blogId = 0 )
        {
        	if( $blogId == 0 ) {
            	$result = $this->removeGlobalTemplate( $templateName );
            }
            else {
            	$result = $this->removeBlogTemplate( $templateName, $blogId );
            }

            return $result;
        }

        /**
         * Adds a new template. If the template already exists, it is not
         * added again.
         *
         * @param templateName The name of the template we'd like to add
         * @param blogId The identifier of the blog to which we'd like to add
         * the template. If blogId == 0, then the template will be added as global.
         *
         * @return Returns true if ok or false otherwise.
         */
        function addTemplate( $templateName, $blogId = 0 )
        {
        	if( $blogId == 0 ) {
            	// the template is global
                $result = $this->addGlobalTemplate( $templateName );
            }
            else {
            	// the template is local
                $result = $this->addLocalTemplate( $templateName, $blogId );
            }

            return $result;
        }

        /**
         * Adds a global template to the site.
         *
         * @param templateName Name of the template we'd like to add
         * @return Returns true if successful or false otherwise.
         */
        function addGlobalTemplate( $templateName )
        {
        	$config =& Config::getConfig();

            // check if the value is there and if so, do nothing
            $templateSets = new TemplateSets();
            if( $templateSets->isTemplate( $templateName ))
            	return true;

			// let's make sure that we actually got an array... if not, then
			// let's recreate the array to avoid further problems down the road!
			$templates = $templateSets->getGlobalTemplates();
            if( !is_array($templates)) {
            	print("recreating the array!");
            	$templates = Array();
            }

            // if not there, we can add it
            array_push( $templates, $templateName );

            // and store the values
            $config->saveValue( "templates", $templates );

            return true;
        }

        /**
         * Adds a local template to the given blog.
         *
         * @param templateName Name that the template will use
         * @param blogId the identifier of the blog to which we're going to install
         * the new template.
         * @return True if successful or false otherwise.
         */
        function addLocalTemplate( $templateName, $blogId )
        {
        	$blogs = new Blogs();
            $blog = $blogs->getBlogInfo( $blogId );
            $blogSettings = $blog->getSettings();
            if( empty($blogSettings) )
            	return false;

            // get the array with the templates
            $blogTemplates = $blogSettings->getValue( "blog_templates" );

            // add a new one unless it's already there
            if( $blogTemplates == "" || $blogTemplates == null )
            	$blogTemplates = Array();

            if( in_array( $templateName, $blogTemplates ))
            	return true;

            array_push( $blogTemplates, $templateName );
            $blogSettings->setValue( "blog_templates", $blogTemplates );
            $blog->setSettings( $blogSettings );
            $blogs->updateBlog( $blog );

            return true;
        }

        /**
         * Creates the blog-specific folder where templates are stored.
         * If the folder is not there, it will be created. If it is already there,
         * then nothing will happen.
         *
         * @param blogId The identifier of the blog.
         */
        function createBlogTemplateFolder( $blogId )
        {
            // make sure that the blog-specific folder exists
            $templateFolder = $this->getBaseTemplateFolder();
            $blogTemplateFolder = "$templateFolder/blog_".$blogId;
            if( !File::isDir( $blogTemplateFolder )) {
            	File::createDir( $blogTemplateFolder, 0755 );
            }

            return $blogTemplateFolder;
        }

        /**
         * returns true if the given template file exists in the given template set, or false
         * if not.
         *
         * @param fileName The name of the file, without the .template extension
         * @param templateSet name of the template set.
         * @return Returns true if file exists and is readable, or false otherwise.
         */
        function templateExists( $fileName, $templateSet )
        {
            $filePath = $this->getBaseTemplateFolder()."/".$templateSet."/".$fileName.".template";

            // first check if it exists at all
            if( !File::exists( $filePath, $templateSet ))
                return false;

            // and if it does, check if it can be read
            if( !File::isReadable( $filePath, $templateSet ))
                return false;

            return true;
        }

		/**
		 * returns the path where the admin templates are stored
		 *
		 * @return a path
		 */
		function getAdminTemplateFolder()
		{
			$templatePath = TemplateSetStorage::getBaseTemplateFolder()."/admin";

			return $templatePath;
		}

		/**
		 * returns the folder where the templates used by a plugin
		 * are stored
		 *
		 * @para pluginId
		 * @return At path
		 */
		function getPluginTemplateFolder( $pluginId )
		{
			$templateFolder = "./plugins/".$pluginId."/templates/";

			return $templateFolder;
		}

		/**
		 * returns true if the template set has a screenshot available in disk
		 *
		 * @param templateName The name of the template
		 * @param blogId If the template is blog specific, then the blog id and if it is global, then
		 * '0' or no parameeter
		 */
		function isScreenshotAvailable( $templateName, $blogId = 0 )
		{
			// build up the path to the screenshot file
			$templatePath = $this->getTemplateFolder( $templateName, $blogId );
			// and return whether it is available or not
			$screenshotPath = $templatePath."/screenshot.jpg";
			return( File::isReadable( $screenshotPath ));
		}

		/**
		 * returns the path to the blog screenshot
		 *
		 * @param templateName
		 * @param blogId
		 * @return The http path to the screenshot file or empty if not available
		 */
		function getScreenshotUrl( $templateName, $blogId = 0)
		{
			// check if available at all...
			if( !$this->isScreenshotAvailable( $templateName, $blogId ))
				return $this->getNoScreenshotUrl();

			$config =& Config::getConfig();
			$templateFolder = $this->getBaseTemplateFolder();
			$baseUrl = $config->getValue( "base_url" );
			if( $blogId == 0 )
				$screenshotPath = "$baseUrl/$templateFolder/$templateName/screenshot.jpg";
			else
				$screenshotPath = "$baseUrl/$templateFolder/blog_{$blogId}/$templateName/screenshot.jpg";

			return $screenshotPath;
		}

		/**
		 * returns a default picture for those occasions when there is no screenshot
		 * available for the template
		 *
		 * @return a url
		 */
		function getNoScreenshotUrl()
		{
			$config =& Config::getConfig();
			$baseUrl = $config->getValue( "base_url" );
			$url = $baseUrl."/imgs/no-template-screenshot.jpg";

			return $url;
		}

		/**
		 * returns the path to the 'templates/misc/' folder
		 *
		 * @return path
		 * @static
		 */
		function getMiscTemplateFolder()
		{
			$templatePath = TemplateSetStorage::getBaseTemplateFolder()."/misc";
			return $templatePath;
		}
	}
?>