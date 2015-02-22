<?php

	
	
	/**
	 * \defgroup Net_HTTP
	 *
	 * Package that contains several classes and code related to HTTP, such as code for making
	 * HTTP requests or code for dealing with certain HTTP headers
	 */

	/**
	 * \ingroup Net_HTTP
	 * 
     * HttpVars compatibility package, which allows to fetch some of php's basic
     * global variables without having to worry about which version of php we're using.
     * The problem here is that since PHP 4.1.0 things like $_REQUEST, $_POST, $_GET, etc
     * are available, and before that their equivalents were $HTTP_GET_VARS,
     * $HTTP_POST_VARS and so on. By using this package and calling the functions
     * getPostVars, getGetVars, getSessionVars/setSessionVars we will get rid of any
     * incompatibility with the version of php we are running while having access to the
     * variables we most need.
     */
    class HttpVars  
    {

    	/**
         * Returns an array with all the variables in the GET header, fetching them
         * either from $_GET (PHP >= 4.1.0) or $HTTP_GET_VARS (PHP < 4.1.0)
         *
         * @return An associative array with the values of the GET header.
         * @static
         */
        function getGet()
        {
        	if( phpversion() >= "4.1.0" )
            	$getVars = $_GET;
            else {
            	global $HTTP_GET_VARS;
                $getVars = $HTTP_GET_VARS;
            }

            return $getVars;
        }

    	/**
         * Returns an array with all the variables in the GET header, fetching them
         * either from $_POST (PHP >= 4.1.0) or $HTTP_POST_VARS (PHP < 4.1.0)
         *
         * @return An associative array with the values of the POST header.
         * @static
         */
        function getPost()
        {
        	if( phpversion() >= "4.1.0" )
            	$postVars = $_POST;
            else {
            	global $HTTP_POST_VARS;
                $postVars = $HTTP_POST_VARS;
            }

            return $postVars;
        }

    	/**
         * Returns an array with all the variables in the session, fetching them
         * either from $_SESSION (PHP >= 4.1.0) or $HTTP_SESSION_VARS (PHP < 4.1.0)
         *
         * @return An associative array with the values of the session.
         * @static
         */
        function getSession()
        {
        	if( phpversion() >= "4.1.0" )
            	$sessionVars = $_SESSION;
            else {
            	global $HTTP_SESSION_VARS;
                $sessionVars = $HTTP_SESSION_VARS;
            }

            return $sessionVars;
        }

        /**
         * Saves the array in the session.
         *
         * @param sessionVars An array that will be used as the values for the http session.
         * @return Always returns true.
         * @static
         */
        function setSession( $sessionVars )
        {
        	//print("saving: ");print_r($sessionVars);

        	if( phpversion() >= "4.1.0" ) {
                foreach( $sessionVars as $key => $value ) {
                	$_SESSION["$key"] = $value;
                }
            }
            else {
            	global $HTTP_SESSION_VARS;
                foreach( $sessionVars as $key => $value ) {
                	$HTTP_SESSION_VARS["$key"] = $value;
                }
            }

            return true;
        }

        /**
         * Returns an array with the contents of the $_COOKIE global variable, if PHP version >= 4.1.0
         * or the values of the array HTTP_COOKIE_VARS if we're using a lower version.
         *
         * @return An associative array with all the cookies created by our application.
         * @static
         */
        function getCookie()
        {
        	if( phpversion() >= "4.1.0" )
            	$cookieVars = $_COOKIE;
            else {
            	global $HTTP_COOKIE_VARS;
            	$cookieVars = $HTTP_COOKIE_VARS;
            }

            return $cookieVars;
        }

        /**
         * Returns the value of the $_REQUEST array. In PHP >= 4.1.0 it is defined as a mix
         * of the $_POST, $_GET and $_COOKIE arrays, but it didn't exist in earlier versions.
         * If we are running PHP < 4.1.0, then we will manually create it by merging the needed
         * arrays.
         *
         * @return An associative array containing the variables in the GET, POST and COOKIES header.
         * @static
         */
        function getRequest()
        {
        	if( phpversion() >= "4.1.0" )
            	$requestVars = $_REQUEST;
            else {
            	$postVars = HttpVars::getPost();
                $getVars  = HttpVars::getGet();
                $cookieVars = HttpVars::getCookie();

                $requestVars = array_merge( $getVars, $postVars, $cookieVars );
            }

            return $requestVars;
        }
        
        /**
         * returns the value of a certain key from the request
         *
         * @param key
         * @return The value, or empty if not found
         */
        function getRequestValue( $key )
    	{
	    	$request = HttpVars::getRequest();
            if ( isset($request[$key]) )
	    	    return( $request[$key] );   
            return '';
        }

        /**
         * Sets the value of the $_REQUEST array in PHP 4.1.0 or higher. If using a lower version,
         * then the content of this array will be copied into $HTTP_GET_VARS
         *
         * @param requestArray An associative array with the contents of our future $_REQUEST
         * array
         * @return Returns always true.
         */
        function setRequest( $requestArray )
        {
        	if( phpversion() >= "4.1.0" ) {
            	foreach( $requestArray as $key => $value ) {
                	$_REQUEST["$key"] = $value;
                }
            }
            else {
            	HttpVars::setGet( $requestArray );
            }

            return true;
        }
        
        /**
         * sets a value in the request
         *
         * @param key
         * @param value
         * @return true
         */
        function setRequestValue( $key, $value )
        {
            $request = HttpVars::getRequest();
            $request["$key"] = $value;
            HttpVars::setRequest( $request );
            
            return true;
        }

        /**
         * Sets the value of the $_GET array in PHP 4.1.0 or higher and of the
         * $HTTP_GET_VARS if lower.
         *
         * @param getArray An associative array with the contents of our future $_GET
         * array
         * @return Returns always true.
         */
        function setGet( $getArray )
        {
        	if( phpversion() >= "4.1.0" ) {
            	foreach( $getArray as $key => $value ) {
                	$_GET["$key"] = $value;
                }
            }
            else {
            	global $HTTP_GET_VARS;
                foreach( $getArray as $key => $value ) {
                	$HTTP_GET_VARS["$key"] = $value;
                }
            }

            return true;
        }

        /**
         * Returns the $_SERVER array, otherwise known as $HTTP_SERVER_VARS in versions older
         * than PHP 4.1.0
         *
         * @return An associative array with the contents of the $_SERVER array, or equivalent.
         * @static
         */
        function getServer()
        {
            if( phpversion() >= "4.1.0" )
            	$serverVars = $_SERVER;
            else {
            	global $HTTP_SERVER_VARS;
                $serverVars = $HTTP_SERVER_VARS;
            }

            return $serverVars;
        }

        function getFiles()
        {
            if( phpversion() >= "4.1.0" )
            	$files = $_FILES;
            else {
            	global $HTTP_POST_FILES;
                $files = $HTTP_POST_FILES;
            }

            return $files;
        }

        /**
         * Returns the base URL of the script
         *
         * @return A string containing the base URL of the script
         * @static
         */
        function getBaseUrl()
        {
            $serverVars = HttpVars::getServer();
            if ( !isset($serverVars['HTTPS']) || strtolower($serverVars['HTTPS']) != 'on' ) {
                $protocol = 'http://';
            } else {
                $protocol = 'https://';
            }
            $host      = $serverVars["HTTP_HOST"];
            $scriptUrl = $serverVars["PHP_SELF"];

            return $protocol . $host . $scriptUrl;
        }
    }
?>
