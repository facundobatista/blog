<?php

	lt_include( PLOG_CLASS_PATH."class/view/blogview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/templatesets/templatesetstorage.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/templateservice.class.php" );	
	
    /**
     * default profile used if none specified
     */
    define( 'RSS_VIEW_DEFAULT_PROFILE', 'rss20' );	

    /**
     * \ingroup View
     * @private
     *
     * Provides RSS output.
     *
     * It works like any other view that extends the BlogView object, except that instead of
     * using the templates available in
     */
    class RssView extends BlogView
    {
    
        var $_profile;

    	function RssView( $blogInfo, $profile, $data = Array())
        {
			$this->BlogView( $blogInfo, "", SMARTY_VIEW_CACHE_CHECK, $data );
		
			$ts = new TemplateSetStorage();
            if( !$ts->templateExists( $this->_profile, 'rss' )) {
                // if not, then we will use the default one
                $this->_profile = RSS_VIEW_DEFAULT_PROFILE;
            }
			
			// we need to overwrite the $this->_template object with the Template object of our choice...
            $this->_profile = $profile;
			$templateService = new TemplateService();
            $this->_template = $templateService->CachedTemplate( $this->_profile, 'rss', $this->_blogInfo );
			
			// set the correct content type
            $this->setContentType( 'text/xml' );		
        }
    }
?>