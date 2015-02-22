<?php

	lt_include( PLOG_CLASS_PATH."class/database/dbobject.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/articlestatus.class.php" );

    /**
	 * \ingroup DAO
	 *
     * Represents a global blog category.
     */
	class GlobalArticleCategory extends DbObject 
	{
	
		var $_id;
		var $_name;
		var $_description;
		var $_properties;
		var $_mangledName;
		var $_numArticles;
		var $_numActiveArticles;

		function GlobalArticleCategory( $name, $description = "", $properties = Array(), $id = -1 )
		{
			$this->DbObject();
		
			$this->_id = $id;
			$this->_name = $name;
			$this->_description = $description;
			$this->_properties = $properties;
			$this->_numArticles = 0;
			$this->_numActiveArticles = 0;
			$this->_mangledName = "";
			
			$this->_pk = "id";
			$this->_fields = Array( "id" => "getId",
			                        "name" => "getName",
			                        "description" => "getDescription",
			                        "properties" => "getProperties",
			                        "num_articles" => "getNumArticles",
			                        "num_active_articles" => "getNumActiveArticles",
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
        function getNumArticles( $status = POST_STATUS_ALL )        
        {
        	if( $status == POST_STATUS_ALL )
        		return( $this->_numArticles );
        	elseif( $status == POST_STATUS_PUBLISHED )
        		return( $this->_numActiveArticles );
        	else {
	        	lt_include( PLOG_CLASS_PATH."class/dao/globalarticlecategories.class.php" );        	
    	    	$categories = new GlobalArticleCategories();
        		return( $categories->getNumArticlesCategory( $this->getId(), $status ));
        	}
        }
        
        function setNumArticles( $numArticles )
        {
        	$this->_numArticles = $numArticles;
        }
        
        function getNumActiveArticles()
        {
        	return( $this->_numActiveArticles );
        }
        
        function setNumActiveArticles( $numActiveArticles )
        {
        	$this->_numActiveArticles = $numActiveArticles;
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