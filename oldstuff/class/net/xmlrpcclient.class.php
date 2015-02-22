<?php

	
    lt_include( PLOG_CLASS_PATH."class/net/xmlrpc/IXR_Library.lib.php" );

    /** 
     * \ingroup Net
     *
     * Implements a basic XMLRPC client, which offers methods such as
     * ping (implementing the weblogUpdates.ping methods)
     */
    class XmlRpcClient extends IXR_Client 
	{

    	var $_url;

    	/**
         * Creates an XMLRPC client
         *
         * @param url The path to the XMLRPC interface of the remote host
         */
    	function XmlRpcClient( $url )
        {
        	$this->IXR_Client( $url );

            $this->_url = $url;
        }

        /**
         * Returns the url of the client
         *
         * @return The client url
         */
        function getUrl()
        {
        	return $this->_url;
        }

        /**
         * Performs a weblogsUpdate.ping remote call to the host we've set in
         * the constructor.
         * Please check http://www.xmlrpc.com/discuss/msgReader$2014?mode=topic for reference
         * on the weblogsUpdate.ping call.
         *
         * @param weblogName The name of the weblog.
         * @param weblogUrl The url of the weblog.
         * @return Returns An array whose key is the url and the value is either "OK" if successful
         * or an error message as returned by the xmlrpc library otherwise.
         */
        function ping( $weblogName, $weblogUrl )
        {
        	$this->debug=false;
        	$result = $this->query( "weblogUpdates.ping", $weblogName, $weblogUrl );
            $response = $this->getResponse();

            $pingResult = Array();
            if( !$result || $this->isError() || $response["flerror"] == 1 ) {
            	//print("there was an error!");
                //print("message = ".$response["message"]);
                $pingResult[$this->_url] = $response["message"];
            }
            else {
            	//print("no error!");
                $pingResult[$this->_url] = "OK";
            }

            return $pingResult;
        }
    }
?>
