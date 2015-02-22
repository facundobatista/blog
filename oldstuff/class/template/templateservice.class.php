<?php

    lt_include( PLOG_CLASS_PATH.'class/config/config.class.php' );
    lt_include( PLOG_CLASS_PATH.'class/file/file.class.php' );

	/**
	 * default permissions used to create temporary template folders. Seems like
	 * Smarty creates them as 0771 but we have been adviced to create
	 * them as 775 as per this discussion: http://bugs.plogworld.net/view.php?id=253
	 */
	define( 'DEFAULT_TEMPLATE_TEMP_FOLDER_PERMISSIONS', 0775 );

    /**
     * \ingroup Template
     *
     * Factory class that takes care of providing Template or CachedTemplate objects
     * whenever requested.
     *
     * The advantage of using this TemplateService class is that we can delegate on it things
     * like finding the folder where the template is, choosing the right template depending
     * on the client type (normal browser, wap-enabled device, etc)
     *
     * In order to find the most suitable template, it takes several things into account:
     *
     * <ul>
     * <li>Settings stored for the current blog</li>
     * <li>User agent of the client</li> (<b>NOTE: </b>will not be implemented yet)
     * <li>The default template specified in the server-wide configuration file</li>
     * </ul>
     *
     * TemplateService is the preferred way to generate instances of the Template class:
     *
     * <pre>
     *  $ts = new TemplateService();
     *  // it's enough with "main", there is no need to specify "main.template" as the template name
     *  $template = $ts->Template( "main", $blogInfo->getBlogTemplate(), $blogInfo );
     *  $template->assign( "string", "This is a sample string" );
     *  ...
     *  print($template->display());
     * </pre>
     */
    class TemplateService
	{

    	/**
         * Constructor
         */
    	function TemplateService()
        {

        }

        /**
         * Generates a Template object for the given template name. This fuction does <b>not</b>
         * require the full path to the file!!
         *
		 * @param templateName The name of the template, it is not necessary to provide
		 * the .template extension.
         * @param layout A predefined layout style, which corresponds with the name of a
         * folder under the templates/ folder so 'blueish' would mean templates/blueish/
         * @param blogInfo If this parameter is not null, then it will be used to locate
         * a blog-specific template. Otherwise, we will only look for the template
         * in the "global" folders. This parameter defaults to 'null'.
         * @return A Template object representing the template file we asked for.
         */
        function Template( $templateName, $layout, $blogInfo = null )
        {
            lt_include( PLOG_CLASS_PATH . 'class/template/template.class.php' );

			// get some information about the folder where the template is and the template file
			$templateInfo = $this->_getTemplateFileInfo( $templateName, $layout, $blogInfo );
			$templateFileName = $templateInfo['templateFileName'];
			$templateFolder = $templateInfo['templateFolder'];

            // create the template and make sure if we we have to force Smarty
            // to look for it somewhere else other than the default folder
            $t = new Template( $templateFileName );
            if( $templateFolder != '' )
            	$t->setTemplateDir( $templateFolder );

            $t->assign( 'templatename', $templateName );
			$t->assign( 'blogtemplate', $templateFolder.'/'.$layout );
			$t->config_dir = $templateFolder.'/'.$layout;

			// change a few things...
			$t = $this->_configureTemplateSettings( $t, $blogInfo, $layout );

            return $t;
        }

		/**
		 * Returns a Template or CachedTemplate object pointing to a custom template.
		 *
		 * @param templateName The name of the template, it is not necessary to provide
		 * the .template extension.
         * @param layout A predefined layout style, which corresponds with the name of a
         * folder under the templates/ folder so 'blueish' would mean templates/blueish/
		 * @param cached Whether the custom template should be cached or not (this will affect
		 * the kind of the returned object)
		 * @return a Template or CachedTemplate object, depending on whether
		 * $cached is 'true' or 'false'
		 */
		function customTemplate( $templateName, $layout, $cached = false )
		{
	        lt_include( PLOG_CLASS_PATH.'class/template/cachedtemplate.class.php' );

			// get a nice Template object
			$config =& Config::getConfig();
			if( $cached )
				$t = $this->CachedTemplate( $templateName, $layout );
			else
				$t = $this->Template( $templateName, $layout );

			$tmpFolder = $config->getValue( "temp_folder" );
			$templateTmpFolder = $tmpFolder.'/'.$layout;
			$t->cache_dir = $templateTmpFolder;
			$t->compile_dir = $templateTmpFolder;

			// and now make sure that there is a folder where we can save
			// our rendered templates
			if( !File::exists( $templateTmpFolder )) {
				File::createDir( $templateTmpFolder, DEFAULT_TEMPLATE_TEMP_FOLDER_PERMISSIONS );
			}

			$t->compile_check = $config->getValue( 'template_compile_check' );

			return $t;
		}

		/**
		 * returns a template from the admin folder. It still uses TemplateService::Template but
		 * exports additional information to the template such as the base template path so that
		 * we can use {$admintemplatepath} from Smarty to get the right path, etc.
		 *
		 * @param templateName The name of the template, it is not necessary to provide
		 * the .template extension.
		 * @param blogInfo
		 * @return A Template object pointing to a template from the templates/admin/ folder
		 */
		function AdminTemplate( $templateName, $blogInfo = null )
		{
	        lt_include( PLOG_CLASS_PATH.'class/template/templatesets/templatesetstorage.class.php' );

			$t = $this->Template( $templateName, 'admin', $blogInfo );
			$t->assign( 'admintemplatepath', TemplateSetStorage::getAdminTemplateFolder());
			
			return $t;
		}

		/**
		 * returns a CachedTemplate object, which works in exactly the same way as a Template
		 * object but its contents will be cached as soon as they are generated. The lifetime
		 * of cached contents is controlled via the 'template_cache_lifetime' configuration
		 * parameter, but contents will be regenerated automatically as soon as
		 * CacheControll::resetBlogCache() is called.
		 *
		 * @param templateName The name of the template, it is not necessary to provide
		 * the .template extension.
         * @param layout A predefined layout style, which corresponds with the name of a
         * folder under the templates/ folder so 'blueish' would mean templates/blueish/
		 * @param blogInfo
		 * @return a CachedTemplate object pointing to the right .template file in disk
		 */
        function CachedTemplate( $templateName, $layout, $blogInfo = null )
        {
	        lt_include( PLOG_CLASS_PATH.'class/template/cachedtemplate.class.php' );

			// get some information about the folder where the template is and the template file
			$templateInfo = $this->_getTemplateFileInfo( $templateName, $layout, $blogInfo );
			$templateFileName = $templateInfo['templateFileName'];
			$templateFolder = $templateInfo['templateFolder'];

            // create the template and make sure if we we have to force Smarty
            // to look for it somewhere else other than the default folder
            $t = new CachedTemplate( $templateFileName );
            if( $templateFolder != '' )
            	$t->setTemplateDir( $templateFolder );

            $t->assign( 'templatename', $templateName );
			$t->assign( 'blogtemplate', $templateFolder.'/'.$layout );
			$t->config_dir = $templateFolder.'/'.$layout;

			// change a few things...
			$t = $this->_configureTemplateSettings( $t, $blogInfo );
			
			$t->useTemplateLoadOrder = true;

            return $t;
        }

		/**
		 * @private
		 * Factored out from above...
		 */
		function _getTemplateFileInfo( $templateName, $layout, $blogInfo )
		{
	        lt_include( PLOG_CLASS_PATH.'class/template/templatesets/templatesetstorage.class.php' );

            // build the file name
            if( $blogInfo == null ) {
            	$templateFileName = $layout.'/'.$templateName.'.template';
				$templateFolder = TemplateSetStorage::getBaseTemplateFolder();
            }
            else {
            	//
            	// might be the case that the template is not local but global, so
                // by default, global templates will always have preference over
                // local templates. If the template is global, then
                //
				$baseTemplateFolder = TemplateSetStorage::getBaseTemplateFolder();
                $globalTemplateFolder = $baseTemplateFolder.'/'.$layout;
                $localTemplateFolder  = $baseTemplateFolder.'/'.BLOG_BASE_TEMPLATE_FOLDER.$blogInfo->getId().'/'.$layout;

                //print("local = $localTemplateFolder - global = $globalTemplateFolder<br/>");
                $templateFileName = $layout.'/'.$templateName.'.template';
                if( !File::isDir( $globalTemplateFolder )) {
                	//$templateFileName = $layout."/".$templateName.".template";
                    $templateFolder = $baseTemplateFolder.'/'.BLOG_BASE_TEMPLATE_FOLDER.$blogInfo->getId();
                }
				else
					$templateFolder = $baseTemplateFolder.'/';
            }

			$result['templateFileName'] = $templateFileName;
			$result['templateFolder'] = $templateFolder;

			return $result;
		}

		/**
		 * Returns a Template object loaded from a plugin template
		 * Plugins are different in the sense that they store their templates in the
		 * plugins/xxx/templates, where 'xxx' is the plugin identifier
		 *
		 * @param pluginId The id of the plugin, which matches the name of a folder
		 * under the plugins/ folder.
		 * @param templateName Name of the template, without the .template extension
		 * @param blogInfo
		 * @return a Template object
		 * @see PluginCachedTemplate
		 */
		function PluginTemplate( $pluginId, $templateName, $blogInfo = null )
		{
            lt_include( PLOG_CLASS_PATH . 'class/template/templatesets/templatesetstorage.class.php' );
            lt_include( PLOG_CLASS_PATH . 'class/template/template.class.php' );

			// define the template file name
			$templateFolder = TemplateSetStorage::getPluginTemplateFolder( $pluginId );
			$templateFileName = $templateFolder.$templateName.'.template';
            $t = new Template( $templateFileName );
			$t->setTemplateDir( $templateFolder );

            $t->assign( 'templatename', $templateName );
			$t->assign( 'admintemplatepath', TemplateSetStorage::getAdminTemplateFolder());
			$t->assign( 'plugintemplatepath', $templateFolder );

			// change a few things...
			$t = $this->_configureTemplateSettings( $t, $blogInfo );
			
			

            return $t;
		}

		/**
		 * Returns a CachedTemplate object loaded from a plugin template
		 * Plugins are different in the sense that they store their templates in the
		 * plugins/xxx/templates, where 'xxx' is the plugin identifier
		 *
		 * @param pluginId The id of the plugin, which matches the name of a folder
		 * under the plugins/ folder.
		 * @param templateName Name of the template, without the .template extension
		 * @param blogInfo
		 * @return a CachedTemplate object
		 */
		function PluginCachedTemplate( $pluginId, $templateName, $blogInfo = null )
		{
	        lt_include( PLOG_CLASS_PATH.'class/template/cachedtemplate.class.php' );

			// define the template file name
			$templateFolder = TemplateSetStorage::getPluginTemplateFolder( $pluginId );
			$templateFileName = $templateFolder.$templateName.'.template';
            $t = new CachedTemplate( $templateFileName );
			$t->setTemplateDir( $templateFolder );

            $t->assign( 'templatename', $templateName );
			$t->assign( 'admintemplatepath', TemplateSetStorage::getAdminTemplateFolder());
			$t->assign( 'plugintemplatepath', $templateFolder );

			// change a few things...
			$t = $this->_configureTemplateSettings( $t, $blogInfo );

            return $t;
		}

		/**
		 * @private
		 */
		function _configureTemplateSettings( $t, $blogInfo, $layout = "" )
		{
			// change a few things...
            $config =& Config::getConfig();
			$tmpFolder = $config->getValue( 'temp_folder' );
            if (strpos($tmpFolder,'.') === 0)
                $tmpFolder = rtrim(PLOG_CLASS_PATH,'/').ltrim($tmpFolder,'.');
			if( $blogInfo == null )
				$blogTmpFolder = $tmpFolder;
			else {
				$blogTmpFolder = $tmpFolder.'/'.$blogInfo->getId();
				if( !File::exists( $blogTmpFolder )) {
					File::createDir( $blogTmpFolder, DEFAULT_TEMPLATE_TEMP_FOLDER_PERMISSIONS );
				}
				$t->secure_dir[] = "./templates/blog_".$blogInfo->getId()."/$layout";
			}

            $t->cache_dir    = $blogTmpFolder;
            $t->compile_dir  = $blogTmpFolder;

			$t->compile_check = $config->getValue( 'template_compile_check' );

			return $t;
		}
    }
?>