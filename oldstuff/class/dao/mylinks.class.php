<?php

	lt_include( PLOG_CLASS_PATH."class/dao/model.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/mylink.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/mylinkscategories.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/daocacheconstants.properties.php" );	
	
    /**
	 * \ingroup DAO
	 *
     * Model for the myLinks feature
     */
    class MyLinks extends Model 
	{
	
		function MyLinks()
		{
			$this->Model();
		
			$this->table = $this->getPrefix()."mylinks";
		}

        /**
		 * Retrieves the links of the given blog.
         *
         * @param blogId Identifier of the blog
         * @param categoryId Category of the links
		 * @param searchTerms
		 * @param page
		 * @param itemsPerPage
		 */
		function getLinks( $blogId, $categoryId = 0, $searchTerms = "", $page = -1, $itemsPerPage = 15 )
		{
			$blogLinks = $this->getMany( "blog_id", 
			                             $blogId, 
			                             CACHE_MYLINKS_ALL,
			                             Array( CACHE_MYLINKS => "getId" ),
			                             Array( "date" => "DESC" ),
										 $searchTerms,
			                             $page,
			                             $itemsPerPage );			                             
			    
            if( !$blogLinks )
            	return Array();
            	
			$result = Array();
			if( $categoryId > 0 ) {
				foreach( $blogLinks as $link ) {
					if( $link->getCategoryId() == $categoryId )
						$result[] = $link;
				}
			}
			else
				$result = $blogLinks;			

			return( $result );
		}

		/**
		 * returns how many links a blog has
		 *
		 * @param blogId
		 * @param categoryId
		 * @param searchTerms
		 * @return an integer
		 */
		function getNumLinks( $blogId, $categoryId = 0, $searchTerms = "" )
		{
			return( count( $this->getLinks( $blogId, $categoryId, $searchTerms )));
		}		

        /**
         * Adds a link to the database
         *
         * @param myLink a MyLink object
         * @param blogId The blog id
         * @return True if successful or false otherwise.
         */
        function addMyLink( &$myLink )
        {
        	$result = $this->add( $myLink );
			
			if( $result ) {
				// clean the cache
                $this->_cache->removeData( $myLink->getId(), CACHE_MYLINKS );
                $this->_cache->removeData( $myLink->getBlogId(), CACHE_MYLINKS_ALL );			
				// mark the corresponding link categories as modified now
				$linkCategories = new MyLinksCategories();
				$category = $myLink->getMyLinkCategory();
				$category->setLastModification( Timestamp::getNowTimestamp());
				$category->setNumLinks( $category->getNumLinks() + 1 );
				$linkCategories->updateMyLinksCategory( $category );
			}

            return $result;
        }

        /**
         * Removes a MyLink object from the database.
         *
         * @param linkId The link identifier.
         * @param blogId The blog identifier.
         * @return True if successful or false otherwise.
         */
        function deleteMyLink( $linkId, $blogId )
        {
        	$link = $this->getMyLink( $linkId, $blogId );
        	if( $link ) {
        		$this->delete( "id", $linkId );
	            $this->_cache->removeData( $blogId, CACHE_MYLINKS_ALL );
	            $this->_cache->removeData( $linkId, CACHE_MYLINKS );
				$linkCategories = new MyLinksCategories();
				$linkCategory = $link->getMyLinkCategory();
				$linkCategory->setLastModification( Timestamp::getNowTimestamp());
				$linkCategory->setNumLinks( $linkCategory->getNumLinks() - 1 );				
				$linkCategories->updateMyLinksCategory( $linkCategory );
				return( true );
        	}
        	else
        		return false;
        }

        /**
         * Removes all the posts from the given blog
         *
         * @param blogId The blog identifier
         */
        function deleteBlogMyLinks( $blogId )
        {
        	if( $this->delete( "blog_id", $blogId )) {
	        	return( $this->_cache->removeData( $blogId, CACHE_MYLINKS_ALL ));
        	}
        	else
        		return false;
        }        

        /**
         * Retrieves a single link from disk.
         *
         * @param linkId The link identifier
         * @param blogId The blog identifier
         * @return The MyLink object containing information or false if it didn't exist
         */
        function getMyLink( $linkId, $blogId = -1 )
        {
        	$blogLink = $this->get( "id", $linkId, CACHE_MYLINKS );
        	if( !$blogLink )
        		return false;

			if( $blogId == -1 )
				return $blogLink;
				
			if( $blogLink->getBlogId() == $blogId )
				return $blogLink;
			else
				return false;
        }

        /**
         * Updates a link in the database.
         *
         * @param myLink A MyLink object with the information we'd like to update.
         * @return True if successful or false otherwise.
         */
        function updateMyLink( &$myLink )
        {
			// get the previous version of this link before saving it
			$prevVersion = $this->getMyLink( $myLink->getId());
		
        	$result = $this->update( $myLink );

            if( !$result ) {
            	return false;
            }

            // mark the corresponding link categories as modified now
            $linkCategories = new MyLinksCategories();
            $linkCategories->updateCategoryModificationDate( $myLink->getCategoryId());
			// clean the cache
            $this->_cache->removeData( $myLink->getId(), CACHE_MYLINKS );
            $this->_cache->removeData( $myLink->getBlogId(), CACHE_MYLINKS_ALL );
			// and update the link category counters
			lt_include( PLOG_CLASS_PATH."class/dao/mylinkscategories.class.php" );
			$linkCategories = new MyLinksCategories();
			if( $prevVersion->getCategoryId() != $myLink->getCategoryId()) {
				// add one to the new category
				$newCategory = $myLink->getMyLinkCategory();
				$newCategory->setNumLinks( $newCategory->getNumLinks() + 1 );
				// remove one from the old category
				$oldCategory = $prevVersion->getMyLinkCategory();
				$oldCategory->setNumLinks( $oldCategory->getNumLinks() - 1 );
				// update the categories
				$linkCategories->updateMyLinksCategory( $newCategory );
				$linkCategories->updateMyLinksCategory( $oldCategory );
			}
			
          	return true;
        }
		
		/**
		 * @see Model::getSearchConditions
		 */
		function getSearchConditions( $searchTerms )
		{
			return( "(name LIKE '%".Db::qstr( $searchTerms )."%' OR description LIKE'%".Db::qstr( $searchTerms )."%'".
			        " OR url LIKE '%".Db::qstr( $searchTerms )."%')" );
		}
        
		/**
		 * @private
		 */
        function mapRow( $row )
        {
            $blogLink = new MyLink( $row["name"], 
                                    $row["description"], 
			                        $row["url"], 
			                        $row["blog_id"], 
			                        $row["category_id"], 
									$row["date"], 
									$row["rss_feed"],
									unserialize($row["properties"]),
									$row["id"] );
									
			return $blogLink;

        }
    }
?>