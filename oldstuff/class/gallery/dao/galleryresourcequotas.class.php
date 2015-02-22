<?php

	
	
	define( "GLOBAL_QUOTA_DEFAULT", 5000000 );

	/**
	 * \ingroup Gallery
	 *
	 * returns information about quotas, both global-wise and per-blog quotas
	 */
	class GalleryResourceQuotas 
	{
	
		/**
		 * returns the current global quota set
		 *
		 * @static
		 * @return
		 */
		function getGlobalResourceQuota()
		{
			lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );		
			$config =& Config::getConfig();
			$quota = $config->getValue( "resources_quota", GLOBAL_QUOTA_DEFAULT );
			
			return $quota;
		}
		
		/**
		 * Returns the quota usage of a user
		 *
		 * @param userId The user whose quota usage we would like to know
		 * @return The number of bytes used
		 * @static
		 */
		function getBlogResourceQuotaUsage( $userId )
		{
			//
			// :HACK:
			// this is done so that we can keep this method static while still easily
			// executing an sql query!
			//
			$model = new Model();
			$prefix = $model->getPrefix();
		
			// we can use one query to calculate this...
			$query = "SELECT SUM(file_size) AS total_size FROM {$prefix}gallery_resources
			          WHERE owner_id = '".Db::qstr( $userId )."'";
			$result = $model->Execute( $query );
			
			if( !$result ) 
				return 0;
			$row = $result->FetchRow();
            $result->Close();
			if( isset( $row["total_size"] ))
				$quota = $row["total_size"];
			else
				$quota = 0;
				
			return( $quota );
		}
		
		/**
		 * returns whether the blog would be over its allocated quota
		 * if we are to add a file of the given size
		 *
		 * @param blogId
		 * @param fileSize
		 * @return
		 * @static
		 */
		function isBlogOverResourceQuota( $blogId, $fileSize )
		{
			// current allocated quota
			lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
			$blogs = new Blogs();
			$blog = $blogs->getBlogInfo( $blogId );
			if( !$blog )
				return false;
				
			$blogQuota = $blog->getResourcesQuota();
			
			// but if the quota is 0, then for sure we won't be over the quota :)
			if( $blogQuota == 0 )
				return false;
				
			// if not, calculate how many bytes we currently have
			$currentBytes = GalleryResourceQuotas::getBlogResourceQuotaUsage( $blogId );
								
			if( ($currentBytes + $fileSize) > $blogQuota )
				return true;
			else
				return false;
        }
	}
?>