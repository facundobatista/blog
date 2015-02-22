<?php

	lt_include( PLOG_CLASS_PATH."class/dao/model.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/articlecommentstatus.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/daocacheconstants.properties.php" ); 
	lt_include( PLOG_CLASS_PATH."class/dao/usercomment.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/trackback.class.php" );	

	/** 
	 * different orders that comments can have
	 */
	define( "COMMENT_ORDER_OLDEST_FIRST", 1 );
	define( "COMMENT_ORDER_NEWEST_FIRST", 2 );
	
	/**
	 * whether we'd like to fetch a comment, trackback or anything
	 */
	define( "COMMENT_TYPE_COMMENT", 1 );
	define( "COMMENT_TYPE_TRACKBACK", 2 );	
	define( "COMMENT_TYPE_ANY", -1 );
	
	/**
	 * \ingroup DAO
	 *
	 * Since comments and trackbacks are now in the same table, this class contains all the
	 * common code needed to deal with these items. Most of the methods are exactly the same in both
	 * ArticleComments and Trackbacks except that they take an additional parameter called 'status'
	 * which can either be
	 *
	 * - COMMENT_TYPE_COMMENT
	 * - COMMENT_TYPE_TRACKBACK
	 * - COMMENT_TYPE_ANY
	 *
	 * Depending on whether we'd like to retrieve a trackback or a comment.
	 */
	class CommentsCommon extends Model 
	{

		var $_blogSettings;
		var $blogSettings;
		var $timeDiff;

		function CommentsCommon()
		{
			$this->Model();
			$this->table = $this->getPrefix()."articles_comments";
		}

		/**
		 * Adds a comment to an article
		 *
		 * @param comment the UserComment object that we're going to add.
		 * @return Returns true if successful or false if error. Also in case of success, it will modify the UserComment
		 * object passed by reference and include its new id.
		 */
		function addComment( &$comment ) 
		{
			if(( $result = $this->add( $comment ))) {
				lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
				$this->_cache->removeData( $comment->getArticleId(), CACHE_ARTICLE_COMMENTS_BYARTICLE_NEWEST_TO_OLDEST );
				$this->_cache->removeData( $comment->getArticleId(), CACHE_ARTICLE_COMMENTS_BYARTICLE_OLDEST_TO_NEWEST );
				// update the article comments
				$article = $comment->getArticle();
				$blog = $article->getBlogInfo();
				if( $comment->getType() == COMMENT_TYPE_COMMENT ) {
					$article->setNumComments( $this->getNumItems( $this->getPrefix().'articles_comments', 
																  'article_id = '.$article->getId().
																  ' AND status = '.COMMENT_STATUS_NONSPAM.
																  ' AND type = '.$comment->getType()));
					$article->setTotalComments($this->getNumItems( $this->getPrefix().'articles_comments', 
														 'article_id = '.$article->getId().' AND type = '.$comment->getType()));
					$blog->setTotalComments($this->getNumItems( $this->getPrefix().'articles_comments', 
																'blog_id = '.$blog->getId().' AND type = '.$comment->getType()));
				}
				else {
					$article->setNumTrackbacks( $this->getNumItems( $this->getPrefix().'articles_comments', 
																  'article_id = '.$article->getId().
																  ' AND status = '.COMMENT_STATUS_NONSPAM.
																  ' AND type = '.$comment->getType()));
					$article->setTotalTrackbacks($this->getNumItems( $this->getPrefix().'articles_comments', 
														 'article_id = '.$article->getId().' AND type = '.$comment->getType()));
					$blog->setTotalTrackbacks($this->getNumItems( $this->getPrefix().'articles_comments', 
																  'blog_id = '.$blog->getId().' AND type = '.$comment->getType()));					
				}

				$articles = new Articles();
				$result = $articles->updateArticle( $article );
				lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
				$blogs = new Blogs();
				$blogs->updateBlog( $blog );
			}
			return( $result );
		}

		/**
		 * Retrieves all the comments for a post
		 *
		 * @param artid The article identifier
		 * @param order The order in which comments should be retrieved
		 * @param status The status that the comment should have, use COMMENT_STATUS_ALL for
		 * all possible statuses
		 * @param page
		 * @param itemsPerPage
		 * @return False if error or an array of ArticleComments objects
		 */
		function getPostComments( $artid, 
								  $order = COMMENT_ORDER_NEWEST_FIRST, 
								  $status = COMMENT_STATUS_ALL, 
								  $type = COMMENT_TYPE_ANY, 
								  $page = -1, 
								  $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
		{
			$query = "SELECT * FROM ".$this->getPrefix()."articles_comments ".
			         "WHERE article_id = '".Db::qstr( $artid )."'";
			
			if( $status != COMMENT_STATUS_ALL )
				 $query .= "AND status = '".Db::qstr( $status )."'";
			
			if( $type != COMMENT_TYPE_ANY )
				$query .= " AND type = '".Db::qstr( $type )."'";				
			
			if( $order == COMMENT_ORDER_NEWEST_FIRST )
				$query .= " ORDER BY date DESC";
			else
				$query .=" ORDER BY date ASC";
			
			$result = $this->Execute( $query, $page, $itemsPerPage );
			
			if( !$result )
				return( Array());
													
			$results = Array();
			
			while( $row = $result->FetchRow()) {
				$results[] = $this->mapRow( $row );
			}
				
				
			return( $results );						   
		}
		
		/**
		 * Returns the total number of comments for a post
		 *
		 * @param artId the post id
		 * @param status
		 * @return The number of comments
		 */
		function getNumPostComments( $artId, $status = COMMENT_STATUS_ALL, $type = COMMENT_TYPE_ANY )
		{
			lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
			$numComments = 0;
			$articles = new Articles();
			$article = $articles->getArticle( $artId );			

			if(!$article)
				return 0;
			
			if( $type == COMMENT_TYPE_COMMENT ) {
				if( $status == COMMENT_STATUS_ALL ) {
					$numComments = $article->getTotalComments();
				}
				elseif( $status == COMMENT_STATUS_NONSPAM ) {
					$numComments = $article->getNumComments();
				}
				elseif( $status == COMMENT_STATUS_SPAM ) {
					$numComments = $article->getTotalComments() - $article->getNumComments();
				}
				else {
					$numComments = 0;
				}
			}
			else if($type == COMMENT_TYPE_TRACKBACK) {
				if( $status == COMMENT_STATUS_ALL ) {
					$numComments = $article->getTotalTrackBacks();
				}
				elseif( $status == COMMENT_STATUS_NONSPAM ) {
					$numComments = $article->getNumTrackBacks();
				}
				elseif( $status == COMMENT_STATUS_SPAM ) {
						$numComments = $article->getTotalTrackbacks() - $article->getNumTrackbacks();
				}
				else {
					$numComments = 0;
				}
			}
			else{
				$numComments = $this->getNumPostComments($artId, $status, COMMENT_TYPE_COMMENT) +
					$this->getNumPostComments($artId, $status, COMMENT_TYPE_TRACKBACK);
			}
			
			return( $numComments );
		}
		
		/**
		 * returns the number of comments that a blog has
		 *
		 * @param blogId
		 * @param status
		 * @param type
		 * @param searchTerms
		 * @return The number of comments, or 0 if error or no comments
		 */
		function getNumBlogComments( $blogId, $status = COMMENT_STATUS_ALL, $type = COMMENT_TYPE_ANY, $searchTerms = "" )
		{
			if( $status == COMMENT_STATUS_ALL && $type != COMMENT_TYPE_ANY && $searchTerms == "" ) {
				// fast case, we can load the blog and query one of its intrinsic fields
				lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
				$blogs = new Blogs();
				$blogInfo = $blogs->getBlogInfo( $blogId );
				if( !$blogInfo )
					$numComments = 0;
				else {
					if( $type == COMMENT_TYPE_COMMENT )
						$numComments = $blogInfo->getTotalComments();
					else
						$numComments = $blogInfo->getTotalTrackbacks();
				}
			}
			else {			
				// create the table name
				$prefix = $this->getPrefix();
				$table = "{$prefix}articles_comments c";
				// and the condition if any...
				$cond = "c.blog_id = '".Db::qstr($blogId)."'";
				if( $status != COMMENT_STATUS_ALL )
					$cond .= " AND c.status = '".Db::qstr($status)."'";
				if( $type != COMMENT_TYPE_ANY )
					$cond .= " AND c.type = '".Db::qstr($type)."'";
				if( $searchTerms != "" )
					$cond .= " AND ".$this->getSearchConditions( $searchTerms );
					//print("type = ".$type." - cond = $cond");
				$numComments = $this->getNumItems( $table, $cond );
			}
			
			return( $numComments );
		}

		/**
		 * Removes a comment from a post. It also updates all the other posts that
		 * have this one as the parent and makes them look as if they were 'top level'
		 * comments with no parent.
		 *
		 * @param artid The article identifier.
		 * @param commentid The comment identifier.
		 */
		function deleteComment( $commentid )
		{
			$comment = $this->getComment( $commentid );
			if( $comment )	{
				$this->delete( "id", $commentid );
				// update all the other posts
				$query = "UPDATE ".$this->getPrefix()."articles_comments SET parent_id = 0 WHERE parent_id = '".
						 Db::qstr($commentid)."' AND article_id = '".
						 Db::qstr( $comment->getArticleId())."'";
				$result = $this->Execute( $query );
				$this->_cache->removeData( $comment->getArticleId(), CACHE_ARTICLE_COMMENTS_BYARTICLE_NEWEST_TO_OLDEST );
				$this->_cache->removeData( $comment->getArticleId(), CACHE_ARTICLE_COMMENTS_BYARTICLE_OLDEST_TO_NEWEST );
				$this->_cache->removeData( $comment->getId(), CACHE_ARTICLE_COMMENTS );
				
				// update the blog and the article counters
				lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
				lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );

				$article = $comment->getArticle();
				$blog = $article->getBlogInfo();
				$type = $comment->getType();
				if( $type == COMMENT_TYPE_COMMENT ) {
					$article->setNumComments( $this->getNumItems( $this->getPrefix().'articles_comments', 
																  'article_id = '.$article->getId().' AND status = '.COMMENT_STATUS_NONSPAM.
																  ' AND type = '.$comment->getType()));
					$article->setTotalComments( $this->getNumItems( $this->getPrefix().'articles_comments', 
																	'article_id = '.$article->getId().' AND type = '.$comment->getType()));
					$blog->setTotalComments($this->getNumItems( $this->getPrefix().'articles_comments', 
																'blog_id = '.$blog->getId().' AND type = '.$comment->getType()));
				}
				else {
					$article->setNumTrackbacks( $this->getNumItems( $this->getPrefix().'articles_comments', 
																  'article_id = '.$article->getId().' AND status = '.COMMENT_STATUS_NONSPAM.
																  ' AND type = '.$comment->getType()));
					$article->setTotalTrackbacks($this->getNumItems( $this->getPrefix().'articles_comments', 
																  'article_id = '.$article->getId().' AND type = '.$comment->getType()));
					$blog->setTotalTrackbacks($this->getNumItems( $this->getPrefix().'articles_comments',
																  'blog_id = '.$blog->getId().' AND type = '.$comment->getType()));
				}
				$blogs = new Blogs();
				$blogs->updateBlog( $blog );
				$articles = new Articles();
				$articles->updateArticle( $article );				
			}
			else {
				return false;
			}
				
			return( true );
		}		

		/**
		 * Updates a comment. It also takes into account status changes and updates counters in
		 * the blogs and articles table accordingly.
		 *
		 * @param comment An UserComment object
		 * @return true if update successful or false otherwise.
		 */
		function updateComment( $comment )
		{
			// we need to undo the time offset before updating the comment, or else the comment will be saved
			// with the time offset applied
			$blogInfo = $comment->getBlogInfo();
			$blogOffset = $blogInfo->getValue( "time_offset" );
			if( $blogOffset != 0 ) {
				// if there's an offset...
				$newDate = Timestamp::getDateWithOffset( $comment->getDate(), -$blogOffset );
				$comment->setDate( $newDate );
			}
	
			if(($result = $this->update( $comment ))) {
				// reset the cache
				$this->_cache->removeData( $comment->getArticleId(), CACHE_ARTICLE_COMMENTS_BYARTICLE_NEWEST_TO_OLDEST );
				$this->_cache->removeData( $comment->getArticleId(), CACHE_ARTICLE_COMMENTS_BYARTICLE_OLDEST_TO_NEWEST );
				$this->_cache->removeData( $comment->getId(), CACHE_ARTICLE_COMMENTS );
				// update counters in the articles table according to the status
				lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
				$articles = new Articles();
				// load the article
				$article = $comment->getArticle();
				if( $comment->getType() == COMMENT_TYPE_COMMENT ) {
					$article->setNumComments( $this->getNumItems( $this->getPrefix().'articles_comments', 
																  'article_id = '.$article->getId().' AND status = '.COMMENT_STATUS_NONSPAM.
																  ' AND type = '.$comment->getType()));
					$article->setTotalComments( $this->getNumItems( $this->getPrefix().'articles_comments', 
																  'article_id = '.$article->getId().' AND type = '.$comment->getType()));
				}
				elseif( $comment->getType() == COMMENT_TYPE_TRACKBACK ) {
					$article->setNumTrackbacks( $this->getNumItems( $this->getPrefix().'articles_comments', 
																  'article_id = '.$article->getId().' AND status = '.COMMENT_STATUS_NONSPAM.
																  ' AND type = '.$comment->getType()));
					$article->setTotalTrackbacks( $this->getNumItems( $this->getPrefix().'articles_comments', 
																  'article_id = '.$article->getId().' AND type = '.$comment->getType()));
				}
				$articles->updateArticle( $article );
			}

			// this is not the cleanest solution but it will do the trick for now...
			if( $blogOffset != 0 ) {
				$newDate = Timestamp::getDateWithOffset( $comment->getDate(), $blogOffset );
				$comment->setDate( $newDate );
			}
			
			return( $result );
		}

		/**
		 * returns a single comment, identified by its identifier
		 *
		 * @param type
		 */
		function getComment( $id, $type = COMMENT_TYPE_ANY )
		{
			$comment = $this->get( "id" , $id, CACHE_ARTICLE_COMMENTS );
			if( !$comment )
				return false;
			if( $type != COMMENT_TYPE_ANY ) {
				if( $comment->getType() != $type ) {
					return false;
				}
			}
				
			return( $comment );
		}
		
		/**
		 * returns the comments received in the blog
		 *
		 * @param blogId
		 * @param order
		 * @param status
		 * @param type
		 * @param searchTerms
		 * @param page
		 * @param itemsPerPage
		 * @return An array of ArticleComment objects
		 */
		function getBlogComments( $blogId, 
								  $order = COMMENT_ORDER_NEWEST_FIRST, 
								  $status = COMMENT_STATUS_ALL, 
								  $type = COMMENT_TYPE_ANY, 
								  $searchTerms = "", 
								  $page = -1, 
								  $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
		{
			/**
			 * :TODO:
			 * - implement this method in a better way!
			 */
			$prefix = $this->getPrefix();
			$query = "SELECT c.*
					  FROM {$prefix}articles_comments c
					  WHERE c.blog_id = '".Db::qstr( $blogId )."'";
			if( $status != COMMENT_STATUS_ALL )
				$query .= " AND status = $status";
			if( $type != COMMENT_TYPE_ANY )
				$query .= " AND type = '".Db::qstr($type)."'";
			if( $searchTerms != "" ) {
				$query .= " AND ".$this->getSearchConditions( $searchTerms );
			}

			// check in which order we should display those comments
			if( $order == COMMENT_ORDER_NEWEST_FIRST )
				$query .= " ORDER BY date DESC";
			else
				$query .= " ORDER BY date ASC";
			
			$result = $this->Execute( $query, $page, $itemsPerPage );
			
			if( !$result )
				return false;
				
			if( $result->RowCount() == 0 ){
				$result->Close();			
				return Array();
			}
				
			$comments = Array();
			lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
			$articles = new Articles();
			while( $row = $result->FetchRow()) {
				$comments[] = $this->mapRow( $row );
			}
			$result->Close();			

			/*if( $page > -1 && $amount > -1 )
				$comments = array_slice( $comments, ($page-1) * $amount, $amount );*/
						 
			return( $comments );
		}
		
		/**
		 * @private
		 * @see Model::mapRow()
		 */
		function mapRow( $row )
		{
			lt_include( PLOG_CLASS_PATH."class/dao/usercomment.class.php" );
			lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );

			$prefix = $this->getPrefix();
			$date = $row["date"];
			$articleId = $row["article_id"];
			$blogId = $row["blog_id"];
			
			$blogs =  new Blogs();
			$blogInfo = $blogs->getBlogInfo( $blogId );
			$blogSettings = $blogInfo->getSettings();
			$timeDiff = $blogSettings->getValue( "time_offset" );
			
			$date = Timestamp::getDateWithOffset( $date, $timeDiff );

			$comment = new UserComment( $row["article_id"],
										$row['blog_id'],
										$row["parent_id"],
										$row["topic"],
										$row["text"],
										$date,
										$row["user_name"],
										$row["user_email"],
										$row["user_url"],
										$row["client_ip"],
										$row["spam_rate"],
										$row["status"],
										unserialize($row["properties"]),
										$row["id"] );
										
			// set the normalized text and topic
			$comment->setNormalizedText( $row['normalized_text'] );
			$comment->setNormalizedTopic( $row['normalized_topic'] );
			$comment->setType( $row['type'] );
			$comment->setUserId( $row['user_id'] );

			return $comment;
		}
		
		/**
		 * @see Model::getSearchConditions
		 *
		 * @param searchTerms
		 */
		function getSearchConditions( $searchTerms )
		{
			lt_include( PLOG_CLASS_PATH."class/dao/searchengine.class.php" );
			
			$query = SearchEngine::adaptSearchString($searchTerms);

			// MARKWU: I also need to take care when there are multiplu search term

			// Split the search term by space
			$query_array = explode(' ',$query);
		
			$db =& Db::getDb();
			if( $db->isFullTextSupported()) {
				// fast path used when FULLTEXT searches are supported
				$where_string = "(MATCH(c.normalized_text) AGAINST ('{$query}' IN BOOLEAN MODE))";
			}
			else {
				// old and slower path for those cases when they are not
				$where_string = "(";
				$where_string .= "((c.normalized_topic LIKE '%{$query_array[0]}%') OR (c.normalized_text LIKE '%{$query_array[0]}%'))";
				for ( $i = 1; $i < count($query_array); $i = $i + 1) {
					$where_string .= " AND ((c.normalized_topic LIKE '%{$query_array[$i]}%') OR (c.normalized_text LIKE '%{$query_array[$i]}%'))";
				}
				$where_string .= " OR ((c.normalized_topic LIKE '%{$query}%') OR (c.normalized_text LIKE '%{$query}%'))";
				$where_string .= ")";
			}

			return( $where_string );		
		}
		
		/**
		 * Delete all the blog comments
		 */
		function deleteBlogComments( $blogId )
		{
			// we really don't bother about caches or comment, as this method is only being called
			// when deleting a blog, which means that everything is deleted anyway			
			return( $this->delete( "blog_id", $blogId ));
		}
		
		/**
		 * Delete all the article comments
		 *
		 * @param articleId
		 */
		function deleteArticleComments( $articleId )
		{
			lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
			
			$articles = new Articles();
			$art = $articles->getArticle( $articleId );
			
			// there is no article 
			if( !$art )
				return( false );				
			
			if(( $res = $this->delete( "article_id", $articleId ))) {
				// comments deleted successfully, we can update all the counters out there...
				$blog = $art->getBlogInfo();
				// update the counter to reflect the number of comments
				$blog->setTotalComments( $this->getNumItems( $this->getPrefix()."articles_comments", 
				                                             "blog_id = '".Db::qstr($blog->getId())."'".
				                                             " AND type = ".COMMENT_TYPE_COMMENT ));
				// and the one to reflect the number of trackbacks
				$blog->setTotalTrackbacks( $this->getNumItems( $this->getPrefix()."articles_comments", 
				                                             "blog_id = '".Db::qstr($blog->getId())."'".
				                                             " AND type = ".COMMENT_TYPE_TRACKBACK ));				                                              
				
				$blogs = new Blogs();
				$blogs->updateBlog( $blog );
			}
			
			return( $res );
		}

		/**
		 * Search for an identical comment (or trackback) already saved to the database
		 *
		 * @param comment
		 */
        function getIdentical( $comment )
        {
            $query = "SELECT COUNT(id) AS total FROM ".$this->getPrefix()."articles_comments ".
                "WHERE topic = '".Db::qstr($comment->getTopic())."' AND ".
                "text = '".Db::qstr($comment->getText())."' AND ".
                "article_id = '".Db::qstr($comment->getArticleId())."' AND ".
                "parent_id = '".Db::qstr($comment->getParentId())."' AND ".
                "client_ip = '".Db::qstr($comment->getClientIp())."' AND ".
                "user_name = '".Db::qstr($comment->getUserName())."' AND ".
                "user_email = '".Db::qstr($comment->getUserEmail())."' AND ".
                "user_url = '".Db::qstr($comment->getUserUrl())."'";
            if( $comment->type != COMMENT_TYPE_ANY )
                $query .= " AND type = '".Db::qstr($comment->getType())."'";

            $result = $this->Execute( $query );

            if( !$result )
                return false;

            $row = $result->FetchRow();
            $result->Close();
            
            if( $row["total"] >= 1 )
                return true;
            else
                return false;
        }
	}
?>