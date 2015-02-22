<?php

	lt_include( PLOG_CLASS_PATH."class/dao/model.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/blogcategory.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/daocacheconstants.properties.php" );	

	/**
	 * \ingroup DAO
	 *
	 * Provides an interface for working with BlogCategory objects
	 */		
	class BlogCategories extends Model
	{
	
		function BlogCategories()
		{
			$this->Model();			
			$this->table = $this->getPrefix()."blog_categories";
		}
		
		/**
		 * loads the given blog category
		 *
		 * @param id
		 * @return false if there was an error loading or a BlogCategory object if it was found
		 */
		function getBlogCategory( $id )
		{
			return( $this->get( "id", $id, CACHE_BLOGCATEGORIES ));
		}
		
		/**
		 * adds a new blog category
		 *
		 * @param category A BlogCategory object
		 * @return true if successful or false otherwise. Upon success, $category->getId() will
		 * return the new id assigned to the object in the db.
		 */
		function addBlogCategory( &$category )
		{
			if( ($result = $this->add( $category, Array( CACHE_BLOGCATEGORIES => "getId" )))) {
				$this->_cache->removeData( "_all_", CACHE_BLOGCATEGORIES_ALL );
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
		function deleteBlogCategory( $id )
		{
			if( ($result = $this->delete( "id", $id ))) {
				$this->_cache->removeData( $id, CACHE_BLOGCATEGORIES );
				$this->_cache->removeData( "_all_", CACHE_BLOGCATEGORIES_ALL );
			}
			
			return( $result );
		}
		
		/**
		 * update a given blog category
		 *
		 * @param category A BlogCategory object
		 * @return True if successful or false otherwise
		 */
		function updateBlogCategory( $category )
		{
			if( ($result = $this->update( $category ))) {
				$this->_cache->removeData( $category->getId(), CACHE_BLOGCATEGORIES );
				$this->_cache->removeData( "_all_", CACHE_BLOGCATEGORIES_ALL );				
			}
			
			return( $result );
		}
		
		/**
		 * returns the total amount of blog categories in the database
		 *
		 * @return an integer
		 */
		function getNumBlogCategories( $searchTerms = "" )
		{
			return( count( $this->getBlogCategories( $searchTerms )));
		}
		
		/**
		 * returns all blog categories
		 *
		 * @param searchTerms		 
		 * @param page
		 * @param itemsPerPage
		 * @return An array of BlogCategory objects or false in case of error
		 */
		function getBlogCategories( $searchTerms = "", $page = -1, $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
		{
			$categories = $this->getAll( "all", 
			                             CACHE_BLOGCATEGORIES_ALL, 
			                             Array( CACHE_BLOGCATEGORIES => "getId" ),
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
		 * @param blogStatus
		 * @return an integer
		 */
		function getNumBlogsCategory( $categoryId, $status = BLOG_STATUS_ALL )
		{
			$cond = "b.blog_category_id = '".Db::qstr( $categoryId )."'";			
			if( $status != BLOG_STATUS_ALL )
				$cond .= " AND status = '".Db::qstr( $status )."'";
			
			return( $this->getNumItems( $this->getPrefix()."blogs", $cond ));
			          
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
			$category = new BlogCategory( $row["name"],
			                              $row["description"],
							    		  unserialize($row["properties"]),
								    	  $row["id"] );
			$category->setNumBlogs( $row["num_blogs"] );
			$category->setNumActiveBlogs( $row["num_active_blogs"] );
				
			return( $category );
		}
	}
?>