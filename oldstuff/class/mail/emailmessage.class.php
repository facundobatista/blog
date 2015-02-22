<?php

	

    define( "MAX_LINE_LENGTH", 998 );

	/**
	 * \ingroup Mail
	 * 
     * Represents an email message and has basic setter and getter methods for all the most
     * basic attributes of an email message (To:, From:, Bcc:, etc)
     */
    class EmailMessage  
    {

    	var $_toAddrs;
        var $_ccAddrs;
        var $_bccAddrs;
        var $_subject;
        var $_body;
        var $_mimeType;
        var $_from;
        var $_fromName;
        var $_charset;
        

    	/**
         * Constructor
         */
    	function EmailMessage()
        {
        	

            $this->_toAddrs = Array();
            $this->_ccAddrs = Array();
            $this->_bccAddrs = Array();
            // use iso-8859-1 as the default character set
            $this->_charset = "iso-8859-1";

            $this->_mimeType = "text/plain";
        }

        /**
         * Adds a destination
         *
         * @param to Destination address.
         */
        function addTo( $to )
        {
        	array_push( $this->_toAddrs, rtrim($to) );
        }

		/**
		 * Resets the current list of destinations and adds this new one as the only one. 
		 *
         * @param to Destination address.
		 */
		function setTo( $to )
		{
			$this->_toAddrs = Array();
			$this->addTo( $to );
		}

        /**
         * Adds a Cc:
         *
         * @param cc The address where we want to Cc this message
         */
        function addCc( $cc )
        {
        	array_push( $this->_ccAddrs, rtrim($cc) );
        }

		/**
		 * Resets the current list of Cc destinations and adds this new one as the only one. 
		 *
         * @param to Destination address.
		 */
		function setCc( $to )
		{
			$this->_ccAddrs = Array();
			$this->addCc( $to );
		}

        /**
         * Adds a Bcc address
         *
         * @param bcc The adddress where we want to Bcc
         */
        function addBcc( $bcc )
        {
        	array_push( $this->_bccAddrs, rtrim($bcc) );
        }

		/**
		 * Resets the current list of Bcc destinations and adds this new one as the only one. 
		 *
         * @param to Destination address.
		 */
		function setBcc( $to )
		{
			$this->_bccAddrs = Array();
			$this->addBcc( $to );
		}

        /**
         * Sets the from address
         *
         * @param from The originatory address
         */
        function setFrom( $from )
        {
        	$this->_from = $from;
        }

        /**
         * Sets the from name
         *
         * @param fromname The originatory name
         */
        function setFromName( $fromname )
        {
        	$this->_fromName = $fromname;
        }        

        /**
         * Sets the subject of the message
         *
         * @param subject Subject of the message
         */
        function setSubject( $subject )
        {
        	$this->_subject = $subject;
        }

        /**
         * Sets the body of the message
         *
         * @param body The text for the body of the message
         */
        function setBody( $body )
        {
        	$this->_body = $body;
        }

        /**
         * Sets the MIME type. The default is 'text/plain'
         *
         * @param type The MIME type
         */
        function setMimeType( $type )
        {
        	$this->_mimeType = $type;
        }

        /**
         * Returns the "To:" list, properly arranged
         *
         * @return An string with the 'to:' field
         */
        function getTo()
        {
        	return $this->_toAddrs;
        }

        /**
         * Returns the "Cc:" list, properly arranged
         *
         * @return An string with the 'Cc:' field
         */
        function getCc()
        {
        	return $this->_ccAddrs;
        }

        /**
         * Returns the "Bcc:" list, properly arranged
         *
         * @return An string with the 'Bcc:' field
         */
        function getBcc()
        {
        	return $this->_bccAddrs;
        }

        /**
         * Returns the From address.
         *
         * @return The from address.
         */
        function getFrom()
        {
        	return $this->_from;
        }

        /**
         * Returns the body.
         *
         * @return The body.
         */
        function getBody()
        {
           return $this->_body;
        }

        /**
         * Returns the subject
         *
         * @return The subject.
         */
        function getSubject()
        {
        	return $this->_subject;
        }

        /**
         * Gets the MIME content type of the message
         *
         * @return The MIME type
         */
        function getMimeType()
        {
        	return $this->_mimeType;
        }
        
       /**
         * Returns the From name.
         *
         * @return The from name.
         */
        function getFromName()
        {
        	return $this->_fromName;
        }
        
        /**
         * Sets the character set of the message
         *
         * @param charset The new character set
         */
        function setCharset( $charset )
        {
            $this->_charset = $charset;
        }
        
        /**
         * Retrieves the character set that was set for this message. Returns
         * by default 'iso-8859-1' if no other has been set
         *
         *Ê@return the character set
         */
        function getCharset()
        {
            return( $this->_charset );
        }
    }
?>
