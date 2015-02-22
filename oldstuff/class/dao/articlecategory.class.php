<?php

	lt_include( PLOG_CLASS_PATH."class/database/dbobject.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/articlestatus.class.php" );

    /**
	 * \ingroup DAO
	 *
     * Represents an article category.
     */
	class ArticleCategory extends DbObject 
	{

		var $_id;
		var $_name;
		var $_url;
        var $_blogId;
        var $_numArticles;
        var $_inMainPage;
		var $_parentId;
		var $_parentCategory;
		var $_description;
		var $_lastModification;
		var $_blog;
		var $_mangledName;
		var $_articles;

		/**
		 * Creates an article category.
		 *
		 * @param name The name given to the new category
		 * @param url Not used.
		 * @param blogId The id of the blog to which this category id assigned.
		 * @param inMainPage Whether posts belonging to this category should be shown in the front page of the blog or not.
		 * @param description Description of the category, defaults to nothing.
		 * @param numArticles Number of articles in this category, defaults to '0'.
		 * @param properties Not used.
		 * @param id Id assigned to this category in the database, defaults to '-1' and it is ignored for new categories.
		 * @param lastModification Date when this category was last modified.
		 * @param parentId Id of the parent category, not used as of LifeType 1.1.
		 */
		function ArticleCategory( $name, $url, $blogId, $inMainPage, $description = "", $numArticles = 0, $properties = Array(), $id = -1, $lastModification=null, $parentId = 0)
		{
	        lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );

			$this->DbObject();
			
			$this->_name = $name;
            $this->_url = $url;
			$this->_id = $id;
            $this->_blogId = $blogId;
		    $this->setInMainPage( $inMainPage );
            $this->_numArticles = $numArticles;
			$this->setProperties( $properties );
			$this->_parentId = $parentId;
			$this->_description = $description;
			$this->_lastModification = new Timestamp($lastModification);
			$this->_articles = Array();
			$this->_numArticles = 0;
			$this->_numPublishedArticles = 0;
			$this->_blog = null;
			$this->_mangledName = "";
			
			$this->_pk = "id";
			$this->_fields = Array( "name" => "getName",
			                        "url" => "getUrl",
			                        "blog_id" => "getBlogId",
			                        "parent_id" => "getParentId",
			                        "description" => "getDescription",
			                        "in_main_page" => "isInMainPage",
			                        "last_modification" => "getLastModification",
			                        "properties" => "getProperties",
			                        "num_articles" => "getNumAllArticles",
			                        "num_published_articles" => "getNumPublishedArticles",
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
         * @private
         */
		function setURL( $newURL )
		{
			$this->_url = $newURL;
		}

        /**
         * @private
         */
        function setBlogId( $blogId )
        {
        	$this->_blogId = $blogId;
        }
		
		/**
		 * sets the parent category id
		 *
		 * @param parentId The id of the parent category
		 */
		function setParentId( $parentId )
		{
			$this->_parentId = $parentId;
		}
		
		/**
		 * sets the parent ArticleCategory object
		 *
		 * @param An ArticleCategory object representing the parent category
		 */
		function setParentCategory( $parentCategory )
		{
			$this->_parentCategory = $parentCategory;
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
         * @private
         */
		function getURL()
		{
			return $this->_url;
		}

        /**
         * Returns the identifier of the blog to which this category belongs.
         *
         * @return An integer value containing the identifier to which this category belongs.
         */
        function getBlogId()
        {
        	return $this->_blogId;
		}

        /**
         * Returns how many articles have been categorized under this category.
         *
		 * @param status A valid post status
         * @return An integer value
         */
        function getNumArticles( $status = POST_STATUS_PUBLISHED )
        {
        	if( $status == POST_STATUS_ALL )
        		return( $this->_numArticles );
        	elseif( $status == POST_STATUS_PUBLISHED )
        		return( $this->_numPublishedArticles );
        	else {
				$origStatus = $status;        	
				if( !is_array( $this->_numArticles[$status] ) || $this->_numArticles[$status] == null ) {
					$categories = new ArticleCategories();
					$this->_numArticles[$status] = $categories->getNumArticlesCategory( $this->getId(), $origStatus );
				}
			
				return( $this->_numArticles[$status] );			
        	}
        }
		
		/**
		 * shorthand method for getting the total number of articles in the category, regardless of the
		 * status
		 *
		 * @return the total number of posts
		 */
		function getNumAllArticles()
		{
			return( $this->getNumArticles( POST_STATUS_ALL ));
		}
		
		/**
		 * Returns the number of articles that have a published status in this category
		 *
		 * @return Number of published articles
		 */
		function getNumPublishedArticles()
		{
			return( $this->_numPublishedArticles );
		}

		/**
		 * Sets the number of articles that have a published status in this category. This method
		 * should usually not be called unless we want to mess up counters.
		 *
		 * @private.
		 */		
		function setNumPublishedArticles( $num )
		{
			$this->_numPublishedArticles = $num;
		}

		/**
		 * Sets the number of articles in this category. This method
		 * should usually not be called unless we want to mess up counters.
		 *
		 * @private.
		 */				
		function setNumArticles( $num )
		{
			$this->_numArticles = $num;
		}		
		
		/**
		 * returns the articles categorized here
		 *
		 * @param an array of Article obejcts
		 */
		function getArticles( $status = POST_STATUS_PUBLISHED )
		{
	        lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );

			if( !is_array( $this->_articles[$status] ) || $this->_articles[$status] == null ) {
				$articles = new Articles();
				// you've got to love these huge method calls...
				$this->_articles[$status] = $articles->getBlogArticles( $this->getBlogId(), 
				                                                        -1, 
												            	  	    -1,
															            $this->getId(),
															            $status );
			}
			
			return( $this->_articles[$status] );
		}
		
		/**
		 * returns the id of the parent category
		 *
		 * @return The id of the parent category
		 */
		function getParentId()
		{
			return $this->_parentId;
		}
		
		/**
		 * returns the parent ArticleCategory object
		 *
		 * @return An ArticleCategory object representing the parent category
		 */
		function getParentCategory()
		{
			return $this->_parentCategory;
		} 

        /**
         * Returns true if the category has been configured to be shown in the main
         * page or not.
         *
         * @return True wether the category is shown in main page, or false otherwise.
         */
        function isInMainPage()
        {
        	return $this->_inMainPage;
        }

        /**
         * If set to true, enables the category to be shown in the main page, or false
         * otherwise.
         *
         * @param show True or false, depending wether we'd like to show the category in
         * the main page or not.
         */
        function setInMainPage( $show )
        {
		if( $show )
			$this->_inMainPage = 1;
		else
			$this->_inMainPage = 0;
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
		 * returns the last modification date
		 *
		 * @return A Timestamp object
		 */
		function getLastModification()
		{
			return $this->_lastModification;
		}

		/**
		 * Sets the date in which this category was last modified
		 *
		 * @param newDate a Timestamp object containing the date in which this category was last modified
		 */
        function setLastModification( $newDate )
        {
            $this->_lastModification = $newDate;
        }      
        
		/**
		 * Returns the 'mangled' version of the name of this category, used for generating nicer
		 * links when custom URLs are enabled. It may not work with double-byte languages.
		 *
		 * @return A string containing a 'url-ified' version of the category name
		 */
        function getMangledName()
        {
			if( $this->_mangledName == "" ) {
				lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );			
				$this->_mangledName = Textfilter::urlize( $this->getName());
			}
			
			return( $this->_mangledName );
        }

		/**
		 * Sets the 'mangled' version of the name of this category, used for generating nicer
		 * links when custom URLs are enabled.
		 *
		 * @param mangledName A string containing a 'url-ified' version of the category name
		 */
        function setMangledName( $mangledName, $modify = false )
        {
            if( $modify ) {
                lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
                $mangledName = Textfilter::urlize( $mangledName );
            }
            $this->_mangledName = $mangledName;
        }
		
		/**
		 * Returns the BlogInfo object to which this category belongs
		 *
		 * @return A BlogInfo object
		 */
		function getBlog()
		{
			if( $this->_blog == null ) {
				lt_include( PLOG_CLASS_PATH."class/dao/bloginfo.class.php" );
				$blogs = new Blogs();
				$this->_blog = $blogs->getBlogInfoById( $this->getBlogId());
			}
			
			return( $this->_blog );
		}
		
		/**
		 * @private
		 * Those attributes that should not be serialized (not cached) are set to 
		 * null.
		 */
		function __sleep()
		{
			$this->_parentCategory = null;
			$this->_blog = null;
			$this->_articles = null;
			return( parent::__sleep());
		}
	}
?>