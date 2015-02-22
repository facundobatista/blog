<?php

	lt_include( PLOG_CLASS_PATH."class/dao/model.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbum.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresources.class.php" );
	lt_include( PLOG_CLASS_PATH.'class/dao/daocacheconstants.properties.php' );
    
    /**
	 * \ingroup Gallery
	 *
     * Database access for GalleryAlbum objects.
	 *
	 * Please keep in mind that in the context of this library, every album has to have an user who
	 * owns it. When this library is used within pLog, users don't own albums but blogs do so we will
	 * use blog identifier instead of user identifiers. The reason for this change on how things are
	 * called was that this library can also be used outside pLog, and outside pLog we will most likely
	 * not have blogs but users.
	 *
	 * @see GalleryAlbum
	 * @see Model
     */
    class GalleryAlbums extends Model
    {

    	var $_childAlbums;
        var $resources;

		/**
		 * Constructor. Calls the Model constructor and does nothing more.
		 */
    	function GalleryAlbums()
        {
        	$this->Model();
            $this->_childAlbums = Array();
			
			$this->table = $this->getPrefix()."gallery_albums";
        }

        /**
         * Returns an array with all the albums that belong to the given
         * owner
         *
         * @param ownerId The identifier of the owner whose albums we'd like to fetch
         * @param onlyShownAlbums Returns only those albums that have the show_album
         * field set to true, or all of them otherwise
		 * @param page The page we'd like to see
		 * @param itemsPerPage number of items per page
		 * @return An array containing GalleryAlbum objects, representing the
         * albums we fetched from the db.
         */
        function getUserAlbums( $ownerId, $onlyShownAlbums = false, $page = DEFAULT_PAGING_ENABLED, $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
        {
        	$albums = $this->getMany( "owner_id",
        	                          $ownerId,
        	                          CACHE_USERALBUMS,
        	                          Array( CACHE_GALLERYALBUM => "getId" ),
									  Array( "date" => "ASC" ),
									  "",
        	                          $page,
        	                          $itemsPerPage );
        	if( !$albums )
        		return Array();
        		
        	$result = Array();
			if( $onlyShownAlbums ) {
				foreach( $albums as $album ) {
					if( $album->getShowAlbum())
						$result[] = $album;
				}
			}
			else
				$result = $albums;
				
			return( $result );
        }       

		/**
		 * Returns a specific album from the database.
		 *
		 * @param id A valid album identifier.
		 * @param ownerId (optional) The album should have the given id _and_ it should
		 * belong to the given user.
		 * @param fetchResources (optional) Whether at the same time, we should also fetch the
		 * resources that are associated to this album. Set it to 'false' if you only need
		 * to access the album and do not need to use methods such as GalleryAlbum::getResources()
		 * to fetch the resources that have been categorized under this album. It of course
		 * saves resources and database queries.
		 * @param onlyShownAlbums Forces to retrieve only the albums that have been set to appear
		 * in the main page. If set to 'true', we will generate an error if the album exists, 
		 * has the specified id and belongs to the specified user _but_ it is not supposed to be 
		 * shown in the main page.
		 * @return Returns a valid GalleryAlbum object or 'false' if the album could not be found.
		 */
        function getAlbum( $id, $ownerId = -1, $fetchResources = true, $onlyShownAlbums = false )
        {
        	$album = $this->get( "id", $id, CACHE_GALLERYALBUM );
        	if( !$album )
        		return false;
        	if( $ownerId > -1 && $album->getOwnerId() != $ownerId )
        		return false;
        	if( $onlyShownAlbums && !$album->getShowAlbum())
        		return false;
        		
        	return( $album );
        }
		
		/**
		 * Returns a specific album from the database given its 'mangled name'
		 *
		 * @param name An album name
		 * @param ownerId (optional) The album should have the given id _and_ it should
		 * belong to the given user.
		 * @param fetchResources (optional) Whether at the same time, we should also fetch the
		 * resources that are associated to this album. Set it to 'false' if you only need
		 * to access the album and do not need to use methods such as GalleryAlbum::getResources()
		 * to fetch the resources that have been categorized under this album. It of course
		 * saves resources and database queries.
		 * @param onlyShownAlbums Forces to retrieve only the albums that have been set to appear
		 * in the main page. If set to 'true', we will generate an error if the album exists, 
		 * has the specified id and belongs to the specified user _but_ it is not supposed to be 
		 * shown in the main page.
		 * @return Returns a valid GalleryAlbum object or 'false' if the album could not be found.
		 */
		function getAlbumByName( $name, $ownerId = -1, $fetchResources = true, $onlyShownAlbums = false )
        {
			// there might be more than one with the same name...
        	$albums = $this->getMany( "mangled_name", $name, CACHE_GALLERYALBUMBYNAME );
            if(!$albums){
                return false;
            }
			foreach( $albums as $album ) {
				if( $this->check( $album, $ownerId, $onlyShownAlbums )) {
                    return $album;
				}
			}
            
            return false;
		}

		/**
		 * @private
		 */
		function check( $album, $ownerId = -1, $onlyShownAlbums = false )
		{
			if( $ownerId > -1 && $album->getOwnerId() != $ownerId )
				return false;
			if( $onlyShownAlbums && !$album->getShowAlbum())
				return false;
			return( true );
		}
				
		
        /**
         * Returns an array with all the child albums of the given album, but only
         * the ones at the first level (it is not recursive!)
         *
         * @param albumId The album identfitier whose children we'd like to get.
		 * @param ownerId To whom should this album belong.
         * @param onlyShownAlbums Returns only those albums that have the show_album
         * field set to true, or all of them otherwise
         * @return An array of GalleryAlbum objects
         */
        function getChildAlbums( $albumId, $ownerId, $onlyShownAlbums = false )
        {
        	$albums = $this->getUserAlbums( $ownerId, $onlyShownAlbums );
			
			// return an empty array if there are no albums
        	if( !$albums )
        		return Array();
				
        	$result = Array();
        	foreach( $albums as $album ) {
        		if( $album->getParentId() == $albumId )
        			$result[] = $album;
        	}
        	
        	return( $result );
        }

        /**
         * Returns an array with all the parent album ids of the given album
         *
         * @param album The album object whose parents we'd like to get.
         * @return An array of GalleryAlbum Id
         */        
		function getAllParentAlbumIds( $album ) {
		            
		    $currentParentId = $album->getParentId();
		    $parentAlbumsIds = array();
		
		    while ( $currentParentId != 0 ) {
		        $parentAlbumsIds[] = $currentParentId;
		        $parentAlbum = $this->getAlbum( $currentParentId, -1, false, false );
		        $currentParentId = $parentAlbum->getParentId();
		    }
		    return $parentAlbumsIds;
		}
		
        /**
         * Adds an album to the database
         *
		 * @param album A GalleryAlbum object, with all its data filled in
		 * @return Returns true if successful or false otherwise.
		 * @see GAlleryAlbum
         */
		function addAlbum( &$album )
        {        	
        	if(( $result = $this->add( $album ))) {
    	        $this->_cache->removeData( $album->getOwnerId(), CACHE_USERALBUMS );
    	        $this->_cache->removeData( $album->getOwnerId(), CACHE_USERALBUMS_NESTED );    	        
    	        if( $album->getParentId() > 0 ) {
	    	        // update the counters of the parent if there's any
    	        	$parent = $album->getParent();
    	        	if( $parent ) {
	    	        	$parent->setNumChildren( $parent->getNumChildren() + 1 );
    		        	$this->updateAlbum( $parent );
    		        }
    	        }
    	    }
            
            return $result;
        }

        /**
         * updates an album in the db
         *
         * @param album A GalleryAlbum object that already exists in the db.
		 * @param Returns true if successful or false otherwise.
         */
        function updateAlbum( $album )
        {
            if ($album->getId() == $album->getParentId()){
				return false;
			}
			
			if ($album->getParentId() != 0) {
			    $parentAlbums = $this->getAllParentAlbumIds( $this->getAlbum( $album->getParentId(), -1, false, false ) ) ;
			
			    foreach ( $parentAlbums as $parentAlbum ) {
			        if ( $parentAlbum == $album->getId() )
			            return false;
			    }
			}
			
			// load the previous version of this album
			$prevVersion = $this->getAlbum( $album->getId());			
			
			$result = $this->update( $album );
			if( $result ) {
	            // remove the album from the cache
    	        $this->_cache->removeData( $album->getId(), CACHE_GALLERYALBUM );
        	    // remove the cached album hierarchy array
            	$this->_cache->removeData( $album->getOwnerId(), CACHE_USERALBUMS );			
				// and the cache for nested albums too
				$this->_cache->removeData( $album->getOwnerId(), CACHE_USERALBUMS_NESTED );
				
				// update the counters of the parent album
				if( $prevVersion->getParentId() != $album->getParentId()) {
					// increase the counter of the new album
					$parent = $album->getParent();
					if( $parent )
					{
						$parent->setNumChildren( $parent->getNumChildren() + 1 );
						// update the objects
						$this->updateAlbum( $parent );
					}
					// decrease the number of children in the previous parent
					$prevParent = $prevVersion->getParent();
					if( $prevParent )
					{
						$prevParent->setNumChildren( $prevParent->getNumChildren() - 1 );
						// update the objects
						$this->updateAlbum( $prevParent );
					}
				}
			}			

            return( $result );
        }

        /**
         * removes an album from the db
         *
         * @param albumId The album identifier
         * @param userId The user identifier to whom the album belongs (optional)
		 * @return True if successful or false otherwise.
         */
        function deleteAlbum( $albumId, $userId = -1 )
        {
            // remove the cached album hierarchy array 1st .. 
            // too bad we need to load the album before deleting it, but this method
            // won't get called all to often anyway :)
            $album = $this->getAlbum( $albumId );
            if( empty( $album ) )
            	return false;
            	
            if( $userId > -1 )
            	if( $album->getOwnerId() != $userId )
            		return false;
            		
            $this->delete( "id", $albumId );
            $this->_cache->removeData( $album->getOwnerId(), CACHE_USERALBUMS );
            $this->_cache->removeData( $album->getOwnerId(), CACHE_USERALBUMS_NESTED );            
            //$this->delete( "parent_id", $album->getId());
            
            // update the counters
            $parent = $album->getParent();
            if( $parent ) {
	            $parent->setNumChildren( $parent->getNumChildren() - 1 );
	            $this->updateAlbum( $parent );
	        }
            
            return( true );
        }

        /**
         * Removes all the albums from the given ownerId
         *
         * @param ownerId The blog identifier
         */
        function deleteUserAlbums( $ownerId )
        {
            $userAlbums = $this->getUserAlbums( $ownerId, false, -1);

            // remove resources belong to the owner one by one
            foreach( $userAlbums as $album ) {
                $this->deleteAlbum( $album->getId(), $album->getOwnerId() );
            }

            return true;
        }

		/**
		 * returns all the albums of the blog in an array. The key of the array is the
		 * parent id of all the albums in the position, and each position is either an
		 * array with all the albums that share the same parent id or empty if none
		 *
		 * @param userId 
		 * @param albumId - unused
		 * @return An associative array
		 */
		function getUserAlbumsGroupedByParentId( $userId, $albumId = 0 )
		{
            $userAlbums = $this->getUserAlbums( $userId );                        
            $albums = Array();
 
            if( $userAlbums ) {
                foreach( $userAlbums as $album ) {
                    $key = $album->getParentId();
					if( isset($albums["$key"] )) {
						if( $albums["$key"] == "" )
							$albums["$key"] = Array();
					}
                    $albums["$key"][] = $album;                 
                    $ids[] = $album->getId();
                }
            }
            return $albums;
		}
		
		/**
		 * returns the albums of a blog ordered in the same way as they are nested. Please use
		 * $album->getValue("level") to check their proper nesting.
		 *
		 * @param userId
		 */
		function getNestedAlbumList( $userId )
		{
			$nestedAlbums = $this->_cache->getData( $userId, CACHE_USERALBUMS_NESTED );
			if( !$nestedAlbums ) {
				// cache the data for later use... this is quite a costly operation so 
				// it's probably worth caching it!
				$albums = $this->getUserAlbumsGroupedByParentId( $userId );
				$nestedAlbums = $this->_getNestedAlbumList( $albums );
				$this->_cache->setData( $userId, CACHE_USERALBUMS_NESTED, $nestedAlbums );
			}
			
			return $nestedAlbums;
		}
		
		
		/** 
		 * @static
		 * @private
		 */
		function _getNestedAlbumList( $albums, $start = 0, $level = -1 ) 
		{
			$level++;
			if( !array_key_exists( $start, $albums ) || $albums["$start"] == "" )
				return Array();				
				
			foreach( $albums["$start"] as $album ) {
				// do the replacing
				$album->setValue( "level", $level );
				$results[] = $album;
				
				// make a recursive call				
				$tmp = $this->_getNestedAlbumList( $albums, $album->getId(), $level);
				foreach( $tmp as $tmpAlbum )
					$results[] = $tmpAlbum;
			}
			
			return $results;
		}
		
		/**
		 * @see Model::getSearchConditions()
		 */
		function getSearchConditions( $searchTerms )
		{
			$db =& Db::getDb();
			if( $db->isFullTextSupported()) {			
				$query = "MATCH(normalized_name,normalized_description) AGAINST ('".Db::qstr($searchTerms)."')";
			}
			else {
				$query = "name LIKE '%".Db::qstr( $searchTerms )."%' OR normalized_description LIKE '%".Db::qstr( $searchTerms )."%'";
			}
			
			return( $query );
		}
		
		/**
		 * @see Model::mapRow()
		 * @private
		 */
		function mapRow( $row )
		{
        	$album = new GalleryAlbum( $row["owner_id"],
                                       $row["name"],
                                       $row["description"],
                                       $row["flags"],
                                       $row["parent_id"],
                                       $row["date"],
                                       unserialize($row["properties"]),
                                       $row["show_album"],
                                       $row["id"] );
            $album->setNumResources( $row['num_resources'] );
            $album->setNumChildren( $row['num_children'] );
			$album->setMangledName( $row['mangled_name'] );
            
            return $album;
		}
    }
?>