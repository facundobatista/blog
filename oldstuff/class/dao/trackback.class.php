<?php


	lt_include( PLOG_CLASS_PATH."class/dao/usercomment.class.php" );

	/**
     * Represents a trackback item, even though in pLog 1.1 trackbacks are 
	 * nothing else than remote comments and in fact they are even stored in the
	 * same table as comments.
	 *
	 * \ingroup DAO
     */
    class TrackBack extends UserComment 
	{

        /**
         * Constructor. Creates a new trackback object.
         *
         * @param url The permalink of the post.
         * @param title The title of the post.
         * @param articleId The article id to which this trackback item was pointing.
         * @param excerpt An excerpt of the post.
         * @param blogName The name of the blog which is ping backing to us.
         * @param date Date of the post.
		 * @param clientIp the IP address where the trackback is coming from
         * @param id Identifier of this item.
         */
        function TrackBack( $url, $title, $articleId, $blogId, $excerpt, $blogName, $date, $clientIp, $spamRate = 0, $status = COMMENT_STATUS_NONSPAM, $properties = Array(), $id = -1 )
        {
			// initialize the UserComment object
			$this->UserComment( $articleId,
			                    $blogId,
			                    0,
								$title,
								$excerpt,
								$date,
								$blogName,
								'',
								$url,
								$clientIp, 
								$spamRate,
								$status,
								$properties,
								$id );
			// and mark it as a trackback
			$this->setType( COMMENT_TYPE_TRACKBACK );
        }
    }
?>
