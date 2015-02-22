<?php

	lt_include( PLOG_CLASS_PATH."class/view/blogview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/templatesets/templatesets.class.php" );
	
    /**
     * \ingroup View
     * @private
     *
     * Loads the given template. The only difference between this class and all
     * the other views is that this one receives as a parameter from the TemplateAction
     * class the name of the template to load, and then renders it.
     * The TemplateAction action takes care of rendering non-standard templates.
     */
    class TemplateView extends BlogView 
    {

    	var $_templateFile;

    	/**
         * Constructor.
         *
         * @param blogInfo The BlogInfo object carrying information about the blog we are
         * rendering the template for. It is required by the constructor of the class
         * BlogView.
         * @param templateFile The filename of the template to load.
         */
    	function TemplateView( $blogInfo, $templateFile, $data = Array())
        {
        	$this->BlogView( $blogInfo, $templateFile, SMARTY_VIEW_CACHE_CHECK, $data  );
    	}
    }
?>
