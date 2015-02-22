<?php

	lt_include( PLOG_CLASS_PATH.'class/file/finder/filefinder.class.php' );
	lt_include( PLOG_CLASS_PATH.'class/template/templatesets/templatesetstorage.class.php' );
	lt_include( PLOG_CLASS_PATH.'class/template/templatesets/templatesets.class.php' );
	
	/**
	 * \ingroup Template
	 *
	 * This class looks for new templates, be it new blog-specifc templates or global templates. It
	 * will just scan the contents of a given folder and add any new templates it can find.
	 *
	 * @see FileFinder
	 */
	class TemplateFinder extends FileFinder
	{
		
		/**
		 * constructor
		 *
		 * @param templateFolder Starting folder where we will look for files. If none
		 * is specified, the default ./templates folder will be used (or the one that has
		 * been configured in the "templates_folder" parameter in the plog_config table
		 * @see FileFinder::FileFinder
		 */
		function TemplateFinder( $templateFolder = null )
		{
			// if there is no template folder, use the default one
			if( $templateFolder == null )
				$templateFolder = TemplateSetStorage::getBaseTemplateFolder();
				
			$this->_templateFolder = $templateFolder;
			
			$this->FileFinder( $templateFolder );
		}
		
		/**
		 * reimplemented from FileFinder::findKeyForFile
		 *
		 * @param fileName
		 */
		function getKeyForFile( $fileName )
		{
			// regular expression that determines which files should be ignored. This should
			// be updated every time we add a new folder to the templates/ folder that shouldn't
			// be considered an additional template!!
			$ignoreRegexp = '/(unported|bin|LifeType|default|rss|wizard|summary|misc|admin|blog_[0-9]*|^\..+)$/i';
			
                // If it isn't a directory, it can't be a template
            if(!File::isDir($fileName)){
                return null;
            }
            
			// get base name from the current full path
			$fileKey = basename( $fileName );

			// see if the ignore expression matches
			if( preg_match( $ignoreRegexp, $fileKey, $matches ))
				$fileKey = null;
				
			return( $fileKey );
		}
		
		/**
		 * @see FileFinder::find()
		 * @return An array with the ids of the new template sets
		 */
		function find( $currentTemplates = null )
		{
			// if no parameter, then use the list of default global templates
			if( $currentTemplates == null ) {
				$currentTemplates = TemplateSets::getGlobalTemplates();
			}
			
			// call the parent method after the preparations
			parent::find( $currentTemplates );
			
			// and return any new templates
			return( $this->getNew());
		}
	}
?>
