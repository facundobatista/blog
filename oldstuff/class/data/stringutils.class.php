<?php

	

    /**
     * \ingroup Data
     *
     * Basic methods for formatting or doing nasty things to strings :-)
     */
    class StringUtils  
    {

        /** 
         * Alias for htmlspecialchars
         * @static
         */
        function htmlTranslate( $string )
        {
        	return htmlspecialchars( $string );
        }

        /**
         * cuts a string at a given character
         *
         * @param string
         * @param n
         * @return The reduced string
         * @static
         */
        function cutString( $string, $n )
        {
        	return substr( $string, 0, $n );
        }

        /**
         * Returns an array with all the links in a string.
         *
         * @param string The string
         * @return An array with the links in the string.
         * @static
         */
        function getLinks( $string )
        {
		$regexp = "|<a[^>]+href=[\"']{0,1}([^'\"]+?)[\"']{0,1}[^>]*>(.+)</a>|iU";
            $result = Array();

            if( preg_match_all( $regexp, $string, $out, PREG_PATTERN_ORDER )) {
            	foreach( $out[1] as $link ) {
             		array_push( $result, $link );
            	}
            }

            return $result;
        }
		
		/**
		 * Returns a size formatted and with its unit: "bytes", "KB", "MB" or "GB"
		 *
		 * @param size The amount
		 * @return A string with the formatted size.
		 * @static
		 */
		function formatSize( $size )
		{
			if ($size < pow(2,10)) return $size." bytes";
			if ($size >= pow(2,10) && $size < pow(2,20)) return round($size / pow(2,10), 0)." KB";
			if ($size >= pow(2,20) && $size < pow(2,30)) return round($size /pow(2,20), 1)." MB";
			if ($size > pow(2,30)) return round($size / pow(2,30), 2)." GB";
		}

        /**
         * Returns a string in a readable and url-compliant format.
         *
         * @param string The string
         * @return A string ready to use in urls.
         * @static
         * @see TextFilter::urlize()
         */
        function text2url( $string )
        {
		    // remove unnecessary spaces and make everything lower case
		    $string = preg_replace( "/ +/", " ", strtolower($string) );

            // special rule for dashes, I think it looks nicer :-p
            $string = str_replace(' - ', '-', $string);

            // removing a set of reserved characters (rfc2396: ; / ? : @ & = + $ ,)
            $string = str_replace(array(';','/','?',':','@','&','=','+','$',','), '', $string);

            // replace some characters to similar ones (more readable uris)
            $search  = array(' ', 'ä', 'ö', 'ü','ë','ï','é','è','à','ç',);
            $replace = array('_','ae','oe','ue','e','i','e','e','a','c');
            $string = str_replace($search, $replace, $string);

            // remove everything we didn't so far...
            $string = preg_replace("/[^a-z0-9_-]/", "", $string);
            
            // urlencode everything, in case we missed something ;-)
            return urlencode($string);
        }
		
		/**
		 * extremely lame function. 
		 *
		 * @param count how many times we'd like to repeat the character
		 * @param char The character (or string) we'd like to repeat
		 * @return The resulting string
		 * @static
		 */
 		function pad( $count, $char = " ")
		{
			$i=0;
			$result = "";
			while( $i < $count ) {
				$result .= $char;
				$i++;
			}
			
			return $result;
		}		
    }
?>
