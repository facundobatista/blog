<?php

    /**
     * \ingroup Validator
     *
     * This is the base class that the Rule classes implement. Classes extending this one
     * can use the private method _setError for raising custom error codes (in addition to 
     * returning 'false' from the validate() method)
     *
     * Client classes can use the public method getError() to retrieve this error codes
     * and perhaps show a more meaningful message based on its value (if any)
     */
    class Validation 
    {
        var $_error;

        /**
         * Initialize the validation scheme
         */
        function Validation()
        {
            
            $this->_error = false;
        }

        /**
         * Set a custom error code or message
         * 
         * @param error The new error code
         */
        function _setError($error)
        {
            $this->_error = $error;
        }

        /**
         * For client classes, use this method to retrieve the custom error code which was (if any)
         * that was set in the validate() method. 
         *
         * It is not guaranteed that an unsuccessful result of the validate() method will set a custom
         * error message, as that is completely up to the Validator class. Also depending on the 
         * class we might get different return values from this method.
         *
         * @return An extended error message
         */
        function getError()
        {
            return $this->_error;
        }

        /**
         * Implementation of the validation logic
         *
         * @param value The value that we're going to validate
         * @return true if successful or false otherwise
         * @see Rule::validate()
         * @see Validator::validate()
         */
        function validate($value)
        {
            throw(new Exception("Validation::validate: This method must be implemented by child classes."));
            die();
        }
    }

?>
