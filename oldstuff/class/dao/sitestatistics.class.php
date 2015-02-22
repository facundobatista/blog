<?php

	lt_include( PLOG_CLASS_PATH."class/dao/model.class.php" );

	/**
     * This class provides some statistics about the site.
	 *
	 * \ingroup DAO
     */
	class SiteStatistics extends Model 
	{

    	function SiteStatistics()
        {
        	$this->Model();
        }

        function getTotalFromQuery( $query )
        {
            $result = $this->Execute( $query );

            $row = $result->FetchRow();
            $result->Close();

            return $row["total"];
        }

        function getNumberOfBlogs()
        {
        	$query = "SELECT COUNT(id) AS total FROM ".$this->getPrefix()."blogs";

            return $this->getTotalFromQuery( $query );
        }

        function getNumberOfPosts()
        {
        	$query = "SELECT COUNT(id) AS total FROM ".$this->getPrefix()."articles WHERE status = 1";

            return $this->getTotalFromQuery( $query );
        }

        function getNumberOfUsers()
        {
        	$query = "SELECT COUNT(id) AS total FROM ".$this->getPrefix()."users";

            return $this->getTotalFromQuery( $query );
        }

        function getNumberOfPostsToday()
        {
            lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );

        	$today = new Timestamp();
            $todayTimestamp = $today->getYear().$today->getMonth().$today->getDay();
        	$query = "SELECT COUNT(id) AS total FROM ".$this->getPrefix()."articles".
                     " WHERE date LIKE '$todayTimestamp%' AND status = 1";

            return $this->getTotalFromQuery( $query );
        }

        function getNumberOfPostsThisMonth()
        {
            lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );

        	$today = new Timestamp();
            $monthTimestamp = $today->getYear().$today->getMonth();
        	$query = "SELECT COUNT(id) AS total FROM ".$this->getPrefix()."articles".
                     " WHERE date LIKE '$monthTimestamp%' AND status = 1";

            return $this->getTotalFromQuery( $query );
        }

        function getAllStatistics()
        {
        	$stats = Array();

            $stats["totalblogs"] = $this->getNumberOfBlogs();
            $stats["totalusers"] = $this->getNumberOfUsers();
            $stats["totalposts"] = $this->getNumberOfPosts();
            $stats["poststoday"] = $this->getNumberOfPostsToday();
            $stats["poststhismonth"] = $this->getNumberOfPostsThisMonth();

            return $stats;
        }
    }
?>
