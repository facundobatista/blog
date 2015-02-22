<?php

    lt_include( PLOG_CLASS_PATH."class/dao/status/genericstatuslist.class.php" );
	
	define( "USER_STATUS_ALL", -1, true ); 
    define( "USER_STATUS_ACTIVE", 1, true );
    define( "USER_STATUS_DISABLED", 2, true );
    define( "USER_STATUS_UNCONFIRMED", 3, true );
    
    /**
     * This class keeps track of all the possible status that a user can have. If plugins dynamically 
	 * register new user statuses, this class will still be able to handle them
	 * 
	 * \ingroup DAO
     */    
    class UserStatus extends GenericStatusList
    {
    
        /**
         * returns a list with all the user statuses that have been defined
         * so far in the code.
         *
         * @return Returns an array where every position is an array with two
         * keys: "constant" and "value", where "constant" is the name of the constant
         * that defines this status and "value" is the value assigned to it
         */
        function getStatusList( $includeStatusAll = false )
        {
			return( GenericStatusList::getStatusList( "USER_STATUS_", "USER_STATUS_ALL", $includeStatusAll ));
        }
        
        /**
         * @param status The status code we'd like to check
         * 
         * @return Returns true if the status is valid or false otherwise
         */
        function isValidStatus( $status )
        {
	    	$statusList = UserStatus::getStatusList( true );
	    	return( in_array( $status, $statusList ));
        }
        
        /**
         * returns the default status code for this class
         *
         * @return The default status
         */
        function getDefaultStatus()
        {
	     	return( USER_STATUS_ALL );   
        }
    }
?>
