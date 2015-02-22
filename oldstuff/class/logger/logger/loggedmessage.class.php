<?php

    /**
     * LoggedMessage contains information about a log message such as priority, text
	 * etc...
     *
	 * \ingroup logger
     */
    class LoggedMessage
    {
        /**
         * An associative array of message parameters.
         */
        var $params;
		
		/**
		 * public attributes that hold useful information
		 */
		var $class;
		var $line;
		var $fullFileName;
		var $file;
		var $args;
		var $function;

        /**
         * Create a new qMessage instance.
         *
         * @param params An associative array of parameters.
         */
        function LoggedMessage($message = "", $params = NULL)
        {
			$this->_message = $message;
			
			$this->getLocationInfo();

            $this->params = ($params == NULL) ? array() : $params;
        }

		/**
		 * returns the string message that is going to be logged
		 *
		 * @return a string
		 */
		function getMessage()
		{
			$message = "";
		
			if( is_array( $this->_message )) {
				// if we're logging an array
				$message = "Arrary [ ";
				foreach( $this->_message as $key => $value ) {
					$message .= "$key => $value, ";
				}
				$message .= " ]";
			}
			elseif( is_object( $this->_message )) {
				// or if we're logging an object
				$message = "Object: ".get_class( $this->_message )." [ ";
				foreach( get_object_vars( $this->_message ) as $key => $value ) {
					$message .= "$key => $value, ";
				}
				$message .= " ]";
			}
			else {
				// or whatever we're logging
				$message = $this->_message;
			}
			
			return( $message );
		}

        /**
         * Retrieve a parameter.
         *
         * @param name A parameter name.
         *
         * @return string A parameter value, if a parameter with the given name
         *                exists, otherwise <b>NULL</b>.
         */
        function &getParameter ($name)
        {
            if (isset($this->params[$name]))
            {
                return $this->params[$name];
            }

            return NULL;
        }

        /**
         * Set a parameter.
         *
         * @param string A parameter name.
         * @param string A parameter value.
         */
        function setParameter ($name, $value)
        {
            $this->params[$name] = $value;
        }
		
		/**
		 * @private
		 * Returns a string representation of an array
		 */
		function _getParametersString( $paramArray )
		{
			$result = "";
			
			if( is_array( $paramArray )) {
				foreach( $paramArray as $key => $value ) {
					$result .= "$key => $value, ";
				}
			}
			
			return( $result );
		}
		
		/**
		 * get some nice information about location, file where the event was generated
		 * etc. This information will only be available if the function debug_backtrace()
		 * is available if not, no location information will be provided
		 *
		 * @return Returns always true. 
		 */
		function getLocationInfo()
		{
			if( function_exists( "debug_backtrace" )) {
				// get the backtrace
				$trace = debug_backtrace();
				
				// at the very bottom, we have the caller
				//$callerInfo = array_pop( $trace );
				$callerInfo = $trace[3];
				
				// fill in the information that we need
				$this->fullFileName = $callerInfo["file"];
				$this->file = basename( $this->fullFileName );
				$this->line = $callerInfo["line"];
				$this->class = $callerInfo["class"];
                if( isset($callerInfo["args"]) ) {
                    $this->args = $this->_getParametersString( $callerInfo["args"] );
                } else {
                    $this->args = NULL;
                }
				$this->function = $callerInfo["function"];
			}
			else {
				$this->fullFileName = "";
				$this->line = "";
				$this->file = "";
				$this->class = "";
				$this->args = "";
				$this->function = "";
			}
			
			return true;
		}
    }
?>
