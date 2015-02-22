<?php

	lt_include( PLOG_CLASS_PATH."class/dao/model.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/userstatus.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/blogstatus.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/daocacheconstants.properties.php" );	
    
	/**
	 * This is the base class that defines the methods that all user-data providers must implement.
	 * It also provides some common methods that will be shared by all user-data providers and that therefore
	 * should exist in the parent class rather than being copied many times in the child classes.
	 *
	 * \ingroup User_Data_Providers
	 */
	class BaseUserDataProvider extends Model
	{
	
		var $_providerConfig;
		
		/**
		 * Constructor of the class
		 *
		 * @param providerConfig a Properties object with information about the 
		 * provider-specific parameters, which may vary depending on the provider
		 */
		function BaseUserDataProvider( $providerConfig )
		{
			$this->Model();
			
			$this->_providerConfig = $providerConfig;
		}
		
		/**
		 * Returns the config specific data
		 *
		 * @return a Properties object with the provider configuration, as it was defined
		 * in the userdata.properties.php file
		 */
		function getProviderConfiguration()
		{
			return( $this->_providerConfig );
		}
		
        /**
         * Returns true if the user is in the database and the username
         * and password match
         *
         * @param user Username of the user who we'd like to authenticate
         * @param pass Password of the user
         * @return true if user and password correct or false otherwise.
         */
        function authenticateUser( $user, $pass )
        {
        }

        /**
         * Returns all the information associated to the user given
         *
         * @param user Username of the user from who we'd like to get the information
         * @param pass Password of the user we'd like to get the information
         * @return Returns a UserInfo object with the requested information, or false otherwise.
         */
        function getUserInfo( $user, $pass )
        {
        }

        /**
         * Retrieves the user information but given only a username
         *
         * @param username The username of the user
         * @return Returns a UserInfo object with the requested information, or false otherwise.
         */
        function getUserInfoFromUsername( $username )
        {
        }

        /**
         * Retrieves the user infromation but given only a userid
         *
         * @param userId User ID of the user from whom we'd like to get the information
         * @return Returns a UserInfo object with the requested information, or false otherwise.
         */
        function getUserInfoFromId( $userid, $extendedInfo = false )
        {
        }

        /**
         * Returns an array of BlogInfo objects with the information of all the blogs to which
         * a user belongs
         *
         * @param userId Identifier of the user
         * @return An array of BlogInfo objects to whom the user belongs.
         */
        function getUsersBlogs( $userid, $status = BLOG_STATUS_ALL )
        {
			lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
            $usersBlogs = Array();
            $blogs = new Blogs();
            $ids = Array();

            // check if the user is the owner of any blog
            $prefix = $this->getPrefix();
            $owner = "SELECT * FROM {$prefix}blogs WHERE owner_id = '".Db::qstr( $userid )."'";
			if( $status != BLOG_STATUS_ALL ) 
				$owner .= " AND status = '".Db::qstr( $status )."'";
           
			$result = $this->Execute( $owner );

            while( $row = $result->FetchRow($result)) {
                $usersBlogs[] = $blogs->mapRow( $row );
                $ids[] = $row["id"];
            }
            $result->Close();

            // and now check to which other blogs he or she belongs
            $otherBlogs = "SELECT DISTINCT p.blog_id AS blog_id FROM {$prefix}users_permissions p, {$prefix}blogs b
                           WHERE p.user_id = '".Db::qstr($userid)."' AND b.id = p.blog_id";
            if( !empty($usersBlogs)) {
	         	$blogIds = implode( ",", $ids );
	         	$otherBlogs .= " AND p.blog_id NOT IN (".$blogIds.")";
            }
            if( $status != BLOG_STATUS_ALL )
            	$otherBlogs .= " AND b.status = '".Db::qstr( $status )."'";
            	
            $result = $this->Execute( $otherBlogs );
            // now we know to which he or she belongs, so we only have
            // to load the information about those blogs
            while( $row = $result->FetchRow($result)) {
				$id = $row["blog_id"];
                $usersBlogs[] = $blogs->getBlogInfo( $id );
            }
            $result->Close();

            sort($usersBlogs);
            
            return $usersBlogs;
        }
        
        /**
         * @private
         */
        function mapRow( $query_result )
        {
	        lt_include( PLOG_CLASS_PATH."class/dao/userinfo.class.php" );
	        
			isset( $query_result["properties"] ) ? $properties = unserialize( $query_result["properties"] ) : $properties = Array();			
			
            $userInfo = new UserInfo( $query_result["user"], 
			                          $query_result["password"],
                                      $query_result["email"],
                                      $query_result["about"],
                                      $query_result["full_name"],
                                      $query_result["resource_picture_id"],
                                      $properties,
                                      $query_result["id"]);
                                                                            
            // set some permissions
			isset( $query_result["site_admin"] ) ? $siteAdmin = $query_result["site_admin"] : $siteAdmin = 0;
            $userInfo->setSiteAdmin( $siteAdmin );
            $userInfo->setStatus( $query_result["status"] );

            return $userInfo;
        }        

        /**
         * Returns an array with all the users available in the database
         *
		 * @param status
		 * @param searchTerms
         * @param orderBy
         * @param page
         * @param itemsPerPage
         * @return An array containing all the users.
         */
        function getAllUsers( $status = USER_STATUS_ALL, $searchTerms = "", $orderBy = "", $page = -1, $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
        {
        }

        /**
         * Updates the information related to a user. This method should be called by child classes
         * so the user permissions (which are plog-specific) can be updated at the same time!
         *
         * @param userInfo An UserInfo object containing the <b>already udpated</b> information of the
         * user we would like to update.
         * @return Returns true if ok or false otherwise.
         */
        function updateUser( $userInfo )
        {
			lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );	        
            return( true );
        }

        /**
         * Adds a user to the database.
         *
         * @param user An UserInfo object with the necessary information
         * @return Returns the identifier assigned to the user, or false if there was any error. It will also modify the
		 * UserInfo object passed by parameter and set its database id.
         */
        function addUser( &$user )
        {
        }

        /**
         * Returns an array with all the users that belong to the given
         * blog.
         *
         * @param blogId The blog identifier.
         * @param includeOwner Wether to include the owner of the blog or not.
         * @param status
         * @param searchTerms
         * @return An array with the information about the users who belong in
         * one way or another to that blog.
         */
        function getBlogUsers( $blogId, $includeOwner = true, $status = USER_STATUS_ALL, $searchTerms = "" )
        {
            $users = Array();
	        $prefix = $this->getPrefix();

            // get the information about the owner, if requested so
            if( $includeOwner ) {
				lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
				$blogs = new Blogs();
				$blogInfo = $blogs->getBlogInfo( $blogId );				
                array_push( $users, $this->getUserInfoFromId( $blogInfo->getOwnerId()));
            }

            // now get the other users who have permission for that blog.
            $query2 = "SELECT DISTINCT user_id FROM {$prefix}users_permissions WHERE blog_id = '".Db::qstr( $blogId )."'";
            $result2 = $this->Execute( $query2 );
            if( !$result2 ) // if error, return what we have so far...
                return $users;

            while( $row = $result2->FetchRow()) {
                array_push( $users, $this->getUserInfoFromId( $row["user_id"] ));
            }
            $result2->Close();

            return $users;
        }
        
        /**
         * Removes users from the database
         *
         * @param userId The identifier of the user we are trying to remove
         */
        function deleteUser( $userId )
        {
        }        

        /**
         * returns the total number of users
         *
		 * @param status
		 * @param searchTerms
         * @return total number of users
         */
        function getNumUsers( $status = USER_STATUS_ALL, $searchTerms = "" )
        {
        }

        /**
         * check if the email account has been registered
         * @return true if the email account has been registered
         */
        function emailExists($email)
        {
        }

		/**
		 * @final
		 * Grants the given user is the login_perm. This is something that all integrations
		 * must use or else users won't be allowed to log in.
		 *
		 * @param userInfo A UserInfo object
		 * @return True if successful or false otherwise
		 */
		function grantLoginPermission( $userInfo )
		{
			lt_include( PLOG_CLASS_PATH."class/dao/permissions.class.php" );
			lt_include( PLOG_CLASS_PATH."class/dao/userpermission.class.php" );
			lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );
			
			$perms = new Permissions();
			$loginPerm = $perms->getPermissionByName( "login_perm" );
			$perm = new UserPermission( $userInfo->getId(),    // user id
			                            0,   // it's a global permission, no blog id needed
			                            $loginPerm->getId()  // id of the permission
			);
			$userPerms = new UserPermissions();
			$userPerms->grantPermission( $perm, true );
		}		
	}
?>