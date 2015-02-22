<?php

	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	lt_include( PLOG_CLASS_PATH."class/net/url.class.php" );
	
	/**
	 * \ingroup Net_HTTP
	 *
	 * encapsulates most of the logic needed to extract info about
	 * the subdomain, given a subdomain url
	 */
	class Subdomains 
	{
		/**
		 * returns an array with two positions: $array["username"] and $array["blogname"] as
		 * extracted from the request. This method is static
		 *
		 * @static
		 * @return an associative array
		 */
		function getSubdomainInfoFromRequest()
		{
        	lt_include( PLOG_CLASS_PATH."class/net/linkparser.class.php" );			
			$config =& Config::getConfig();
			$url = new Url( $config->getValue( "subdomains_base_url"));
			$lp =  new LinkParser( $url->getHost());
			$server = HttpVars::getServer();
			$httpHost = $server["HTTP_HOST"];
			$result = $lp->parseLink( $httpHost );	
			return( $result );
		}
		
		/**
		 * returns true if a given url is using a subdomain or not. It works by comparing
		 * the url with "base_url" from the plog_config table. If they match, then the incoming
		 * url is *not* using subdomains. Otherwise, it will return true
		 *
		 * @param url If null, use $_SERVER["HTTP_HOST"]
		 * @return true if the given url is subdomained or not
		 * @static
		 */
		function isSubdomainUrl( $url = null )
		{
			// prepare the url
			if( $url == null ) {
				$server = HttpVars::getServer();
				$urlObject = new Url( "http://".$server["HTTP_HOST"] );
			}
			else
				$urlObject = new Url( $url );
			
			// and now get the base_url
			$config =& Config::getConfig();
			$baseUrlObject = new Url( $config->getValue( "base_url" ));

                // ignore "www." prefixes - that doesn't necessarily make it a subdomain
            $base = preg_replace("/^www./", "", $baseUrlObject->getHost());
            $current = preg_replace("/^www./", "", $urlObject->getHost());

			// and finally check if whether they match or not
			if($base == $current){
				$isSubdomain = false;
            }
            else{
				$isSubdomain = true;
            }
			
			// return it...
			return( $isSubdomain );
		}
		
		/**
		 * Returns true if subdomains are enabled in one way or another in the configuration
		 *
		 * @return True if enabled or false otherwise
	     * @static		
		 */
		function getSubdomainsEnabled()
		{
			lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );			
			$config =& Config::getConfig();
			
            $blog_domains_enabled =
                ($config->getValue("subdomains_enabled", 0) &&
                (strpos($config->getValue("subdomains_base_url", ""),
                        "{blogdomain}") !== FALSE));

			return( $blog_domains_enabled );
		}
		
		/**
		 * Returns an array with the list of available domains
		 *
		 * @return True if successful or false otherwise
		 */
		function getAvailableDomains()
		{
			lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
			$config =& Config::getConfig();
			
            $available_domains = $config->getValue("subdomains_available_domains", "");
            if($available_domains){
                $available_domains = explode(" ", $available_domains);
            }

			return( $available_domains );
		}
		
		/**
		 * Returns true if the given domain name is one of the available ones
		 * 
		 * @param domain The domain name we'd like to check
		 * @return True if successful or false otherwise
	     * @static
		 * @see Subdomains::getAvailableDomains
		 */		
		function isDomainAvailable( $domain )
		{
			$domains = Subdomains::getAvailableDomains();
			return( array_key_exists( $domain, array_flip( $domains )));
		}
		
		/**
		 * Returns true if the given subdomain name is valid (i.e. complies with the domain naming restrictions)
		 *
		 * @param domain The name we'd like to check
		 * @return True if successful or false otherwise
		 */
		function isValidDomainName( $domain )
		{
            // Use forbidden_usernames for domains as well, since they are related
            // in that we don't want people to register www.xyz or forums.xyz
            // through these subdomains either
			lt_include( PLOG_CLASS_PATH."class/data/validator/domainvalidator.class.php" );
			$val =  new DomainValidator();
			return( $val->validate( $domain ));
		}
		
		/**
		 * Returns true if the domain is unique or false otherwise
		 *
		 * @param domain
		 * @return true if successful and false otherwise
		 */
		function domainNameExists( $domain, $ignoreBlogId=0 )
		{
			lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
			$blogs = new Blogs();
            $blog = $blogs->getBlogInfoByDomain( $domain );
			$valid = is_object($blog);
			if($valid && $ignoreBlogId != 0){
                return ($blog->getId() != $ignoreBlogId);
            }
			return( $valid );
		}
	}
?>