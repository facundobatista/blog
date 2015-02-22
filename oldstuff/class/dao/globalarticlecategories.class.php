<?php

	lt_include( PLOG_CLASS_PATH."class/dao/model.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/globalarticlecategory.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/daocacheconstants.properties.php" );	
	
	/**
	 * \ingroup DAO
	 *
	 * Provides an interface for working with GlobalArticleCategory objects
	 */
	class GlobalArticleCategories extends Model
	{
	
		function GlobalArticleCategories()
		{
			$this->Model();			
			$this->table = $this->getPrefix()."global_articles_categories";
		}
		
		/**
		 * loads the given blog category
		 *
		 * @param id
		 * @return false if there was an error loading or a BlogCategory object if it was found
		 */
		function getGlobalArticleCategory( $id )
		{
			return( $this->get( "id", $id, CACHE_GLOBALCATEGORIES ));
		}
		
		/**
		 * adds a new global article category
		 *
		 * @param category A GlobalArticleCategory object
		 * @return true if successful or false otherwise. Upon success, $category->getId() will
		 * return the new id assigned to the object in the db.
		 */
		function addGlobalArticleCategory( &$category )
		{
			if( ($result = $this->add( $category, Array( CACHE_GLOBALCATEGORIES => "getId" )))) {
				$this->_cache->removeData( "_all_", CACHE_GLOBALCATEGORIES_ALL );
			}
				
			return( $result );
		}
		
		/**
		 * deletes a blog category. Warning: the upper layers must have already made sure that there
		 * are no blogs that point to this blog categories *before* removing it, or else we could have
		 * problems with data integrity.		 
		 *
		 * @param id
		 * @return True if successful, false otherwise
		 */
		function deleteGlobalArticleCategory( $id )
		{
			if( ($result = $this->delete( "id", $id ))) {
				$this->_cache->removeData( $id, CACHE_GLOBALCATEGORIES );
				$this->_cache->removeData( "_all_", CACHE_GLOBALCATEGORIES_ALL );
			}
			
			return( $result );
		}
		
		/**
		 * update a given global article category
		 *
		 * @param category A GlobalArticleCategory object
		 * @return True if successful or false otherwise
		 */
		function updateGlobalArticleCategory( $category )
		{
			if(( $result = $this->update( $category ))) {
				$this->_cache->removeData( $category->getId(), CACHE_GLOBALCATEGORIES );
				$this->_cache->removeData( "_all_", CACHE_GLOBALCATEGORIES_ALL );
			}
			
			return( $result );
		}
		
		/**
		 * returns the total amount of global article categories in the database
		 *
		 * @param searchTerms
		 * @return an integer
		 */
		function getNumGlobalArticleCategories( $searchTerms = "" )
		{
			return( count( $this->getGlobalArticleCategories( $searchTerms )));
		}
		
		/**
		 * returns all blog categories
		 *
		 * @param searchTerms
		 * @param page
		 * @param itemsPerPage
		 * @return An array of BlogCategory objects or false in case of error
		 */
		function getGlobalArticleCategories( $searchTerms = "", $page = -1, $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
		{
			$categories = $this->getAll( "all", 
			                             CACHE_GLOBALCATEGORIES_ALL, 
			                             Array( CACHE_GLOBALCATEGORIES => "getId" ),
			                             Array( "name" => "ASC" ),
										 $searchTerms,
			                             $page,
			                             $itemsPerPage );
	                             
			if( !$categories )
				return( Array());
			                             			                             
			return( $categories );
		}
		
		/**
		 * returns how many blogs have been categorized under this category
		 *
		 * @param categoryId
		 * @param status
		 * @return an integer
		 */
		function getNumArticlesGlobalCategory( $categoryId, $status = ARTICLE_STATUS_ALL )
		{
			$cond = "a.global_category_id = '".Db::qstr( $categoryId )."'";			
			if( $status != BLOG_STATUS_ALL )
				$cond .= " AND status = '".Db::qstr( $status )."'";
			
			return( $this->getNumItems( $this->getPrefix()."articles", $cond ));
			          
		}

		/**
		 * @see Model::getSearchConditions()
		 */
		function getSearchConditions( $searchTerms ) 
		{
			return( "name LIKE '%".Db::qstr( $searchTerms )."%' OR description LIKE '%".Db::qstr( $searchTerms )."%'" );
		}
		
		/**
		 * @private
		 */
		function mapRow( $row )
		{
			$category = new GlobalArticleCategory( $row["name"],
			                              $row["description"],
							    		  unserialize($row["properties"]),
								    	  $row["id"] );
			$category->setNumArticles( $row["num_articles"] );
			$category->setNumActiveArticles( $row["num_active_articles"] );
				
			return( $category );
		}
	}
?>