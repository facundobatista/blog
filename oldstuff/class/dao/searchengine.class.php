<?php

	lt_include( PLOG_CLASS_PATH."class/dao/articlestatus.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/model.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/searchresult.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/blogstatus.class.php" );			

	define( "SEARCH_ARTICLE", 1 );
	define( "SEARCH_BLOG", 2 );
	define( "SEARCH_GALLERYRESOURCE", 3 );
	    
    /**
	 * \ingroup DAO
	 *
	 * Provides search facilities.
	 *
	 * This class provides methods for searching through articles, comments and custom fields.
	 *
	 * @see Articles
	 * @see ArticleComments
	 * @see CustomFields
     */
    class SearchEngine extends Model
    {
		/**
         * takes the search string as originally input by a user and makes it "better", in the sense
         * that for example converts it to "term1 AND term2" instead of "term1 OR term2" which is the
         * default behaviour. In order to do so, the "+" operator must be added before each one of the
         * search terms as long as it is not already there.
         *
         * @param searchTerms The original search string
         * @return Returns an 'improved' version of the search terms
		 * @static
         */ 
        function adaptSearchString( $searchTerms )
        {
			// load this module only if needed...
			lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );		
			$tf = new Textfilter();
			$resultTerms = $tf->filterCharacters( $searchTerms, Array( '"', ';', '.' ));
            $resultTerms = Db::qstr($resultTerms);			
			
            $resultTerms = trim($resultTerms);
            return $resultTerms;
        }

        /**
         * MARKWU:
         * takes the search string as originally input by a user and makes it "better", in the sense
         * that for example converts it to "term1 AND term2" instead of "term1 OR term2" which is the
         * default behaviour. In order to do so, the "+" operator must be added before each one of the
         * search terms as long as it is not already there. This one is for public use, and will also 
         * convert the search string to a search terms array.
         *
         * @param searchTerms The original search string
         * @return Returns an 'improved' and 'arraized' version of the search terms
         */ 
        function getAdaptSearchTerms ( $searchTerms )
        {
            $resultTerms = Db::qstr($searchTerms);          
            $resultTerms = trim($resultTerms);
            $resultTerms = explode(' ',$resultTerms);
            return $resultTerms;
        }
		
		/**
		 * Searches through articles, custom fields and comments only if enabled
		 *
		 * @param blogId The blog id where we're searching, or -1 if we're doing a global search
		 * @param searchTerms A string containing the search terms
		 * @param status In case we're looking for a particular status
		 * @param includeFuture
		 * @param page Page of results to return
		 * @param itemsPerPage Number of items per page
		 * @return
		 * @see searchArticles
		 * @see searchCustomFields
		 * @see searchComments
		 */
		function search( $blogId, $searchTerms, $status = POST_STATUS_PUBLISHED, $includeFuture = true, $page = -1, $itemsPerPage = -1 )
		{			
			// calculate the conditions right away, they will be used by both sides of the union
			$conds = "";
			if( $blogId != -1 )
				$conds .= " AND a.blog_id = ".Db::qstr( $blogId );
			if( $status != -1 ) 
				$conds .= " AND a.status = ".$status;
			if( !$includeFuture )
				$conds .= " AND a.date < NOW()";

			// first change
			$prefix = $this->getPrefix();
			
			// check if we can use fulltext indexes
			$db =& Db::getDb();
			if( $db->isFullTextSupported()) {
				$query = "(SELECT a.* FROM {$prefix}articles a 
						  INNER JOIN {$prefix}articles_text at ON a.id = at.article_id
				          WHERE MATCH(at.normalized_text, at.normalized_topic) AGAINST ('".Db::qstr($searchTerms)."' IN BOOLEAN MODE) 
				          {$conds})
					 	  UNION
						  (SELECT a.* FROM {$prefix}articles a 
						  INNER JOIN {$prefix}custom_fields_values cfv ON a.id = cfv.article_id
						  WHERE MATCH(cfv.normalized_value) AGAINST ('".Db::qstr($searchTerms)."' IN BOOLEAN MODE) 
						  {$conds})
						  ORDER BY date DESC";
			}
			else {
				$query = "SELECT DISTINCT a.id AS id FROM {$prefix}articles a ".
				         "WHERE ".$this->getArticleSearchConditions( $searchTerms )." {$conds} ORDER BY a.date DESC";
			}

			$result = $this->Execute( $query, $page, $itemsPerPage );

			lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
			$articles = new Articles();

			if( !$result )
				return( Array());

			$results = Array();
			while( $row = $result->FetchRow()) {
				// depending on whether fulltext is available, we will have either fetched all we need so that
				// we can map the row directly into an object, or then we have to use the cache to get all
				// articles one by one
				if( $db->isFullTextSupported()) 
					$results[] = new SearchResult( $articles->mapRow( $row ), SEARCH_RESULT_ARTICLE, $searchTerms );
				else
					$results[] =  new SearchResult( $articles->getArticle( $row["id"] ), SEARCH_RESULT_ARTICLE, $searchTerms );
			}

			return( $results );
		}
		
		/**
		 * Returns the number of search results, but this method only applies to article
		 * searches. If you're interested in getting the number of total matching blogs or
		 * resources, use getNumSiteSearchResults
		 *
		 * @param blogId
		 * @param searchTerms
		 * @param status
		 * @return
		 */
		function getNumSearchResults( $blogId, $searchTerms, $status, $includeFuture = true )
		{
			$prefix = $this->getPrefix();	

			// calculate the additional conditions beforehad
			$conds = "";
			if( $blogId != -1 )
				$conds .= " AND a.blog_id = ".Db::qstr( $blogId );					
			if( $status != -1 )
				$conds .= " AND a.status = ".$status;							
			if( !$includeFuture )
				$conds .= " AND a.date < NOW()";
			
			// check if the db supports fulltext searches and if so act accordingly		
			$db =& Db::getDb();
			if( $db->isFullTextSupported()) {
				// faster path via the fulltext indexes
				$query = "(SELECT COUNT(a.id) AS total FROM {$prefix}articles a 
						  INNER JOIN {$prefix}articles_text at ON a.id = at.article_id
				          WHERE MATCH(at.normalized_text, at.normalized_topic) AGAINST ('".Db::qstr($searchTerms)."' IN BOOLEAN MODE) 
				          {$conds})
					 	  UNION
						  (SELECT COUNT(a.id) AS total FROM {$prefix}articles a 
						  INNER JOIN {$prefix}custom_fields_values cfv ON a.id = cfv.article_id
						  WHERE MATCH(cfv.normalized_value) AGAINST ('".Db::qstr($searchTerms)."' IN BOOLEAN MODE) 
						  {$conds})";
				// execute the query, and it should give us exactly two rows: one per each one of the queries of the union, so 
				// the total amount of posts that match the search condition should be the sum of those two rows
				$result = $this->Execute( $query );
				if( !$result )				
					return 0;
					
				$total = 0;
				while( $row = $result->FetchRow()) {
					$total += $row["total"];
				}
			}
			else {
                    // alternative, slower path
				$query = $this->getArticleSearchConditions( $searchTerms ) . " {$conds} ";
				$total = $this->getNumItems( "{$prefix}articles a", $query, "a.id" );				
			}
			
			return( $total );			
		}
		
		/**
		 * @private
		 * Returns a string that can be used as part of WHERE condition in an SQL query and that will return
		 * all the articles in any blog that match the given search terms. It works by building 3
		 * different queries, one to find all the matching articles, one to find all the matching custom fields
		 * and another one to find all the matching comments and then puts the results together. 
		 */
		function getArticleSearchConditions( $searchTerms )
		{
			lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
			lt_include( PLOG_CLASS_PATH."class/dao/articlecomments.class.php" );
			lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfieldsvalues.class.php" );
			
			$prefix = $this->getPrefix();
			
			// get the search conditions for articles
			$articles = new Articles();
			$articlesConds = $articles->getSearchConditions( $searchTerms );
			
			// now for comments
			$comments = new ArticleComments();
			$tmpCond = $comments->getSearchConditions( $searchTerms );
			$query = "SELECT c.article_id AS article_id FROM {$prefix}articles_comments c 
			          WHERE $tmpCond AND c.status = ".COMMENT_STATUS_NONSPAM;
			$result = $this->Execute( $query );
				
			$ids = Array();
			while( $row = $result->FetchRow()) {
				$ids[] = $row['article_id'];
			}
			$result->Close();
			if ( !empty( $ids ) )
				$commentsConds = 'a.id IN ('.implode( ', ', $ids ).')';
			else
				$commentsConds = 'a.id = -1';							
			
			// and finally for custom fields
			$fields = new CustomFieldsValues();
			$tmpCond = $fields->getSearchConditions( $searchTerms );
			$query = "SELECT v.article_id AS article_id FROM {$prefix}custom_fields_values v WHERE $tmpCond";
			$result = $this->Execute( $query );	
				
			$ids = Array();
			while( $row = $result->FetchRow()) {
				$ids[] = $row['article_id'];
			}
			$result->Close();
			if ( !empty( $ids ) )
				$fieldsConds = 'a.id IN ('.implode( ', ', $ids ).')';
			else
				$fieldsConds = 'a.id = -1';
			
			// and try to build a combined query
			$query = "(({$articlesConds}) OR ({$commentsConds}) OR ({$fieldsConds}))";
			
			return( $query );
		}
		
		/**
		 * performs a site-wide search
		 * @param searchTerms An string with the terms for the search
		 * @param searchType One of the following: SEARCH_ARTICLE, SEARCH_BLOG or SEARCH_GALLERYRESOURCE
		 * @param status The status. Each item has its own constants for status codes, so this value will depend
		 * on what we're looking for.
		 * @param includeFuture Whether to include posts with a future date in the results
		 * @param page The page of results to get
		 * @param itemsPerPage Number of items per page
		 * @return
		 * @see search
		 * @see searchArticles
		 * @see searchCustomFields
		 * @see searchComments
		 */
		function siteSearch( $searchTerms, $searchType, $status = -1, $includeFuture = true, $page = -1, $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
		{
			switch ( $searchType )
			{
				case SEARCH_BLOG:
					if ( $status == -1 ) 
						$status = BLOG_STATUS_ACTIVE;			
					$result = $this->searchBlogs( $searchTerms, $status, $includeFuture, $page, $itemsPerPage );
				break;
				case SEARCH_GALLERYRESOURCE:
					$result = $this->searchGalleryResources( '_all_', $searchTerms, $page, $itemsPerPage );
				break;
				default:
					// search articles and any other value of the $searchType parameter
					if ( $status == -1 ) 
						$status = POST_STATUS_PUBLISHED;
					$result = $this->search( -1, $searchTerms, $status, $includeFuture, $page, $itemsPerPage );
			}
			return $result;				
		}
		
		/**
		 * Returns the total number of matching items, be it articles, blogs or files.
		 *
		 * @param searchTerms An string with the terms for the search
		 * @param searchType One of the following: SEARCH_ARTICLE, SEARCH_BLOG or SEARCH_GALLERYRESOURCE
		 * @param status The status. Each item has its own constants for status codes, so this value will depend
		 * on what we're looking for.
		 * @param includeFuture whether to include future articles in the search
		 * @return The number of matching items for the given search terms
		 */
		function getNumSiteSearchResults( $searchTerms, $searchType, $status = -1, $includeFuture = true )
		{
			if( $searchType == SEARCH_ARTICLE ) {
				if( $status == -1 ) 
					$status = POST_STATUS_PUBLISHED;
				
				$result = $this->getNumSearchResults( -1, $searchTerms, $status, $includeFuture );
			}
			elseif( $searchType == SEARCH_BLOG ) {
				if ( $status == -1 ) 
					$status = BLOG_STATUS_ACTIVE;							
				
				lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
				
				$blogs = new Blogs();
				$result = $blogs->getNumBlogs( $status, ALL_BLOG_CATEGORIES, $searchTerms );
			}
			else {
				lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresources.class.php" );
				
				$resources = new GalleryResources();
				$result = $resources->getNumUserResources( -1,  // owner id
														   GALLERY_NO_ALBUM,   // any album
														   GALLERY_RESOURCE_ANY,   // of any kind
														   $searchTerms   // search terms
														  );													
			}
			
			return( $result );
		}

        /**
         * Returns an array of SearchResult objects containing information about the search, such as the
         * relevance (not very relevant, though :)), and the BlogObject
         *
		 * @param searchTerms An string with the terms for the search
		 * @param blogStatus		
		 * @param page The page of results to get
		 * @param itemsPerPage Number of items per page
         * @return An array of SearchResult objects
         */
        function searchBlogs( $searchTerms, $blogStatus = BLOG_STATUS_ACTIVE, $page = -1, $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
        {
			lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
	
			$blogs = new Blogs();
			$blogsResult = $blogs->getAllBlogs( $blogStatus,  
			                                    ALL_BLOG_CATEGORIES,
			                                    $searchTerms,
                              					$page, 
                               					$itemsPerPage );											
														  
			$results = Array();
			foreach( $blogsResult as $blog ) {
				$results[] = new SearchResult( $blog, SEARCH_RESULT_ARTICLE, $searchTerms );
			}
			
			return( $results );
        }

        /**
         * Returns an array of SearchResult objects containing information about the searchand the GalleryResource object
         *
		 * @param ownerId the id of the blog/user whose pictures we're searching, or "-1" or "_all_" to search through
		 * all of them.
		 * @param searchTerms An string with the terms for the search
		 * @param page The page of results to get
		 * @param itemsPerPage Number of items per page		
         * @return An array of SearchResult objects
         */
        function searchGalleryResources( $ownerId, $searchTerms, $page = -1, $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
        {
			lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresources.class.php" );
	
			$galleryResources = new GalleryResources();
			$galleryResourcesResult = $galleryResources->getUserResources( $ownerId, 
                                   										   GALLERY_NO_ALBUM, 
									                                   	   GALLERY_RESOURCE_ANY,
																	   	   $searchTerms,
									                                   	   $page, 
									                                   	   $itemsPerPage );											
														  
			$results = Array();
			foreach( $galleryResourcesResult as $galleryResource ) {
				$results[] = new SearchResult( $galleryResource, SEARCH_RESULT_GALLERYRESOURCE, $searchTerms );
			}
			
			return( $results );
        }               
    }
?>