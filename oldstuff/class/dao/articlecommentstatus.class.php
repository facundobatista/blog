<?php

    lt_include( PLOG_CLASS_PATH."class/dao/status/genericstatuslist.class.php" );
	
	define( "COMMENT_STATUS_ALL", -1, true ); 
	define( "COMMENT_STATUS_NONSPAM", 0, true );
    define( "COMMENT_STATUS_SPAM", 1, true );

    /**
     * This class keeps track of all the possible status that a blog can have. If plugins dynamically 
	 * register new blog statuses, this class will still be able to handle them
	 * 
	 * \ingroup DAO
     */        
    class ArticleCommentStatus extends GenericStatusList
    {
    
        /**
         * returns a list with all the statuses that have been defined for comments. 
		 * If you wish to define your own custom status, then simply in your plugin define
		 * COMMENT_STATUS_XXX and give it a sensible value.
         *
         * @return Returns an array where every position is an array with two
         * keys: "constant" and "value", where "constant" is the name of the constant
         * that defines this status and "value" is the value assigned to it
		 * @static
         */
        function getStatusList( $includeStatusAll = false )
        {
			return( GenericStatusList::getStatusList( "COMMENT_STATUS_", "COMMENT_STATUS_ALL", $includeStatusAll ));
        }
		
		/**
		 * returns whether a status has previously been defined or not
		 *
		 * @param status
		 * @return true if valid status or false otherwise
		 */
		function isValidStatus( $status )
		{
			// check if the status is valid
			if( $status == "" )
				return false;
			
			$isValid = in_array( $status, ArticleCommentStatus::getStatusList());
			
			return $isValid;
		}
    }
?>