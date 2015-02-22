<?php

	

    /**
     * \ingroup Security
     *
     * this object is passed from PipelineFilter objects to the Pipeline, and
     * carries information about what the outcome of the processing
     * was. If negative (the method PipelineResult::isValid() returns false), then
     * the processing will be stopped and no more filters will be executed. Control will
     * be returned to the calling class which will decide what to do.
     *
     * @see Pipeline
     */
	class PipelineResult  
	{
		
		var $_valid;
 		var $_errorCode;
		var $_errorMessage;
		var $_view;		

        /**
         * Constructor of this type of object
         *
         * @param valid By default, all results are positive. If not, please specify otherwise
         * @param errorCode Which error code to return. Error codes depend on the filter and might
         * or might not be interpreted by the calling class.
         * @param errorMessage Error message that should be returned, if any.
         */
    	function PipelineResult( $valid = true, $errorCode = 0, $errorMessage = "" )
        {
        	$this->_valid = $valid;
            $this->_errorCode = $errorCode;
            $this->_errorMessage = $errorMessage;
			$this->_view = null;
        }

        /**
         * Returns wether the pipeline failed or succeeded
         *
         * @return A boolean value, true if successful or false otherwise
         */
        function isValid()
        {
        	return $this->_valid;
        }
        
        /**
         * Sets whether this is a valid result, or if processing will be stopped here
         *
         * @param valid Whether the processing was successful (valid) or not
         */
        function setValid( $valid )
        {
            $this->_valid = $valid;
            
            return true;
        }

        /**
         * Extended error code
         *
         * @return An error code carrying extended information. The value of this
         * field is completely depending on the implementation of the filter
         */
        function getErrorCode()
        {
        	return $this->_errorCode;
        }
        
        /**
         * Sets the extended error code.
         *
         * @param errorCode the new error code
         */
        function setErrorCode( $errorCode )
        {
            $this->_errorCode = $errorCode;
            
            return true;
        }

        /**
         * An optional error message
         *
         * @return An string describing the error, if any
         */
        function getErrorMessage()
        {
        	return $this->_errorMessage;
        }
        
        /**
         * Sets the error message.
         *
         * @param errorMessage A string representing the new error message
         */
        function setErrorMessage( $errorMessage ) 
        {
            $this->_errorMessage = $errorMessage;
            
            return true;
        }
		
		/**
		 * Whether this result object has a custom view or not
		 *
		 * @return True if there is a custom view object that should be shown instead of the
		 * default one or false otherwise
		 */
		function hasView()
		{
			return( $this->_view != null );
		}	
		
		/**
		 * Returns a custom view that can be used instead of the default
		 * ErrorView
		 *
		 * @param view A valid View, or a class extending from the base View
		 * @return nothing
		 */
		function setView( $view )
		{
			$this->_view = $view;
		}
		
		/**
		 * returns the custom view, if any
		 *
		 * @return A valid View, or a class extending from the base View
		 */
		function getView()
		{
			return( $this->_view );
		}
    }
?>