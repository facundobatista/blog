<?php

	lt_include( PLOG_CLASS_PATH."class/database/dbobject.class.php" );

    /**
	 * \ingroup DAO
     * Represents a record form the plog_filtered_content table
     *
     * The key of this class is the regexp that will be used to match
     * words against it.
     */
    class BayesianFilterInfo extends DbObject 
	{
    	
        var $_id;
        var $_blogId;
        var $_totalSpam;
        var $_totalNonSpam;
        
        /**
         * Creates a new FilteredContent object
         *
         * @param regExp the regular expression
         * @param blogId The blog identifier to which this rule belongs
         * @param reason Why this rule has been set-up
         * @param date When this rule was added
         * @param id Identifier of this rule
         */
    	function BayesianFilterInfo($blogId, $totalSpam, $totalNonSpam, $id = -1)
        {
        	$this->DbObject();

            $this->_id           = $id;
            $this->_blogId       = $blogId;
            $this->_totalSpam    = $totalSpam;
            $this->_totalNonSpam = $totalNonSpam;            
        }

        function getId()
        {
        	return $this->_id;
        }

        function getBlogId()
        {
        	return $this->_blogId;
        }

        function getTotalSpam()
        {
        	return $this->_totalSpam;
        }
        
        function getTotalNonSpam()
        {
        	return $this->_totalNonSpam;
        }
    }
?>
