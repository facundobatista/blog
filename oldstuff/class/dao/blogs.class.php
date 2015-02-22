<?php

    lt_include( PLOG_CLASS_PATH."class/dao/model.class.php" );    
    lt_include( PLOG_CLASS_PATH."class/dao/blogstatus.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/daocacheconstants.properties.php" );
    
    define( "ALL_BLOG_CATEGORIES", 0 );   
    
    /**
     * \ingroup DAO
     * DAO class for BlogInfo objects, which represent a blog.
     */
    class Blogs extends Model
    {
    	function Blogs()
    	{
    		$this->Model();    	
    		$this->table = $this->getPrefix()."blogs";
    	}

        /**
         * Returns information about a blog.
         *
         * @param blogId Identifier of the blog we want to get information
         * @param extendedInfo
         * @return Returns a BlogInfo object containing information about the blog
         */
        function getBlogInfo( $blogId )
        {
            lt_include( PLOG_CLASS_PATH . 'class/dao/bloginfo.class.php' );
            return( $this->get( "id", $blogId,
                                CACHE_BLOGINFOS,
                                Array( CACHE_BLOGIDBYNAME => "getMangledBlogName",
                                       CACHE_BLOGIDBYDOMAIN => "getCustomDomain")));
        }

        /**
         * Returns information about a blog.
         *
         * @param blogName Identifier of the blog we want to get information
         * @return Returns a BlogInfo object containing information about the blog
         */
        function getBlogInfoByName( $blogName, $extendedInfo = false )
        {
            lt_include( PLOG_CLASS_PATH . 'class/dao/bloginfo.class.php' );        
        	return( $this->get( "mangled_blog", $blogName,
                                CACHE_BLOGIDBYNAME,
                                Array( CACHE_BLOGINFOS => "getId" )));
        }

        function getBlogInfoByDomain($blogDomain, $extendedInfo = false){
            lt_include(PLOG_CLASS_PATH . 'class/dao/bloginfo.class.php');
        	$stuff = $this->get( "custom_domain", $blogDomain,
                                CACHE_BLOGIDBYDOMAIN,
                                 Array( CACHE_BLOGINFOS => "getId" ));
            if($stuff)
                return $stuff;
            
            return $this->get("custom_domain", preg_replace("/^www./", "", $blogDomain),
                              CACHE_BLOGIDBYDOMAIN,
                              Array( CACHE_BLOGINFOS => "getId" ));
        }
		
		/**
		 * @see Model::getSearchConditions
		 */
		function getSearchConditions( $searchTerms )
		{
			return( "blog LIKE '%".Db::qstr( $searchTerms )."%'" );
		}

		/**
		 * Updates blog category counters
		 *
		 * @private
		 */
		function updateBlogCategoriesLink( $blogInfo, $oldBlogInfo = null )
		{
			lt_include( PLOG_CLASS_PATH."class/dao/blogcategories.class.php" );
			$blogCategories = new BlogCategories();
			$blogCategory = $blogInfo->getBlogCategory();
			
			if( $blogCategory ) {
				$blogCategory->setNumActiveBlogs($this->getNumItems( $this->getPrefix().'blogs', 'blog_category_id = '.$blogCategory->getId().' AND status = '.BLOG_STATUS_ACTIVE ));
				$blogCategory->setNumBlogs($this->getNumItems( $this->getPrefix().'blogs', 'blog_category_id = '.$blogCategory->getId()));
				$blogCategories->updateBlogCategory( $blogCategory );
			}
			
			if( $oldBlogInfo ) {
				$oldBlogCategory = $oldBlogInfo->getBlogCategory();
				if ($oldBlogCategory)
				{
					$oldBlogCategory->setNumActiveBlogs($this->getNumItems( $this->getPrefix().'blogs', 'blog_category_id = '.$oldBlogCategory->getId().' AND status = '.BLOG_STATUS_ACTIVE ));
					$oldBlogCategory->setNumBlogs($this->getNumItems( $this->getPrefix().'blogs', 'blog_category_id = '.$oldBlogCategory->getId()));
					$blogCategories->updateBlogCategory( $oldBlogCategory );
				}		
			}
			
			return( true );
		}
		
        /**
         * Updates the configuration of a blog
         *
         * @param blogId Blog identifier
         * @param blogInfo A BlogInfo object containing all the information of the blog
         * @param return Returns true if everything's ok or false otherwise
         */
        function updateBlog( &$blog )
        {
			// load the previous version of this blog
			$prevVersion = $this->getBlogInfo( $blog->getId());
			
			// check that the mangled_blog field is unique
            $mangledBlog = $blog->getMangledBlogName();
            $i = 1;
            // check if there already is a blog with the same mangled name
			$done = false;
            while(( $tmpBlog = $this->getBlogInfoByName( $mangledBlog )) && !$done)
            {
				if( $tmpBlog->getId() != $blog->getId()) {
	                $i++;
	                $mangledBlog = substr($mangledBlog, 0,
	                               ($i > 2) ? strlen($mangledBlog)-strlen($i-1) : strlen($mangledBlog)).$i;
				}
				else {
					$done = true;
				}
            }
            $blog->setMangledBlogName($mangledBlog);				
			
			if( ($result = $this->update( $blog ))) {
				// reset the caches
				$this->_cache->removeData( $blog->getCustomDomain(), CACHE_BLOGIDBYDOMAIN );
				$this->_cache->removeData( $blog->getMangledBlogName(), CACHE_BLOGIDBYNAME );
				$this->_cache->removeData( $blog->getId(), CACHE_BLOGINFOS );
									
				// update blog categories
				$this->updateBlogCategoriesLink( $blog, $prevVersion );
				
				// some settings in the BlogSettings object might affect other database items, such
				// as the sorting order of categories and link cateegories. Hence, we should reset
				// those caches too
				$this->_cache->removeData( $blog->getId(), CACHE_ARTICLE_CATEGORIES_BLOG );
				$this->_cache->removeData( $blog->getId(), CACHE_MYLINKCATEGORIES_ALL );
			}

            // always return true,
            // $result is only false if nothing was changed, but that really isn't an error?
            return true;
        }

         /**
          * Adds a new blog to the database.
          *
          * @param blog A BlogInfo object with the necessary information
          * @see BlogInfo
          * @return False if unsuccessful or true otherwise. It will also set the database id of the
          * parameter passed by reference in case it is successful.
          */
         function addBlog( &$blog )
         {
            // source classes
            lt_include( PLOG_CLASS_PATH."class/dao/bayesianfilterinfos.class.php" );
            lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );

            $blogSettings = $blog->getSettings();
            if( !$blogSettings )
                $blogSettings = new BlogSettings();

			// check that the mangled_blog field is unique
            $mangledBlog = $blog->getMangledBlogName();
            $i = 1;
            // check if there already is a blog with the same mangled name
            while($this->getBlogInfoByName( $mangledBlog ))
            {
                $i++;
                $mangledBlog = substr($mangledBlog, 0,
                               ($i > 2) ? strlen($mangledBlog)-strlen($i-1) : strlen($mangledBlog)).$i;
            }
            $blog->setMangledBlogName($mangledBlog);

			$blogId = $this->add( $blog );
			
			// update blog categories
			$this->updateBlogCategoriesLink( $blog );

            // create the row for the bayesian filter info
            $bayesianFilterInfo = new BayesianFilterInfos();
            $bayesianFilterInfo->insert( $blogId );
            
            $this->_cache->setData( $blogId, CACHE_BLOGINFOS, $blog );
//            $this->_cache->setData( $blog->getMangledBlogName(), CACHE_BLOGIDBYNAME );

            // and return the blog identifier
            return $blogId;
         }

         /**
          * Returns all the blogs defined for the site in an array, sorted by its
          * blog identifier.
          *
          * @param status
          * @param searchTerms
          * @param page
          * @param itemsPerPage
          *
          * @return Returns an array with all the blogs defined for this site. The array
          * is sorted by the blog identifier, so that $blogs[$blogId] will give us the information
          * of the blog with $blogId as its identifier.
          */
         function getAllBlogs( $status = BLOG_STATUS_ALL, 
                               $blogCategoryId = ALL_BLOG_CATEGORIES,
                               $searchTerms = "", 
                               $page = -1, 
                               $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
        {
			$where = "";
            if( $status != BLOG_STATUS_ALL )
                $where = "status = '".Db::qstr($status)."'";
			
			if( $blogCategoryId != ALL_BLOG_CATEGORIES ) {
				if( $where != "" ) 
					$where .= " AND ";
				$where .= " blog_category_id = '".Db::qstr($blogCategoryId)."'";
			}

			if( $searchTerms != "" ){
                if( $where != "" )
                    $where .= " AND ";
				$where .= $this->getSearchConditions( $searchTerms );
            }
				
			if( $where != "" )
				$where = " WHERE $where";			

            $query = "SELECT * FROM ".$this->getPrefix()."blogs $where ORDER BY id DESC";

            $result = $this->Execute( $query, $page, $itemsPerPage );

            if( !$result )
                return false;

            $blogs = Array();
            while( $row = $result->FetchRow()) {
				// map the row we just loaded
                $blog = $this->mapRow( $row );
                $blogs[$blog->getId()] = $blog;
				
				// and cache whatever we loaded for later use, just in case
				$this->_cache->setData( $blog->getId(), CACHE_BLOGINFOS, $blog );
				$this->_cache->setData( $blog->getMangledBlogName(), CACHE_BLOGIDBYNAME, $blog );				
				$this->_cache->setData( $blog->getCustomDomain(), CACHE_BLOGIDBYDOMAIN, $blog );				
            }
            $result->Close();			

            return $blogs;									
        }    

        /**
         * returns only an array with all the blog ids
         *
         * @return an array with blog ids
         */
        function getAllBlogIds()
        {
            $query = "SELECT id FROM ".$this->getPrefix()."blogs";

            $result = $this->Execute( $query );
            if( !$result )
                return Array();

            $blogIds = Array();
            while( $row = $result->FetchRow()) {
                $blogIds[] = $row['id'];
            }
            $result->Close();			

            return $blogIds;
        }

        /**
         * returns the total number of blogs in the site
         *
         * @param status
         * @param searchTerms
         *
         * @return The number of blogs
         */
        function getNumBlogs( $status = BLOG_STATUS_ALL, $blogCategoryId = ALL_BLOG_CATEGORIES, $searchTerms = "" )
        {	
			$where = "";
			$statusCond = "";
            if( $status != BLOG_STATUS_ALL )
                $statusCond = "status = '".Db::qstr($status)."'";
			$where .= $statusCond;			

			if( $blogCategoryId != ALL_BLOG_CATEGORIES )
				$where .= " AND blog_category_id = '".Db::qstr($blogCategoryId)."'";				
										
			if( $searchTerms != "" ) {				
				$searchCond = $this->getSearchConditions( $searchTerms );
                if( $where != "" )
                    $where .= " AND";
 				$where .= " ".$searchCond;
            }

			return( $this->getNumItems( $this->getPrefix()."blogs", $where ));
        }

        /**
         * Removes a blog from the database. It also removes all its posts, its posts categories
         * its links, its links categories, its trackbacks and its comments
         *
         * @param blogId the id of the blog we'd like to delete
         * @return boolean success of the operation
         */
        function deleteBlog( $blogId )
        {
			// update blog categories
			$blog = $this->getBlogInfo( $blogId );
			$this->updateBlogCategoriesLink( $blog );	

            // source class
            lt_include( PLOG_CLASS_PATH."class/template/templatesets/templatesets.class.php" );
	
			// delete the blog template sets
			$templateSets = new TemplateSets();
			$tsStorage = new TemplateSetStorage();
			$blogTemplates = $templateSets->getBlogTemplates( $blogId );
			foreach( $blogTemplates as $template ) {
				$tsStorage->removeBlogTemplate( $template, $blogId );
			}
			// when done, remove the parent "blog_X" folder
			File::deleteDir( TemplateSetStorage::getBlogBaseTemplateFolder( $blogId ));

            // and finally, remove the cache and delete the blog
			$this->_cache->removeData( $blog->getCustomDomain(), CACHE_BLOGIDBYDOMAIN );
			$this->_cache->removeData( $blog->getMangledBlogName(), CACHE_BLOGIDBYNAME );
			$this->_cache->removeData( $blog->getId(), CACHE_BLOGINFOS );
            return( $this->delete( "id", $blogId ));
        }

		/**
         * @private
         */
        function mapRow( $row )
        {
            // source class
            lt_include( PLOG_CLASS_PATH."class/dao/bloginfo.class.php" );

            // create new BlogInfo
            $blogInfo = new BlogInfo( stripslashes($row["blog"]),
                                      $row["owner_id"],
                                      stripslashes($row["about"]),
                                      unserialize($row["settings"]),
                                      $row["id"] );

            // load information about the blog status
            $blogInfo->setStatus( $row["status"] );
			// load information abotu the blog category id
			$blogInfo->setBlogCategoryId( $row["blog_category_id"] );
			// counters
			$blogInfo->setTotalPosts( $row['num_posts'] );
			$blogInfo->setTotalTrackbacks( $row['num_trackbacks'] );
			$blogInfo->setTotalComments( $row['num_comments'] );
			// mangled blog
			$blogInfo->setMangledBlogName( $row['mangled_blog'], false );
			$blogInfo->setCustomDomain( $row['custom_domain'] );
            // show in summary or not
			$blogInfo->setShowInSummary( $row['show_in_summary'] );
			// create date and update date
			$blogInfo->setCreateDate( $row['create_date'] );
			$blogInfo->setUpdateDate( $row['last_update_date'] );

            return $blogInfo;
        }
    }
?>