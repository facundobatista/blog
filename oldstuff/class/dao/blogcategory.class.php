<?php

	lt_include( PLOG_CLASS_PATH."class/database/dbobject.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/blogstatus.class.php" );

    /**
	 * \ingroup DAO
	 *
     * Represents a global blog category
     */
	class BlogCategory extends DbObject 
	{
	
		var $_id;
		var $_name;
		var $_description;
		var $_properties;
		var $_mangledName;
		var $_numBlogs;
		var $_numActiveBlogs;

		function BlogCategory( $name, $description = "", $properties = Array(), $id = -1 )
		{
			$this->DbObject();
		
			$this->_id = $id;
			$this->_name = $name;
			$this->_description = $description;
			$this->_properties = $properties;
			$this->_numBlogs = 0;
			$this->_numActiveBlogs = 0;
			
			$this->_pk = "id";
			$this->_fields = Array( "id" => "getId",
			                        "name" => "getName",
			                        "description" => "getDescription",
			                        "properties" => "getProperties",
			                        "num_blogs" => "getNumBlogs",
			                        "num_active_blogs" => "getNumActiveBlogs",
			                        "mangled_name" => "getMangledName" );
		}

        /**
         * @private
         */
		function setId( $newId )
		{
			$this->_id = $newId;
		}

        /**
         * @private
         */
		function setName( $newName )
		{
			$this->_name = $newName;
		}
				
		/**
		 * sets the description
		 *
		 * @param description
		 */
		function setDescription( $desc )
		{
			$this->_description = $desc;
		}

        /**
         * Returns the identifier assigned to this category.
         *
         * @return An integer value with the category number.
         */
		function getId()
		{
			return $this->_id;
		}

        /**
         * Returns the name assigned to the category.
         *
         * @return A string value with the name assigned to the category.
         */
		function getName()
		{
			return $this->_name;
		}

        /**
         * Returns how many articles have been categorized under this category.
         *
		 * @param status A valid post status
         * @return An integer value
         */
        function getNumBlogs( $status = BLOG_STATUS_ACTIVE )        
        {
        	if( $status == BLOG_STATUS_ALL )
        		return( $this->_numBlogs );
        	elseif( $status == BLOG_STATUS_ACTIVE )
        		return( $this->_numActiveBlogs );
        	else {
	        	lt_include( PLOG_CLASS_PATH."class/dao/blogcategories.class.php" );        	
    	    	$categories = new BlogCategories();
        		return( $categories->getNumBlogsCategory( $this->getId(), $status ));
        	}
        }
        
        function setNumBlogs( $numBlogs )
        {
        	$this->_numBlogs = $numBlogs;
			if( $this->_numBlogs < 0 ) 
				$this->_numBlogs = 0;
        }
        
        function getNumActiveBlogs()
        {
        	return( $this->getNumBlogs( BLOG_STATUS_ACTIVE ));
        }
        
        function setNumActiveBlogs( $numActiveBlogs )
        {
        	$this->_numActiveBlogs = $numActiveBlogs;
			if( $this->_numActiveBlogs < 0 )
				$this->_numActiveBlogs = 0;
        }
		
		/**
		 * returns a list with all the blogs that have been categorized under this category
		 *
		 * @return An Array of BlogInfo objects
		 */
		function getBlogs( $status = BLOG_STATUS_ACTIVE )
		{		
		}
		
		/**
		 * returns the description
		 *
		 * @return The description
		 */
		function getDescription()
		{
			return $this->_description;
		}
		
		/**
		 * @private
		 * For future use
		 */
		function getProperties()
		{
			return( $this->_properties );
		}

		function setMangledName( $mangledName )
		{
			$this->_mangledName = $mangledName;
		}
		
		function getMangledName()
		{
			if( $this->_mangledName == "" ) {
				lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
				$this->_mangledName = Textfilter::urlize( $this->getName() );
			}
			
			return( $this->_mangledName );
		}		
	}
?>
