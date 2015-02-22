<?php

	lt_include( PLOG_CLASS_PATH."class/view/blogview.class.php" );

    /**
     * \ingroup View
     * @private
     *
     * Generic view for those actions that do not need special customizations
     * in the view (like most of them :P)
     */
    class BlogTemplatedView extends BlogView 
	{

   		/**
         * Initializes the parent class
         */
    	function BlogTemplatedView( $blogInfo, $template, $data = Array())
        {
        	$this->BlogView( $blogInfo, $template, SMARTY_VIEW_CACHE_CHECK, $data );
    	}
    }
?>