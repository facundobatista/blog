<?php

	lt_include( PLOG_CLASS_PATH.'class/data/validator/validator.class.php' );
    lt_include( PLOG_CLASS_PATH.'class/file/file.class.php' );

    define( 'ERROR_TEMPLATE_NOT_INSIDE_FOLDER', -1 );
    define( 'ERROR_MISSING_BASE_FILES', -2 );

    /**
     * \ingroup Validator
     *
     * This is a more complex Validator (and a very specific one) that will validate whether
     * the contents of a given folder are the same of a valid template set. In order to be valid
     * a template folder must contain the following template files:
     *
     * - main.template
     * - postandcomments.template
     * - commentarticle.template
     * - posttrackbacks.template
     * - error.template
     * - album.template
     * - albums.template
     * - resource.template
     *
     * The template files should be inside a folder with the same name as template set.
     *
     * If the template set is not valid, this class will set one of the following errors:
     * 
     * - ERROR_TEMPLATE_NOT_INSIDE_FOLDER
     * - ERROR_MISSING_BASE_FILES
     *
     * This Validator class is also a good example of the more complex things that can be achieved
     * with Validator classes, not just simple data validation.
     */
    class TemplateSetValidator extends Validator 
	{

    	var $_templateName;
        var $_folder;
        var $_fullName;

        // these are the basic files that should be present in every
        // template set. This array is now empty since Lifetype 1.2 supports "partial" templates
		// that only provide the needed files while everything else loaded from templates/default/
        var $_basicFiles = Array();


    	function TemplateSetValidator( $templateName, $folder )
        {
        	$this->Validator();

            $this->_templateName = $templateName;
            $this->_folder    = $folder;

            $this->_fullName = $this->_folder;
            if( $this->_fullName[strlen($this->_fullName)-1] != '/' )
            	$this->_fullName[strlen($this->_fullName)] = '/';
            $this->_fullName .= $templateName.'/';
        }

        /**
         * Returns true if the template is a valid template set
         */
        function validate()
        {
        	// first of all, check that the folder exists
            if( !File::isDir( $this->_fullName ))
            	return ERROR_TEMPLATE_NOT_INSIDE_FOLDER;

            // now check that all the basic files are available
            foreach( $this->_basicFiles as $basicFile ) {
            	if( !File::isReadable( $this->_fullName.$basicFile )) {
                	return ERROR_MISSING_BASE_FILES;
				}
            }

            return true;
        }
    }
?>