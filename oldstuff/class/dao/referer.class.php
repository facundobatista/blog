<?php

	lt_include( PLOG_CLASS_PATH."class/database/dbobject.class.php" );

    /**
	 * \ingroup DAO
     * Represents a record from the 'referers' table, used to keep track
     * of the referers to articles.
     */
    class Referer extends DbObject 
	{

    	var $_url;
        var $_articleId;
        var $_blogId;
        var $_count;
        var $_id;
        var $_date;
        var $_timestamp;

    	/**
         * Constructor. Creates a new constructor object from the given
         * information.
         *
         * @param url The url specified in the referer header.
         * @param articleId The article from which we 'captured' the referer.
         * @param blogId The blog identifier.
         * @param date The date of the hit.
         * @param count How many times this referer has hit the article.
         * @param id Identifier of the referer.
         */
    	function Referer( $url, $articleId, $blogId, $date, $count = 0, $id = 0 )
        {
            lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );

			$this->DbObject();
        	$this->_url = $url;
            $this->_articleId = $articleId;
            $this->_blogId = $blogId;
            $this->_count = $count;
            $this->_id = $id;
            $this->_date = $date;
            $this->_timestamp = new Timestamp( $date );
        }

        /**
         * Returns the url of the referer.
         *
         * @return The url of the referer.
         */
        function getUrl()
        {
        	return $this->_url;
        }

        /**
         * Returns the identifier of the article hit by this referer.
         *
         * @return The article identifier.
         */
        function getArticleId()
        {
        	return $this->_articleId;
        }

        /**
         * Returns the identifier associated to this referer.
         *
         * @return The identifier of this object.
         */
        function getId()
        {
        	return $this->_id;
        }

        /**
         * Returns the number of times this referer has hit this article.
         *
         * @return The number of times.
         */
        function getCount()
        {
        	return $this->_count;
        }

        /**
         * Aliast for getCount()
         */
        function getHits()
        {
        	return $this->getCount();
        }

        /**
         * Returns the blog identifier.
         *
         * @return The blog identifier.
         */
        function getBlogId()
        {
        	return $this->_blogId;
        }

        /**
         * Returns the sql date.
         *
         * @return The date as appears in the database.
         */
        function getDate()
        {
        	return $this->_date;
        }

        /**
         * Returns the Timestamp object representing the date.
         *
         * @return The Timestamp object representing the date.-
         */
        function getDateObject()
        {
        	return $this->_timestamp;
        }        
    }
?>
