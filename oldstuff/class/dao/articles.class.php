<?php

    lt_include( PLOG_CLASS_PATH.'class/dao/model.class.php' );
    lt_include( PLOG_CLASS_PATH.'class/dao/article.class.php' );
    lt_include( PLOG_CLASS_PATH.'class/dao/articlestatus.class.php' );
	lt_include( PLOG_CLASS_PATH.'class/dao/daocacheconstants.properties.php' );
    
    /**
	 * \ingroup DAO
	 *
     * Model for the Articles
     */
    class Articles extends Model
    {
        function Articles()
        {
            $this->Model();
			
			$this->table = $this->getPrefix()."articles";
			$this->pk = "id";
        }

        /**
         * Gets an article from the database, given its id. Also manages the
         * cache for articles.
         *
         * @param articleId Identifier of the article we want to fetch
         * @return Returns an Article object or 'false' otherwise.
         */
        function getArticle( $articleId )
        {
        	return( $this->get( "id", $articleId, CACHE_ARTICLES ));
        }

        /**
         * Gets an article from the database, given its id
         *
         * @param artId Identifier of the article we want to fetch
         * @param blogId If set, the article must belong to the given blog
         * @return Returns an Article object or 'false' otherwise.
         */
        function getBlogArticle( $artId, 
                                 $blogId = -1,
                                 $includeHiddenFields = true, 
                                 $date = -1, 
                                 $categoryId = -1, 
                                 $userId = -1, 
                                 $status = POST_STATUS_ALL )
                                 
        {
            $article = $this->getArticle( $artId );

            if( !$article )
                return false;

            if( $blogId != -1 ) {
                if( $article->getBlogId() != $blogId ) {
                    return false;
                }
            }
            if( $userId != -1 ) {
            	if( $article->getUserId() != $userId ) {
            		return false;
            	}
            }
            if( $status != POST_STATUS_ALL && $article->getStatus() != $status )
            	return false;
            	
            return $article;
        }
        
            /**
         * Gets an article from the database, given its slug, this is used
         * with the fancy permalinks
         *
         * @param artTitle Identifier of the article we want to fetch
         * @param blogId If set, the article must belong to the given blog
         * @return Returns an Article object or 'false' otherwise.
         */        
        function getBlogArticleByTitle( $articleTitle, 
                                        $blogId, 
                                        $includeHiddenFields = true, 
                                        $date = -1, 
                                        $categoryId = -1, 
                                        $userId = -1, 
                                        $status = POST_STATUS_PUBLISHED,
										$maxDate = -1 )
        {

            if(!$articleTitle)
                return false;
            
                // load all the articles with the same title
			$articles = $this->getMany( "slug",
			                            $articleTitle,
										CACHE_ARTICLES_BYNAME,
                                                    Array( CACHE_ARTICLES => "getId" ));
			$found = false;
			if($articles){
                foreach( $articles as $article ) {
                    if( $article->getBlogId() == $blogId && $this->check( $article, $date, $categoryId,
                                      $status, $userId, $maxDate )) {
                        $found = true;
                        break;
                    }
                        // if not, continue with the next...
                }
            }
			
			if( !$found ) {
                return false;
			}
			
			return( $article );
        }

        /**
         * @private
         */
        function _getBlogArticleFromQuery($query)
        {
            // we send the query and then fetch the first array with the result
            $result = $this->Execute( $query );

            if( $result == false )
                return false;

            if ( $result->RecordCount() == 0){
                $result->Close();
                return false;
            }

            $row = $result->FetchRow( $result );
            $article = $this->mapRow( $row );
            $result->Close();
            
            return $article;        
        }

        /**
         * Returns the article that goes after the one we give as the parameter.
         *
         * @param article An article object         
		 * @return An Article object with the next article or false if there was no article.
         */
        function getBlogNextArticle( $article )
        {
            if(!$article)
                return false;

            lt_include( PLOG_CLASS_PATH.'class/data/timestamp.class.php' );
            $blogInfo = $article->getBlogInfo();
            $blogSettings = $blogInfo->getSettings();

			// we need to keep the timestamp in mind
			$date = $article->getDateObject();
			$articleCorrectedDate = Timestamp::getDateWithOffset( $article->getDate(), 
                                                                 -($article->getTimeOffset()));

			$query = "SELECT * FROM ".$this->getPrefix()."articles
			          WHERE blog_id = ".$article->getBlogId()." AND
					        date > '".$articleCorrectedDate."' AND
							status = ".POST_STATUS_PUBLISHED;
            if(!$blogSettings->getValue("show_future_posts_in_calendar"))
                $query .= " AND date <= NOW()";
            $query .= " ORDER BY DATE ASC LIMIT 0,1";

			$article = $this->_getBlogArticleFromQuery( $query, false );
			
			return( $article );
        }

        /**
         * Returns the article that goes before the one we give as the parameter.
         *
         * @param article An article object
         * @return An Article object with the previous article or false if there was no article.
         */
        function getBlogPrevArticle( $article )
        {
            if(!$article)
                return false;

            lt_include( PLOG_CLASS_PATH.'class/data/timestamp.class.php' );

			// we need to keep the timestamp in mind
			$date = $article->getDateObject();
			$articleCorrectedDate = Timestamp::getDateWithOffset( $article->getDate(), 
                                                                 -($article->getTimeOffset()));
																 
			$query = "SELECT * FROM ".$this->getPrefix()."articles
			          WHERE blog_id = ".$article->getBlogId()." AND
					        date < '".$articleCorrectedDate."' AND
							status = ".POST_STATUS_PUBLISHED."
					  ORDER BY DATE DESC
					  LIMIT 0,1";							
									 
			$article = $this->_getBlogArticleFromQuery( $query, false );
			
			return( $article );
		}
		
		/**
		 * @see getBlogArticles
		 */
		function getNumBlogArticles( $blogId,
                                     $date = -1,
                                     $categoryId = 0,
                                     $status = POST_STATUS_PUBLISHED,
                                     $userId = 0,
                                     $maxDate = 0,
                                     $searchTerms = "")
		{
            $postStatus = $status;
		    $prefix = $this->getPrefix();
            $where = $this->buildWhere( $blogId, $date, -1, $categoryId, $status, $userId, $maxDate, $searchTerms );
            $query = "SELECT COUNT(a.id) AS total FROM {$prefix}articles a, {$prefix}articles_categories c, {$prefix}article_categories_link l WHERE $where ";

            $result = $this->_db->Execute( $query );
            
            if( !$result )
            	return 0;
            
			/**
			 * :HACK:
			 * this really is a dirty hack...
			 */
			if( $categoryId > 0 ) {
				$row = $result->FetchRow();
				$number = $row["total"];
			}
			else {
				$number = $result->RowCount();
			}
            $result->Close();
            return( $number );                 
		}
		
		/**
		 * @private
		 * returns true whether the given article matches the given conditions, or false otherwise
		 */
		function check( $article, 
		                $date = -1, 
		                $categoryId = 0, 
		                $status = POST_STATUS_PUBLISHED, 
		                $userId = 0, 
		                $maxDate = 0 )
		{
			if( $status != POST_STATUS_ALL ) {
				if( $article->getStatus() != $status )
					return false;
			}
			if( $categoryId > 0 ) {
				$found = false;
				foreach( $article->getCategoryIds() as $catId ) {
					if( $categoryId == $catId ) {
						$found = true;
					}
				}
				if( !$found )
					return false;
			}
			if( $userId > 0 ) {		
				if( $article->getUserId() != $userId )
					return false;
			}
			if( $date != -1 && $maxDate == 0 ) {
				$t = $article->getDateObject();
				$postDate = substr($t->getTimestamp(),0,strlen($maxDate));
				if( $postDate != $date ) {
					return false;
				}				
			}
			elseif( $maxDate > 0 && $date != -1 ) {	
				$t = $article->getDateObject();
				$postDate = substr($t->getTimestamp(),0,strlen($maxDate));
                // we need to check both ends of the range
				if( $postDate >= $maxDate || $postDate <= $date) {
					return false;					
				}
			}
			
			return( true );
		}
		
		/**
		 * @see Model::getSearchCondition
		 */
		function getSearchConditions( $searchTerms ) 
		{
			/** 
			 * this method is kinda difficult... In order to generate a valid search condition
			 * we need to actually run the LIKE query in the {$prefix}_articles_text table first since
			 * Model::getAll() and Model::getMany() can not and will not perform JOIN operations, which is
			 * what we would need in this situation. This means that from one single complex query with
			 * LIKE and JOIN, we get two less complex queries (hopefully!)
			 *
			 * This method will return a string containing something like
			 *
		     * WHERE article_id IN (id, of, the, articles, whose, article_text, matched)
			 */
			 
			// prepare the query string			 
			lt_include( PLOG_CLASS_PATH."class/dao/searchengine.class.php" );
			$searchTerms = SearchEngine::adaptSearchString( $searchTerms );
			
			$db =& Db::getDb();
			if( $db->isFullTextSupported()) {
				// fastpath when FULLTEXT is supported
				$whereString = " MATCH(normalized_text, normalized_topic) AGAINST ('{$searchTerms}' IN BOOLEAN MODE)";
			}
			else {
	            // Split the search term by space
	            $query_array = explode(' ', $searchTerms);

	            // For each search terms, I should make a like query for it   
	            $whereString = "(";
	            $whereString .= "((normalized_topic LIKE '%{$query_array[0]}%') OR (normalized_text LIKE '%{$query_array[0]}%'))";
	            for ( $i = 1; $i < count($query_array); $i = $i + 1) {
				
	                $whereString .= " AND ((normalized_topic LIKE '%{$query_array[$i]}%') OR (normalized_text LIKE '%{$query_array[$i]}%'))";
	            }
	            $whereString .= " OR ((normalized_topic LIKE '%{$searchTerms}%') OR (normalized_text LIKE '%{$searchTerms}%'))";
	            $whereString .= ")";			
			}
							
			$query = "SELECT article_id FROM ".$this->getPrefix()."articles_text
			          WHERE $whereString";			
			
			// execute the query and process the result if any
			$result = $this->Execute( $query );			
			if( !$result )
				return( "" );
			
			$ids = Array();
			while( $row = $result->FetchRow()) {
				$ids[] = $row['article_id'];
			}
			$result->Close();
			if ( !empty( $ids ) )
				$searchCondition = 'a.id IN ('.implode( ', ', $ids ).')';
			else
				$searchCondition = 'a.id = -1';
			
			return( $searchCondition );
		}
		
		/**
		 * builds a WHERE clause for a query
		 *
		 * @private
		 */
		function buildWhere( $blogId, 
		                     $date = -1, 
							 $amount = -1, 
							 $categoryId = 0, 
							 $status = 0, 
							 $userId = 0, 
							 $maxDate = 0, 
							 $searchTerms = "" )
		{
            $postStatus = $status;
		    $prefix = $this->getPrefix();
            if($blogId == -1){
                $query = "a.blog_id = a.blog_id";
            }
            else{
                $query = "a.blog_id = ".Db::qstr($blogId);
            }
            if( $date != -1 ) {
				// consider the time difference
				lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
				$blogs = new Blogs();
    	        $blogInfo = $blogs->getBlogInfo( $blogId );
				$blogSettings = $blogInfo->getSettings();
        	    $timeDifference = $blogSettings->getValue( "time_offset" );
				$SecondsDiff = $timeDifference * 3600;
                $query .= " AND FROM_UNIXTIME(UNIX_TIMESTAMP(a.date)+$SecondsDiff)+0 LIKE '$date%'";
            }

            // the common part "c.id = a.category_id" is needed so that
            // we don't get one article row as many times as the amount of categories
            // we have... due to the sql 'join' operation we're carrying out
            if( $categoryId == -1 )
                $query .= " AND c.id = l.category_id AND a.id = l.article_id ";
            else {
                if( $categoryId > 0 )
                    $query .= " AND a.id = l.article_id AND l.category_id = $categoryId AND c.id = l.category_id";
                else {
                    $query .= " AND c.id = l.category_id AND a.id = l.article_id AND c.in_main_page = 1";
                }
            }

            if( $status > 0 )
                $query .= " AND a.status = '$postStatus'";
            if( $userId > 0 )
                $query .= " AND a.user_id = ".Db::qstr($userId);
            if( $maxDate > 0 )
                $query .= " AND a.date <= '$maxDate'";
				
			// in case there were some search terms specified as parameters...
			if( $searchTerms != "" ) {
				$whereString = $this->getSearchConditions( $searchTerms );
				// and add it to the current search
				$query .=" AND {$whereString} ";	
			}
				
            if( $categoryId <= 0 )		
                $query .= " GROUP BY a.id ";
                
            return $query;
		}
		
        /**
         * Returns all the articles for a given blog, according to the conditions specified in 
         * the call. If this function is too cumbersome to use (I reckon it might be, 
         * too many parameters that have been added over time due to new requirements).
         *
         * @param blogId Identifier of the blog from where we want to fetch the articles
         * @param date date in MySQL TIMESTAMP(14) format
         * @param amount The maximum amount of posts that we would like to be returned.
         * @param categoryId A category identifier. If specified, only the posts of 
                             the given category will be returned
         * @param status If specified, only the posts with given status will be returned.
         * @param userId If specified, only the posts that belong to the specified user 
                         will be returned
         * @param maxDate a date in MySQL TIMESTAMP(14)
		 * @param searchTerms in case we would like to further refine the filtering, 
                              we can also use search features
         * @return Returns an array with all the articles from the given blog
         */

        function getBlogArticles( $blogId, 
                                  $date         = -1, 
                                  $amount       = -1, 
                                  $categoryId   = 0, 
                                  $status       = POST_STATUS_PUBLISHED, 
                                  $userId       = 0, 
                                  $maxDate      = 0, 
                                  $searchTerms  = "", 
                                  $page         = -1 )
        {
            // build the query
            // the query gets quite complicated to build because we have to take care of plenty
            // of conditions, such as the maximum date, the amount, the category,
            // wether the category has to be shown in the main page or not, etc...
            $postStatus = $status;
		    $prefix = $this->getPrefix();
		    $where = $this->buildWhere( $blogId, $date, $amount, $categoryId, $status, $userId, $maxDate, $searchTerms );
            $query = "SELECT a.id as id, a.id, a.date,
                             a.user_id,a.blog_id,a.status,a.properties,
                             a.num_reads, a.slug, 1 AS relevance, a.num_comments AS num_comments,
							 a.num_nonspam_comments AS num_nonspam_comments, a.num_trackbacks AS num_trackbacks,
							 a.num_nonspam_trackbacks AS num_nonspam_trackbacks, 
							 a.global_category_id AS global_category_id,
							 a.in_summary_page AS in_summary_page,
							 a.modification_date AS modification_date
							 FROM {$prefix}articles a, {$prefix}articles_categories c, 
                             {$prefix}article_categories_link l";
			if( $searchTerms != "" )
				$query .= ", {$prefix}articles_text t ";
			$query .= " WHERE ";
			if( $searchTerms != "" )
				$query .= " t.article_id = a.id AND ";
			$query .= " $where";
                      
	
			// if we're doing a search, we should sort by relevance
			if( $searchTerms != "" ) {
				$query .= " ORDER BY relevance";			
			}
			else {
				// check article order preference, default to newest first
				lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
				$blogs = new Blogs();
    	        $blogInfo = $blogs->getBlogInfo( $blogId );
				$blogSettings = $blogInfo->getSettings();

				if($blogSettings->getValue( "articles_order" ) == 1) {
					$query .= " ORDER BY a.date ASC";				
				} else {
					$query .= " ORDER BY a.date DESC";				
				}
			}
			
            // we don't need limits if we're getting the posts for a given day
            if( ($amount > 0) && ($date == -1) && ($page == -1 ))
                $query .= " LIMIT $amount;"; 
            
            // in case we're using a paged display
            if( $page > 0 ) {
				$start = (($page - 1) * $amount);		        
				$query .= " LIMIT $start, $amount";   
            }

			// execute the query
            $result = $this->Execute( $query );

            if( !$result )
                return Array();
				
			if( $result->RowCount() == 0 ){
                $result->Close();
				return Array();
            }
            
			$articles = Array();
            while( $row = $result->FetchRow()) {
				// map the row to an object
				$article = $this->mapRow( $row );
				$articles[] = $article;
				// and cache it for later use, we might need it
				$this->_cache->setData( $article->getId(), CACHE_ARTICLES, $article );
				$this->_cache->setMultipleData( $article->getPostSlug(), CACHE_ARTICLES_BYNAME, $article );
            }
			            
            $result->Close();            
            
            return $articles;		
        }
		
        /**
         * Gets the number of posts per month per year from the database
         *
         * @param blogId The numeric identifier of the blog from which we'd like to 
                         calculate this values
         * @return A 2-dimensional associative array where the first index is the year and the second
         * index is the number of the month: result[2003][11] = _posts for november 2003_
         */
        function getNumberPostsPerMonth( $blogId )
        {

            $archives = $this->_cache->getData( $blogId, CACHE_ARTICLESPERMONTH );
            $arcnives = false;

            if( !$archives ) {
                lt_include( PLOG_CLASS_PATH . 'class/dao/blogs.class.php' );
                $blogs = new Blogs();
                $blogInfo = $blogs->getBlogInfo( $blogId );
                $blogSettings = $blogInfo->getSettings();

				$prefix = $this->getPrefix();
                if( $blogSettings->getValue("show_future_posts_in_calendar") )
                    $numPostsPerMonthQuery = "SELECT COUNT(id) AS 'count',
                                              YEAR(date) AS 'year',
                                              MONTH(date) AS 'month'
                                              FROM {$prefix}articles 
                                              WHERE status = 1 AND blog_id = $blogId
                                              GROUP BY YEAR(date),MONTH(date) 
                                              ORDER BY YEAR(date) DESC,MONTH(date) DESC;";
                else
                    $numPostsPerMonthQuery = "SELECT COUNT(id) AS 'count',
                                                YEAR(date) AS 'year',
                                                MONTH(date) AS 'month'
                                              FROM {$prefix}articles
                                              WHERE status = 1 AND blog_id = $blogId 
                                              AND date <= NOW() 
                                              GROUP BY YEAR(date),MONTH(date) 
                                              ORDER BY YEAR(date) DESC,MONTH(date) DESC;";

                $result = $this->Execute( $numPostsPerMonthQuery);
                if( $result == false )
                    return false;

                $archives = Array();
                while( $row = $result->FetchRow()) {
                    $archives[$row["year"]][$row["month"]] = $row["count"];
                }
                $result->Close();
                $this->_cache->setData( $blogId, CACHE_ARTICLESPERMONTH, $archives );
            }

            return $archives;
        }
		
        /**
         * like the one above but with a few changes, such as always showing posts in the future
         * and returning all the months in the array, even if the total amount was '0'
         * Only used in the "editPosts" screen of the admin interface
         */
        function getNumberPostsPerMonthAdmin( $blogId )
        {
        	$prefix = $this->getPrefix();
			$numPostsPerMonthQuery = "SELECT DISTINCT YEAR(date) AS year,MONTH(date) AS month
			                          FROM {$prefix}articles
			                          WHERE blog_id = '".Db::qstr($blogId)."'
			                          ORDER BY YEAR(date) DESC,MONTH(date) DESC;";

            $result = $this->Execute( $numPostsPerMonthQuery);
            if( !$result )
            	return Array();
            	
            while( $row = $result->FetchRow()) {
            	$year = $row["year"];
            	$month = $row["month"];
            	$archives[$year][$month] = 1;
            }
            $result->Close();

            return $archives;
        }

        /**
         * The same as the one above but for just one month
         *
         * @param blogId The identifier of the blog from which we'd like to calculate this
         * @param year Yeardddd
         * @param month Month from which we'd like to calculate this
         * @return An associative array where the index is the day of the month and the value
         * is the number of posts made that day.
         */
        function getDaysWithPosts( $blogId, $year = null , $month = null )
        {
            lt_include( PLOG_CLASS_PATH.'class/data/timestamp.class.php' );
            $t = new Timestamp();            
            // if month and/or year are empty, get the current ones
            if( $year == null ) 
                $year = $t->getYear();
            if( $month == null )
                $month = $t->getMonth();
                
            $blogs = new Blogs();
            $blogInfo = $blogs->getBlogInfo( $blogId );
            $blogSettings = $blogInfo->getSettings();

            $timeDifference = $blogSettings->getValue( "time_offset" );

            $SecondsDiff = $timeDifference * 3600;

            // check whether we're supposed to show posts that happen in the future or not
            $prefix = $this->getPrefix();
            $numPostsPerDayQuery = "SELECT date
                                    FROM {$prefix}articles 
                                    WHERE status = 1 
                                    AND blog_id = $blogId
                                    AND MONTH(FROM_UNIXTIME(UNIX_TIMESTAMP(date) + $SecondsDiff)) = $month 
                                    AND YEAR(FROM_UNIXTIME(UNIX_TIMESTAMP(date) + $SecondsDiff)) = $year";

            if( !$blogSettings->getValue( "show_future_posts_in_calendar" )) {
                $numPostsPerDayQuery .= " AND date <= NOW()";
            }

            $result = $this->Execute( $numPostsPerDayQuery );

            if( !$result )
                return Array();

            lt_include( PLOG_CLASS_PATH.'class/data/timestamp.class.php' );

            $postsPerDay = Array();
            while( $row = $result->FetchRow()) {
                // we can use this auxiliary function to help us...
                $date = Timestamp::getTimestampWithOffset( $row['date'], $timeDifference );
                $day = $date->getDay();
                $postsPerDay[intval($day)] = 1;
            }           
            $result->Close();

            return $postsPerDay;
        }

        /**
         * adds records to the table that holds the many-to-many relationship between
         * categories and posts in the blog.
		 *
		 * @param articleId
		 * @param categories
		 * @return True
         */
        function addPostCategoriesLink( $article )
        {        	
            lt_include( PLOG_CLASS_PATH.'class/database/db.class.php' );
            lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );
            
            $articleId = $article->getId();
            $categories = $article->getCategoryIds();

			// nothing to do if the $categories array is not ehem, an array :)
			if( !is_array( $categories ))
				return true;
				
			$articleCategories = new ArticleCategories();
            foreach( $categories as $categoryId ) {
	
				$query = "INSERT INTO ".$this->getPrefix()."article_categories_link (article_id, category_id) VALUES (".
				         "'".Db::qstr( $articleId )."', '".Db::qstr( $categoryId )."')";

                $this->Execute( $query );

               	$category = $articleCategories->getCategory( $categoryId );                
                if( $article->getStatus() == POST_STATUS_PUBLISHED ) {
                	//$category->setNumPublishedArticles( $category->getNumPublishedArticles() +1 );
					lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );
					$category->setLastModification( new Timestamp());
                }
				//$category->setNumArticles( $category->getNumAllArticles() + 1 );
				$articleCategories->updateCategory( $category );
            }

            return true;
        }

        /**
         * removes the relationship between posts and categories from the database. This
         * method should only be used when removing an article!!
         */
        function deletePostCategoriesLink( $article )
        {
            lt_include( PLOG_CLASS_PATH.'class/database/db.class.php' );
            lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );

			$articleId = $article->getId();
			$query = "DELETE FROM ".$this->getPrefix()."article_categories_link WHERE article_id = '".Db::qstr( $article->getId())."'";

            if( ( $result = $this->Execute( $query ))) {
            	// updated the category counters
            	$articleCategories = new ArticleCategories();
            	foreach( $article->getCategories() as $category ) {
					$articleCategories->updateCategory( $category );
            	}
				// clean the cache that contains the links
				$this->_cache->removeData( $article->getId(), CACHE_ARTICLE_CATEGORIES_LINK );				
            }
            
            return( $result );
        }

        /**
         * update the links between a post and its categories
         * (basically, we use brute force here: first remove them and then recreate them again...
         * It takes less time than going through all of them and checking if they exist or not.
		 *
		 * @private
         */
        function updatePostCategoriesLink( $article, $oldArticle = null )
        {
			if( $oldArticle != null ) {
				if( !$this->deletePostCategoriesLink( $oldArticle ))
					return false;
			}

            return $this->addPostCategoriesLink( $article );
        }
		
		/**
		 * Updates global article category counters
		 *
		 * @private
		 */
		function updateGlobalArticleCategoriesLink( $article, $oldArticle = null )
		{
			lt_include( PLOG_CLASS_PATH."class/dao/globalarticlecategories.class.php" );
			$cats = new GlobalArticleCategories();
			$artCategory = $article->getGlobalCategory();
			
			if ($artCategory) {
				$artCategory->setNumArticles( $this->getNumItems( $this->getPrefix()."articles", "global_category_id = ".$artCategory->getId()));
				$artCategory->setNumActiveArticles( $this->getNumItems( $this->getPrefix()."articles", "global_category_id = ".$artCategory->getId()." AND status = ".POST_STATUS_PUBLISHED ));
				$cats->updateGlobalArticleCategory( $artCategory );
			}
			
			if( $oldArticle ) {
				$oldCategory = $oldArticle->getGlobalCategory();
				if ($oldCategory)
				{
					$oldCategory->setNumArticles( $this->getNumItems( $this->getPrefix()."articles", "global_category_id = ".$oldCategory->getId()));
					$oldCategory->setNumActiveArticles( $this->getNumItems( $this->getPrefix()."articles", "global_category_id = ".$oldCategory->getId()." AND status = ".POST_STATUS_PUBLISHED ));
					$cats->updateGlobalArticleCategory( $oldCategory );
				}		
			}
			
			return( true );
		}

        /**
         * Adds a new article to the database
         *
         * @param newArticle An Article object with all the necessary information.
         * @return Returns true if article was added successfully or false otherwise. If successful, it will modify the parmeter
		 * passed by reference and set its database id.
         */
        function addArticle( &$newArticle )
        {
            $blogInfo = $newArticle->getBlogInfo();

                // Check if we need to force the article slug to be unique
            lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
			$config =& Config::getConfig();
            if($config->getValue("force_posturl_unique"))
            {
                $slug = $newArticle->getPostSlug();
                $i = 1;
                    // check if there already is an article with the same mangled name
                while($this->getBlogArticleByTitle($slug,
                                                   $newArticle->getBlog()))
                {
                    $i++;
                        // and if so, assign a new one
                        // if we already tried with blogname+"i" we have
                        // to strip "i" before adding it again!
                    $slug = substr($slug, 0,
                                   ($i > 2) ? strlen($slug)-strlen($i-1) : strlen($slug)).$i;
                }
                $newArticle->setPostSlug($slug);
            }
            
	    lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );
    
	    // check whether the categories are valid for this blog
            $newCategoryIds = $newArticle->getCategoryIds();
            $blogCategories = new ArticleCategories();
            foreach($newCategoryIds as $catId){
                if(!$blogCategories->getCategory($catId, $blogInfo->getId()))
                    return false;
            }
            
            lt_include( PLOG_CLASS_PATH.'class/dao/customfields/customfields.class.php' );
            
            $result = $this->add( $newArticle );
			
            if( !$result )
            	return false;
				
			$this->addArticleText( $newArticle );
			
            // and create the link between the post and its categories
            $this->addPostCategoriesLink( $newArticle );
			
			// update global article categories
			$this->updateGlobalArticleCategoriesLink( $newArticle );

            // and save the custom fields
            $customFields = new CustomFieldsValues();
			$fields = $newArticle->getCustomFields();
            if( is_array( $fields )) {
            	foreach( $fields as $field ) {
                	$customFields->addCustomFieldValue( $field->getFieldId(), 
                    	                                $field->getValue(), 
                        	                            $newArticle->getId(), 
                            	                        $newArticle->getBlogId());
            	}
        	}
            
            // update the blog counters
            if( $newArticle->getStatus() == POST_STATUS_PUBLISHED ) {
				lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );	
                lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
	            $blogs = new Blogs();
        	    $blogInfo->setTotalPosts( $blogInfo->getTotalPosts() + 1 );
				$blogInfo->setUpdateDate( Timestamp::getNowTimestamp());
            	$blogs->updateBlog( $blogInfo );
            }

            // and finally clear the cache :)
			lt_include( PLOG_CLASS_PATH."class/dao/recentarticles.class.php" );
			RecentArticles::resetRecentArticlesCache( $newArticle->getBlogId());
            $this->_cache->removeData( $newArticle->getBlogId(), CACHE_ARTICLESPERMONTH );

            return( $newArticle->getId());
        }
		
		/**
		 * saves the text of an article to the database
		 *
		 * @param newArticle the Article object that we have just saved
		 * @param 
		 * @return true if successful or false otherwise
		 */
		function addArticleText( $newArticle )
		{
            lt_include( PLOG_CLASS_PATH . 'class/data/textfilter.class.php' );

			$filter = new Textfilter();
			$query = "INSERT INTO ".$this->getPrefix()."articles_text (article_id, topic, text, normalized_text, normalized_topic, mangled_topic) ".
			         " VALUES ('".Db::qstr($newArticle->getId())."',".
			         "'".Db::qstr($newArticle->getTopic())."',".
			         "'".Db::qstr($newArticle->getText(false))."',".
			         "'".Db::qstr($filter->normalizeText( $newArticle->getText(false)))."',".
			         "'".Db::qstr($filter->normalizeText( $newArticle->getTopic()))."',".
			         "'')";

			return( $this->Execute( $query ));
		}
		
		/**
		 * returns the text fields of an article
		 *
		 * @param articleId
		 * @return an array
		 */
		function getArticleText( $articleId )
		{
            $text = $this->_cache->getData( $articleId, CACHE_ARTICLETEXT );

            if( !$text ) {
				$query = "SELECT text, normalized_text, topic, normalized_topic FROM ".$this->getPrefix()."articles_text ".
				         "WHERE article_id = '".Db::qstr( $articleId )."'";
                $result = $this->Execute( $query );    
                $text = $result->FetchRow();
                $result->Close();
                $this->_cache->setData( $articleId, CACHE_ARTICLETEXT, $text );
            }

            return $text;
		}
		
		/**
		 * updates the text of an article
		 *
		 * @param article an Article object
		 * @return true if successful or false otherwise
		 */
		function updateArticleText( $article )
		{
			lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
			$filter = new Textfilter();

			$query = "UPDATE ".$this->getPrefix()."articles_text SET ".
			         "topic = '".Db::qstr($article->getTopic())."', ".
			         "text = '".Db::qstr($article->getText(false))."', ".
			         "normalized_text = '".Db::qstr($filter->normalizeText( $article->getText(false)))."', ".
			         "normalized_topic = '".Db::qstr($filter->normalizeText( $article->getTopic()))."' ".
			         "WHERE article_id = '".Db::qstr( $article->getId())."'";

            $this->_cache->removeData( $article->getId(), CACHE_ARTICLETEXT );

			return($this->Execute( $query ));
		}
		
        /**
         * Updates an article in the database
         *
         * @param article The Article object that we'd like to update in the database.
         * @return Returns true if update was successful or false otherwise.
         */
        function updateArticle( $article )
        {
            $blogInfo = $article->getBlogInfo();

                // Check if we need to force the article slug to be unique
            lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
			$config =& Config::getConfig();
            if($config->getValue("force_posturl_unique"))
            {
                $slug = $article->getPostSlug();

                    // remove the cached data now, before the slug changes
                $this->_cache->removeData( $slug, CACHE_ARTICLES_BYNAME );

                $i = 1;
                    // check if there already is an article with the same mangled name
                while($existingArticle = $this->getBlogArticleByTitle($slug,
                                                                      $article->getBlog()))
                {
                        // if we found ourselves, it is okay to keep using this name
                        // NOTE: if someone changed the unique setting after two posts
                        // were published with the same slug, they will have to update
                        // BOTH articles in order to make sure that all slugs are unique
                        // (or at least update the later article, since the first article
                        // will continue to use the same slug)
                    if($existingArticle->getId() == $article->getId())
                        break;

                        // found a match, so assign a new one
                        // if we already tried with slug+"i" we have
                        // to strip "i" before adding it again!
                    $i++;
                    $slug = substr($slug, 0,
                                   ($i > 2) ? strlen($slug)-strlen($i-1) : strlen($slug)).$i;
                }
                $article->setPostSlug($slug);
            }
            
			// keep the old version, since we're going to need it to udpate the category counters
			$oldArticle = $this->getArticle( $article->getId());

                // check whether the categories are valid for this blog
            if($article->getStatus() != POST_STATUS_DELETED){
        	lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );

                $newCategoryIds = $article->getCategoryIds();
                $blogCategories = new ArticleCategories();
                foreach($newCategoryIds as $catId){
                    if(!$blogCategories->getCategory($catId, $blogInfo->getId()))
                        return false;
                }
            }
            
			// and now update the new version
        	$result = $this->update( $article );

            // we don't bother doing anything else if the query above failed...
            if( !$result )
                return false;
				
			// update the article text
			$this->updateArticleText( $article );

			// update categories
            if( !$this->updatePostCategoriesLink( $article, $oldArticle )) {
                return false;
			}
			
			// update custom fields
			if( !$this->updateArticleCustomFields( $article->getId(), $article->getBlogId(), 
                                                   $article->getCustomFields())) {
                return false;
			}
			
			// update global article categories
			if( !$this->updateGlobalArticleCategoriesLink( $article, $oldArticle )) {
                return false;
			}

			// update the blog counter
			if( $oldArticle->getStatus() == POST_STATUS_PUBLISHED && $article->getStatus() != POST_STATUS_PUBLISHED ) {
				lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );				
		    	$blogs = new Blogs();
	        	$blogInfo->setTotalPosts( $blogInfo->getTotalPosts() - 1 );
				$blogInfo->setUpdateDate( Timestamp::getNowTimestamp());
	            $blogs->updateBlog( $blogInfo );
	        } elseif ( $oldArticle->getStatus() != POST_STATUS_PUBLISHED && $article->getStatus() == POST_STATUS_PUBLISHED ) {
				lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );		
		    	$blogs = new Blogs();
	        	$blogInfo->setTotalPosts( $blogInfo->getTotalPosts() + 1 );
				$blogInfo->setUpdateDate( Timestamp::getNowTimestamp());	
	            $blogs->updateBlog( $blogInfo );
	    	}

            // clean up the cache
			lt_include( PLOG_CLASS_PATH."class/dao/recentarticles.class.php" );
			RecentArticles::resetRecentArticlesCache( $article->getBlogId());			
            $this->_cache->removeData( $article->getBlogId(), CACHE_ARTICLESPERMONTH );
			$this->_cache->removeData( $article->getId(), CACHE_ARTICLETEXT );
            $this->_cache->setData( $article->getId(), CACHE_ARTICLES, $article );
			$this->_cache->removeData( $article->getPostSlug(), CACHE_ARTICLES_BYNAME );

            return true;
        }

        /**
         * updates the custom fields used by an article. It's actually easier to remove them all
         * and readd them again than check one by one if it already exists and if so updating it
         * and if not, add it
         *
         * @param artId
         * @param blogId
         * @param fields
         * @return True if successful or false otherwise
         */
        function updateArticleCustomFields( $artId, $blogId, $fields )
        {
        	lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfieldsvalues.class.php" );
            $customFields = new CustomFieldsValues();

            // first remove the values
            if( !$customFields->removeArticleCustomFields( $artId ))
                return false;

            foreach( $fields as $field ) {
                $customFields->addCustomFieldValue( $field->getFieldId(), 
                                                    $field->getValue(), 
                                                    $artId, $blogId );
            }

            return true;
        }

        /**
         * Updates the number of times a post has been read. This method does not just increase the num_read
         * counter by one but it can set it to whatever we want... Usually the value we pass in '$numReads' will
         * be the old value + 1, but it could be whatever.
         *
         * @param articleId A valid article identifier.
         * @param numReads A value, meaning how many times the post has been read.
         * @return Returns true if successful or false otherwise.
         */
        function updateArticleNumReads( $articleId )
        {
            lt_include( PLOG_CLASS_PATH . 'class/database/db.class.php' );

            $query = "UPDATE ".$this->getPrefix()."articles SET ".
                     " num_reads = num_reads+1, date = date".
			         " WHERE id = '".Db::qstr( $articleId )."'";

            $result = $this->Execute( $query );

            return $result;
        }
		
		/**
		 * similar as the one above but it takes an article 'name' instead of an article id
		 * @see updateArticleNumReads
		 * @param articleName an article "name" (the post 'slug')
		 * @return true if successful or false otherwise
		 */
        function updateArticleNumReadsByName( $articleName )
        {
            if(!$articleName)
                return false;
            
            // we have to build up the query, which will be pretty long...
            $query = "UPDATE ".$this->getPrefix()."articles SET ".
                     " num_reads = num_reads+1, date = date".
                     " WHERE slug = '".Db::qstr($articleName)."'";

            $result = $this->Execute( $query );

            return $result;
        }		

        /**
         * Removes an article from the database
         *
         * If forever == true, the article is physically removed from the database.
         * Otherwise, the 'status' field is set to 'deleted'
         *
         * Problem is, that MySQL will automatically update the 'date' field because he feels
         * like it... even if we explicitely say date = old_date... grrreat :P
         *
         * Valid article identifier, blog identifier and user identifier are required to remove an
         * article. It was done for security reasons and to make perfectly clear that we are removing
         * an article (so that we wouldn't be deleting the wrong one if there was any bug!)
         *
         * @param artId A valid article identifier
         * @param userid A valid user identifier
         * @param blogId A valid blog identifier
         * @param forever A boolean meaning whether the post should be removed forever or simply its status
         * should be set to 'deleted'
         * @return Returns true if successful or false otherwise.
         */
        function deleteArticle( $artId, $userId, $blogId, $forever = false )
        {
            lt_include( PLOG_CLASS_PATH.'class/dao/articlecomments.class.php' );
            lt_include( PLOG_CLASS_PATH.'class/database/db.class.php' );
            lt_include( PLOG_CLASS_PATH.'class/dao/trackbacks.class.php' );            
            lt_include( PLOG_CLASS_PATH.'class/dao/customfields/customfieldsvalues.class.php' );
            lt_include( PLOG_CLASS_PATH.'class/dao/articlenotifications.class.php' );
            
            $article = $this->getBlogArticle( $artId, $blogId, true, -1, -1, $userId );
            if( !$article )
            	return false;

            if( $forever ) {
				// delete the text
				$this->deleteArticleText( $artId );
				
				// update the links with article categories
				$this->deletePostCategoriesLink( $article );
				
				// update global article categories
				$this->updateGlobalArticleCategoriesLink( $article );

 				// update the blog counters
				if( $article->getStatus() == POST_STATUS_PUBLISHED ) {            
		            $blogs = new Blogs();
	    	        $blogInfo = $article->getBlogInfo();
	        	    $blogInfo->setTotalPosts( $blogInfo->getTotalPosts() - 1 );
	            	$blogs->updateBlog( $blogInfo );
	            }
				
				// delete the article comments
				$comments = new ArticleComments();
				$comments->deleteArticleComments( $article->getId());

				// and finally, delete the article data
            	if( !$this->delete( "id", $artId ))
            		return false;        

	            // remove all related cache
				lt_include( PLOG_CLASS_PATH."class/dao/recentarticles.class.php" );
				RecentArticles::resetRecentArticlesCache( $blogId );                
    	        $this->_cache->removeData( $blogId, CACHE_ARTICLESPERMONTH );
        	    $this->_cache->removeData( $artId, CACHE_ARTICLES );                
            }
            else {
            	$article->setStatus( POST_STATUS_DELETED );
            	$this->updateArticle( $article );
            }

            return true;
        }
		
		/**
		 * removes the text of an article
		 * 
		 * @param articleId
		 * @private
		 * @return true if successful or false otherwise
		 * @see Articles::deleteArticle
		 */
		function deleteArticleText( $articleId )
		{
			$query = "DELETE FROM ".$this->getPrefix()."articles_text WHERE article_id = '".Db::qstr( $articleId )."'";
			return( $this->Execute( $query ));
		}

        /**
         * Removes all the posts from the given blog
         *
         * @param blogId The blog identifier
         */
        function deleteBlogPosts( $blogId )
        {	
			$query = "SELECT id, user_id, blog_id FROM ".$this->getPrefix()."articles WHERE blog_id = '".Db::qstr( $blogId )."'";
			$result = $this->Execute( $query );
			
			if( !$result )
				return false;

            while( $row = $result->FetchRow()) {
                $this->deleteArticle( $row["id"], $row["user_id"], $row["blog_id"], true );
            }

            return true;
        }

		/**
		 * @private
		 */
        function mapRow( $query_result )
        {
	    lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );
            lt_include( PLOG_CLASS_PATH.'class/data/timestamp.class.php' );
            lt_include( PLOG_CLASS_PATH.'class/dao/users.class.php' );
            lt_include( PLOG_CLASS_PATH.'class/dao/blogs.class.php' );

			$id = $query_result['id'];
			
            // this is a little dirty trick or otherwise the old
            // that don't have the 'properties' field will not work
            // as they will appear to have comments disabled
            if( $query_result['properties'] == "" ) {
                $tmpArray = Array( 'comments_enabled' => true );
                $query_result['properties'] = serialize($tmpArray);
            }

			$blogs         = new Blogs();
            $blogId        = $query_result['blog_id'];
            $blogInfo      = $blogs->getBlogInfo( $blogId );

			$blogSettings  = $blogInfo->getSettings();			
			
			if( $blogSettings )
				$timeDiff = $blogSettings->getValue( 'time_offset' );
			else
				$timeDiff = 0;
				
            // we can use this auxiliary function to help us...
            $date = Timestamp::getDateWithOffset( $query_result['date'], $timeDiff );
			$modifDate = Timestamp::getDateWithOffset( $query_result['modification_date'], $timeDiff );

            $categories = new ArticleCategories();
			$articleCategories = $categories->getArticleCategories( $query_result['id'], $query_result['blog_id'] );
            $categoryIds = Array();
			foreach( $articleCategories as $category )
				array_push( $categoryIds, $category->getId());
				
			// get the article text
			$postText = $this->getArticleText( $query_result['id'] );

            $article = new Article( $postText['topic'],
                                    $postText['text'],
                                    $categoryIds,
                                    $query_result['user_id'],
                                    $query_result['blog_id'],
                                    $query_result['status'],
                                    $query_result['num_reads'],
                                    unserialize($query_result['properties']),
									$query_result['slug'],
                                    $query_result['id'] );
									
            // and fill in all the fields with the information we just got from the db
            $article->setDate( $date );
			$article->setModificationDate( $modifDate );
            $article->setTimeOffset( $timeDiff );
			$article->setCategories( $articleCategories );            
            // get information about the categories of the article
			$article->setBlogInfo( $blogInfo );
            /*if ( $this->users === null )
                $this->users = new Users();
			$article->setUserInfo( $this->users->getUserInfoFromId( $query_result['user_id'] ));*/
			
			// counters
			$article->setTotalComments( $query_result['num_comments'] );
			$article->setNumComments( $query_result['num_nonspam_comments'] );
			$article->setTotalTrackbacks( $query_result['num_trackbacks'] );
			$article->setNumTrackbacks( $query_result['num_nonspam_trackbacks'] );
			
			// global article category
			$article->setGlobalCategoryId( $query_result['global_category_id'] );
			
			// shown in summary or not
			$article->setInSummary( $query_result['in_summary_page'] );

            return $article;

        }
    }
?>
