<?php

	lt_include( PLOG_CLASS_PATH."class/database/dbobject.class.php" );

    /**
	 * \ingroup DAO
	 * 
     * The links one can add to a blog can also be filed under different categories. This object
     * are the abstraction of those categories and offer methods to check the value of some of
     * its attributes.
     */
    class MyLinksCategory extends DbObject 
	{

		var $_id;
        var $_name;
        var $_blogId;
        var $_numLinks;
		var $_lastModification;

        /**
         * Constructor.
         *
         * @param name Name of the category
         * @param blogId Identifier of the blog to which it belongs
         * @param id Identifier of the link
         */
        function MyLinksCategory( $name, $blogId, $numLinks = 0, $properties = Array(), $id = -1 )
        {
	        lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );

			$this->DbObject();

			$this->_name = $name;
            $this->_blogId = $blogId;
            $this->_numLinks = $numLinks;
            $this->_id = $id;
			$this->setProperties( $properties );
			$this->_lastModification = new Timestamp();
			$this->_links = null;
			$this->_numLinks = 0;
			
			$this->_pk = "id";
			$this->_fields = Array(
			   "name" => "getName",
			   "blog_id" => "getBlogId",
			   "last_modification" => "getLastModification",
			   "properties" => "getProperties",
			   "num_links" => "getNumLinks"
			);
        }

        /**
         * Returns the identifier assigned to this category in the database.
         *
         * @return The identifier assigned to this category in the database.
         */
        function getId()
        {
        	return $this->_id;
        }

        /**
         * Returns the name given to this category.
         *
         * @return A string representing the name of this category.
         */
        function getName()
        {
        	return $this->_name;
        }

        /**
         * Returns the identifier to which this this link category belongs.
         *
         * @return An integer value.
         */
        function getBlogId()
        {
        	return $this->_blogId;
        }

        /**
         * @private
         */
        function setId( $id )
        {
        	$this->_id = $id;
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
        function setBlogId( $blogId )
        {
        	$this->_blogId = $blogId;
        }

        /**
         * Returns how many links have been categorized under this category.
         *
         * @return Number of links.
         */
        function getNumLinks()
        {
        	return $this->_numLinks;
        }

        /**
         * sets the links
         *
         * @param $links
         * @return True
         */
        function setLinks( $links )
        {
        	$this->_links = $links;
        }
		
		/**
		 * adds a link to the category
		 *
		 * @param link A MyLink object
		 * @return nothing
		 */
		function addLink( $link )
		{
			$this->_links[] = $link;
		}

        /**
         * Returns an array of MyLink objects
         *
         * @return Array of MyLink object
         */
        function getLinks()
        {
			if( $this->_links === null ) {
				$myLinks = new MyLinks();
				$categoryLinks = $myLinks->getLinks( $this->getBlogId(), $this->getId());
				$this->setLinks( $categoryLinks );			
			}
			
        	return $this->_links;
        }
		
		/**
		 * sets the last modification date
		 *
		 * @param date
		 * @return nothing
		 */
		function setLastModification( $date )
		{
			$this->_lastModification = $date;
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
		
		function setNumLinks( $numLinks )
		{
			$this->_numLinks = $numLinks;
		}		
    }
?>
