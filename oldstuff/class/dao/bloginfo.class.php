<?php

	lt_include( PLOG_CLASS_PATH."class/database/dbobject.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/blogsettings.class.php" );
	
	/**
	 * \ingroup DAO
	 *
     * This is the representation of a blog. It contains all the information we need to know,
     * such as the name of the blog, the owner, description, etc.
     */
    class BlogInfo extends DbObject
    {

        var $_blog;
        var $_owner; // owner id
        var $_about;
        var $_settings;
        var $_id;

        // More optional information for each blog. Only used when we load this data
        var $_createDate;
        var $_updateDate;
        var $_totalPosts;
        var $_totalComments;
        var $_totalTrackbacks;
        var $_ownerInfo;
        var $_usersInfo;
        var $_mangledBlog;
        var $_customDomain;

        // the TemplateSet representing the template set used by the blog
        var $_templateSet;

        // the Locale object
        var $_locale;
		var $_blogLocale;

        // the status
        var $_status;
		
		// the blog category
		var $_categoryId;
		var $_category;

        function BlogInfo( $blog, $owner, $about, $settings, $id = -1 )
        {
        	lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );
        
        	$this->DbObject();
        
            $this->setBlog( $blog );
            $this->_owner = $owner;
            $this->_about = $about;
            $this->_settings = $settings;
            if( empty( $this->_settings ) )
                $this->_settings = new BlogSettings();

            $this->_id = $id;

            $this->_createDate = "";
            $this->_updateDate = "";
            $this->_totalPosts = 0;
            $this->_totalTrackbacks = 0;
            $this->_totalComments = 0;
            $this->_usersInfo = null;
            
            $t = new Timestamp();
            $this->_createDate = $t->getTimestamp();
            $this->_updateDate = $t->getTimestamp();

            // no template set loaded
            $this->_templateSet = null;

            // default status is active
            $this->_status = BLOG_STATUS_ACTIVE;

            // values that are loaded on demand
            $this->_ownerInfo = null;
			
			// information about the blog category id
			$this->_category = null;
			$this->_categoryId = 0;
			
			// show in the summary by default
			$this->_showInSummary = true;
			
			$this->_pk = "id";
			$this->_fields = Array(
				"blog" => "getBlog",
				"owner_id" => "getOwnerId",
				"about" => "getUnformattedAbout",
				"settings" => "getSettings",
				"mangled_blog" => "getMangledBlogName",
				"status" => "getStatus",
				"show_in_summary" => "getShowInSummary",
				"blog_category_id" => "getBlogCategoryId",
				"create_date" => "getCreateDate",
				"last_update_date" => "getUpdateDate",
				"num_posts" => "getTotalPosts",
				"num_comments" => "getTotalComments",
				"num_trackbacks" => "getTotalTrackbacks",
				"custom_domain" => "getCustomDomain"
			);
        }

        /**
         * Returns the short name of the blog.
         *
         * @return A string with the short name of the blog.
         */
        function getBlog()
        {
            return $this->_blog;
        }

        /**
         * Returns the identifier of the user who owns this journal.
         *
         * @return An integer value representing the identifier of the user who owns this blog.
         */
        function getOwnerId()
        {
            return $this->_owner;
        }

        /**
         * Returns the identifier of the user who owns this journal.
         *
         * @return An integer value representing the identifier of the user who owns this blog.
         * @deprecated Use getOwnerId() instead.
         */
        function getOwner()
        {
            return $this->_owner;
        }

        /**
         * Returns a longer and descriptive text about this blog. It can also be empty since it is
         * configurable from within the "Blog Settings" in the administration interface.
         *
		 * @param format Whether basic formatting should be applied to the text
         * @return A string containing the more descriptive text about the journal.
         */
        function getAbout( $format = true )
        {
			$text = $this->_about;
			
			if( $format ) {
				lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
				$text = TextFilter::autoP( $text );
			}
			
            return( $text );
        }

		/** 
		 * @return Returns the information about this blog without any formatting.
		 */
		function getUnformattedAbout()
		{
			return( $this->getAbout( false ));
		}

        /**
         * Returns a BlogSettings object with information about the settings of the journal.
         * @private
         */
        function getSettings()
        {
            return $this->_settings;
        }

        function getSetting( $setting )
        {
            return $this->_settings->getValue( $setting );
        }

        /**
         * returns a key from the blog settings
         */
        function getValue( $value )
        {
            if( !$this->_settings )
                return "";

            return $this->getSetting( $value );
        }

        /**
         * returns a key from the blog settings
         */
        function setValue( $key, $value )
        {
            if( !$this->_settings )
                return true;

            $this->_settings->setValue( $key, $value );

            return true;
        }

        /**
         * implemented from DbObject. Merges a list of properties with the current settings
         *
         * @param properties
         */
        function addProperties( $properties )
        {
            return $this->setProperties( $properties );
        }

        /**
         * adds an array of pairs (key,value) to the blog settings
         */
        function setProperties( $properties )
        {
            // nothing to do if we don't get an array as parameters
            if( !is_array($properties))
                return true;

            foreach( $properties as $key => $value ) {
                $this->setValue( $key, $value );
            }

            return true;
        }

		/** 
		 *
		 */
        function getCreateDateObject()
        {
        	lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );        
            return( new Timestamp( $this->_createDate ));
        }
        
        function getCreateDate()
        {
        	return( $this->_createDate );
        }
        
        function setCreateDate( $date )
        {
        	$this->_createDate = $date;
        }        

        /**
         */
        function getUpdateDateObject()
        {
        	lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );
            return( new Timestamp( $this->_updateDate ));
        }
        
        function getUpdateDate()
        {
        	return( $this->_updateDate );
        }
        
        function setUpdateDate( $date )
        {
        	$this->_updateDate = $date;
        }

        /**
         */
        function getTotalPosts()
        {
            return $this->_totalPosts;
        }

        /**
         * @private
         */                
        function setTotalPosts( $totalPosts )
        {
        	$this->_totalPosts = $totalPosts;
        }

        /**
         * @private
         */        
        function setTotalComments( $totalComments )
        {
        	$this->_totalComments = $totalComments;
        }

        /**
         */
        function getTotalComments()
        {
            return $this->_totalComments;
        }
        
        /**
         * @private
         */
        function setTotalTrackbacks( $totalTrackbacks )
        {
        	$this->_totalTrackbacks = $totalTrackbacks;
        }

        /**
         */
        function getTotalTrackbacks()
        {
            return $this->_totalTrackbacks;
        }

        /**
         * Gets information about the owner of this blog
         * @return return a UserInfo object which contains much more info about the owner of the blog
         */
        function getOwnerInfo()
        {
	        lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );

            if( $this->_ownerInfo === null ) {
                $users = new Users();
                $ownerInfo = $users->getUserInfoFromId( $this->_owner );
                $this->setOwnerInfo( $ownerInfo );
            }

            return $this->_ownerInfo;
        }

        /**
         * Return information about all the users who belong to this blog.
         *
         * @return An array of UserInfo objects
         * @see UserInfo
         */
        function getUsersInfo()
        {
	        lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );

            if( $this->_usersInfo === null ) {
                $users = new Users();
                $blogUsers = $users->getBlogUsers( $this->getId());
                $this->setUsersInfo( $blogUsers );
            }

            return $this->_usersInfo;
        }

        /**
         * Returns the identifier of this blog.
         *
         * @return An integer value with the identifier of this blog.
         */
        function getId()
        {
            return $this->_id;
        }

        /**
         * @private
         */
        function setBlog( $blog )
        {
	        lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );

            $tf = new Textfilter();
            $this->_blog = $tf->filterAllHTML($blog);
        }

        /**
         * @param owner id
         * @private
         */
        function setOwner( $owner )
        {
            $this->_owner = $owner;
        }

        /**
         * @private
         */
        function setAbout( $about )
        {
            $this->_about = $about;
        }

        /**
         * @private
         */
        function setSettings( $settings )
        {
            $this->_settings = $settings;
        }

        /**
         * @private
         */
        function setOwnerInfo( $newOwnerInfo )
        {
            $this->_ownerInfo = $newOwnerInfo;
        }

        /**
         * @private
         */
        function setUsersInfo( $newUsersInfo )
        {
            $this->_usersInfo = $newUsersInfo;
        }

        /**
         * @private
         */
        function setId( $id )
        {
            $this->_id = $id;
        }

        /**
         * returns the name of the template used by this blog
         *
         * @param the name of the template set used by this blog
         */
        function getTemplate()
        {
            return $this->getSetting( "template");
        }

        /**
         * sets the template name
         *
         * @param template The name of the template that we'd like to use
         * @return always true
         */
        function setTemplate( $template )
        {
            // save the template in the settings
            $this->_settings->setValue( "template", $template );
            // and reset the TemplateSet object so that it gets reloaded next time
            // somebody uses BlogInfo::getTemplateSet
            $this->_templateSet = null;

            return true;
        }

        /**
         * this method is some kind of a shortcut for a very common operation: obtaining the
         * correct RequestGenerator object so that we can generate urls based on the information
         * from this blog. This is very handy for example in the summary page where we have to
         * generate lots of different urls for lots of different blogs.
         *
         * @return A RequestGenerator object
         */
        function getBlogRequestGenerator()
        {
	        lt_include( PLOG_CLASS_PATH."class/net/requestgenerator.class.php" );

            return RequestGenerator::getRequestGenerator( $this );
        }

        /**
         * returns a TemplateSet object with the information about the template used
         * by the blog
         *
         * @return a TemplateSet object
         */
        function getTemplateSet()
        {
            // since including these files is quite a costly operation, let's do it only
            // whenever we have to instead of always and always always... :)
            if( $this->_templateSet === null ) {
                lt_include( PLOG_CLASS_PATH."class/template/templatesets/templatesets.class.php" );
                $ts = new TemplateSets();
                $this->_templateSet = $ts->getTemplateSet( $this->getTemplate(), $this->getId());
                if( $this->_templateSet == null ) {
                    // what if the admin removes the current template set used by this blog???
                    $this->_templateSet = $ts->getDefaultTemplateSet();
                }
            }

            return $this->_templateSet;
        }

        /**
         * changes the template set used by the blog
         *
         * @param templateSet A TemplateSet object
         * @return always true
         */
        function setTemplateSet( $templateSet )
        {
            $this->setTemplate( $templateSet->getName());

            return true;
        }

        /**
         * returns the right locale object for the blog
         *
         * @param a Locale object
         */
        function &getLocale()
        {
            if( $this->_locale === null ) {
                lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );
                $this->_locale =& Locales::getLocale( $this->getSetting( "locale" ), "en_UK" );
            }

            return $this->_locale;
        }

        function &getBlogLocale()
        {
            if( $this->_blogLocale === null ) {
                lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );
                $this->_blogLocale =& Locales::getBlogLocale( $this->getSetting( "locale" ), "en_UK" );
            }

            return $this->_blogLocale;
        }


        /**
         * sets the new locale for the blog
         *
         * @param a valid Locale object
         * @return Always true
         */
        function setLocale( $locale )
        {
            $this->_locale = $locale;
            $this->_settings->setValue( "locale", $locale->getLocaleCode());

            return true;
        }

        /**
         * returns the status of the blog
         *
         * @return the current status of the blog
         */
        function getStatus()
        {
            return $this->_status;
        }

        /**
         * sets the current status of the blog
         *
         * @param status
         * @return always true
         */
        function setStatus( $status )
        {
            $this->_status = $status;

            return true;
        }

        /**
         * returns the quota for this blog, or the value of the global quota
         * for blogs in case this blog has no quota assigned
         *
         * @return the resources quota
         */
        function getResourcesQuota()
        {
            $quota = $this->getSetting( "resources_quota" );

            // if there is no quota for this blog, then fetch it from the global
            // settings
            if( $quota == "" ) {
            	lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresourcequotas.class.php" );
            	$quota = GalleryResourceQuotas::getGlobalResourceQuota();
            }

            return $quota;
        }

        /**
         * sets the quota
         *
         * @param quota
         */
        function setResourcesQuota( $quota )
        {
            $this->_settings->setValue( "resources_quota", $quota );

            return true;
        }
		
		/**
		 * returns the blog category id. You shouldn't probably use this method, 
		 * BlogInfo::getBlogCategory() will return the real BlogCategory object for you.
		 *
		 * @return the blog category id
		 */
		function getBlogCategoryId()
		{
			return( $this->_categoryId );
		}
		
		/**
		 * sets the blog category id
		 *
		 * @param the new id
		 */
		function setBlogCategoryId( $categoryId )
		{
			if( $categoryId != $this->_categoryId )
				$this->_category = null;
		
			$this->_categoryId = $categoryId;			
		}
		
		/**
		 * loads the blog category
		 *
		 * @return A BlogCategory object
		 */
		function getBlogCategory()
		{
			// check if the category has already been loaded and if not, load it and save a reference
			// to it in the object
			if( $this->_categoryId == 0 )
				return( false );
				
			if( $this->_category == null ) {
				lt_include( PLOG_CLASS_PATH."class/dao/blogcategories.class.php" );				
				$blogCategories = new BlogCategories();				
				$this->_category = $blogCategories->getBlogCategory( $this->_categoryId );
			}
			
			return( $this->_category );
		}
		
		/**
		 * set the mangled name of this blog
		 *
		 * @param mangledBlog the new mangled blog name
		 */
		function setMangledBlogName( $mangledBlog, $modify = false )
		{
			if( $modify ) {
	        	lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
				$mangledBlog = Textfilter::domainize( $mangledBlog );
			}
			$this->_mangledBlog = $mangledBlog;
		}
		
		/**
		 @return the mangled name of this blog
		 */
		function getMangledBlogName()
		{
	        // fill in the field if it hasn't been filled yet
	        if( $this->_mangledBlog === null ) {
	        	lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
				$this->setMangledBlogName( $this->getBlog(), true );
			}
	        	
	    	return( $this->_mangledBlog );  
		}

		
		/**
		 * set the custom domain of this blog
		 *
		 * @param customDomain the new custom domain
		 */
		function setCustomDomain($customDomain)
		{
	        lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );			
	        $this->_customDomain = Textfilter::domainize($customDomain);
		}
        
		/**
		 @return the custom domain of this blog
		 */
		function getCustomDomain()
		{
            // fill in the field if it hasn't been filled yet
	        if($this->_customDomain === null){
	        	lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );		
	        	$this->_customDomain = Textfilter::domainize($this->getMangledBlog());
            }

	    	return($this->_customDomain);
		}
		
		/**
		 * @see getMangledBlogName
		 * Alias for the method above
		 */
		function getMangledBlog()
		{
			return( $this->getMangledBlogName());
		}
		
		/**
		 * @return whether this blog should be shown in the summary page
		 */
		function getShowInSummary()
		{
			return( $this->_showInSummary );
		}
		
		/** 
		 * Whether to show this blog in the summary page or not.
		 *
		 * @param showInSummary
		 */
		function setShowInSummary( $showInSummary )
		{
			$this->_showInSummary = $showInSummary;
		}
		
		/**
		 * @private
		 */
		function __sleep()
		{
			$this->_ownerInfo = null;
			$this->_usersInfo = null;
			$this->_category  = null;
			$this->_locale    = null;
			$this->_blogLocale = null;
			//return( get_object_vars( $this ));
			return( parent::__sleep());
		}
	}
?>
