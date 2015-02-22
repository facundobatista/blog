<?php

    lt_include( PLOG_CLASS_PATH."class/dao/status/genericstatuslist.class.php" );
	
	define( "POST_STATUS_ALL", -1, true ); 
    define( "POST_STATUS_PUBLISHED", 1, true );
    define( "POST_STATUS_DRAFT", 2, true );
    define( "POST_STATUS_DELETED", 3, true );	
    
    /**
     * This class keeps track of all the possible status that an article can have. If plugins dynamically 
	 * register new article statuses, this class will still be able to handle them.
	 * 
	 * These are the out of the box statues for posts:
	 *
	 * - POST_STATUS_PUBLISHED
	 * - POST_STATUS_DRAFT
	 * - POST_STATUS_DELETED
	 *
	 * \ingroup DAO
     */    	
    class ArticleStatus extends GenericStatusList
    {
    
        /**
         * returns a list with all the post statuses that have been defined
         * so far in the code.
         *
         * @return Returns an array where every position is an array with two
         * keys: "constant" and "value", where "constant" is the name of the constant
         * that defines this status and "value" is the value assigned to it
         */
        function getStatusList( $includeStatusAll = false )
        {
			return( GenericStatusList::getStatusList( "POST_STATUS_", "POST_STATUS_ALL", $includeStatusAll ));
        }
    }
?>