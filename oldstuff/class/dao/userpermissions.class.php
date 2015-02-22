<?php

    lt_include( PLOG_CLASS_PATH."class/dao/model.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/userpermission.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/daocacheconstants.properties.php" );	

    define( "PERMISSION_BLOG_USER", 2 );

    /**
     * Handles user permissions.
     *
     * \ingroup DAO
     */
    class UserPermissions extends Model 
    {

        function UserPermissions()
        {
            $this->Model();			
			$this->table = $this->getPrefix()."users_permissions";
        }

        /**
         * Retrieves the permissions for a given user.
         *
         * @param userId The user identifier
         * @param blogId The blog identifier
         * @return An Array of UserPermission objects with all the different permissions
         * that the user has for the given blog. the array can be empty if the user has no
         * privileges over a blog.
         */
        function getUserPermissions( $userId, $blogId )
        {
            $perms = Array();
			
			// get all the user permissions from the db table
			$userPerms = $this->getMany( "user_id",
			                             $userId,
			                             CACHE_USER_PERMISSIONS );
			
			if( !is_array( $userPerms ))
				$userPerms = Array();
				
			foreach( $userPerms as $perm ) {
				if( $perm->getBlogId() == $blogId )
					$perms[$perm->getPermissionId()] = $perm;
			}
										
            return( $perms );
        }

        /**
         * Grants a permission to a user, for a given blog.
         * Any permission can be granted, as long as it exists.
         *
		 * @param perm A UserPermission object containing information abotu the permissions
		 * and user that we'd like to grant
		 * @param doNotCleanCache Whether we should ignore the code that triggers the cache. You should
		 * probably not use this parameter if in doubt.
         * @return Returns true if the permission was set or already existed, or false
         * if there was any problem.
         */
        function grantPermission( &$perm, $doNotCleanCache = false )
        {
            // check if the permission has already been granted
			$userPerms = $this->getUserPermissions( $perm->getUserId(), $perm->getBlogId());
			$found = false;
			foreach( $userPerms as $userPerm ) {
				if( $userPerm->getUserId() == $perm->getUserId() && $userPerm->getBlogId() == $perm->getBlogId() && 
				    $userPerm->getPermissionId() == $perm->getPermissionId()) {
					$found = true;
					break;
				}
			}
			if( $found )
				return( true );
				
			// if not, grant it now
			if(( $result = $this->add( $perm ))) {
				if( !$doNotCleanCache ) {
					$this->_cache->removeData( $perm->getUserId(), CACHE_USER_PERMISSIONS );
					$this->_cache->removeData( $perm->getUserId(), CACHE_USERINFO );
					$userInfo = $perm->getUserInfo();
					$this->_cache->removeData( $userInfo->getUserName(), CACHE_USERIDBYNAME );
				}
			}
			
			return( $result );
        }

        /**
         * Removes a permission from a user, for a given blog.
         *
         * @param userId The user which will be revoked the permission
         * @param blogId The blog to which this permission will be revoked
         * @param permissionId The identifier of the permission that will be revoked
         * @return Returns true if the permission didn't exist or if the permission was
         * successfully removed, or false otherwise.
         */
        function revokePermission( $userId, $blogId, $permissionId )
        {
            // check if the permission has already been removed or doesn't exist
			$userPerms = $this->getUserPermissions( $userId, $blogId );
			foreach( $userPerms as $perm ) {
				if( $perm->getUserId() == $userId && $perm->getBlogId() == $blogId && 
				    $perm->getPermissionId() == $permissionId ) {
					$found = true;
					break;
				}
			}
			if( !$found )
				return( true );

			// build the query to remove the row
			$query = "DELETE FROM ".$this->getPrefix()."users_permissions WHERE user_id = '".Db::qstr( $userId )."'
			          AND blog_id = '".Db::qstr( $blogId ) ."' AND permission_id = '".Db::qstr( $permissionId )."'";
			// and execute it
			$result = $this->Execute( $query );
			if( $result ) {
				$this->_cache->removeData( $userId, CACHE_USER_PERMISSIONS );
			}
			
			return( $result );
        }

		/**
		 * Removes all the user permissions from a given blog
		 *
		 * @param userId
		 * @param blogId
		 * @return True if successful or false otherwise
		 */
		function revokePermissions( $userId, $blogId ) 
		{
			// build the query to remove the row
			$query = "DELETE FROM ".$this->getPrefix()."users_permissions WHERE user_id = '".Db::qstr( $userId )."'
			          AND blog_id = '".Db::qstr( $blogId )."'";
			
			// and execute it
			$result = $this->Execute( $query );
			if( $result ) {
				$this->_cache->removeData( $userId, CACHE_USER_PERMISSIONS );
			}
			
			return( $result );			
		}

        /**
         * removes all the permissions for a given blog, useful when we're removing
         * the blog from the database.
         *
         * @param blogId The blog from which we'd like to remove the permissions.
         * @return true
         */
        function revokeBlogPermissions( $blogId )
        {
			lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );		
			
			$blogs = new Blogs();
			$blogInfo = $blogs->getBlogInfo( $blogId );
			if( !$blogInfo )
				return false;
			$blogUsers = $blogInfo->getUsersInfo();
			
			if(( $result = $this->delete( "blog_id", $blogId ))) {
				foreach( $blogUsers as $user ) {
					$this->_cache->removeData( $user->getId(), CACHE_USER_PERMISSIONS );
				}
			}

            return $result;
        }

        /**
         * revokes all the permissions that a user may have
         *
         * @param userId The identifier of the user
         * @return true
         */
        function revokeUserPermissions( $userId )
        {
			if( $result = $this->delete( "user_id", $userId )) {
				$this->_cache->removeData( $userId, CACHE_USER_PERMISSIONS );
			}
			
			return( $result );
        }

		/**
		 * Returns the number of users who have a certain permission
		 *
		 * @param permId
		 */
		function getNumUsersWithPermission( $permId )
		{
			$query = "SELECT COUNT(DISTINCT user_id) AS total FROM ".$this->getPrefix()."users_permissions
			          WHERE permission_id = ".Db::qstr( $permId );
			
            // execute it
            $result = $this->Execute( $query );

            if( !$result )
                return 0;

            // if no error, get the result
            $row = $result->FetchRow();
            $result->Close();

            $total = $row["total"];
            if( $total == "" ) $total = 0;

            return $total;			
		}
		
		/**
		 * Returns all users who have the given permission, using paging if necessary
		 *
		 * @param permId
		 * @param page
		 * @param itemsPerPage
		 * Return an Array of UserInfo objects or an empty array if no users have this
		 * permission.
		 */
		function getUsersWithPermission( $permId, $page = -1, $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
		{
			$query = "SELECT user_id FROM ".$this->getPrefix()."users_permissions WHERE permission_id = '".Db::qstr( $permId )."'";
			
			$result = $this->Execute( $query );
			
			if( !$result ) 
				return( Array());
				
			$users = Array();
			lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
			$users = new Users();
			while( $row = $result->FetchRow()) {
				// we'll get them one by one so that we can make proper usage of the cache
				$results[] = $users->getUserInfoFromId( $row["user_id"] );
			}
			
			return( $results );
		}        
		
		/**
		 * Returns all users who have the given permission, identified by its name
		 *
		 * @param permName
		 * @param page
		 * @param itemsPerPage
		 * Return an Array of UserInfo objects or an empty array if no users have this
		 * permission.
		 */
		function getUsersWithPermissionByName( $permName, $page = -1, $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
		{
			$perms = new Permissions();
			$perm = $perms->getPermissionByName( $permName );
			if( !$perm )
				return Array();
				
			return( $this->getUsersWithPermission( $perm->getId(), $page, $itemsPerPage ));
		}		

        /**
         * @private
         */
        function mapRow( $row )
        {
            $perm = new UserPermission( $row["user_id"], 
			                            $row["blog_id"], 
										$row["permission_id"], 
										$row["id"] );

            return $perm;
        }
    }
?>