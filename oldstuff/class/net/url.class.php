<?php

	

	/**
	 * \ingroup Net
	 *
	 * Encapsulates a definition of an object representing a URL
	 *
	 * Provides getters and setters for all the parts of the url:
     * <ul>
	 * <li>url (the complete url)</li>
	 * <li>scheme (http, file, ftp)</li>
	 * <li>host</li>
	 * <li>user</li>
	 * <li>password</li>
	 * <li>path</li>
	 * <li>query (anything after the question mark "?")</li>
	 * <li>fragment (anything after the hash mark "#")</li>
     * </ul>
	 * Every time a change is made in one of the fields the
	 * url string is recalculated so that any call to getUrl
	 * will return the right one.
	 */
	class Url  
	{

		var $_url;
		var $_scheme;
		var $_host;
		var $_port;
		var $_user;
		var $_pass;
		var $_path;
		var $_query;
		var $_fragment;

		/**
		 * given a string representing a valid URL, build the object. If the string is not a valid
		 * URL, the constructor will not generate an error but the results of calling any of the getter
		 * methods are undefined
		 *
		 * @param url A string with a valid URL
		 */
		function Url( $url )
		{
			$this->setUrl(trim($url));
		}

        function isValid(){
            lt_include(PLOG_CLASS_PATH."class/data/validator/httpurlvalidator.class.php");
            $validator = new HttpUrlValidator();
            return $validator->validate($this->_url);
        }

        
		/**
		 * @private
		 */
		function _calculateFields()
		{
			$parts = @parse_url( $this->_url );

			$keys = Array( "scheme", "host", "port", "user", "pass",
			              "path", "query", "fragment" );

			// this saves us time ;)
			foreach( $keys as $key ) {
                if (isset($parts[$key])) {
					$var = "_{$key}";
					$this->$var = $parts["$key"];
                }
			}
		}

		/**
		 * @return returns the URL as it was given in the constructor
		 */
		function getUrl()
		{
			return $this->_url;
		}

		/**
		 * sets a new URL string, which will overwrite the previous one.
		 *
		 * @param the new URL string
		 */
		function setUrl( $url )
		{
			$this->_url = $url;

			$this->_calculateFields();
		}

		/**
		 * @return returns the scheme of the given url (http, file, ftp, ...)
		 */
		function getScheme()
		{
			return $this->_scheme;
		}

		/**
		 * sets a new scheme
		 *
		 * @param Scheme The new scheme (http, file, ftp, ...)
		 */
		function setScheme( $scheme )
		{
			$this->_scheme = $scheme;

			$this->glueUrl();
		}

		/**
		 * @return Returns the host specified in this URL
		 */
		function getHost()
		{
			return $this->_host;
		}

		/**
		 * sets a new host
		 *
		 * @param Host the new host
		 */
		function setHost( $host )
		{
			$this->_host = $host;

			$this->glueUrl();
		}

		/**
		 * @return Returns the port that was specified in the original URL, or 80 if there was nothing
		 * specified
		 */
		function getPort()
		{
			return $this->_port;
		}

		/**
		 * sets a new port
		 *
		 * @param port the new port
		 */
		function setPort( $port )
		{
			$this->_port = $port;

			$this->glueUrl();
		}

		/**
		 * @return Returns the user that was specified in the URL, if any.
		 */
		function getUser()
		{
			return $this->_user;
		}

		/** 
		 * sets a new user in the URL
		 *
		 * @param user The new username
		 */
		function setUser( $user )
		{
			$this->_user = $user;

			$this->glueUrl();
		}

		/**
		 * @return Returns the password that was set in the URL
		 */
		function getPass()
		{
			return $this->_pass;
		}

		/**
		 * sets a new password in the URL
		 *
		 * @param pass the new password
		 */
		function setPass( $pass )
		{
			$this->_pass = $pass;

			$this->glueUrl();
		}

		/**
		 * @return Returns the path
		 */
		function getPath()
		{
			return $this->_path;
		}

		/**
		 * sets the new path
		 *
		 * @param path The new path
		 */
		function setPath( $path )
		{
			$this->_path = $path;

			$this->glueUrl();
		}

		/**
		 * @return Returns the query
		 */
		function getQuery()
		{
			return $this->_query;
		}

        /**
         * Returns the query as an array of items
         *
         * @return An associative array where the keys are the name
         * of the parameters and the value is the value assigned to
         * the parameter.
         */
        function getQueryArray()
        {
        	// first, separate all the different parameters
        	$reqParams = explode( "&", $this->_query );

            $results = Array();
            foreach( $reqParams as $param ) {
            	// now, for every parameter, get rid of the '='
                $parts = explode( "=", $param );
                $var = $parts[0];
                $value = urldecode($parts[1]);

                $results[$var] = $value;
            }

            return $results;
        }

        /** 
         * sets a new query
         *
         * @param query The new query
         */
		function setQuery( $query )
		{
			$this->_query = $query;

			$this->glueUrl();
		}

		/**
		 * @return Returns the fragment
		 */
		function getFragment()
		{
			return $this->_fragment;
		}

		/**
		 * Sets a new fragment
		 *
		 * @param fragment The new fragment
		 */
		function setFragment( $fragment )
		{
			$this->_fragment = $fragment;

			$this->glueUrl();
		}

		/**
		 * Puts all the pieces back in place, and returns the resulting
		 * url. It is usually not necessary to call this method to obtain the new URL once we've called
		 * any of the setter methods of this class, since it is done automatically. Doing
		 *
		 * <pre>
		 *  $url->setScheme( "ftp" );
		 *  print("new url = ".$url->getUrl());
		 * </pre>
		 *
		 * is enough to obtain the updated URL string.
		 *
		 * Extracted from http://www.php.net/manual/en/function.parse-url.php
		 *
		 * @return a valid URL generated from the different parts of the object
		 */
		function glueUrl()
		{
 			$uri = $this->_scheme ? $this->_scheme.':'.((strtolower($this->_scheme) == 'mailto') ? '':'//'): '';
 			$uri .= $this->_user ? $this->_user.($this->_pass? ':'.$this->_pass:'').'@':'';
 			$uri .= $this->_host ? $this->_host : '';
 			$uri .= $this->_port ? ':'.$this->_port : '';
 			$uri .= $this->_path ? $this->_path : '';
 			$uri .= $this->_query ? '?'.$this->_query : '';
 			$uri .= $this->_fragment ? '#'.$this->_fragment : '';

			$this->_url = $uri;

			return $uri;
		}
	}
?>
