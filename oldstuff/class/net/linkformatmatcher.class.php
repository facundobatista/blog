<?php

    lt_include( PLOG_CLASS_PATH."class/net/linkparser.class.php" );
    
    
    /**
     * \ingroup Net
     *
     * Identifies the right kind of url, based on the format of our links. This class is only taken
     * into use when custom urls are enabled, and it basically loops through all provided regular expressions
     * and a given HTTP request to check which one of them is the matching one. The matching engine (converting
     * from *_link_format format to a valid regexp) is provided by the LinkParser class.
     *
     * Once ready, it will call the LinkFormatMatcher::getParameters() to retrieve the right parameters
     * for the request (the core does not understand this kind of requests, and this class acts as some sort
     * of a filter that parses an incoming nicer link, gets the information based on a regexp and puts it back to
     * the request in a more "normal" format)
     *
     * Users or plugin developers should never need to use this class directly. 
     *
     * @see LinkParser     
     */
    class LinkFormatMatcher 
    {
    
        var $_request;
        var $_formats;
        var $_params;
        
        /**
         * @param request
         * @param formats
         */
        function LinkFormatMatcher( $request, $formats )
        {
            $this->_request = $request;
            $this->_formats = $formats;
        }
        
        /**
         * @return 
         */
        function identify()
        {
            foreach( $this->_formats as $key => $format ) {
                $lp = new LinkParser( $format );
                $params = $lp->parseLink( $this->_request );
                if( $params !== false ) {
                    // return the key assigned to the format that matched
                    $this->_params = $params;
                    return $key;
                }
            }
        }
        
        function getParameters()
        {
            return $this->_params;
        }
    }
?>