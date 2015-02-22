<?php

	lt_include( PLOG_CLASS_PATH."class/dao/model.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articlecategory.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/daocacheconstants.properties.php" );
	
	/* 	
	 * different modes in which the listing of categories can be obtained
	 */
	define( "BLOG_CATEGORIES_DEFAULT_ORDER", 0 );
	define( "BLOG_CATEGORIES_MOST_RECENT_UPDATED_FIRST", 1 );
	define( "BLOG_CATEGORIES_OLDEST_FIRST", 2 );
	define( "BLOG_CATEGORIES_NEWEST_FIRST", 3 );
	define( "BLOG_CATEGORIES_ALPHABETICAL_ORDER", 4 );
	define( "BLOG_CATEGORIES_REVERSE_ALPHABETICAL_ORDER", 5 );
	define( "BLOG_CATEGORIES_MOST_ARTICLES_FIRST", 6 );
	


	/**
	 * \ingroup DAO
	 *	
     * Implementation of the database logic for dealing with article categories. This class offers all the most
	 * common methods: getCategory, addCategory, deleteCategory, etc. 
     */
	class ArticleCategories extends Model
    {

    	/**
         * Constructor, does nothing special.
         */
    	function ArticleCategories()
        {
        	$this->Model();
        	
        	$this->table = $this->getPrefix()."articles_categories";
        }

        /**
         * Returns an article category from the database, given its id
         *
         * @param categoryId Identifier of the category
         * @param blogId The blog identifier to which the category belongs
         * @return an ArticleCategory object representing a category from the database
         */
        function getCategory( $categoryId, $blogId = -1 )
        {
        	$category = $this->get( "id", 
			                        $categoryId, 
        	                        CACHE_ARTICLE_CATEGORIES );
        	if( !$category )
        		return false;
        	if( $blogId > -1 && $category->getBlogId() != $blogId )
        		return false;
        		
        	return( $category );
        }
        
        /**
         * returns a category based on its name
         *
         * @param categoryName
         * @param blogId
         * @return An ArticleCategory object
         */
        function getCategoryByName( $categoryName, $blogId = -1 )
        {
        	$categories = $this->getMany( "mangled_name", 
			                              $categoryName, 
        	                              CACHE_ARTICLE_CATEGORIES_BYNAME,
        	                              Array( CACHE_ARTICLE_CATEGORIES => "getId" ));
										  									
			// no categories
        	if( !$categories )
        		return false;
			if( count( $categories ) == 0 )
				return false;
				
			// there might be more than one in several different blogs...
			$found = false;
			//while( !$found ) {
			foreach( $categories as $category ) {
				//$category = array_pop( $categories );
				if( $blogId > -1 && $category->getBlogId() == $blogId ) {
					$found = true;
					break;
				}
			}
			
			// check if we found something...
			if( $found )
				return( $category );
			else
				return false;
        }
		
		/**
		 * @see Model::getSearchConditions
		 */
		function getSearchConditions( $searchTerms )
		{
			return( "(name LIKE '%".Db::qstr($searchTerms)."%' OR description LIKE '%".Db::qstr($searchTerms)."%')" );
		}
        
        /**
         * Returns the categories that belong to a given blog
         *
         * @param blogId The identifer of the blog from which we want to fetch the
         * categories.
         * @param onlyInMainPage Returns only the categories that have been configured
         * to be shown in the main page (attribute in_main_page is '1') By default
         * it is set to 'false', so that we always return all the categories of the given
         * blog.
		 * @param In which order the list of categories should be returned. It can be any of the following
		 * options:
		 * <ul>
		 * <li>BLOG_CATEGORIES_DEFAULT_ORDER</li>
		 * <li>BLOG_CATEGORIES_MOST_RECENT_UPDATED_FIRST</li>
		 * <li>BLOG_CATEGORIES_OLDEST_FIRST</li>
		 * <li>BLOG_CATEGORIES_NEWEST_FIRST</li>
		 * <li>BLOG_CATEGORIES_ALPHABETICAL_ORDER</li>
		 * <li>BLOG_CATEGORIES_REVERSE_ALPHABETICAL_ORDER</li>
		 * <li>BLOG_CATEGORIES_MOST_ARTICLES_FIRST</li>
		 *</ul>
		 * The default value is BLOG_CATEGORIES_DEFAULT_ORDER
		 * @param searchTerms
		 * @param page
		 * @param itemsPerPage
         * @return An array containing all the categories from a given blog.
         */
        function getBlogCategories( $blogId, 
		                            $onlyInMainPage = false, 
									$order = BLOG_CATEGORIES_DEFAULT_ORDER, 
									$searchTerms = "", 
									$page = -1, 
									$itemsPerPage = 15 )
        {
			switch( $order ) {
				case BLOG_CATEGORIES_MOST_RECENT_UPDATED_FIRST:
					$sorting = Array( "last_modification" => "DESC" );
					break;
				case BLOG_CATEGORIES_OLDEST_FIRST:
					$sorting = Array( "id" => "ASC" );
					break;
				case BLOG_CATEGORIES_NEWEST_FIRST:
					$sorting = Array( "id" => "DESC" );
					break;
				case BLOG_CATEGORIES_ALPHABETICAL_ORDER:
					$sorting = Array( "name" => "ASC" );
					break;
				case BLOG_CATEGORIES_REVERSE_ALPHABETICAL_ORDER:
					$sorting = Array( "name" => "DESC" );
					break;
				case BLOG_CATEGORIES_MOST_ARTICLES_FIRST:
					$sorting = Array( "num_published_articles" => "DESC" );
					break;
				default:
					$sorting = Array();
					break;
			}
			
			$categories = $this->getMany( "blog_id",
			                              $blogId,
			                              CACHE_ARTICLE_CATEGORIES_BLOG,
			                              Array( CACHE_ARTICLE_CATEGORIES => "getId" ),
			                              $sorting,
										  $searchTerms );
			                              
			if( !$categories )
				return Array();
			
			/**
			 * :TODO:
			 * implement the ordering conditions!
			 */

            $result = Array();
            if( $onlyInMainPage && $categories ) {
            	foreach( $categories as $category )
            		if( $category->isInMainPage())
            			$result[] = $category;
            }
            else
            	$result = $categories;
				
			// apply the slicing
        	if( $page > -1 ) {
        		// return only a subset of the items
				$start = (($page - 1) * $itemsPerPage );
                $result = array_slice( $result, $start, $itemsPerPage );        		
        	}			
            	
            return( $result );
		}		
		
		/**
		 * @private
		 */
		function mapRow( $row )
		{
        	$category = new ArticleCategory( $row["name"],
                                             $row["url"],
                                             $row["blog_id"],
                                             $row["in_main_page"],
											 $row["description"],
                                             $row["num_articles"],
											 unserialize($row["properties"]),
                                             $row["id"]);
            $category->setNumArticles( $row["num_articles"] );
            $category->setNumPublishedArticles( $row["num_published_articles"] );
            $category->setLastModification( $row["last_modification"] );
			$category->setMangledName( $row["mangled_name"] );

            return( $category );
		}
		

        /**
         * Adds an article category to the database.
         *
         * @param category A Category object with all the information needed to add it
         * to the database
         * @return False if error or the article category identifier if successful.
		 * @see Category
         */
        function addArticleCategory( &$articleCategory )
        {
            $mangledName = $articleCategory->getMangledName();
            $i = 1;
                // check if there already is a category with the same mangled name
            while($this->getCategoryByName($mangledName,
                                           $articleCategory->getBlogId()))
            {
                $i++;
                    // and if so, assign a new one
                    // if we already tried with blogname+"i" we have
                    // to strip "i" before adding it again!
                $mangledName = substr($mangledName, 0,
                               ($i > 2) ? strlen($mangledName)-strlen($i-1) : strlen($mangledName)).$i;
            }
            $articleCategory->setMangledName($mangledName);

            if(( $result = $this->add( $articleCategory, Array( CACHE_ARTICLE_CATEGORIES => "getId" )))) {
        		$this->_cache->removeData( $articleCategory->getBlogId(), CACHE_ARTICLE_CATEGORIES_BLOG );
				$this->_cache->removeData( $articleCategory->getMangledName(), CACHE_ARTICLE_CATEGORIES_BYNAME );
        	}
        	
        	return( $result );
        }

        /**
         * Removes a category from the database. We should check <b>beforehand</b> that such category
         * does not have any article classified under it, since then we would have problems
         * with the database.
         *
         * @param $categoryId The identifier of the category
         * @param $blogId The blog identifier to which the article belongs
         * @return True if success or false otherwise.
         */
        function deleteCategory( $categoryId, $blogId )
        {
        	$category = $this->getCategory( $categoryId, $blogId );
        	if( $category ) 
        	{
				if ( $this->delete( "id", $categoryId ) ) 
				{
					$this->_cache->removeData( $categoryId, CACHE_ARTICLE_CATEGORIES );
					$this->_cache->removeData( $blogId, CACHE_ARTICLE_CATEGORIES_BLOG );
					$this->_cache->removeData( $category->getMangledName(), CACHE_ARTICLE_CATEGORIES_BYNAME );
					foreach( $category->getArticles() as $article)
					{
						$this->_cache->removeData( $article->getId(), CACHE_ARTICLE_CATEGORIES_LINK );
					}
				}
        		return( true );
        	}
        	else
        		return( false );
        }

        /**
         * Removes all the posts from the given blog
         *
         * @param blogId The blog identifier
         */
        function deleteBlogCategories( $blogId )
        {
        	if(($result = $this->delete( "blog_id", $blogId ))) {
        		$this->_cache->removeData( "all", CACHE_ARTICLE_CATEGORIES_BLOG );
        		/**
        		 * :TODO:
        		 * remove each single cached category!
        		 */
        	}
        	
        	return( $result );
        }

        /**
         * Updates a category.
         *
         * @param category An ArticleCategory object with the information about the category we're
         * going to update.
         * @return True if successful or false otherwise.
         */
        function updateCategory( $category )
        {
			// set the counter fields
			$category->setNumArticles( $this->getNumItems( 
				$this->getPrefix()."article_categories_link", 
				"category_id = '".Db::qstr($category->getId())."'",
				"category_id"
			));
			// number of published articles
			$category->setNumPublishedArticles( $this->getNumItems (
					$this->getPrefix()."article_categories_link acl, ".$this->getPrefix()."articles a",
					"acl.category_id = '".Db::qstr($category->getId())."' and acl.article_id = a.id and a.status = ".POST_STATUS_PUBLISHED,
					"acl.category_id"
			));

            $mangledName = $category->getMangledName();
            $i = 1;
                // check if there already is a category with the same mangled name
            while($existingCategory = $this->getCategoryByName($mangledName,
                                                               $category->getBlogId()))
            {
                    // if we found ourselves, it is okay to keep using this name
                if($existingCategory->getId() == $category->getId())
                    break;

                $i++;
                    // and if so, assign a new one
                    // if we already tried with blogname+"i" we have
                    // to strip "i" before adding it again!
                $mangledName = substr($mangledName, 0,
                               ($i > 2) ? strlen($mangledName)-strlen($i-1) : strlen($mangledName)).$i;
            }
            $category->setMangledName($mangledName);

            if( $result = $this->update( $category )) {
				$this->_cache->removeData( $category->getBlogId(), CACHE_ARTICLE_CATEGORIES_BLOG );
        		$this->_cache->setData( $category->getId(), CACHE_ARTICLE_CATEGORIES, $category );
        	}
        	
        	return( $result );
        }
		
		
		/**
         * returns all the categories that an article has been assigned to an article
         *
         * @param artcleId The artcle id
         * @param blogId The blog id. This is an optiona parameter however, it is recommended to
         * include the blog id parameter in order to speed up the SQL query!
         * @return An aray of ArticleCategories object that were assigned to this article.         
         */
        function getArticleCategories( $articleId, $blogId = -1 )
        {
        	$categoryLinks = $this->_cache->getData( $articleId, CACHE_ARTICLE_CATEGORIES_LINK );

        	if( !$categoryLinks ) {
				$query = "SELECT category_id FROM ".$this->getPrefix()."article_categories_link
				          WHERE article_id = '".Db::qstr( $articleId )."'";
				if(( $result = $this->Execute( $query ))) {
					$categoryLinks = Array();
					while( $row = $result->FetchRow()) {
						$categoryLinks[] = $row["category_id"];
					}
                    $result->Close();			
					$this->_cache->setData( $articleId, CACHE_ARTICLE_CATEGORIES_LINK, $categoryLinks );
				}
				else
					return( Array());
        	}        	

			$categories = Array();
			foreach( $categoryLinks as $categoryLink ) {
				$category = $this->getCategory( $categoryLink );
				if( $category )
					$categories[] = $category;
			}
            return $categories;
        }
        
		/**
		 * returns how many categories a blog has
		 *
		 * @param blogId
		 * @param includeHidden
		 * @param searchTerms 
		 * @return an integer
		 */
		function getBlogNumCategories( $blogId, $includeHidden = false, $searchTerms =  "" )
		{
			$conds = Array();
			if( !$includeHidden )
				$conds[] = "in_main_page = 1";
			if( $searchTerms != "" )
				$conds[] = "(".$this->getSearchConditions( $searchTerms ).")";
			
			$conds[] = "blog_id = '".Db::qstr( $blogId )."'";
				
			$cond = implode( " AND ", $conds );
			
			return( $this->getNumItems( $this->table, $cond ));
		}
    }
?>