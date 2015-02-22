<?php

	lt_include( PLOG_CLASS_PATH."class/dao/commentscommon.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/usercomment.class.php" );

    /**
	 * \ingroup DAO
	 *
     * Model for the comments each article can have
     */
	class ArticleComments extends CommentsCommon 
	{

    	function ArticleComments()
        {
        	$this->CommentsCommon();
        }

		/**
		 * Retrieves all the comments for a post
		 *
		 * @param artid The article identifier
		 * @param order The order in which comments should be retrieved
		 * @param status The status that the comment should have, use COMMENT_STATUS_ALL for
		 * all possible statuses
		 * @param page
		 * @param itemsPerPage
		 * @return False if error or an array of ArticleComments objects
		 */
		function getPostComments( $artid, $order = COMMENT_ORDER_NEWEST_FIRST, $status = COMMENT_STATUS_ALL, $page = -1, $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
		{
			return( CommentsCommon::getPostComments( $artid, $order, $status, COMMENT_TYPE_COMMENT, $page, $itemsPerPage ));
		}
		
        /**
         * Returns the total number of comments for a post
		 *
		 * @param artId the post id
		 * @param status
		 * @return The number of comments
         */
        function getNumPostComments( $artId, $status = COMMENT_STATUS_ALL )
        {
			return( CommentsCommon::getNumPostComments( $artId, $status, COMMENT_TYPE_COMMENT ));

        }

        /**
         * removes all comments marked as spam from the database
         */
        // TODO: CommentsCommon::purgeSpamComments doesn't exist,
        // maybe copy it from purgedata.class.php?
        function purgeSpamComments()
        {
			return( CommentsCommon::purgeSpamComments( COMMENT_TYPE_COMMENT ));
        }
		
		
		/**
		 * returns a single comment, identified by its... identifier :)
		 */
		function getComment( $id )
		{
			return( CommentsCommon::getComment( $id, COMMENT_TYPE_COMMENT ));
		}
		
		/**
		 * returns the lastest $maxItems comments received in the blog
		 *
		 * @param blogId
		 * @param order
		 * @param status
		 * @param searchTerms,
		 * @param page
		 * @param itemsPerPage
		 * @return An array of ArticleComment objects
		 */
		function getBlogComments( $blogId, $order = COMMENT_ORDER_NEWEST_FIRST, $status = COMMENT_STATUS_ALL, $searchTerms = "", $page = -1, $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
		{
			return( CommentsCommon::getBlogComments( $blogId, $order, $status, COMMENT_TYPE_COMMENT, $searchTerms, $page, $itemsPerPage ));
		}
		
        /**
         * Returns the total number of comments for a given blog
		 *
		 * @param artId the post id
		 * @param status
		 * @param type
		 * @param searchTerms
		 * @return The number of comments
         */		
		function getNumBlogComments( $blogId, $status = COMMENT_STATUS_ALL, $type = COMMENT_TYPE_ANY, $searchTerms = "" )
        {
			return( CommentsCommon::getNumBlogComments( $blogId, $status, $type, $searchTerms ));
		}

	}
?>
