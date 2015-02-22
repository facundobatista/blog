<?php

	lt_include( PLOG_CLASS_PATH."class/dao/model.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/mylinkscategory.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/mylinks.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/daocacheconstants.properties.php" );	
	
	define( "MYLINKS_CATEGORIES_NO_ORDER", 0 );
	define( "MYLINKS_CATEGORIES_ALPHABETICAL_ORDER", 1 );
	define( "MYLINKS_CATEGORIES_REVERSE_ALPHABETICAL_ORDER", 2 );
	define( "MYLINKS_CATEGORIES_MOST_LINKS_FIRST", 3 );
	define( "MYLINKS_CATEGORIES_LESS_LINKS_FIRST", 4 );
	define( "MYLINKS_CATEGORIES_LAST_UPDATED_FIRST", 5 );
	define( "MYLINKS_CATEGORIES_LAST_UPDATED_LAST", 6 );
	
    /**
	 * \ingroup DAO
	 *
     * Model for retrieving MyLinksCategory objects from the database
     */
	class MyLinksCategories extends Model 
	{
	
		function MyLinksCategories()
		{
			$this->Model();
			
			$this->table = $this->getPrefix()."mylinks_categories";
		}

        /**
         * Returns the categories for my_links for a given blog
         *
         * @param blogId Identifier of the blog.
		 * @param order
		 * @param searchTerms
		 * @param page
		 * @param itemsPerPage
         */
        function getMyLinksCategories( $blogId,
                                       $order = MYLINKS_CATEGORIES_NO_ORDER, 
									   $searchTerms = "",
                                       $page = -1, 
                                       $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
        {
			/*
			 * :TODO:
			 * Implement sorting here!!!
			 */
			if( $order == MYLINKS_CATEGORIES_ALPHABETICAL_ORDER )
				$order = Array( "name" => "ASC" );
			elseif( $order == MYLINKS_CATEGORIES_REVERSE_ALPHABETICAL_ORDER )
				$order = Array( "name" => "DESC" );
			elseif( $order == MYLINKS_CATEGORIES_MOST_LINKS_FIRST )
				$order = Array( "num_links" => "DESC" );
			elseif( $order == MYLINKS_CATEGORIES_LESS_LINKS_FIRST )
				$order = Array( "num_links" => "ASC" );
			elseif( $order == MYLINKS_CATEGORIES_LAST_UPDATED_FIRST ) 
				$order = Array( "last_modification" => "ASC" );
			elseif( $order == MYLINKS_CATEGORIES_LAST_UPDATED_LAST )
				$order = Array( "last_modification" => "ASC" );
			else
				$order = Array();
			
            $blogCategories = $this->getMany( "blog_id", 
                                             $blogId,
                                             CACHE_MYLINKCATEGORIES_ALL, 
                                             Array( CACHE_MYLINKCATEGORIES => "getId" ),
                                             $order,
											 $searchTerms );
                                             
            if( !$blogCategories )
            	$blogCategories = Array();
				
			// apply the slicing
        	if( $page > -1 ) {
        		// return only a subset of the items
				$start = (($page - 1) * $itemsPerPage );
                $blogCategories = array_slice( $blogCategories, $start, $itemsPerPage );        		
        	}						
                                             
            return( $blogCategories );
        }

        /**
         * Adds a category to the database of link categories.
         *
         * @param linkCategory A MyLinkCategory object with the information we need.
         * @return Returns true if successful or false otherwise.
         */
        function addMyLinksCategory( &$myLinksCategory )
        {
        	if(( $result = $this->add( $myLinksCategory, Array( CACHE_MYLINKCATEGORIES => "getId" )))) {
        		$this->_cache->removeData( $myLinksCategory->getBlogId(), CACHE_MYLINKCATEGORIES_ALL );
        	}
        	
        	return( $result );
        }

        /**
         * Removes a link category from the database.
         *
         * @param categoryId Category identifier.
         * @param blogId Blog identifier.
         * @return True if successful or false otherwise.
         */
        function deleteMyLinksCategory( $categoryId, $blogId )
        {
	
        	$category = $this->getMyLinksCategory( $categoryId, $blogId );
        	if( $category ) {
        		if( $this->delete( "id", $categoryId )) {
        			$this->_cache->removeData( $category->getId(), CACHE_MYLINKCATEGORIES );
        			$this->_cache->removeData( $blogId, CACHE_MYLINKCATEGORIES_ALL );
        		}
        	}
        	else
        		return false;

			return( true );
        }

        /**
         * Removes all the links categories from the given blog
         *
         * @param blogId The blog identifier
         */
        function deleteBlogMyLinksCategories( $blogId )
        {	
			$res = false;
			$categories = $this->getMyLinksCategories( $blogId );
        	if(( $res = $this->delete( "blog_id", $blogId ))) {
        		$this->_cache->removeData( $blogId, CACHE_MYLINKCATEGORIES_ALL );	
				// remove all other categories
				foreach( $categories as $category  ) {
					$this->_cache->removeData( $category->getId(), CACHE_MYLINKCATEGORIES );
				}
			}
			
			return( $res );
        }        

        /**
         * Retrieves a single link category from the database
         *
         * @param categoryId Category identifier.
         * @param blogId Blog identifier.
         * @return The MyLinksCategory object containing the information.
         */
        function getMyLinksCategory( $categoryId, $blogId = 0 )
        {
        	$myLinksCategory = $this->get( "id", $categoryId, CACHE_MYLINKCATEGORIES );

			if( !$myLinksCategory )
				return false;

        	if( $blogId > 0 && $myLinksCategory->getBlogId() != $blogId )
        		return false;

            return $myLinksCategory;
        }

        /**
         * Updates a category.
         *
         * @param category The MyLinkCategory object we're trying to update.
         * @return True if successful or false otherwise.
         */
        function updateMyLinksCategory( $category )
        {
        	if( ($result = $this->update( $category ))) {
        		$this->_cache->removeData( $category->getId(), CACHE_MYLINKCATEGORIES );
        		$this->_cache->removeData( $category->getBlogId(), CACHE_MYLINKCATEGORIES_ALL );
        	}
        	
        	return( $result );
        }

		/**
		 * returns how many link categories a blog has
		 *
		 * @param blogId
		 * @param searchTerms
		 * @return an integer
		 */		
		function getNumMyLinksCategories( $blogId, $searchTerms = "" )
		{
			return( count( $this->getMyLinksCategories( $blogId, MYLINKS_CATEGORIES_NO_ORDER, $searchTerms )));
		}

		/**
		 * marks a category as updated now, changing the last_modfication field to
		 * NOW()
		 *
		 * @param categoryId the id of the category we're trying to update
		 * @return true if successful or false otherwise
		 */
		function updateCategoryModificationDate( $categoryId )
		{
			lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );
			$category = $this->getMyLinksCategory( $categoryId );
			if( $category ) {
				$category->setLastModification( Timestamp::getNowTimestamp());
				return( $this->update( $category ));
			}
			else
				return false;
		}

        /**
         * update last modification field
         */
		function updateLastModification( $categoryId , $lastModification)
		{
			lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );
			$category = $this->getMyLinksCategory( $categoryId );
			if( $category ) {
				$category->setLastModification( Timestamp::getNowTimestamp());
				return( $this->update( $category ));
			}
			else
				return false;
		}
		
		/**
		 * @see Model::getSearchConditions
		 */
		function getSearchConditions( $searchTerms ) 
		{
			return( "name LIKE '%".$searchTerms."%'" );
		}
		
		/**
		 * @private
		 */
		function mapRow( $row )
		{
        	$myLinksCategory = new MyLinksCategory( $row["name"],
                                                    $row["blog_id"],
                                                    $row["num_links"],
													unserialize($row["properties"]),
                                                    $row["id"] );

			$myLinksCategory->setLastModification( new Timestamp( $row["last_modification"] ));
			$myLinksCategory->setNumLinks( $row["num_links"] );

            return $myLinksCategory;
		}
    }
?>