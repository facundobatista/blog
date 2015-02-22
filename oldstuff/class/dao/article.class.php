<?php
    lt_include( PLOG_CLASS_PATH."class/database/dbobject.class.php" );
    lt_include( PLOG_CLASS_PATH.'class/dao/articlestatus.class.php' );
	lt_include( PLOG_CLASS_PATH.'class/dao/articlecommentstatus.class.php' );

    define( 'POST_EXTENDED_TEXT_MODIFIER', '[@more@]' );

    /**
	 * \ingroup DAO
     * This class represents an article from the database, and provides methods to access all its objects.
     */
	class Article extends DbObject 
	{

        /**
         * Private members. Use the getXXX classes to access them
         * @private
         */
		var $_id;
		var $_topic;
		var $_text;
		var $_categoryIds;
        var $_categories;
		var $_user;
		var $_date;
		var $_modificationDate;
		var $_timestamp;
		var $_modificationTimestamp;
		var $_comments;
		var $_userInfo;
		var $_blog;
		var $_blogInfo;
		var $_status;
        var $_totalComments;
		var $_numComments;
        var $_numReads;
        var $_numTrackbacks;
        var $_totalTrackbacks;
        var $_properties;
		var $_timeOffset;
		var $_customFields;
		var $_trackbacks;
		var $_slug;
		var $_nextArticle;
		var $_prevArticle;
		var $_globalCategory;
		var $_inSummary;

        /**
         * Constructor.
         *
         * @param topic Topic of the article
         * @param text Text of the article
         * @param categories An array with the ids of all the categories to which this article belongs
         * @param user Identifier of the user who posted the article
         * @param blog Identifier of the blog to which the article belongs
         * @param status Can be either POST_STATUS_PUBLISHED, POST_STATUS_DRAFT or POST_STATUS_DELETED
         * @param numReads How many times the article has been read.
         * @param properties An associative array containing some settings for the post,
         * such as if comments are enabled/disabled, if it's a private or public post,
         * or its password.
         * @param id If present, the identifier of the article in the database
         */
		function Article( $topic, $text, $categories, $user, $blog, $status, $numReads, $properties = Array(), $slug = "", $id = -1 )
		{
            lt_include( PLOG_CLASS_PATH.'class/data/timestamp.class.php' );

			$this->_topic    = $topic;
			$this->_text     = $text;
			$this->_categoryIds = $categories;
			$this->_categories = null;
			$this->_user     = $user;
			$this->_blog     = $blog;
			$this->_id       = $id;
			$this->_status   = $status;
			$this->_comments = null;
            $this->_totalComments = 0;
            $this->_numComments = 0;
            $this->_numTrackbacks = 0;
            $this->_totalTrackbacks = 0;
            $this->_numReads = $numReads;
			if($slug == "")
                $this->setPostSlug($topic);
			else 
                $this->setPostSlug($slug);
			
			// by default it'll have current time
			$this->_timestamp = new Timestamp();
			$this->_date      = $this->_timestamp->getTimestamp();
			$this->_modificationTimestamp = new Timestamp();
			$this->_modificationDate = $this->_timestamp->getTimestamp();
			//$this->_timestamp = 0;
			//$this->_date = 0;
            $this->_properties = $properties;
			$this->_timeOffset = 0;
			$this->_customFields = null;
			$this->_trackbacks = null;
			$this->_userInfo = null;
			$this->_blogInfo = null;
			$this->_globalCategoryId = 0;
			$this->_inSummary = true;

			$this->_pk = "id";
			$this->_fields = Array(
			    "date" => "getDateWithoutOffset",
			    "modification_date" => "getModificationDateWithoutOffset",
			    "user_id" => "getUserId",
			    "blog_id" => "getBlogId",
			    "status" => "getStatus",
			    "num_reads" => "getNumReads",
			    "properties" => "getProperties",
			    "slug" => "getPostSlug",
			    "num_comments" => "getTotalComments",
			    "num_trackbacks" => "getTotalTrackbacks",
			    "num_nonspam_trackbacks" => "getNumTrackbacks",
			    "num_nonspam_comments" => "getNumComments",
			    "global_category_id" => "getGlobalCategoryId",
				"in_summary_page" => "getInSummary"
			);		
		}

        /**
         * Returns the topic of the article
         * @return The topic
         */
		function getTopic()
		{
			return $this->_topic;
		}

        /**
         * Returns the text of the article
         * @param replace a string, usually html syntax, to "split"
         * the intro and the extended text. Default is <br/>.
         * @return The text
         */
		function getText( $replace = '' )
		{
            if( $replace !== false )
				return str_replace( POST_EXTENDED_TEXT_MODIFIER, $replace, $this->_text );
            else
            	return $this->_text;
		}

		/**
		 * returns the intro text for the post
		 * 
		 * @return The intro text for the post
		 */
        function getIntroText()
        {
        	$postParts = explode( POST_EXTENDED_TEXT_MODIFIER, $this->_text );
            return $postParts[0];
        }
		
        /**
         * sets the intro text
         *
         * @param introText the new intro text
         */
		function setIntroText( $introText )
		{
			$postParts = explode( POST_EXTENDED_TEXT_MODIFIER, $this->_text );
			if( $this->hasExtendedText())
				$this->_text = $introText.POST_EXTENDED_TEXT_MODIFIER.$postParts[1];
			else
				$this->setText( $introText );
				
			return true;
		}

		/**
		 * returns the extended text
		 *
		 * @return the extended text, if any.
		 */
        function getExtendedText()
        {
            $postParts = explode( POST_EXTENDED_TEXT_MODIFIER, $this->_text );
            $extendedText = "";
            if ( count($postParts) > 1 )
            {
                $extendedText = $postParts[1];
            }
            return $extendedText;
        }
		
        /**
         * sets the extended text
         *
         * @param extendedText the new extended text
         */
		function setExtendedText( $extendedText )
		{
			$postParts = explode( POST_EXTENDED_TEXT_MODIFIER, $this->_text );
			$this->_text = $postParts[0].POST_EXTENDED_TEXT_MODIFIER.$extendedText;
			
			return true;
		}		

		/**
		 * returns whether this article has extended text or not
		 *
		 * @return true if this article has extended text or false otherwise
		 */
        function hasExtendedText()
        {
            $extendedText = trim($this->getExtendedText());
            if($extendedText == "<br />" || $extendedText == "<br/>" ||
               $extendedText == "<p/>" || $extendedText == "<p />" ||
               $extendedText == "" ) 
            	return false;
        	else
            	return( strlen($this->getExtendedText()) > 0 );
        }
		
        /** 
         * returns an array with all the category ids that this post has
         *
         * @return An array of category ids
         */
		function getCategoryIds()
		{
		  return $this->_categoryIds;
		}

        /**
         * Returns the ArticleCategory object.
         *
         * @return The ArticleCategory object.
         */
        function getCategories()
        {
        	if( $this->_categories == null ) {
        		lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );        			
				$categories = new ArticleCategories();
                $this->_categories = Array();
        		foreach( $this->getCategoryIds() as $categoryId ) {
        			if(( $category = $categories->getCategory( $categoryId )))
	        			$this->_categories[] = $category;
        		}
        	}
        	
        	return $this->_categories;
        }
		
		/**
		 * this method will return the first category of the article, but it's here only
		 * for compatibility reasons!!! Please use getCategories and then loop through the 
		 * returned array
		 *
		 * @return an ArticleCategory object
		 */
		function getCategory()
		{
            $categories = $this->getCategories();
			return $categories[0];
		}

        /**
         * Returns the identifier assigned to this article.
         *
         * @return An integer value representing the identifier.
         */
		function getId()
		{
			return $this->_id;
		}

        /**
         * The identifier of the user who posted this comment
         *
         * NOTE: This is rather disturbing, i would expect to get a User Object
         * when calling getUser(), i've added getUserId() which is more 
         * precise, but left this one in here to stay backward compatible.
         *
         * @deprecated Use getUserId() instead
         * @return An integer value representing the user idenfitier.
         * @see getUserInfo
         */
		function getUser()
		{
			return $this->_user;
		}

		/**
		 * Returns the identifier of the user who posted this article
		 *
		 * @return An integer
		 */
        function getUserId()
        {
			return $this->_user;
        }

        /**
         * Returns the date, 14-digit format straight from the database.
         *
         * @return The 14-digit representation of the date.
         */
		function getDate()
		{
			return $this->_date;
		}
		
		/**
		 * Return the date without the time offset applied. This is used so that we don't save
		 * the wrong date to the database when updating this post. For general purpose date queries,
		 * you don't need to use this method, please use getDate.
		 *
		 * @return an SQL timestamp
		 * @see getDate
		 */
		function getDateWithoutOffset()
		{
			// get the offset
			$offset = $this->getTimeOffset();
			// and calculate the date without offset
			lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );
			$date = Timestamp::getDateWithOffset( $this->getDate(), -$offset );			
			return( $date );
		}

        /**
         * Returns an array of UserComment objects, with all the comments that have been received for
         * this article.
         *
		 * @param onlyActive return only those comments that are not marked as spam
         * @return An array of UserComment objects.
         * @see UserComment
         */
		function getComments( $status = COMMENT_STATUS_NONSPAM )
		{
			// load the comments if they haven't been loaded yet
			if( is_null( $this->_comments[$status] )) {
				lt_include( PLOG_CLASS_PATH.'class/dao/articlecomments.class.php' );			
				$userComments =  new ArticleComments();
				$blogInfo = $this->getBlogInfo();
				$blogSettings = $blogInfo->getSettings();
				$this->setComments( $userComments->getPostComments( $this->getId(), $blogSettings->getValue( 'comments_order' ), $status ), $status );
			}	
			return( $this->_comments[$status] );
		}
		
        /**
         * Returns an array of Trackback objects, with all the trackbacks that have been received for
         * this article.
         *
		 * @param onlyActive return only those trackbacks that are not marked as spam		 
         * @return An array of Trackback objects.
         * @see Trackback
         */
		function getTrackbacks( $onlyActive = true )
		{
			// load the comments if they haven't been loaded yet
			if( is_null($this->_trackbacks) ) {    
				lt_include( PLOG_CLASS_PATH.'class/dao/trackbacks.class.php' );			
				$trackbacks =  new Trackbacks();
				$blogInfo = $this->getBlogInfo();
				$blogSettings = $blogInfo->getSettings();
				$this->setTrackbacks( $trackbacks->getArticleTrackBacks( $this->getId(), 
                                                                         COMMENT_STATUS_ALL ));
			}

			// if we only want to return the active ones, then we have to loop through
			// the array once more			
			if( $onlyActive ) {
				$tbs = Array();
				foreach( $this->_trackbacks as $trackback ) {
					if( $trackback->getStatus() == COMMENT_STATUS_NONSPAM )
						$tbs[] = $trackback;
				}
			}
			else
				$tbs = $this->_trackbacks;
				
			return( $tbs );
		}		

        /**
         * Returns the UserInfo object containing information about the owner of this post.
         *
         * @return A UserInfo object.
         * @see UserInfo
         */
		function getUserInfo()
		{
			// load the user if it hasn't been loaded yet
			if( is_null($this->_userInfo) ) {
				lt_include( PLOG_CLASS_PATH.'class/dao/users.class.php' );
				$users = new Users();
				$this->setUserInfo( $users->getUserInfoFromId( $this->getUser()));
			}
		
			return $this->_userInfo;
		}


        /**
         * Returns the blog identifier to which this article belongs.
         *
         * NOTE: This is rather disturbing, i would expect to get a Blog Object
         * when calling getBlog(), i've added getBlogId() which is more 
         * precise, but left this one in here to stay backward compatible.
         *
         * @deprecated Use getBlogId() instead
         * @return An integer value representing the blog identifier.
         * @see getBlogInfo
         */
		function getBlog()
		{
			return $this->_blog;
		}

        /**
         * Returns the blog identifier to which this article belongs.
         *
         * @return An integer value representing the blog identifier.
         * @see getBlogInfo
         */
        function getBlogId()
        {
			return $this->_blog;
        }

        /**
         * Returns the status of the article:
         * <ul><li>published</li><li>draft</li><li>deleted</li></ul>
         *
         * @return A string value representing the status of the post.
         */
		function getStatus()
		{
			return $this->_status;
		}

        /**
         * Returns the BlogInfo object with information regarding the blog to which this
         * article belongs.
         *
         * @return The BlogInfo object.
         * @see BlogInfo
         */
		function getBlogInfo()
		{
			if( $this->_blogInfo == null ) {
				lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
				$blogs = new Blogs();
				$this->_blogInfo = $blogs->getBlogInfo( $this->getBlogId());
			}
			
			return( $this->_blogInfo );
		}

        /**
         * Returns the number of times this article has been visited.
         *
         * @return An integer value representing the number of times this article has been read.
         */
        function getNumReads()
        {
        	return $this->_numReads;
        }

        /**
         * Returns the properties array
         *
         * @return An associative array
         */
        function getProperties()
        {
        	return $this->_properties;
        }

        /**
         * returns whether comments are enabled or not
         *
         * @return true if comments are enabled for this post or false otherwise
         */
        function getCommentsEnabled()
        {
        	if( isset( $this->_properties["comments_enabled"] ) )
        		$commentsEnabled = $this->_properties["comments_enabled"];
        	else
				$commentsEnabled = "";
				
            if( $commentsEnabled == "" )
            	$commentsEnabled = false;

            return $commentsEnabled;
        }

        /**
         * @private
         */
		function setUserInfo( $newUserInfo )
		{
			$this->_userInfo = $newUserInfo;
		}

        /**
         * @private
         */
		function setTopic( $newTopic )
		{
			$this->_topic = $newTopic;
		}

        /**
         * @private
         */
		function setText( $newText )
		{
			$this->_text = $newText;
		}

        /**
         * Changes the category id
         * @private
         */
		function setCategoryIds( $newCategories )
		{
			$this->_categoryIds = $newCategories;
		}

        /**
         * @private
         */
        function setCategories( $categories )
        {
        	$this->_categories = $categories;
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
		function setUser( $newUser )
		{
			$this->_user = $newUser;
		}

        /**
         * @private
         */
		function setComments( $comments, $status = COMMENT_STATUS_NONSPAM )
		{
			if( !isset( $this->_comments[$status] ))
				$this->_comments[$status] = Array();
				
			$this->_comments[$status] = $comments;
			
			return true;
		}
		
        /**
         * @private
         */
		function setTrackbacks( $trackbacks )
		{
			$this->_trackbacks = $trackbacks;
			if( !is_array( $this->_trackbacks ))
				$this->_trackbacks = Array();
			
			return true;
		}
		

        /**
         * @private
         */
		function setBlog( $blog )
		{
			$this->_blog;
		}

        /**
         * @private
         */
		function setStatus( $status )
		{
			$this->_status = $status;
		}

        /**
         * Returns true wether this post has the 'DRAFT' status.
         *
         * @return True if the post is a draft or false otherwise.
         */
        function isDraft()
        {
        	return( $this->_status == POST_STATUS_DRAFT );
        }

        /**
         * Returns true wether this post has been deleted or not (status == DELETED)
         *
         * @return True if the post as deleted or false otherwise.
         */
        function isDeleted()
        {
        	return( $this->_status == POST_STATUS_DELETED );
        }

        /**
         * Returns true wether this post has been published or not (status == PUBLISHED)
         *
         * True if the post has been published or false otherwise.
         */
        function isPublished()
        {
        	return( $this->_status == POST_STATUS_PUBLISHED );
        }

        /**
         * Returns the total number of comments that this post has (only the number!!)
         *
         * @return An integer containing the total number of comments that this post has.
         */
		function getTotalComments()
		{
			return( $this->_totalComments );
		}
		
        /**
         * Returns the number of comments that this post has (only the number!!)
         *
         * @return An integer containing the number of comments that this post has.
         */
		function getNumComments()
		{
			return( $this->_numComments );
		}		

        /**
         * Returns the total number of trackback pings that this post has received.
         *
         * @return An integer representing the number of trackback pings.
         */
		function getTotalTrackbacks()
		{
			return( $this->_totalTrackbacks );
		}
		
        /**
         * Returns the number of trackback pings that this post has received.
         *
		 * @param onlyActive return only the number of active (as in non-spam, etc)		 
         * @return An integer representing the number of trackback pings.
         */		
		function getNumTrackbacks()
		{
			return( $this->_numTrackbacks );
		}

        /**
		 * sets the total number of active comments
		 *
         * @private
         */
        function setTotalComments( $totalComments )
        {
        	$this->_totalComments = $totalComments;
        }
        
        /**
         * @private
         */
        function setNumComments( $numComments )
        {
        	$this->_numComments = $numComments;
        }
		
        /**
         * @private
         */
        function setNumTrackbacks( $numTrackbacks )
        {
        	$this->_numTrackbacks = $numTrackbacks;
        }
        
        /**
         * @private
         */
        function setTotalTrackbacks( $totalTrackbacks )
        {
        	$this->_totalTrackbacks = $totalTrackbacks;
        }        

        /**
         * @private
         */
		function setDate( $newDate )
		{
            lt_include( PLOG_CLASS_PATH.'class/data/timestamp.class.php' );

			$this->_date = $newDate;

			$this->_timestamp = new Timestamp( $newDate );
		}
		
		/**
		 * Sets the date in which this article was last modified.
		 *
		 * @param newDate a 14-digit date in SQL's TIMESTAMP format.
		 */
		function setModificationDate( $newDate )
		{
			$this->_modificationDate = $newDate;
			$this->_modificationTimestamp = new Timestamp( $newDate );
		}
		
		/**
		 * Returns the date when the post was last modified
		 *
		 * @return an SQL timestamp
		 */
		function getModificationDate()
		{
			return( $this->_modificationDate );
		}
		
		/**
		 * Return the date without the time offset applied. This is used so that we don't save
		 * the wrong date to the database when updating this post. For general purpose date queries,
		 * you don't need to use this method, please use getDate.
		 *
		 * @return an SQL timestamp
		 * @see getDate
		 */
		function getModificationDateWithoutOffset()
		{
			// get the offset
			$offset = $this->getTimeOffset();
			// and calculate the date without offset
			lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );
			$date = Timestamp::getDateWithOffset( $this->getModificationDate(), -$offset );			
			return( $date );
		}

		/**
		 * Returns the date when the post was last modified as a Timestamp object
		 *
		 * @return a Timestamp object
		 * @see Timestamp
		 */		
		function getModificationTimestamp()
		{
			return( $this->_modificationTimestamp );
		}

        /**
         * Returns the Timestamp object representing the date when this post was posed.
         *
         * @see Timestamp
         * @return A Timestamp object representing the date.
         */
		function getDateObject()
		{
			return $this->_timestamp;
		}

        /**
         * @private
         */
		function setBlogInfo( $blogInfo )
		{
			$this->_blogInfo = $blogInfo;
		}

        /**
         * @private
         */
        function setNumReads( $numReads )
        {
        	$this->_numReads = $numReads;
        }

        /**
         * sets a new Timestamp object for this post, so that we can
         * change the date of the post if necessary.
         */
        function setDateObject( $newTimestamp )
        {
        	$this->_timestamp = $newTimestamp;
            $this->_date      = $this->_timestamp->getTimestamp();
        }

		/**
		 * @private
		 */
        function setProperties( $properties )
        {
        	$this->_properties = $properties;
        }

        /**
         * enables or disables comments
         *
         * @param commentsEnabled Set it to true to enable comments or to false to disable them
         */
        function setCommentsEnabled( $commentsEnabled )
        {
        	$this->_properties["comments_enabled"] = $commentsEnabled;

            return true;
        }
		
        /**
         * sets the time offset
         *
         * @param timeOffset
         */
		function setTimeOffset( $timeOffset )
		{
			$this->_timeOffset = $timeOffset;
		}
		
		/**
		 * gets the time offset
		 *
		 * @return the time offset
		 */
		function getTimeOffset()
		{
			return $this->_timeOffset;
		}
		
		/**
		 * Sets the values that this post has for the custom fields that have been defined so far.
		 *
		 * @param fields An array of CustomFieldValue objects containing some information about the
		 * type of the field, and its value
		 * @see CustomFieldValues
		 * @see CustomFieldValue
		 * @see CustomFieldDefinitions
		 * @see CustomFieldDefinition
		 * @return Always true
		 */
		function setFields( $fields )
		{
			$this->_customFields = $fields;
			
			return true;
		}
		
		/**
		 * Returns the array of CustomFieldValue objects
		 *
		 * @return An array of CustomFieldValue objects
		 */
		function getCustomFields()
		{
			if( is_null( $this->_customFields )) {
                lt_include( PLOG_CLASS_PATH.'class/dao/customfields/customfieldsvalues.class.php' );	
				// get the custom fields
				$customFields = new CustomFieldsValues();
				$fields = $customFields->getArticleCustomFieldsValues( $this->getId(), $this->getBlog());
				$this->setFields( $fields );
			}
			
			return $this->_customFields;
		}
		
		/**
		 * Returns a particular CustomFieldValue object
		 *
		 * @param fieldName
		 * @return A CustomFieldValue object
		 */
		function getFieldObject( $fieldName )
		{			
			// if fields haven't been loaded yet, do so now
			//if( is_null($this->_customFields) )
			//if( !is_array( $this->_customFields ))
				$this->getCustomFields();
		
			if ( !array_key_exists(  $fieldName, $this->_customFields )) {
				return null;
			}
			
			return $this->_customFields["$fieldName"];
		}
		
		/**
		 * sets a particular CustomFieldValue object
		 *
		 * @param fieldValue
		 * @return always true
		 */
		function setFieldObject( $fieldValue )
		{
			// if fields haven't been loaded yet, do so now
			if( is_null($this->_customFields) )
				$this->getCustomFields();		
		
			$fieldName = $fieldValue->getName();
			$this->_customFields["$fieldName"] = $fieldValue;
			
			return true;
		}
		
		/**
		 * Returns the description of the given f ield
		 *
		 * @param fieldName
		 * @return A string
		 */ 
		function getFieldDescription( $fieldName )
		{
			$field = $this->getFieldObject( $fieldName );
			if( $field == "" )
				return "";
			else
				return $field->getDescription();
		}
		
		/**
		 * Gets the value of the given field.
		 *
		 * @param fieldName
		 * @param defaultValue The value that should be returned if the field has no value
		 * @return A string
		 */
		function getField( $fieldName )
		{
			$field = $this->getFieldObject( $fieldName );
			if( $field == "" )
				return "";
			else 
				return $field->getValue();
		}
		
		/**
		 * Tells whether the given field name has a value in this post or not
		 *
		 * @param fieldName
		 * @return True whether it has a value or false otherwise.
		 */
		function hasField( $fieldName )
		{
			return( $this->getField($fieldName) != "" && $this->getField($fieldName) != null );
		}
		
		/**
		 * returns the post slug
		 *
		 * @return a string with the post slug
		 */
		function getPostSlug()
		{
			if( $this->_slug == "" ) {
                lt_include( PLOG_CLASS_PATH.'class/data/textfilter.class.php' );
				$slug = Textfilter::slugify( $this->getTopic());
			} else {
				$slug = $this->_slug;
            }
				
			return $slug;
		}
		
		/**
		 * sets a new post slug
		 *
		 * @param slug the new post slug
		 */
		function setPostSlug( $slug )
        {
            lt_include( PLOG_CLASS_PATH.'class/data/textfilter.class.php' );
            $this->_slug = Textfilter::slugify( $slug );
        }            

		/**
		 * returns the previous article in time
		 *
		 * @return an Article object representing the previous article, in time, in the database
		 */
		function getPrevArticle()
		{
			if( !isset($this->_prevArticle )) {
				lt_include( PLOG_CLASS_PATH.'class/dao/articles.class.php' );			
				$articles = new Articles();
				$this->_prevArticle = $articles->getBlogPrevArticle( $this );
			}
			
			return( $this->_prevArticle );
		}
		
		/**
		 * returns the next article in time
		 *
		 * @return an Article object representing the next article, in time, in the database
		 */
		function getNextArticle()
		{
			if( !isset($this->_nextArticle )) {
				lt_include( PLOG_CLASS_PATH.'class/dao/articles.class.php' );
				$articles = new Articles();
				$this->_nextArticle = $articles->getBlogNextArticle( $this );
			}
			
			return( $this->_nextArticle );
		}
		
		/**
		 * returns all the resources which are linked in this post. It basically looks
		 * at the "id" attribute of all links. When the resource has been added via the
		 * "add resource" pop-up window from the "new post" and "edit post" pages
		 * from the admin interface contains something like "res_XX" where "XX" is the
		 * internal id of the resource.
		 *
		 * If this id attribute is not found this method will not work. We could not find
		 * a better way to identify these resources...
		 * 
		 * @return An array of GalleryResource objects containing all the resources
		 */
		function getArticleResources()
		{
			lt_include( PLOG_CLASS_PATH."class/data/stringutils.class.php" );
			lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresources.class.php" );
			
			// extract all the "res_XX" values
            $regexp ="|<a.*id=\"res_([0-9]+)\".*>.*<\/a>|U";
			$resources = Array();
			$galleryResources = new GalleryResources();
    		if( preg_match_all( $regexp, $this->getText(), $out, PREG_SET_ORDER )) {
				// process the results
				foreach( $out as $match ) {
					$resourceId = $match[1];
					// try to load the resource
					$resource = $galleryResources->getResource( $resourceId, $this->getBlog());					
					if( $resource ) {
						// add it to the array
						$resources[] = $resource;
						// and continue...	
					}
				}
			}			

			return( $resources );
		}
		
		/**
		 * returns the article global category id
		 *
		 * @return the global category id
		 */
		function getGlobalCategoryId()
		{
			return( $this->_globalCategoryId );
		}
		
		/**
		 * sets the global category id
		 *
		 * @param id the new category id
		 */
		function setGlobalCategoryId( $id )
		{
			if( $id != $this->_globalCategoryId )
				$this->_globalCategory = null;
				
			$this->_globalCategoryId = $id;
		}
		
		/**
		 * @return Returns a GlobalArticleCategory object, or null if this article
		 * has not been assigned a global category
		 */
		function getGlobalCategory()
		{
			if( $this->_globalCategory == null ) {
				lt_include( PLOG_CLASS_PATH."class/dao/globalarticlecategories.class.php" );
				$globalCategories = new GlobalArticleCategories();
				$this->_globalCategory = $globalCategories->getGlobalArticleCategory( $this->_globalCategoryId );
			}
			
			return( $this->_globalCategory );
		}
		
		/**
		 * sets a new global category
		 *
		 * @param category A valid GlobalArticleCategory object
		 */
		function setGlobalCategory( $category ) 
		{
			$this->_globalCategory = $category;
			$this->_globalCategoryId = $category->getId();
			return( true );
		}
		
		/**
		 * @private
		 */
		function __sleep()
		{
			// these objects cannot be serialized in the data cache, because otherwise when
			// we update one of them via the admin interface, we will only be updating that object
			// and not the copy of it that was serialized along this Article object!
			$this->_categories = null;
			$this->_userInfo = null;
			$this->_blogInfo = null;
			$this->_nextArticle = null;
			$this->_prevArticle = null;
			$this->_globalCategory = null;
			$this->_customFields = null;
			return( parent::__sleep());
		}
		
		/**
		 * Returns whether this article should appear in the front summary.php page or not
		 *
		 * @return boolean value
		 */
		function getInSummary()
		{
			return( $this->_inSummary );
		}
		
		/**
		 * Whether to make this post appear in the front summary.php page
		 *
		 * @param inSummary
		 */
		function setInSummary( $inSummary )
		{
			$this->_inSummary = $inSummary;
		}
	}
?>