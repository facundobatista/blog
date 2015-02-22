<?php

	lt_include( PLOG_CLASS_PATH."class/database/dbobject.class.php" );

    /**
	 * \ingroup DAO
     * There is a feature where the owner of a post (or all the users who belong
     * to that blog) can receive an email whenever a new comment is added. This class
     * represents a notification.
     */
    class ArticleNotification extends DbObject 
	{

    	var $_id;
        var $_userId;
        var $_blogId;
        var $_articleId;

        /**
         * Creates a new object
         */
        function ArticleNotification( $userId, $blogId, $articleId, $id = -1 )
        {
			$this->DbObject();
        	$this->_userId = $userId;
            $this->_blogId = $blogId;
            $this->_articleId = $articleId;
            $this->_id = $id;
        }

        function getId()
        {
        	return $this->_id;
        }

        function getBlogId()
        {
        	return $this->_blogId;
        }

        function getArticleId()
        {
        	return $this->_articleId;
        }

        function getUserId()
        {
        	return $this->_userId;
        }
    }
?>