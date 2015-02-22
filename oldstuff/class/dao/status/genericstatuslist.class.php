<?php

    

	/**
	 * Generic class for handling custom statuses that are defined via global constants
	 * (as opposed as needing to be registered via some static method in this class) What this class
	 * does is can the list of globally defined constants and grab those that start with a certain prefix,
	 * such as "BLOG_STATUS_", since that will be an indicator that the given constant is a valid status.
	 * While this method is far from clean, it allows to create new statuses on the fly with very
	 * little effort.
	 *
	 * \ingroup DAO
	 */
    class GenericStatusList 
    {
    
        /**
         * returns a list with all the user statuses that have been defined
         * so far in the code.
         *
         * @return Returns an array where every position is an array with two
         * keys: "constant" and "value", where "constant" is the name of the constant
         * that defines this status and "value" is the value assigned to it
         */
        function getStatusList( $prefix, $statusAllId, $includeStatusAll = false )
        {
            // get all the constants defined so far
            $constants = get_defined_constants();
            $statusList = Array();
			
			if( $includeStatusAll ) {
				$statusAllLowercase = strtolower( $statusAllId );
				$statusList["$statusAllLowercase"] = -1;
			}

            foreach( $constants as $constant => $value ) {
                if( substr( $constant, 0, strlen($prefix)) == $prefix && $constant != $statusAllId ) {
					$constant = strtolower($constant);
                    $statusList[ "$constant" ] = $value;
                }
            }

			asort( $statusList );
			
            return $statusList;
        }
        
        /**
         * @param status The status code we'd like to check
         * 
         * @return Returns true if the status is valid or false otherwise. By default this
		 * class returns always true so please override it in your own implementations!
         */
        function isValidStatus( $status )
        {
			return( true );
        }
        
        /**
         * returns the default status code for this class. By default, it is just 'true'
		 * so it is advisable that classes inheriting from this one override this method
		 * with their own
         *
         * @return The default status
         */
        function getDefaultStatus()
        {
	     	return( true );
        }        
    }
?>