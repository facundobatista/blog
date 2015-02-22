<?php

	lt_include( PLOG_CLASS_PATH."class/dao/model.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/bayesianfilterinfo.class.php" );

    /**
	 * \ingroup DAO
	 *
     * Takes care of dealing with fetching filtered contents from the database
     */
	class BayesianFilterInfos extends Model 
	{

        /**
        * -- Add function info here --
        */
    	function BayesianFilterInfos()
        {
        	$this->Model();
        }

        /**
        * -- Add function info here --
        */
        function getBlogBayesianFilterInfo($blogId)
        {
        	$query = "SELECT * FROM ". $this->getPrefix() . "bayesian_filter_info WHERE blog_id = $blogId";

            $result = $this->Execute($query);

            if( !$result )
            	return false;

			if( $result->RowCount() == 0 ) {
				// this is a bit of an special case... for whatever reason there is no spam information
				// for this blog so we have to calculate it now...
				$this->insertLate( $blogId );
				
				// and then we call ourselves again
				return( $this->getBlogBayesianFilterInfo( $blogId ));				
			}

            $row = $result->FetchRow();
            $result->Close();			
            return $this->_mapRowToObject($row);
        }

        /**
        * -- Add function info here --
        */
        function getBayesianFilterInfoFromQuery($query)
        {
            $result = $this->Execute($query);

            if( !$result )	// return an empty array if error
            	return false;

            $row = $result->FetchRow();
            $result->Close();			
            return $this->_mapRowToObject($row);
        }

        /**
        * -- Add function info here --
        */
        function _mapRowToObject($row)
        {
        	return new BayesianFilterInfo($row["blog_id"], $row["total_spam"], $row["total_nonspam"], $row["id"]);
        }
        
        /**
        * -- Add function info here --
        */
        function incTotalSpam($id)
        {
            return $this->_incTotal($id, "total_spam");
        }
        
        /**
        * -- Add function info here --
        */
        function incTotalNonSpam($id)
        {
            return $this->_incTotal($id, "total_nonspam");
        }
        
        /**
        * -- Add function info here --
        */
        function _incTotal($id, $field)
        {	
        	$query = "UPDATE " . $this->getPrefix() . "bayesian_filter_info SET " .
                     "$field = $field + 1 WHERE id=$id;";
            
            return $this->updateFromQuery($query);
        }
        
        /**
        * -- Add function info here --
        */
        function decTotalSpam($id)
        {
            return $this->_decTotal($id, "total_spam");
        }
        
        /**
        * -- Add function info here --
        */
        function decTotalNonSpam($id)
        {
            return $this->_decTotal($id, "total_nonspam");
        }
        
        /**
        * -- Add function info here --
        */
        function _decTotal($id, $field)
        {
        	$query = "UPDATE " . $this->getPrefix() . "bayesian_filter_info SET " .
                     "$field = $field - 1 WHERE id=$id;";
            
            return $this->updateFromQuery($query);
        }
        
        /**
        * -- Add function info here --
        */
        function updateFromQuery($query)
        {
            $result = $this->Execute( $query );

            if( !$result )
            	return false;

            return true;
        }
        
        /**
        * -- Add function info here --
        */
        function insert($blogId, $totalSpam = 0, $totalNonSpam = 0)
        {
        	$query = "INSERT INTO ".$this->getPrefix()."bayesian_filter_info (blog_id, total_spam, total_nonspam) VALUES ('$blogId', '$totalSpam', '$totalNonSpam');";					
            $result = $this->Execute($query);

            if( !$result )
            	return false;

            return true;
        }

        /**
        * -- Add function info here --
        */
        function insertLate($blogId )
        {
			$currentStats = "SELECT SUM(spam_occurrences) AS total_spam, 
			                        SUM(nonspam_occurrences) AS total_nonspam 
			                 FROM ".$this->getPrefix()."bayesian_tokens 
			                 WHERE blog_id = $blogId";
			$result = $this->Execute( $currentStats );
			
			if( $result ) {
				$row = $result->FetchRow();
				isset( $row["total_spam"]) ? $totalSpam = $row["total_spam"] : $totalSpam = 0;
				isset( $row["total_nonspam"]) ? $totalNonSpam = $row["total_nonspam"] : $totalNonSpam = 0;
			}
			else {
				$totalSpam = 0;
				$totalNonSpam = 0;
			}

			return( $this->insert( $blogId, $totalSpam, $totalNonSpam ));
        }

        /**
        * -- Add function info here --
        */
        function deleteBayesianFilterInfoByBlogId( $blogId )
        {
            $query = "DELETE FROM " . $this->getPrefix() . "bayesian_filter_info WHERE blog_id=$blogId";
            
            $result = $this->Execute($query);

            if(!$result)
            	return false;

            return true;
        }          
    }
?>
