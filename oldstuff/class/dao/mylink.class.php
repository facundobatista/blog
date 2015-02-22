<?php

	lt_include( PLOG_CLASS_PATH."class/database/dbobject.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/mylinkscategory.class.php" );

    /**
	 * \ingroup DAO
	 *
     * Represents one of the links that can be created for each blog. The basic information this
     * object holds is the description, the name, the url and the link category to which they belong.
     */
	class MyLink extends DbObject 
	{

		var $_id;
		var $_blogId;
		var $_description;
        var $_name;
		var $_url;
        var $_categoryId;
        var $_category;
        var $_date;
		var $_rssFeed;

        /**
         * Constructor.
         *
         * @param description Description of the link
         * @param url URL assigned to the link
         * @param blogId Identifier of the blog
         * @param categoryId Category of the link
         * @param id Internal idenfitier of the link
         */
		function MyLink( $name, $description, $url, $blogId, $categoryId, $date = '', $rssFeed, $properties = Array(), $id = -1)
		{
			$this->DbObject();

            $this->_name = $name;
			$this->_description = $description;
			$this->_url  = $url;
			$this->_blogId = $blogId;
            $this->_categoryId = $categoryId;
            $this->_id = $id;
			$this->_rssFeed = $rssFeed;
			if( $date == "" ) {
				lt_include( PLOG_CLASS_PATH."class/data/Date.class.php" );				
				$dateObject = new Date();
				$date = $dateObject->getDate();
			}
            $this->setDate( $date );
			$this->setProperties( $properties );
			
			$this->_pk = "id";
			$this->_fields = Array(
			   "category_id" => "getCategoryId",
			   "url" => "getUrl",
			   "name" => "getName",
			   "description" => "getDescription",
			   "blog_id" => "getBlogId",
			   "rss_feed" => "getRssFeed",
			   "date" => "getDate",
			   "properties" => "getProperties"
			);
		}

        /**
         * @private
         */
        function setName( $name )
        {
        	$this->_name = $name;
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
		function setBlogId( $newBlogId )
		{
			$this->_blogId = $newBlogId;
		}

        /**
         * @private
         */
		function setDescription( $newText )
		{
			$this->_description = $newText;
		}

        /**
         * @private
         */
		function setUrl( $newURL )
		{
			$this->_url = $newURL;
		}

        /**
         * @private
         */
        function setCategoryId( $categoryId )
        {
        	$this->_categoryId = $categoryId;
        }

        /**
         * Returns the name that was given to this link.
         *
         * @return A string value with the name.
         */
        function getName()
        {
        	return $this->_name;
        }

        /**
         * Returns the identifier that this link has in the database.
         *
         * @return An integer value.
         */
		function getId()
		{
			return $this->_id;
		}

        /**
         * Returns the identifier of the blog to which this link belongs.
         *
         * @return An integer value.
         */
		function getBlogId()
		{
			return $this->_blogId;
		}

        /**
         * Returns the description assigned to this link.
         *
         * @return An string with the description.
         */
        function getDescription()
		{
			return $this->_description;
		}

        /**
         * Returns the url to which this link is pointing. Wouldn't be a link
         * without a url, huh? :)
         *
         * @return A string representing the url.
         */
		function getUrl()
		{
			return $this->_url;
		}

        /**
         * The link category identifier this link has been filed under.
         *
         * @return An integer value.
         */
        function getCategoryId()
        {
        	return $this->_categoryId;
		}
		
        function setDate( $newDate )
        {
            lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );

            $this->_date = $newDate;
            $this->_timestamp = new Timestamp( $newDate );
        }
        
        function getMyLinkCategory()
        {        
        	if( !$this->_category ) {
        		lt_include( PLOG_CLASS_PATH."class/dao/mylinkscategories.class.php" );
        		$categories = new MyLinksCategories();
        		$this->_category = $categories->getMyLinksCategory( $this->getCategoryId());
        	}        	
        	
        	return( $this->_category );
        }
        
        function getDate()
        {
            return $this->_date;
        }
        
        function getDateObject()
        {
            return $this->_timestamp;
        }
		
		function getRssFeed()
		{
			return $this->_rssFeed;
		}
		
		function setRssFeed( $rssFeed ) 
		{
			$this->_rssFeed = $rssFeed;
		}
}
?>
