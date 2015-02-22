<?php

	lt_include( PLOG_CLASS_PATH."class/database/dbobject.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/userinfo.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/article.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/articlecommentstatus.class.php" );

    /**
     * This class represents a comment made by a casual user to an article. Please use the getter methods
     * exposed here to get all the information regarding this comment, such as the name of the user who
     * posted it, the email address and even the ip of the machine from which the comment was posted.
	 *
	 * \ingroup DAO
     */
	class UserComment extends DbObject 
	{

		var $_id;
		var $_artid;
		var $_userName;
		var $_userEmail;
		var $_userUrl;
		var $_topic;
		var $_text;
		var $_date;
        var $_parentid;
        var $_clientIp;
		var $_timeStamp;
		var $_postInfo;
        var $_spamRate;
        var $_status;
		var $_type;
		var $_normalizedText;
		var $_normalizedTopic;
		var $_article;
		var $_userId;
		var $_user;
		var $_blogInfo;

        /**
         * Creates a new user comment.
         */
		function UserComment( $artid, $blogId, $parentid, $topic, $text, $date = null, $userName = "", $userEmail = "", $userUrl = "", $clientIp = "0.0.0.0", $spamRate = 0, $status = COMMENT_STATUS_NONSPAM, $properties = Array(), $id = -1 )
		{
			

			$this->_topic = $topic;
			$this->_text  = $text;
			$this->_artid = $artid;
            $this->_parentid = $parentid;

			$this->_userName  = $userName;
			$this->_userEmail = $userEmail;
			$this->setUserUrl( $userUrl ); 

            $this->_clientIp  = $clientIp;

			$this->_id = $id;

            $this->_spamRate = $spamRate;
            $this->_status = $status;

            if( $date == null ) {
	            lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );

            	$t = new Timestamp();
                $date = $t->getTimestamp();
            }

			$this->setDate( $date );			
			$this->_article = null;
						
			// by default, we've got a user comment instead of a trackback
			$this->_type = COMMENT_TYPE_COMMENT;
			
			$this->_blogId = $blogId;
			$this->_sendNotification = 0;
			
			$this->_normalizedTopic = '';
			$this->_normalizedText = '';
			
			$this->setProperties( $properties );
			
			$this->_blogInfo = null;
			
			$this->_fields = Array(
			   "article_id" => "getArticleId",
			   "blog_id" => "getBlogId",
			   "topic" => "getTopic",
			   "text" => "getText",
			   "date" => "getDate",
			   "user_email" => "getUserEmail",
			   "user_url" => "getUserUrl",
			   "user_name" => "getUserName",
			   "parent_id" => "getParentId",
			   "client_ip" => "getClientIp",
			   "send_notification" => "getSendNotification",
			   "status" => "getStatus",
			   "spam_rate" => "getSpamRate",
			   "type" => "getType",
			   "normalized_text" => "getNormalizedText",
			   "normalized_topic" => "getNormalizedTopic",
			   "properties" => "getProperties",
			   "user_id" => "getUserId"
			);
		}

        /**
         * Returns the identifier assigned to this comment in the database.
         *
         * @return Returns an integer value representing the identifier.
         */
		function getId()
		{
			return $this->_id;
		}

        /**
         * Sets a new identifier.
         * @private
         */
		function setId( $newid )
		{
			$this->_id = $newid;
		}

        /**
         * Returns the article identifier to which this comment belongs.
         *
         * @return An integer value representing the article identifier.
         */
		function getArticleId()
		{
			return $this->_artid;
		}

        /**
         * Sets the article identifier.
         * @private
         */
		function setArticleId( $newartid )
		{
			$this->_artid = $newartid;
		}

        /**
         * Returns the name of the user who posted this comment.
         *
         * @return A string representing the name of the user, if any.
         */
		function getUserName()
		{
			return $this->_userName;
		}

        /**
         * Sets the username.
         * @private
         */
		function setUserName( $userName )
		{
			$this->_userName = $userName;
		}

        /**
         * Gets the email address of the user who posted this message, as it was posted by him or her, and if any.
         *
         * @return An string with the email address of the user.
         */
		function getUserEmail()
		{
			return $this->_userEmail;
		}

        /**
         * Sets the email address.
         * @private
         */
		function setUserEmail( $userEmail )
		{
			$this->_userEmail = $userEmail;
		}

        /**
         * Returns the address specified by the user in the post, if any.
         *
         * @return The url as specified by the user.
         */
		function getUserUrl()
		{
			return $this->_userUrl;
		}

        /**
         * Sets the user url.
         * @private
         */
		function setUserUrl( $userUrl )
		{
			// fix from yousung
			if ( $userUrl != null && eregi("http", $userUrl) == null )
				$userUrl = "http://".$userUrl;

            		$this->_userUrl   = $userUrl;
		}

        /**
         * Returns the text of the comment.
         *
         * @return A string with the text of the comment.
         */
		function getText()
		{
			return $this->_text;
		}

        /**
         * Sets the text of the comment.
         * @private
         */
		function setText( $newtext )
		{
			$this->_text = $newtext;
		}

        /**
         * Returns the topic of the comment.
         *
         * @return A string withthe topic of the comment.
         */
		function getTopic()
		{
			return $this->_topic;
		}

        /**
         * Sets the topic of the comment.
         * @private
         */
		function setTopic( $newtopic )
		{
			$this->_topic = $newtopic;
		}

        /**
         * Returns the 14-digit date as specified by the database.
         *
         * @return A 14-digit date, straight from the database.
         */
		function getDate()
		{

			return $this->_date;
		}

        /**
         * Sets a new date
         * @private
         */
		function setDate( $newdate )
		{
            // source necessary source
            lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );

			$this->_date = $newdate;

			// update the Timestamp object so that we can have more information
			$this->_timeStamp = new Timestamp( $this->_date );
		}

        /**
         * Returns a Timestamp object representing the date. The Timestamp class offers commodity
         * methods to access only some parts of the date, and to format it according to our tastes.
         *
         * @preturn A Timestamp object representing the date.
         */
		function getDateObject()
		{ 
			return $this->_timeStamp;
		}
		
        /**
         * Returns the identifier of the comment to which this one was replying.
         *
         * @return The identifier of the comment to which this one was replying, or 0 if it was not replying
         * to any.
         */
        function getParentId()
        {
        	return $this->_parentid;
        }

        /**
         * Sets the parent id.
         * @private
         */
        function setParentId( $parentId )
        {
        	return $this->_parentid;
        }

        /**
         * Alias for the function getDateObject
         *
         * @see getDateObject
         */
        function getTimestamp()
        {
        	return $this->getDateObject();
        }

        /**
         * Returns an string representing the ip of the machine from which this comment
         * was posted. It is not very reliable since that ip could have been through many
         * proxies.
         *
         * @return A string representing the ip address.
         */
        function getClientIp()
        {
        	return $this->_clientIp;
        }

        /**
         * Sets the ip address.
         * @private
         */
        function setClientIp( $clientIp )
        {
        	$this->_clientIp = $clientIp;
        }

		/**
		 * Sets the spamn rate of this comment
		 *
		 * @private
		 * @param spamRate The new spam rate
		 */
        function setSpamRate( $spamRate )
        {
        	$this->_spamRate = $spamRate;
        }

		/**
		 * @return Returns the spam rate of this comment
		 */
        function getSpamRate()
        {
        	return $this->_spamRate;
        }

		/**
		 * Sets the status of this comment
		 *
		 * @param status The new status
		 */
        function setStatus( $status )
        {
        	$this->_status = $status;
        }

		/**
		 * @return Returns the new status of this comment
		 */
        function getStatus()
        {
        	return $this->_status;
        }
		
		/**
		 * sets the Article object to which this comment belongs. WARNING: this value
		 * <b>IS NOT ALWAYS</b> initialized!!! The most common situation is that it is not initialized
		 *
		 * @param article An Article object
		 * @return Always true
		 */ 
		function setArticle( $article )
		{
			$this->_article = $article;
			
			return true;
		}
		
		/**
		 * returns the Article to which this comment belongs, or NULL if it has not been loaded
		 *
		 * @return An Article object or NULL if it has not been initialized
		 */
		function getArticle()
		{
			if( $this->_article == null ) {
				$articles = new Articles();
				$this->_article = $articles->getArticle( $this->_artid );
			}
			
			return( $this->_article );
		}
		
		/**
		 * @return Returns the type of this comment, either COMMENT_TYPE_COMMENT or
		 * COMMENT_TYPE_TRACKBACK depending on whether the comment is a "normal" user comment
		 * or a trackback
		 */
		function getType()
		{
			return( $this->_type );
		}
		
		/**
		 * sets the comment type
		 *
		 * @private
		 * @param type The new type:
		 * - COMMENT_TYPE_COMMENT
		 * - COMMENT_TYPE_TRACKBACK
		 */
		function setType( $type )
		{
			$this->_type = $type;
		}
		
		function getSendNotification()
		{
			return( $this->_sendNotification );
		}
		
		function setSendNotification( $sendNotification )
		{
			if( $sendNotification == "" )
				$sendNotificaton = 0;
			$this->_sendNotification = $sendNotification;
		}
		
		function getBlogId()
		{
			return( $this->_blogId );
		}
		
		function setBlogId( $blogId )
		{
			$this->_blogId = $blogId;
		}


        /**
		 * Alais for getUrl()
		 *
         * Returns the permalink of the post that sent the trackback link.
         *
         * @return A string with the permalink.
         */
        function getUrl()
        {
        	return( $this->getUserUrl());
        }

        /**
		 * Alias for getTopic()
		 *
         * Returns the value of the title parameter, as in the trackback ping request. According to the definition
         * of the trackback ping specification, it can be empty.
         *
         * @return The title of the entry.
         */
        function getTitle()
        {
        	return( $this->getTopic());
        }

        /**
		 * Alias for getText(), which is only used by trackbacks.
		 *
         * According to the specification of the trackback protocol, the excerpt is a short string giving
         * more information about the entry related to the ping. Normally, it will be at most, the first 255
         * characters of the entry, but it could also be empty since it is not mandatory.
         *
         * @return A string representing the excerpt.
         */
        function getExcerpt()
        {
        	return( $this->getText());
        }

        /**
		 * Alias for getUserName()
		 *
         * Returns the name of the blog which sent the trackback ping.
         *
         * @return A string containing the name of the blog which sent the trackback ping.
         */
        function getBlogName()
        {
        	return( $this->getUserName());
        }

		/**
		 * Returns the BlogInfo object to which this comment/trackback is linked
		 *
		 * @param return A BlogInfo object
		 */
		function getBlogInfo()
		{
			if( $this->_blogInfo === null ) {
				lt_include( PLOG_CLASS_PATH."class/dao/bloginfo.class.php" );
				$blogs = new Blogs();
				$this->_blogInfo = $blogs->getBlogInfo( $this->getBlogId());
			}
			
			return( $this->_blogInfo );
		}
		
		/**
		 * sets the normalized text
		 *
		 * @param normalizedText
		 */
		function setNormalizedText( $normalizedText )
		{
			// do a normalization again, just in case
			lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );		
			$this->_normalizedText = TextFilter::normalizeText( $normalizedText );
		}

		/**
		 * returns the normalized text
		 *
		 * @return the normalized text
		 */
		function getNormalizedText()
		{
			if( $this->_normalizedText == "" ) {
				$this->setNormalizedText( $this->getText());
			}
			
			return( $this->_normalizedText );
		}

		/**
		 * sets the normalized topic
		 *
		 * @param normalizedText
		 */		
		function setNormalizedTopic( $normalizedTopic )
		{
			// do a normalization again, just in case		
			lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
			$this->_normalizedTopic = TextFilter::normalizeText( $normalizedTopic );
		}

		/**
		 * returns the normalized text
		 *
		 * @return the normalized text
		 */		
		function getNormalizedTopic()
		{
			if( $this->_normalizedTopic == "" ) {
				$this->setNormalizedTopic( $this->getTopic());
			}
			
			return( $this->_normalizedTopic );
		}
		
		/**
		 * Returns true if this comment was posted by an authenticated user
		 * 
		 * @return True if poster was authenticated or false otherwise
		 */
		function IsPosterAuthenticated()
		{
			return( $this->getUser() != false );
		}
		
		/**
		 * Returns the UserInfo object of the user who posted this comment or false if the comment
		 * wasn't posted by an authenticated user or if the user does not exist anymore
		 *
		 * @return An UserInfo object or false if the user doesn't exist anymore or poster was not
		 * authenticated when posting the comment 
		 */
		function getUser()
		{
			if( $this->_userId != 0 ) {
				lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
				$users = new Users();
				$this->_user = $users->getUserInfoFromId( $this->_userId );
			}
			
			return( $this->_user );
		}
		
		/** 
		 * Returns the id of the user who posted this comment, or '0' if the comment was
		 * posted by a non-authenticated user
		 *
		 * @return Id of the user who posted this comment, '0' if the comment was posted
		 * by a non-authenticated user
		 */
		function getUserId()
		{
			return( $this->_userId );
		}
		
		/**
		 * Sets the user id of the user who posted this comment
		 *
		 * @param id The user id of the poster, or 0 if the comment was posted by 
		 * a non-authenticated user
		 */
		function setUserId( $id )
		{
			$this->_userId = $id;
		}
		
		/**
		 * Sets the UserInfo object containing information about the user who posted this
		 * comment.
		 *
		 * @param userInfo A UserInfo object
		 */
		function setUser( $userInfo )
		{
			$this->_user = $userInfo;
			$this->_userId = $userInfo->getId();
		}
		
		/**
		 * @private
		 */
		function __sleep()
		{
			$this->_userInfo = null;
			return( parent::__sleep());			
		}
	}
?>
