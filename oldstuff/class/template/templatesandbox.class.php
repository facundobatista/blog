<?php

    lt_include( PLOG_CLASS_PATH.'class/config/config.class.php' );
	lt_include( PLOG_CLASS_PATH.'class/data/validator/templatesetvalidator.class.php' );

    define( 'TEMPLATE_SANDBOX_ERROR_UNPACKING', -100 );
    define( 'TEMPLATE_SANDBOX_ERROR_FORBIDDEN_EXTENSIONS', -101 );
    define( 'TEMPLATE_SANDBOX_ERROR_CREATING_WORKING_FOLDER', -102 );

	/**
	 * \ingroup Template
	 *
     * This class checks that a file that is going to be added to a template, or
     * a packed file with a template set has the right contents.
     *
     * Everything happens sandboxed because it could be possible that the user is
     * trying to upload an executable file (.php, etc) as part of the template. Sandboxed
     * means that everything happens in a random temporary folder and that in case of
     * error, everything is removed from disk.
     *
     * @see TemplateSetValidator
     */
	class TemplateSandbox  
	{

    	function TemplateSandbox()
        {
        	
        }

        /**
         * Goes through all the files in the folder to see if any of the files
         * has any of the forbidden extensions.
         *
         * @param folder The folder where files are, it will be scanned
         * using a glob-like function
         * @return Returns true if all files are ok or false otherwise.
         */
        function checkForbiddenFiles( $folder )
        {
        	$config =& Config::getConfig();
            $forbiddenFilesStr = $config->getValue( 'upload_forbidden_files' );

            // return true if there's nothing to do
            if( empty($forbiddenFilesStr) )
            	return true;

            // otherwise, turn the thing into an array and go through all of them
			lt_include( PLOG_CLASS_PATH.'class/misc/glob.class.php' );			
            foreach( explode( " ", $forbiddenFilesStr ) as $file ) {
                $files = Glob::myGlob( $folder, $file."*" );
                if( count($files) > 0 )
                	return false;
            }

            return true;
        }

        /**
         * Makes sure that the file is a valid template set. The file can be packed
         * in any of the formats supported by the Unpacker class (.tar.gz, .tar.bz2
         * and .zip as of the time of writing these lines)
         * Returns true if the template is valid or a negative value carrying an
         * error code.
         *
         * @param file The file that contains the template set
         * @return Returns true (positive value) if template set is ok or a negative
         * value otherwise.
         */
        function checkTemplateSet( $file, $filePath )
        {
        	// get the temporary folder
        	$config =& Config::getConfig();
            $tmpFolder = $config->getValue( 'temp_folder' );
            if( $tmpFolder[strlen($tmpFolder)-1] != '/' )
            	$tmpFolder .= '/';

            // get the name of the file, which we will use in many places
            $fileNameParts = explode( '.', $file );
            $fileNameNoExt = $fileNameParts[0];

            // create our working folder
            $workFolder = $tmpFolder.File::getTempName().'/';
            if( !File::createDir( $workFolder, 0777 )) {
            	return TEMPLATE_SANDBOX_ERROR_CREATING_WORKING_FOLDER;
            }

            // now we can unpack the file to the temporary folder
            $unpacker = new Unpacker();
            if( !$unpacker->unpack( $filePath.$file, $workFolder )) {
                $this->cleanUp( $workFolder.$fileNameNoExt );
                if( File::exists( $workFolder)) File::delete( $workFolder );
            	return TEMPLATE_SANDBOX_ERROR_UNPACKING;
            }

            // if the file was correctly unpacked, now we will need the TemplateSetValidator
            // class to do some work for us
            $fileNameNoExt = $this->toTemplateSetName( $file );

            // we can use the checkTenmplateFolder which will do all the rest of
            // the work for us...
            $res = $this->checkTemplateFolder( $fileNameNoExt, $workFolder );
            if( $res < 0 ) {
            	//$this->cleanUp( $workFolder.$fileNameNoExt );
				$this->cleanUp( $workFolder );
				if( File::isReadable( $workFolder ) && File::isDir( $workFolder )) 
					File::delete( $workFolder );
            	return $res;
            }

            $this->cleanUp( $workFolder.$fileNameNoExt );
            File::delete( $workFolder );

            return true;
        }

        /**
         * Once we have a folder with some template files, we make sure that what's
         * inside is fine (no forbidden files, etc)
         *
         * @param templateName
         * @param templateFolder
         * @return 
         */
        function checkTemplateFolder( $templateName, $templateFolder )
        {
        	if( $templateFolder[strlen($templateFolder)-1] != '/')
            	$templateFolder .= '/';

            $tv = new TemplateSetValidator($templateName, $templateFolder );
            if( ($errorCode = $tv->validate()) < 0 ) {
            	return $errorCode;
            }

            // check if there isn't any file with a forbidden extension
            if( !$this->checkForbiddenFiles( $templateFolder.$templateName )) {
            	return TEMPLATE_SANDBOX_ERROR_FORBIDDEN_EXTENSIONS;
            }

            return true;
        }

        /**
         * Cleans all the temporary folders used by this class during
         * its execution.
         *
         * @param $folder The name of the temporary folder used
         * @return Returns true
         */
        function cleanUp( $folder )
        {
        	if( !File::isDir( $folder ))
            	return true;

			// recursively delete the folder
			File::deleteDir( $folder, true );

            return true;
        }

		/**
		 * Convert the upload file name to template set name
		 * 
		 * @param $uploadFile The name of the upload file
		 * @return Return the template set name
		 */
		function toTemplateSetName( $uploadFile )
		{
            $fileWithoutVersion = preg_replace( '/^[0-9.]*_/', '', $uploadFile );
            $fileParts = explode( ".", $fileWithoutVersion );
            $templateName = $fileParts[0];
            return $templateName;
		}
    }
?>