<?php

	lt_include( PLOG_CLASS_PATH."class/net/http/httpclient.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/article.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
    lt_include( PLOG_CLASS_PATH."class/net/requestgenerator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );

    define( "TRACKBACK_SUCCESS", 0 );
    define( "TRACKBACK_FAILED", 1 );
    define( "TRACKBACK_UNAVAILABLE", 2 );

    /**
     * Implementation of a very simple Trackback client. It is capable of autodiscovering
     * the trackback urls in a page, given the right url.
     * It also offers mechanisms to send trackback pings to pages and a function that will
     * send trackback pings to the list of urls specified in an array as a parameter.
	 *
	 * \ingroup DAO
     */
    class TrackbackClient
	{

    	/**
         * Constructor.
         */
    	function TrackbackClient()
        {

        }

    	/**
	     * Uses a very simple regexp to find all the trackback:ping attributes in a page, but
    	 * it works without having to use an xml parser which would complicate things a little...
	     *
    	 * @param page The html code which contains the RDF references embedded.
         * @param pageUrl Additionally, we can have the url of the page so that we can
         * make sure that we are fetching the right trackback url because we can then compare
         * the dc:identifier with the url. If they are the same, then we are getting the right
         * one. But if this is parameter is not provided, we will get the trackback url
         * without making sure it is the right one.
         * @return Returns an array of urls which are the trackbackurls that have been
         * found in the page, or an empty array if there was none found.
         */
         function getTrackbackLinks( $page, $pageUrl = "" ) {
            $regexp ="#<rdf:RDF.*?</rdf:RDF>#s";
    		preg_match_all( $regexp, $page, $out, PREG_SET_ORDER );
            $links = Array();
            foreach( $out as $result ) {
                    // we have to get now from within the rdf code the following identifiers:
                // dc:identifier, trackback:ping and dc:about

                // get the dc identifier
                if(preg_match( "/dc:identifier=\"([^\"]+)\"/", $result[0], $dc_ident))
                	$dcIdentifier = $dc_ident[1];

                // get the trackback:ping
                if(preg_match( "/trackback:ping=\"([^\"]+)\"/", $result[0], $tb_ping)) {
                	$tbPing = $tb_ping[1];

	                // if we provided the url of the page, we have a mechanism to make sure
	                // that we are fetching the right trackback ping
	                if( $dcIdentifier && $pageUrl ) {
                        $matched = 0;
                        
                        $len1 = strlen($pageUrl);
                        $len2 = strlen($dcIdentifier);
                        $length = ($len1 > $len2) ? $len2 : $len1;
                        if( strncasecmp($dcIdentifier,$pageUrl,$length) == 0)
                            $matched = 1;

                        $len1 = strlen(htmlentities($pageUrl));
                        $len2 = strlen($dcIdentifier);
                        $length = ($len1 > $len2) ? $len2 : $len1;
	                	if( strncasecmp($dcIdentifier,htmlentities($pageUrl),$length) == 0)
                            $matched = 1;

                        $len1 = strlen($pageUrl);
                        $len2 = strlen(urldecode($dcIdentifier));
                        $length = ($len1 > $len2) ? $len2 : $len1;
	                	if( strncasecmp(urldecode($dcIdentifier),$pageUrl,$length) == 0)
                            $matched = 1;

                        if($matched == 1){
	                		//print("identifer matches page!!");
	                		array_push( $links, $tbPing );
	                	}
	                }
	                else {
	                	array_push( $links, $tbPing );
	                }
				}
            }

            return $links;
    	}

	    /**
     	* Fetches a page from the given url.
	     *
	     * @param url The url with the page we would like to fetch.
	     * @return Returns a string with the contents of the page or an empty string if the page
	     * could not be fetched.
	     */
	    function fetchPage( $url )
	    {
    		$s = new HttpClient();
        	$result = $s->fetch( $url );

        	if( !$result ) {
                            if($s->timed_out)
                	//print("timed out!!!");
        		return "";

            }
        	else
        		return $s->results;
    	}

    	/**
     	 * Sends a trackback ping to the given url.
     	 *
     	 * @param trackbackUrl the url where to send the trackback ping.
	     * @param article The article object that is pinging the remote host.
     	 * @param blogName The name of the blog sending the trackback ping
     	 * @return Returns true if successful or false otherwise.
     	 */
    	function sendTrackback( $trackbackUrl, $article, $blogInfo )
    	{
    		$s = new HttpClient();
        	$t =& RequestGenerator::getRequestGenerator( $blogInfo );
        	$formvars["title"]     = $article->getTopic();
        	// according to MT's implementation, the excerpt should not be longer than 255 characters.
        	$formvars["excerpt"]   = substr( Textfilter::filterAllHTML( $article->getText() ), 0, 252 )."...";
        	$formvars["url"]       = $t->postPermalink( $article );
        	$formvars["blog_name"] = $blogInfo->getBlog();

        	// submit the form
        	if( !$s->submit( $trackbackUrl, $formvars ))
        		return false;

        	$matched = preg_match( "/.*<error>(.*)<\/error>.*/", $s->results, $res );

        	if( !$matched || $res[1] == 1 )
        		return false;
        	else
        		return true;
    	}

	    /**
    	 * Private function that builds an array with the results for the
     	 * sendTrackbacks function.
	     *
	     * @param url The original url where we tried to send a trackback ping.
	     * @param code Our own internal error code.
	     * @return Returns an array with two positions, the first one with the url
	     * and the second one with the error code.
	     */
	    function _buildResult( $url, $code )
    	{
    		$result = Array();
        	$result["url"] = $url;
        	$result["status"] = $code;

            return $result;
    	}


	    /**
    	 * Tries to autodiscover the trackback links in the pages given as parameters.
	     * The first parameter is an array of urls to blog permalinks which are the urls where
	     * we would like to try to send trackback pings. The trackback urls will be autodiscovered and
	     * then we will attempt to send a trackback link if available.
	     * The result of this function is another array of arrays, where for every position we will
	     * have another array with two positions: <b>"url"</b> will be the original url and <b>"status"</b> will be a value
	     * identifying what happened:<ul>
	     * <li>TRACKBACK_SUCCESS: Trackback was successfully sent</li>
         * <li>TRACKBACK_FAILED: There was some kind of problem sending the trackback</li>
	     * <li>TRACKBACK_UNAVAILABLE: the page did not specify any trackback link.</li>
	     * </ul>
	     *
	     * @param links An array with the urls where we would like to try and send trackback pings.
	     * @param article The Article object with the information we need to send the ping.
	     * @param blogName The name of the blog that is sending the information.
	     * @retun An array with the information explained above.
	     */
    	function sendTrackbacks( $links, $article, $blogName )
    	{

	    	$results = Array();
    		foreach( $links as $link )
        	{
        		// fetch the page
                //print("processing link ".$link."<br/>");
            	$page = $this->fetchPage( $link );
            	if( $page != "" ) {
            		// if the page could be fetched...
                	$tbLinks = $this->getTrackbackLinks( $page, $link );
                	if( empty( $tbLinks)) {
                		// there were no trackback links in the page
                		array_push( $results, $this->_buildResult( $link, TRACKBACK_UNAVAILABLE ));
                        //print("Error: trackback unavailable<br/>");
                	}
                	else {
                		foreach( $tbLinks as $tbLink ) {
                			// try and send a trackback
                            //print("sending to tblink: ".$tbLink."<br/>");
                    		$result = $this->sendTrackback( $tbLink, $article, $blogName );
                    		if( !$result ) {
                        		// if it didn't work, mark it
                            	array_push( $results, $this->_buildResult( $link, TRACKBACK_FAILED ));
                                    //print("Error: trackback failed<br/>");
                    		}
                        	else {
                        		// it actually worked, so we say so ;)
                        		array_push( $results, $this->_buildResult( $link, TRACKBACK_SUCCESS ));
                                    //print("It worked!<br/>");
                        	}
                    	}
                	}
            	}
            	else {
            		// page could not be fetched, so the trackback was not available
            		array_push( $results, $this->_buildResult( $link, TRACKBACK_UNAVAILABLE ));
                        //print("Error: page was empty");
            	}
        	}

        	return $results;
    	}

	    /**
    	 * Send trackbacks directly.
	     * The result of this function is another array of arrays, where for every position we will
	     * have another array with two positions: <b>"url"</b> will be the original url and <b>"status"</b> will be a value
	     * identifying what happened:<ul>
	     * <li>TRACKBACK_SUCCESS: Trackback was successfully sent</li>
         * <li>TRACKBACK_FAILED: There was some kind of problem sending the trackback</li>
	     * </ul>
	     *
	     * @param trackbacks An array with the urls where we would like to try and send trackback pings.
	     * @param article The Article object with the information we need to send the ping.
	     * @param blogName The name of the blog that is sending the information.
	     * @retun An array with the information explained above.
	     */
    	function sendDirectTrackbacks( $trackbacks, $article, $blogName )
    	{

	    	$results = Array();

	    	// to avoid annoying errors with the foreach() loop in case somebody's passing
	    	// something which is not an array
	    	if( !is_array( $trackbacks ))
	    	  $trackbacks = Array();

    		foreach( $trackbacks as $trackback )
        	{
       			// try and send a trackback
                //print("sending to tblink: ".$tbLink."<br/>");
           		$result = $this->sendTrackback( $trackback, $article, $blogName );
           		if( !$result ) {
               		// if it didn't work, mark it
                   	array_push( $results, $this->_buildResult( $trackback, TRACKBACK_FAILED ));
                    //print("Error: trackback failed<br/>");
           		}
               	else {
               		// it actually worked, so we say so ;)
               		array_push( $results, $this->_buildResult( $trackback, TRACKBACK_SUCCESS ));
                    //print("It worked!<br/>");
               	}
        	}

        	return $results;
    	}
    }
?>
