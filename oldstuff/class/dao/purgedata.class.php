<?php

	lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
	
	define( "DEFAULT_PURGE_AMOUNT", 15 );

	/**
	 * This class takes care of purging data, since it's a very complex process anyway
	 */
	class PurgeData extends Blogs
	{
		/**
		 * Deletes a blog and all its data
		 *
		 * @param blogId The id of the blog whose data we'd like to delete
		 */
		function deleteBlogData( $blogId )
		{
			// delete the article categories
			lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );
			$cats = new ArticleCategories();
			$cats->deleteBlogCategories( $blogId );
			// article notifications
			lt_include( PLOG_CLASS_PATH."class/dao/articlenotifications.class.php" );
			$notifications = new ArticleNotifications();
			$notifications->deleteBlogNotifications( $blogId );			
			// comments
			lt_include( PLOG_CLASS_PATH."class/dao/commentscommon.class.php" );
			$comments = new CommentsCommon();
			$comments->deleteBlogComments( $blogId );
			// links
			lt_include( PLOG_CLASS_PATH."class/dao/mylinks.class.php" );			
			$links = new MyLinks();
			$links->deleteBlogMyLinks( $blogId );
			// link categories
			lt_include( PLOG_CLASS_PATH."class/dao/mylinkscategories.class.php" );
			$links = new MyLinksCategories();
			$links->deleteBlogMyLinksCategories( $blogId );
			// referers
			lt_include( PLOG_CLASS_PATH."class/dao/referers.class.php" );			
			$referers = new Referers();
			$referers->deleteBlogReferers( $blogId );
			// permissions
			lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );			
			$perms = new UserPermissions();
			$perms->revokeBlogPermissions( $blogId );
			// resources
			lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresources.class.php" );
			$albums = new GalleryResources();
			$albums->deleteUserResources( $blogId );
			// albums
			lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );			
			$albums = new GalleryAlbums();
			$albums->deleteUserAlbums( $blogId );						
			// articles
			lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
			$articles = new Articles();
			$articles->deleteBlogPosts( $blogId );
			// the blog itself
			lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
			$blogs = new Blogs();
			$blogs->deleteBlog( $blogId );

			// reset the template cache
			CacheControl::resetBlogCache( $blogId, false );
		}
		
        /**
         * removes all blogs that have 'deleted' status
         *
         * @return Returns 	how many we purged, and when this method returns '0', it means that there is nothing else
	     * left to be purged
         */        
        function purgeBlogs( $amount = DEFAULT_PURGE_AMOUNT)
        {                                  
        	$disabledBlogs = $this->getAllBlogs( BLOG_STATUS_DISABLED, ALL_BLOG_CATEGORIES, "", 1, $amount );
        	foreach( $disabledBlogs as $blog ) {
        		$blogId = $blog->getId();
        		$this->deleteBlogData( $blogId );
        	}
        	
        	return( count( $disabledBlogs ));
        }

		/**
		 * Purge spam comments
		 *
		 * @param amount
         * @return Returns false on error, or else number of comments purged,
         *   if 0, there is nothing left to purge
	     */
		function purgeSpamComments( $amount = DEFAULT_PURGE_AMOUNT )
		{
			lt_include( PLOG_CLASS_PATH."class/dao/commentscommon.class.php" );
			
			$query = "SELECT id FROM ".$this->getPrefix()."articles_comments WHERE status = ".COMMENT_STATUS_SPAM." LIMIT 0, $amount";
			$result = $this->Execute( $query );
			
			if( !$result )
				return false;
				
			$deleted = 0;
			$comments = new CommentsCommon();
			
			while( $row = $result->FetchRow()) {
				// the CommentsCommon::deleteComment() method should take care of updating the articles,
				// resettings caches and updating comment counters in articles and blogs
				$comments->deleteComment( $row["id"] );
				$deleted++;
			}
			
			return( $deleted );
		}
		
		/**
		 * Purge articles that have been marked as deleted
         *
         * @return Returns false on error, or else number of posts purged,
         *   if 0, there is nothing left to purge
		 *
		 * @param amount
		 */
		function purgePosts( $amount = DEFAULT_PURGE_AMOUNT ) 
		{
			lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
			
			$query = "SELECT id, user_id, blog_id  FROM ".$this->getPrefix()."articles WHERE status = ".POST_STATUS_DELETED." LIMIT 0, $amount";
			
			$result = $this->Execute( $query );
			
			if( !$result )
				return false;
				
			$deleted = 0;
			$posts = new Articles();
			
			while( $row = $result->FetchRow()) {
				// calling the method in the Articles class will take care of everything else
				$posts->deleteArticle( $row["id"], $row["user_id"], $row["blog_id"], true );
				$deleted++;
			}
			
			return( $deleted );			
		}
		
		/**
		 * Purge users that have been marked as disabled. If these users own a
         * blog, then the blog will also be removed
		 *
         * @return Returns number of users purged,
         *   if 0, there is nothing left to purge
		 *
		 * @param amount
		 */
		function purgeUsers( $amount = DEFAULT_PURGE_AMOUNT )
		{
			lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );

			$users = new Users();
			$deleted = 0;
			// get $amount more users...
			$disabledUsers = $users->getAllUsers( USER_STATUS_DISABLED, "", "", 1, $amount );
			// and process them
	        foreach( $disabledUsers as $user ) {
	        	foreach( $user->getOwnBlogs() as $userBlog ) {					
	        		$this->deleteBlogData( $userBlog->getId());
	        	}
	        	
	        	$users->deleteUser( $user->getId());
				$deleted++;
	        }
	        
	        return( $deleted );			
		}
	}
?>