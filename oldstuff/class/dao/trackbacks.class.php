<?php

	lt_include( PLOG_CLASS_PATH."class/dao/commentscommon.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/trackback.class.php" );

    define( 'NO_TRACKBACKS', 'no trackbacks available' );

	/**
     * Implementation of a trackback feature.
     * The technical specifications of the trackback protocol can be found
     * here: http://www.movabletype.org/docs/mttrackback.html.
	 *
	 * \ingroup DAO
	 *
	 * @see CommentsCommon
     */
    class Trackbacks extends CommentsCommon 
	{

    	/**
         * Initializes the connection to the database
         */
    	function Trackbacks()
        {
        	$this->CommentsCommon();
        }

        /**
         * Adds a trackback to the database.
         *
		 * @param A Trackback object
		 * @return Returns true if successful or false otherwise. Also in case it is successful, it will modify
		 * the original object to include the id of the trackback that was added.
         */
		function addTrackback( &$trackback )
        {
			return( CommentsCommon::addComment( $trackback ));
        }

        /**
         * Returns the trackback items for a given article.
         *
         * @param artId The article identifier.
		 * @param status One of these:
		 * - COMMENT_STATUS_ALL
		 * - COMMENT_STATUS_SPAM
		 * - COMMENT_STATUS_NONSPAM
		 * @param page
		 * @param itemsPerPage
         * @return An array of TrackbackItem objects with the information, or false otherwise.
         */
        function getArticleTrackBacks( $artId, $status = COMMENT_STATUS_ALL, $page = DEFAULT_PAGING_ENABLED, $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
        {
			$tbs = CommentsCommon::getPostComments( $artId,  // article id
			                                        COMMENT_ORDER_NEWEST_FIRST,   // no choice of order for trackbacks
													$status,  // spam or not
													COMMENT_TYPE_TRACKBACK,   // we're loading trackbacks
													$page,
													$itemsPerPage );
			return( $tbs );
        }
		
		/**
		 * returns the 'x' most recent trackbacks from a blog
		 *
		 * @param blogId the blog id
		 * @param amount the maximum numer of trackbacks to return. By default, it will return all trackbacks.
		 * @return an array of Trackback objects
		 */
		function getBlogTrackbacks( $blogId, $status = COMMENT_STATUS_ALL, $searchTerms = "", $page = DEFAULT_PAGING_ENABLED, $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
		{
			return( CommentsCommon::getBlogComments( $blogId, 
			                                         COMMENT_ORDER_NEWEST_FIRST,
													 $status, 
			                                         COMMENT_TYPE_TRACKBACK,
													 $searchTerms,
													 $page,
													 $itemsPerPage ));
		}
        
		/**
		 * returns a list of trackbacks given an array with article ids
		 *
		 * @param artIds An array of article ids
		 * @return An array of Trackback objects
		 * @see getArticleTrackback
		 */
        function getArticleTrackBacksByIds( $artIds )
        {
			return( CommentsCommon::getArticleTrackBacksByIds( $artIds, COMMENT_TYPE_TRACKBACK ));
        }
        
        /**
		 * returns a single trackback, identified by its... identifier :)
		 */
		function getTrackBack( $id )
		{
			return( CommentsCommon::getComment( $id, COMMENT_TYPE_TRACKBACK ));
		}

        /**
         * function factored out from the above
         *
         * @private
         * @param row The row with the information
         */
        function _fillCommentInformation( $row )
        {
        	// ---
            // there we go again doing dirty things to the poor trackbacks...
            // ---
            $prefix = $this->getPrefix();
            $date = $row["date"];
            $articleId = $row["article_id"];

            $blogId = $row["blog_id"];            
            $blogs =  new Blogs();
            $blogInfo = $blogs->getBlogInfo( $blogId );
            $blogSettings = $blogInfo->getSettings();
            $timeDiff = $blogSettings->getValue( "time_offset" );
            // now that we've got the time difference, we can
            // calculate what would the "real" date...
            $date = Timestamp::getDateWithOffset( $date, $timeDiff );

        	$trackback = new TrackBack( $row["user_url"],
                                  $row["topic"],
                                  $row["article_id"],
                                  $row["text"],
                                  $row["user_name"],
                                  $date,
								  $row["client_ip"],
								  $row["spam_rate"],
								  $row["status"],
                                  $row["id"] );							
								  
			return( $trackback );
        }

        /**
         * Returns how many trackbacks have been received for the given article
         *
         * @param artId The article identifier.
		 * @status
         * @return The number of trackbacks received pointing to that article,
         */
        function getNumTrackbacksArticle( $artId, $status = COMMENT_STATUS_ALL )
        {
			return( CommentsCommon::getNumPostComments( $artId, $status, COMMENT_TYPE_TRACKBACK ));
        }
		
		/**
		 * removes a trackback from the database
		 *
		 * @param trackbackId
		 * @param articleId
		 * @return True if successful or false otherwise
		 */
		function deleteTrackback( $trackbackId )
		{
			return( CommentsCommon::deleteComment( $trackbackId, COMMENT_TYPE_TRACKBACK ));
		}
    }
?>