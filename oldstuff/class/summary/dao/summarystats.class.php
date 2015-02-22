<?php

    lt_include( PLOG_CLASS_PATH."class/dao/model.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
    lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articlecommentstatus.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articlestatus.class.php" );
    lt_include( PLOG_CLASS_PATH."class/summary/dao/summarystatsconstants.class.php" ); 
	
    /**
     * This class implements a few methods that can be used to obtain the list of most recent blogs, posts, commets,
     * most commented articles, etc. It is mainly used by the summary.php script but it can also be used by users
     * wishing to integrate the summary page in their sites.
     *
     * @see BlogInfo
     * @see Article
     *
     * \ingroup DAO	
     */
    class SummaryStats extends Model
    {
        
        var $_now;
        var $_startTime;
        var $_summaryPageShowMax;

        function SummaryStats()
        {
            // initialize ADOdb
            $this->Model();

            $this->_now = $this->getNow();
            $this->_startTime = $this->getStartTime( SUMMARY_DEFAULT_TIME_FENCE );
            
            // get the summary_page_show_max from config
            $config =& Config::getConfig();
            $this->_summaryPageShowMax = $config->getValue( "summary_page_show_max", SUMMARY_DEFAULT_PAGE_SHOW_MAX );
        }

        /**
         * Returns the most commented articles so far
         *
         * @param maxPosts The maximum number of posts to return
         * @return An array of Article objects with the most commented articles
         */
        function getMostCommentedArticles( $maxPosts = 0 )
        {
            lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
            lt_include( PLOG_CLASS_PATH."class/dao/commentscommon.class.php" );
			$articles = new Articles();
			
            $maxPosts > 0 ? $max = $maxPosts : $max = $this->_summaryPageShowMax;

			$prefix = $this->getPrefix();			
			$query = "SELECT a.*
					  FROM {$prefix}articles AS a,
					       {$prefix}blogs b
					  WHERE a.blog_id = b.id
					        AND a.status = ".POST_STATUS_PUBLISHED."
							AND b.status = ".BLOG_STATUS_ACTIVE."
                            AND a.date >= ".$this->_startTime."
							AND a.date <= ".$this->_now."
							AND a.in_summary_page = '1'
							AND a.num_nonspam_comments > 0
							AND b.show_in_summary = '1'
					  ORDER BY a.num_nonspam_comments DESC
					  LIMIT 0, $max";

            $result = $this->Execute( $query );

            if( !$result ){
            	return Array();
            }

            $posts = Array();
            while( $row = $result->FetchRow()) {
            	array_push( $posts, $articles->mapRow($row));
            }
            
            $result->Close();

            return $posts;
        }
		
        /**
         * Returns an array with the most read articles
         *
         * @param maxPosts The maximum number of posts to return
         * @return an array of Article objects with information about the posts
         * TODO: performance tuning
         */
        function getMostReadArticles( $maxPosts = 0 )
        {
             lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
             
             $prefix = $this->getPrefix();
			 $articles = new Articles();

             $query = "SELECT a.*
                 FROM {$prefix}articles a, {$prefix}blogs b
                 WHERE a.status = ".POST_STATUS_PUBLISHED."
                 AND a.blog_id = b.id 
				 AND b.status = ".BLOG_STATUS_ACTIVE."
                 AND a.date <= ".$this->_now." AND a.date > ".$this->_startTime."
				 AND in_summary_page = '1'
				 AND b.show_in_summary = '1'
				 ORDER BY a.num_reads DESC";

            if( $maxPosts > 0 )
            	$query .= " LIMIT 0,".$maxPosts;
            else
            	$query .= " LIMIT 0,".$this->_summaryPageShowMax;

            $result = $this->Execute( $query );

            if( !$result )
            	return Array();

            $posts = Array();
            while( $row = $result->FetchRow()) {
				$post = $articles->mapRow($row);
            	array_push( $posts, $post );
            }
            
            $result->Close();            

            return $posts;
        }

        /**
         *returns  list with the most recently created blogs
         *
         * @param maxBlogs The maximum number of blogs to return, or '0' to get all of them
         * @return An array of BlogInfo objects
         * @see BlogInfo
         */
         function getRecentBlogs( $maxBlogs = 0 )
         {
			lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
         	$query = "SELECT * 
			          FROM ".$this->getPrefix()."blogs 
					  WHERE status = ".BLOG_STATUS_ACTIVE." 
					  AND show_in_summary = '1'
					  AND create_date > ".$this->_startTime."
					  ORDER BY create_date DESC";

            if( $maxBlogs > 0 )
            	$query .= " LIMIT 0,".$maxBlogs;
            else
            	$query .= " LIMIT 0,".$this->_summaryPageShowMax;

            $result = $this->Execute( $query );

            if( !$result ){
            	return Array();
            }

            $blogs = Array();
			$blogsDao = new Blogs();
            while( $row = $result->FetchRow()) {
            	$blog = $blogsDao->mapRow( $row );
                $blogs[$blog->getId()] = $blog;
            }
            
            $result->Close();            

            return $blogs;
        }

        /**
         * returns an array with the most active blogs
         *
         * @param maxBlogs How many blogs to return
         * @return An array of BlogInfo objects
         * @see BlogInfo
         */
         function getMostActiveBlogs( $maxBlogs = 0 )
         {
			lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
		 
			$prefix = $this->getPrefix();
			
			$query = "SELECT SUM(((a.num_reads + a.num_comments/5) / (TO_DAYS(NOW()) - TO_DAYS(a.date) + 1)) )/COUNT(a.id) as rank,
			           b.id AS blog_id
			           FROM {$prefix}articles AS a
			           INNER JOIN {$prefix}blogs AS b
			           ON b.id = a.blog_id AND b.status =  ".BLOG_STATUS_ACTIVE."
                       WHERE a.date >= ".$this->_startTime." 
                       AND a.date <= ".$this->_now."
					   AND a.in_summary_page = '1'
			           AND b.show_in_summary = '1'
			           GROUP BY b.id
			           ORDER BY rank DESC";			

            if( $maxBlogs > 0 )
            	$query .= " LIMIT 0,".$maxBlogs;
            else
            	$query .= " LIMIT 0,".$this->_summaryPageShowMax;

            $result = $this->Execute( $query );

            if( !$result ){
                return Array();
            }

            $blogs = Array();
			$blogsDao = new Blogs();
            while( $row = $result->FetchRow()) {
                $blog = $blogsDao->getBlogInfo( $row["blog_id"] );
                $blogs[$blog->getId()] = $blog;
            }
            
            $result->Close();            

            return $blogs;
        }

        /**
         * returns a list with the most recent articles, but only one per blog so that
         * one blog cannot clog the whole main page because they've posted 100 posts today. Posts that were posted
		 * in categories not shown in the main page of each blog will not be shown!
         *
         * @param maxPosts The maximum number of posts to return
         * @return An array of Article objects with the most recent articles.
         */
        function getRecentArticles( $globaArticleCategoryId = ALL_GLOBAL_ARTICLE_CATEGORIES, $maxPosts = 0 )
        {
            lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );

			$prefix = $this->getPrefix();

			$query = "SELECT a.id AS id, a.blog_id AS blog_id
					  FROM {$prefix}articles a, 
					       {$prefix}blogs b
					  WHERE a.date >= ".$this->_startTime." AND a.date <= ".$this->_now."
					        AND a.blog_id = b.id
					        AND b.status = ".BLOG_STATUS_ACTIVE."
					        AND a.status = ".POST_STATUS_PUBLISHED."
							AND b.show_in_summary = '1'
							AND a.in_summary_page = '1'";

			if($globaArticleCategoryId != ALL_GLOBAL_ARTICLE_CATEGORIES)
				$query .= " AND a.global_category_id = '".Db::qstr($globaArticleCategoryId)."'";
				
			$query .= " ORDER BY a.date DESC";

            if( $maxPosts <= 0 )
            	$maxPosts = $this->_summaryPageShowMax;

		// the multiplier here isn't a very elegant solution but what we're trying to avoid
		// here is a situation where if the limit is '10', then a blog posting 10 articles in one
		// go would use all these 10 'slots' in the result set. Then when the list of posts is 
		// post-processed, there would only be one article left... which is definitely not
		// what we'd like
            $query .= " LIMIT 0,".$maxPosts * 15;

            $result = $this->Execute( $query );

            if( !$result )
                return Array();

            $blogs = Array();
            $posts = Array();
            $i     = 0;

			$articles = new Articles();
            while( ($row = $result->FetchRow()) && ($i < $maxPosts) ) {
                if (!in_array($row["blog_id"], $blogs))
                {
                    $blogs[] = $row["blog_id"];
                    array_push( $posts, $articles->getArticle($row["id"]) );
                    $i++;
                }
            }

            $result->Close();            

            return $posts;
        }

        function getPostsByGlobalCategory( $globaArticleCategoryId = ALL_GLOBAL_ARTICLE_CATEGORIES, 
        										 $page = -1, 
        										 $itemsPerPage = SUMMARY_DEFAULT_ITEMS_PER_PAGE )
        {
            lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );

			$prefix = $this->getPrefix();

			$query = "SELECT a.id AS id
					  FROM {$prefix}articles a, 
					       {$prefix}blogs b
					  WHERE a.date <= ".$this->_now."
					        AND a.blog_id = b.id
					        AND b.status = ".BLOG_STATUS_ACTIVE."
					        AND a.status = ".POST_STATUS_PUBLISHED."
							AND a.in_summary_page = '1'
							AND b.show_in_summary = '1'";

			if($globaArticleCategoryId != ALL_GLOBAL_ARTICLE_CATEGORIES)
				$query .= " AND a.global_category_id = '".Db::qstr($globaArticleCategoryId)."'";				

			$query .= " ORDER BY a.date DESC";

            $result = $this->Execute( $query, $page, $itemsPerPage );
            
            if( !$result )
                return Array();

            $posts = Array();
			$articles = new Articles();
            while( $row = $result->FetchRow() ) {
		// if we call Articles::getArticle() we'll be using the cached data
		// if it was already there, instead of mapping the whole row here
                array_push( $posts, $articles->getArticle( $row["id"] ));
            }

            $result->Close();

            return $posts;
        }

        function getNumPostsByGlobalCategory( $globaArticleCategoryId = ALL_GLOBAL_ARTICLE_CATEGORIES )
        {
            lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );

			$prefix = $this->getPrefix();


			$query =" a.date <= ".$this->_now."
					       AND a.blog_id = b.id
					        AND b.status = ".BLOG_STATUS_ACTIVE."
					        AND a.status = ".POST_STATUS_PUBLISHED."
							AND b.show_in_summary = '1'
							AND a.in_summary_page = '1'";

			if($globaArticleCategoryId != ALL_GLOBAL_ARTICLE_CATEGORIES)
				$query .= " AND a.global_category_id = '".Db::qstr($globaArticleCategoryId)."'";				

            return( $this->getNumItems( "{$prefix}articles a, {$prefix}blogs b", $query, "a.id" ));
        }

		/**
		 * @private
		 */
		function getNow() 
		{
			lt_include( PLOG_CLASS_PATH.'class/data/timestamp.class.php' );

            $time = new Timestamp();
			$now = $time->getTimestamp();

			return $now;
		}

		/** 
		 * @private
		 */
		function getStartTime( $duration ) 
		{
			lt_include( PLOG_CLASS_PATH.'class/data/timestamp.class.php' );

            $time = new Timestamp();
            $time->subtractSeconds( $duration * 24 * 60 * 60 );
			$startTime = $time->getYear().$time->getMonth();
			if( $time->getDay() < 10 )
				$startTime .= "0";
			$startTime .= $time->getDay();
			$startTime .= "000000";

			return $startTime;
		}
	/**
 	 * Returns all the most recently posted articles by the given user id
	 *
	 * @param userId
	 * @param maxPosts
	 * @return An Array of Article objects
	 */
	function getUserRecentArticles( $userId, $maxPosts = 10 ) 
	{
		lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
		// query to load the data
		$query = "SELECT * FROM ".$this->getPrefix()."articles WHERE user_id = ".Db::qstr( $userId )." AND status = ".POST_STATUS_PUBLISHED."
			  ORDER BY date DESC";
		
		// process it
		$res = $this->Execute( $query, 1, $maxPosts );
		if( !$res )
			return( Array());

		$posts = Array();
		$articles = new Articles();
		while( $row = $res->FetchRow()) {
			$posts[] = $articles->mapRow( $row );
		}

		return( $posts );
	}       
    }
?>