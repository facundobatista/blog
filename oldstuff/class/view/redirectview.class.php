<?php

	lt_include( PLOG_CLASS_PATH."class/view/view.class.php" );

    /**
     * This view returns no content, it just sends a
     * "Location:" header to the browser so that the browser itself takes care
     * of the redirecting.
     *
     * The redirection itself is performed in the View::render() method.
     */
    class RedirectView extends View
    {
        var $_destUrl;
    
        /**
         * Constructor.
         *
         * @param destUrl Optinally, it can accept the final destination if already known. If not, please
         * call setRedirectUrl before rendering the view
         */
        function RedirectView( $destUrl = "" )
        {
            $this->View();
            
            $this->_destUrl = $destUrl;
        }
        
        /**
         * Reimplemented from View::sendContentType so that no content type is sent
         * when performing redirections.
         *
         * @return Always true
         */
        function sendContentType()
        {
            return( true );
        }
        
        /**
         * Set the destination URL
         *
         * @param url The destination URL
         * @return nothing
         */
        function setRedirectUrl( $url )
        {
            $this->_destUrl = $url;
        }
        
        /**
         * returns the current value of the URL to which this view is redirecting
         *
         * @return a valid URL
         */
        function getRedirectUrl()
        {
            return( $this->_destUrl );
        }
        
        /**
         * Render the view. In our case it only sets up the redirect header and return no
         * content.
         *
         * @see View::render()
         */
        function render()
        {
            // add the header
            $this->addHeaderResponse( "Location: ".$this->getRedirectUrl());
            
            // and call the parent render method
            View::render();
        }
    }
?>