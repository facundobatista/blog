<?php

    lt_include( PLOG_CLASS_PATH."class/dao/userdata/baseuserdataprovider.class.php" );
    lt_include( PLOG_CLASS_PATH."class/database/db.class.php" );
    
    /**
     * Model representing the users in our application. Provides the methods such as
     * authentication and querying for users.
	 *
	 * \ingroup User_Data_Providers
     */
    class PhpBB2UserDataProvider extends BaseUserDataProvider
    {
	    var $_db;
	    var $_prefix;

        /**
         * Initializes the model
         */
        function PhpBB2UserDataProvider( $providerConfig )
        {
            $this->BaseUserDataProvider( $providerConfig );

            // initialize the database connection based on our parameters
            $config = $this->getProviderConfiguration();
            $user = $config->getValue( "user" );
            $pass = $config->getValue( "password" );
            $host = $config->getValue( "host" );
            $db = $config->getValue( "database" );
            $this->_phpbbprefix = $config->getValue( "prefix" );
            
            $this->_dbc =& Db::getNewDb( $host, $user, $pass, $db );                     
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
	        $query = "SELECT * FROM ".$this->_phpbbprefix."users WHERE username = '".Db::qstr( $user )."'
	                  AND user_password = '".md5( $pass )."' AND user_active > 0";
	                  
	        $result = $this->_dbc->Execute( $query );
	        
	        if( !$result )
	        	return false;
	        	
            $ret = ($result->RecordCount() == 1);
            $result->Close();

            if($ret)
                return true;
            else
                return false;    	
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
	        $query = "SELECT * FROM ".$this->_phpbbprefix."users WHERE username = '".Db::qstr( $user )."'
	                  AND user_password = '".md5( $pass )."'";
	                  
	        $result = $this->_dbc->Execute( $query );
	        
	        if( !$result )
	        	return false;
	        	
	        $row = $result->FetchRow();
            $result->Close();

	        return( $this->_mapUserInfoObject( $row ));	        
        }

        /**
         * Retrieves the user information but given only a username
         *
         * @param username The username of the user
         * @return Returns a UserInfo object with the requested information, or false otherwise.
         */
        function getUserInfoFromUsername( $username )
        {
	        $query = "SELECT * FROM ".$this->_phpbbprefix."users WHERE username = '".Db::qstr( $username )."'";	       	       
	                  
	        $result = $this->_dbc->Execute( $query );
	        
	        if( !$result )
	        	return false;
	        		        	
	        if( $result->RowCount() == 0 ){
                $result->Close();
	        	return false;
            }
	        	
	        $row = $result->FetchRow();
            $result->Close();
	        
	        return( $this->_mapUserInfoObject( $row ));	        
        }

        /**
         * Retrieves the user infromation but given only a userid
         *
         * @param userId User ID of the user from whom we'd like to get the information
         * @return Returns a UserInfo object with the requested information, or false otherwise.
         */
        function getUserInfoFromId( $userid, $extendedInfo = false )
        {
	        lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );
	        
	        $query = "SELECT * FROM ".$this->_phpbbprefix."users WHERE user_id = '".Db::qstr( $userid )."'";

	        $result = $this->_dbc->Execute( $query );
	        
	        if( !$result )
	        	return false;
	        	
	        $row = $result->FetchRow();
            $result->Close();
	        
	        return( $this->_mapUserInfoObject( $row ));
        }
        
        function phpBB2AddBlog( $row )
        {
		    // create a new blog
		    lt_include( PLOG_CLASS_PATH."class/dao/bloginfo.class.php" );		
		    lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
            lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
            lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );
		    
		    $blogs = new Blogs();
		    $blog = new BlogInfo( $row["user"],  // name of the new blog
		       	                  $row["id"],  // id of the owner
		           	              "",  // no about
		            	          ""); // no properties either
		    $newBlogId = $blogs->addBlog( $blog );
		     	    	     
            // add a default category and a default post            
            $articleCategories = new ArticleCategories();
            $articleCategory = new ArticleCategory( "General", "", $newBlogId, true );
            $catId = $articleCategories->addArticleCategory( $articleCategory );
            $config =& Config::getConfig();
            $locale =& Locales::getLocale( $config->getValue( "default_locale" ));
            $articleTopic = $locale->tr( "register_default_article_topic" );
            $articleText  = $locale->tr( "register_default_article_text" );
            $article = new Article( $articleTopic, 
                                    $articleText, 
                                    Array( $catId ), 
                                    $row["user_id"], 
                                    $newBlogId, 
                                    POST_STATUS_PUBLISHED, 
                                    0, 
                                    Array(), 
                                    "welcome" );
            $t = new Timestamp();
            $article->setDateObject( $t );
            $article->setInSummary( false );
            $articles = new Articles();
            $articles->addArticle( $article );	           
        }
        
        function _mapUserInfoObject( $row, $extraInfo = false )
        {
	        lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );
	        
	        $plogPhpBB2Data = $this->getpLogPHPBBUserData( $row["user_id"] );

	        $row["user"] = $row["username"];
	        $row["password"] = $row["user_password"];
	        $row["email"] = $row["user_email"];
	        $row["about"] = $plogPhpBB2Data["about"];
	        $row["full_name"] = $plogPhpBB2Data["full_name"];
	        $row["resource_picture_id"] = $plogPhpBB2Data["resource_picture_id"];
			if( $row["resource_picture_id"] == "" ) $row["resource_picture_id"] = 0;
	        $row["properties"] = serialize(Array());
	        $row["id"] = $row["user_id"];   
	        $row["status"] = ($row["user_active"] > 0) ? USER_STATUS_ACTIVE : USER_STATUS_DISABLED;
			$row["site_admin"] = $row["user_level"];	     
	        	        
	       	// does this phpbb user have a blog yet? If so, create one if the configuration
	        // of the user data provider says so
	        $providerConfig = $this->getProviderConfiguration();
	        if( $providerConfig->getValue( "createBlogIfNotExisting" )) {
		
		
		        $userInfo = BaseUserDataProvider::mapRow( $row, true );
		        // check if this user is assigned to any blog
		        $userBlogs = $userInfo->getBlogs();
		        if( empty($userBlogs )) {
					// assign the login_perm permission
					$this->grantLoginPermission( $userInfo );
								
			        $this->phpBB2AddBlog( $row );
			        $userInfo->setBlogs( $this->getUsersBlogs( $userInfo->getId()));
     			}				
	        }
	        else {
		        $userInfo = BaseUserDataProvider::mapRow( $row );
	        }
	        
	        return( $userInfo );
        }

        /**
         * Returns an array with all the users available in the database
         *
		 * @param status
		 * @param includeExtraInfo
         * @param page
         * @param itemsPerPage
         * @return An array containing all the users.
         */
        function getAllUsers( $status = USER_STATUS_ALL, $searchTerms = "", $orderBy = "", $page = -1, $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
        {	        
            $query = "SELECT * FROM ".$this->_phpbbprefix."users WHERE user_id >= 0 ORDER BY user_id ASC";

            $result = $this->_dbc->Execute( $query, $page, $itemsPerPage );            

            $users = Array();

            while ($info = $result->FetchRow( $result ))
                array_push( $users, $this->_mapUserInfoObject( $info ));
            $result->Close();

            return $users;	        	        
        }

        /**
         * Updates the information related to a user
         *
         * @param userInfo An UserInfo object containing the <b>already udpated</b> information of the
         * user we would like to update.
         * @return Returns true if ok or false otherwise.
         */
        function updateUser( $userInfo )
        {
	        $query = "UPDATE ".$this->_phpbbprefix."users SET
	                  username = '".Db::qstr($userInfo->getUserName())."',
	                  user_email = '".Db::qstr($userInfo->getEmail())."',
	                  user_active = '".Db::qstr($userInfo->getPassword())."'
	                  WHERE user_id = '".Db::qstr($userInfo->getId())."'";
	                              
            $result = $this->_dbc->Execute( $query );            
            
            if( !$result )
            	return false;
            
            BaseUserDataProvider::updateUser( $userInfo );
            
			// update plog's phpbb2_user table
			$result = $this->updatepLogPHPBB2UserData( $userInfo );

			return( $result );
        }
        
        /**
         * @private
         * Why the hell couldn't they make the user_id field auto-incrementable???
         */
        function getLastPhpBBUserId()
        {
	       $query = "SELECT MAX(user_id)+1 AS next_id FROM ".$this->_phpbbprefix."users"; 
	       
	       $result = $this->_dbc->Execute( $query );
	       
	       $row = $result->FetchRow();
           $result->Close();
	       
	       return( $row["next_id"] );
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
	        // update the phpbb table
	        $password = $user->getPassword();
	        $id = $this->getLastPhpBBUserId();
	        	
	        $query = "INSERT INTO ".$this->_phpbbprefix."users (user_id,username,user_password,user_email,user_active)
	                  VALUES ($id, '".Db::qstr($user->getUserName())."','".md5($user->getPassword())."','".
                      Db::qstr($user->getEmail())."','1');";                      
                      
            $result = $this->_dbc->Execute( $query );            

            if( !$result )
                return false;
			
			$user->setId( $id );
			
			// update plog's phpbb2_user table
			$this->updatepLogPHPBB2UserData( $user );

            return( $id );
        }
        
        /**
         * @private
         * Updates the plog-specific user data that is used when the phpbb2 integration is enabled, since
         * plog has some extra information that does not fit anywhere in phpbb2
         *
         * @param user A UserInfo object
         * @return true if successful or false otherwise
         */
        function updatepLogPHPBB2UserData( &$user )
        {
	    	// is the user already there?
	    	if( $this->getpLogPHPBBUserData( $user->getId())) {
		    	// we need to run an UPDATE query...
		    	$query = "UPDATE ".$this->getPrefix()."phpbb2_users
		    	          SET full_name = '".Db::qstr( $user->getFullName())."', 
		    	              about = '".Db::qstr( $user->getAboutMyself())."',
		    	              properties = '".Db::qstr( serialize($user->getProperties()))."',
		    	              resource_picture_id = '".Db::qstr( $user->getPictureId())."',
		    	              status = '".Db::qstr( $user->getStatus())."'
		    	          WHERE phpbb_id = '".Db::qstr( $user->getId())."'";    
	    	}
	    	else {
		    	// we need to run an INSERT query...
		    	$query = "INSERT INTO ".$this->getPrefix()."phpbb2_users
		    	          (full_name, about, properties, resource_picture_id,phpbb_id,status)
		    	          VALUES ('".Db::qstr( $user->getFullName())."', '".
		    	          Db::qstr($user->getAboutMyself())."','".
		    	          Db::qstr(serialize($user->getProperties()))."','".
		    	          Db::qstr($user->getPictureId())."','".
		    	          Db::qstr($user->getId())."','".
		    	          Db::qstr($user->getStatus())."');";
	    	}
	    	
	    	$result = $this->Execute( $query );
	    	
			return( true );
        }
        
        /**
         * @private
         * Load the plog-specific phpbb2 user data
         *
         * @param userId
         * @return A row with the extra user data or false otherwise
         */
        function getpLogPHPBBUserData( $userId )
        {
	        $query = "SELECT * FROM ".$this->getPrefix()."phpbb2_users WHERE phpbb_id = '".Db::qstr($userId)."'";
	        
	        $result = $this->Execute( $query );
	
	        if( !$result ) 
	        	return false;
	        	
	        if( $result->RowCount() == 0 ) {
                $result->Close();
                return false;
            }

            $ret = $result->FetchRow();
            $result->Close();

	        return $ret;
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
         * @return total number of users
         */
        function getNumUsers( $status = USER_STATUS_ALL )
        {
	        //
	        // :TODO:
	        // add the status check here!
	        //
	        $query = "SELECT COUNT(id) AS total FROM ".$this->_phpbbprefix."users";
	        
	        $result = $this->_dbc->Execute( $query );
	        
	        // return no users if this doesn't work!
	        if( !$result )
	        	return 0;
	        
	        $row = $result->FetchRow();
            $result->Close();
	        
	        if( $row["total"] == "" )
	        	$row["total"] = 0;
	        	
	        return( $row["total"] );
        }

        /**
         * check if the email account has been registered
         * @return true if the email account has been registered
         */
        function emailExists($email)        
        {
	        $query = "SELECT * FROM ".$this->_phpbbprefix."users WHERE user_email = '".Db::qstr($email)."'";
	        
	        $result = $this->_dbc->Execute( $query );
	        
	        if( !$result )
	        	return false;
            $ret = ($result->RecordCount() > 0);
            $result->Close();
	        return $ret;
        }
    }
?>
