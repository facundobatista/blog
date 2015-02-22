<?php

	lt_include( PLOG_CLASS_PATH."class/database/dbobject.class.php" );

    /**
	 * \ingroup Gallery
	 *
     * Models an album, that holds resources. Every album has also several other fields
     * such as id, date of last modification, and the identifier of the parent album
     * to which it belongs (if any) 
	 *
	 * Albums can also be nested therefore every album can have a parent and one or more than
	 * one set of child albums.
     */
	class GalleryAlbum extends DbObject
    {
    	var $_ownerId;
        var $_name;
        var $_description;
        var $_flags;
        var $_parentId;
		var $_parent;
        var $_date;
        var $_id;
        var $_numResources;
        var $_numChildren;
        var $_children;
        var $_resources;
        var $_properties;
        var $_showAlbum;
		var $_mangledName;
		var $_normalizedName;
		var $_normalizedDescription;
		
		/**
		 * Constructor of the class.
		 *
		 * @param ownerId A valid user identifier
		 * @param name A string representing a valid name for the album. Maximum 255 characters
		 * @param description A string representing a valid description for the album. Maximum 65k characters.
		 * @param flags An integer value resulting or binary OR'ing the following constants: 
		 *  - GALLERY_RESOURCE_PREVIEW_AVAILABLE
		 * (no more as of plog 0.3)
		 * @param parentId A valid and existing album identifier. If none, then set this to '0'
		 * @param date A valid date in TIMESTAMP(14) format
		 * @param properties A serialized array of pairs key=>value
		 * @param showAlbum Whether to show this album in the main page or not.
		 * @param id An identifier for this album. It is recommended not to set it.
		 */
    	function GalleryAlbum( $ownerId, $name, $description = '', $flags, $parentId, $date, $properties, $showAlbum, $id = -1 )
        {
            $this->DbObject();
        	$this->_ownerId = $ownerId;
            $this->_name = $name;
            $this->_description = $description;
            $this->_flags = $flags;
            $this->_parentId = $parentId;
			$this->_parent = null;
            $this->_date = $date;
            $this->_id = $id;
            $this->_numResources = 0;
            $this->_numChildren  = 0;
            $this->_showAlbum = $showAlbum;
            $this->_properties = $properties;
            $this->_children = null;
            $this->_resources = null;
			$this->_mangledName = "";
			$this->_normalizedName = "";
			$this->_normalizedDescription = "";
            
            $this->_pk = "id";
            $this->_fields = Array(
                "owner_id" => "getOwnerId",
                "description" => "getDescription",
                "name" => "getName",
                "flags" => "getFlags",
                "parent_id" => "getParentId",
                "date" => "getDate",
                "properties" => "getProperties",
                "show_album" => "getShowAlbum",
                "mangled_name" => "getMangledName",
                "num_resources" => "getNumResources",
                "num_children" => "getNumChildren",
                "normalized_name" => "getNormalizedName",
                "normalized_description" => "getNormalizedDescription"
            );
        }
        
        function setId( $id )
        {
        	$this->_id = $id;
        }

		/**
		 * Returns the owner id
		 *
		 * @return A valid user id
		 */
        function getOwnerId()
        {
        	return $this->_ownerId;
        }

		/**
		 * Returns the album name
		 *
		 * @return A string containing the name of the album
		 */
        function getName()
        {
        	return $this->_name;
        }
		
		/**
		 * Sets the album name
		 *
		 * @param name New name for the album.
		 * @return Always true.
		 */
        function setName( $name )
        {
        	$this->_name = $name;
			
			return true;
        }

		/**
		 * Returns the description of the album.
		 *
		 * @return A string with the description.
		 */
        function getDescription()
        {
        	return $this->_description;
        }

		/**
		 * Sets the description of the album.
		 *
		 * @param description The new description
		 * @return Always true.
		 */
        function setDescription( $description )
        {
        	$this->_description = $description;
			
			return true;
        }

		/**
		 * Returns the identifier of the parent album, or '0' if there is no parent album
		 *
		 * @return An integer >0
		 */
        function getParentId()
        {
        	return $this->_parentId;
        }
        
		/**
		 * Returns the parent album, or null if there is no parent album
		 *
		 * @return A GalleryAlbum object or null if there is no parent
		 */
        function getParent()
        {
        	if( $this->_parent == null ) {
        		lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );
        		$albums = new GalleryAlbums();
        		$this->_parent = $albums->getAlbum( $this->getParentId(), $this->getOwnerId());
        	}
        	
        	return( $this->_parent );
        }

		/**
		 * Sets the parent id of the album. It is important that it is a valid 
		 * parent id, otherwise reading this album will generate unexpected errors
		 * since it won't be possible to find its parent id!
		 *
		 * @param parentId The identifier of the new parent album.
		 * @return Always true.
		 */
        function setParentId( $parentId )
        {
        	$this->_parentId = $parentId;
			
			return true;
        }
		/**
		 * Returns a Timestamp object representing the date in which this album
		 * was created
		 *
		 * @return A Timestamp object
		 */
        function getTimestamp()
        {
		    lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );        
        	return new Timestamp($this->_date);
        }

		/**
		 * Returns the timestamp of the album as it comes from the database, formatted
		 * as a TIMESTAMP(14) value
		 *
		 * @return A String representing a TIMESTAMP(14) date.
		 */
        function getDate()
        {
        	return $this->_date;
        }

		/**
		 * Returns the identifier of this album
		 *
		 * @return Returns a valid identifier or -1 if none has been set yet.
		 */
        function getId()
        {
        	return $this->_id;
        }
		
		/**
		 * Returns the flags that have been set for this album
		 *
		 * @return An integer value containing the OR'ed flags.
		 */
        function getFlags()
        {
        	return $this->_flags;
        }

		/**
		 * Returns an array of GalleryAlbum objects representing all the child
		 * albums that this album has. 
		 *
		 * @return an array of GalleryAlbum objects.
		 */
        function getChildren()
        {
        	if( $this->_children == null ) {
        		$albums = new GalleryAlbums();
        		$this->_children = $albums->getChildAlbums( $this->getId(), $this->getOwnerId());
        	}
        	
			return $this->_children;
        }

		/**
		 * Sets the children albums of this album.
		 *
		 * @param children An array of GalleryAlbum objects containing information about the
		 * children albums of this album.
		 * @return Always true.
		 */
        function setChildren( $children )
        {
        	$this->_children = $children;
			
			return true;
        }
		
		/**
		 * Returns the number of resources that this album has.
		 *
		 * @return An integer value, or '0' if it has none.
		 */
        function getNumResources()
        {
        	return $this->_numResources;
        }
        
		/**
		 * Returns the number of children that this album has.
		 *
		 * @return An integer value, or '0' if it has none.
		 */
        function getNumChildren()
        {
        	return $this->_numChildren;
        }        

		/**
		 * Sets number of children of this album.
		 *
		 * @param numChildren An integer representing the number of children that this
		 * album has.
		 * @return Always true.
		 */
        function setNumChildren( $numChildren )
        {
        	$this->_numChildren = $numChildren;
        }
		
		/**
		 * Returns the number of resoruces that have been categorized under this album.
		 *
		 * @return An integer value, or '0' if it has none.
		 */
        function setNumResources( $numResources )
        {
        	$this->_numResources = $numResources;
        }

		/**
		 * this breaks our rule big time (remember: this little objects resulting
		 * of dao/ classes should _never_ do any database access by themselves... 
		 * all the information they need has already been fetched. However, 
		 * this proved to be here a great performance improvement in the sense that
		 * fetching a single album did not trigger fetchign the whole shebang
		 * and whatnot from the database of albums and resources...
		 * Sometimes, breaking the rules is a good thing :)
		 */
        function getResources()
        {
			if( $this->_resources == null ) {
				$res = new GalleryResources();
				$this->_resources = $res->getUserResources( $this->getOwnerId(), $this->getId());
			}
			
			return $this->_resources;
        }

		/**
		 * Sets the resources of the album.
		 *
		 * @param resources An array of GalleryResource objects that represents the
		 * resources that have been categorized under this album.
		 * @return Always true.
		 */
        function setResources( $resources )
        {
        	$this->_resources = $resources;
			
			return true;
        }

		/**
		 * Returns an array with the properties.
		 *
		 * @return A valid PHP array.
		 */
        function getProperties()
        {
        	return $this->_properties;
        }

		/**
		 * Sets the value of the 'properties' field of this album.
		 *
		 * @param properties A valid PHP array containing a list of
		 * pairs $key=>$value
		 * @return Always true.
		 */
        function setProperties( $properties )
        {
        	$this->_properties = $properties;
			
			return true;
        }

		/**
		 * Returns whether this album has to be shown in the main page or not.
		 *
		 * @return A boolean value.
		 */
        function getShowAlbum()
        {
        	return $this->_showAlbum;
        }

		/**
		 * Sets whether this album has to be shown in the main page or not.
		 *
		 * @param showAlbum A boolean value
		 * @return Always true.
		 */
        function setShowAlbum( $showAlbum )
        {
        	$this->_showAlbum = $showAlbum;
			
			return true;
        }
        
        function getMangledName()
        {
			if( $this->_mangledName == "" ) {
				lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
				$this->_mangledName = Textfilter::urlize( $this->getName());
			}
			
			return( $this->_mangledName );
        }
		
		function setMangledName( $mangledName )
		{
			$this->_mangledName = $mangledName;
		}

		/**
		 * Returns the normailzied name for full text search
		 *
		 * @return A normailzied name
		 */
        function getNormalizedName()
        {
			if( $this->_normalizedName == "" ) {
				lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
				$this->_normalizedName = Textfilter::normalizeText( $this->getName());
			}
			
			return( $this->_normalizedName );
        }

		/**
		 * Sets the normailzied name of the album.
		 *
		 * @param normalized name
		 */
        function setNormalizedName( $normalizedName )
        {
			$this->_normalizedName = $normalizedName;
        }

		/**
		 * Returns the normailzied description for full text search
		 *
		 * @return A normailzied description
		 */
        function getNormalizedDescription()
        {
			if( $this->_normalizedDescription == "" ) {
				lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
				$this->_normalizedDescription = Textfilter::normalizeText( $this->getDescription());
			}
			
			return( $this->_normalizedDescription );
        }

		/**
		 * Sets the normailzied description of the album.
		 *
		 * @param normalized description
		 */
        function setNormalizedDescription( $normalizedDescription )
        {
			$this->_normalizedDescription = $normalizedDescription;
        }
    }
?>