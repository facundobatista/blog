<?php

    /**
     * \defgroup View
     *
     * A view is the class in pLog that takes care of rendering the content and sending to the user. In pLog
     * each Action class must generate a view that can be sent to the client, which is obtained via the method
     * Action::getView()
     *
     * @see View
     * @see BlogView
     * @see SmartyView
     * @see AdminView
     * @see PluginTemplatedView
     * @see AdminPluginTemplatedView
     */

	
    lt_include( PLOG_CLASS_PATH."class/config/properties.class.php" );
	
	/**
	 * constants that can be used for content types
	 */
	define( "TEXT_HTML_CONTENT_TYPE", "text/html" );
	define( "TEXT_XML_CONTENT_TYPE", "text/xml" );
	
	/**
	 * default content-type that is going to be sent in HTTP headers
	 */
	define( "DEFAULT_VIEW_CHARSET", "iso-8859-1" );
	
	/**
	 * default page where we should start if the "page" parameter is not
	 * available
	 */
	define( "VIEW_DEFAULT_START_PAGE", 1 );
	
	/**
	 * name of the request parameter that contains the page number
	 */
	define( "VIEW_DEFAULT_PAGE_PARAMETER", "page" );

	/**
	 * \ingroup View
	 *
	 * Base class with basic methods related to views. This class should not be used directly since it does
	 * not know anything about blogs, plugins or templates. New blog classes should either extend 
	 * BlogView or PluginTemplatedView, while new admin view classes should extend AdminView or 
	 * AdminPluginTemplatedView.
	 *
	 * This base View class does not know anything about cached views either.
	 *
	 * In case it is necessary to create a class extending directly from View, please implement your own rendering
	 * logic in the View::render() method but do not forget to call it at some point in your render() method.
     */
	class View  
	{

		var $_params;
		var $_contentType;
		var $_headers;
		var $_charset;
		var $_request;

        /**
         * Constructor. Initializes the view with a default content type, character set, etc.
         */
		function View()
        {
			

            $this->_params = new Properties();
			
			// set a default content type and character set for responses
			$this->_contentType = TEXT_HTML_CONTENT_TYPE;
			$this->_charset = DEFAULT_VIEW_CHARSET;
			$this->_headers = Array();
			
			// no form has caused any error when we initialize the view!
			$this->setValue( "formIsError", false );
			// and no form has caused any success yet either!
			$this->setValue( "formIsSuccess", false );
			
			// let's send an HTTP 200 header response... If somebody wants to overwrite it later
			// on, php should keep in mind that the valid one will be the last one so it is
			// fine to do this more than once and twice
			$this->addHeaderResponse( "HTTP/1.0 200 OK" );
			
			// initialize a request object, in case it is needed somewhere
			$this->_request = new Request( HttpVars::getRequest());
        }

        /**
         * Sets a single parameter for the view. These parameters will be passed to the template layer
         * once it gets processed.
         *
         * @param name Name of the parameter
         * @param value Value of the parameter
         */
         function setValue( $name, $value )
         {
            //$this->_params[$name] = $value;
             $this->_params->setValue( $name, $value );
         }

         /**
          * Returns the value identified by the key $key
          *
          * @param name The key
          * @return The value associated to that key
          */
         function getValue( $name )
         {
         	return $this->_params->getValue( $name );
         }
		 
		 /**
		  * sets the default content-type of the view. The default content type, if none specified, is
		  * text/html
		  *
		  * @param contentType The new content type
		  */
		function setContentType( $contentType )
		{
			$this->_contentType = $contentType;
		}
		
		/**
		 * sets the character set. If none is specified, the default content type is ISO-8859-1
		 *
		 * @param charset the character set
		 */
		function setCharset( $charset )
		{
			$this->_charset = $charset;
		}
		
		/**
		 * Adds a new header string to the current list of headers
		 *
		 * @param headerString the new header string
		 * @return Always true
		 */
		function addHeaderResponse( $headerString )
		{
			array_push( $this->_headers, $headerString );
			
			return true;
		}
		
		/**
		 * sets the headers that are going to be sent to the client
		 * from the values in the array. This will remove ALL the headers
		 * that have been set so far!
		 *
		 * @param headers An array of strings
		 * @return always true
		 * @see addHeaderResponse
		 */
		function setHeaders( $headers = Array())
		{
			$this->_headers = $headers;
		}
		
		/**
		 * prints out the content type and character set to the client, by setting the 
		 * Content-Type HTTP response header
		 *
		 * @return always true.
		 */
		function sendContentType()
		{
			// build up the header and send it
			$header = "Content-Type: ".$this->_contentType.";charset=".$this->_charset;
			header( $header );
				
			return true;
		}
		
		/**
		 * sets an error message for the whole form, should that be needed
		 *
		 * @param message
		 * @return Always true
		 */
		function setErrorMessage( $message )
		{
			$this->setValue( "viewErrorMessage", $message );
			$this->setError( true );
			
			return true;
		}
		
		/**
		 * Whether the view has to show some error message or not. Views can
		 * show success messages as well as error messages at the same time.
		 *
		 * @param error
		 * @return Always true 
		 */
		function setError( $error = true )
		{
			$this->setValue( "viewIsError", $error );
			
			return true;
		}
		
		/**
		 * Whether the view has to show some success message or not. Views can show
		 * sucess messages as well as error messages as the same time!
		 * 
		 * @param success
		 * @return Always true
		 */
		function setSuccess( $success = true )
		{
			$this->setValue( "viewIsSuccess", $success );
			
			return true;
		}
		
		/**
		 * sets an success message for the whole form, should that be needed
		 *
		 * @param message
		 * @param formName not used nor required (yet!)
		 * @return Always true
		 */
		function setSuccessMessage( $message )
		{
			$this->setValue( "viewSuccessMessage", $message );
			$this->setSuccess( true );			
			
			return true;
		}
		
		/**
		 * stores a value in the session, associated to one key, in case
		 * the view wants to keep some value for later use such as filter settings
		 * for persisten listings, etc.
		 *
		 * @param param
		 * @param value
		 * @return Always true
		 */
		function setSessionValue( $param, $value )
		{
			$session = HttpVars::getSession();

            // if there is no session data, there's nothing for us to set
            if( !is_array( $session )) 
                return false;		
		
			$viewName = get_class( $this );
			$keyName = "{$viewName}_{$param}";
			$session["$keyName"] = $value;
			HttpVars::setSession( $session );
			
			return true;
		}
		
		/** 
		 * retrieves a parameter from the session
		 *
		 * @param param
		 * @param defaultValue
		 * @return The value associated to the parameter or empty if not
		 * found
		 */
        function getSessionValue( $param, $defaultValue = "" )
        {
            $session = HttpVars::getSession();

            // if there is no session data, there's nothing for us to look for
            if( !is_array( $session )) 
                return false;
            
			$viewName = get_class( $this );
            $keyName = "{$viewName}_{$param}";

            if(isset($session[$keyName]) && !empty($session[$keyName]) ){
                return $session[$keyName];
            } else{
                return $defaultValue;
            }
        }
        

       /**
		 * gets the current page from the HTTP request
		 *
		 * @return the page number from the request
		 * @static
		 */
		function getCurrentPageFromRequest()
		{
            static $page;
            if($page)
                return $page;
            
            $request = new Request( HttpVars::getRequest() );

			// get the page value from the request
			$page = $request->getValue( VIEW_DEFAULT_PAGE_PARAMETER );

			// but first of all, validate it
            lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php");
			$val = new IntegerValidator();
			if( !$val->validate( $page ))
				$page = VIEW_DEFAULT_START_PAGE;
							
			return $page;
		}

        /**
         * Renders the view. Here we would ideally call a template engine, using the
         * values in $this->_params to fill the template 'context' and then display
         * everything. All classes extending the main View class (or any of its child classes
         * such as BlogView) are advised to call parent::render() as the first thing in their
         * own render() method.
         *
         * By default does nothing and it has no parameters
         */
        function render()
        {
            // send the headers we've been assigned if any, alongside the content-type header
            foreach( $this->_headers as $header )
                header( $header );

            $this->sendContentType();            
        }
    }
?>
