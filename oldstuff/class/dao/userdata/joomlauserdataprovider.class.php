<?php

    lt_include( PLOG_CLASS_PATH."class/dao/userdata/baseuserdataprovider.class.php" );
    lt_include( PLOG_CLASS_PATH."class/database/db.class.php" );
    
    define( "JOOMLA_USER_IS_BLOCKED", 1);
    define( "JOOMLA_USER_IS_ACTIVE", 0);
    define( "JOOMLA_SITE_ADMIN", "Super Administrator");
    
    
    /**
     * Model representing the users in our application. Provides the methods such as
     * authentication and querying for users.
	 *
	 * \ingroup User_Data_Providers
     */
     
    class JoomlaUserDataProvider extends BaseUserDataProvider
    //Based on phpbb2userdataprovider.class.php
    {
	    var $_db;
	    var $_joomladbprefix;
	    var $_joomlaauxtable;
	    var $_blogtitle_postfix;

        /**
         * Initializes the model
         */
        function JoomlaUserDataProvider( $providerConfig )
        {
            $this->BaseUserDataProvider( $providerConfig );
			
            //*** Temporarily disabled for causing a fatal error ***/
            // disable all caching for userdata
            //CacheManager::disableCache( CACHE_USERINFO );
            
            // initialize the database connection based on our parameters
            $config = $this->getProviderConfiguration();
            $user = $config->getValue( "user" );
            $pass = $config->getValue( "password" );
            $host = $config->getValue( "host" );
            $db = $config->getValue( "database" );
            $this->_joomladbprefix = $config->getValue( "prefix" );
            $this->_blogtitle_postfix = $config->getValue( "blogtitle_postfix" );
            //phpbb2_users table is created upon installation, we can use it for joomla too
            //but better parameterize its name
            $this->_joomlaauxtable="joomla_users";
            
            $this->_dbc =& Db::getNewDb( $host, $user, $pass, $db );                     
            
            //Oddly, there have been problems in reading Joomla! db in utf-8
            //Setting explicitly connection charset has fixed them
			$dbcharset = $config->getValue( "dbcharset" );
        	$query = "SET NAMES '".$dbcharset."';";
            $result = $this->_dbc->Execute( $query );
                   
            
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
        	$providerConfig = $this->getProviderConfiguration();
        	if( $providerConfig->getValue( "useNewJoomlaAuth" )) {
        		$query = "SELECT * FROM ".$this->_joomladbprefix."users".
	        			" WHERE username = '".$user."'".
	        			" AND block=".JOOMLA_USER_IS_ACTIVE;
        		$result = $this->_dbc->Execute( $query );
				if( !$result ) {
	        		return false; // no such user
				}
				$row = $result->FetchRow();
        		list($hash, $salt) = explode(':', $row['password']);
				$cryptpass = md5($pass.$salt);
				if ($hash != $cryptpass) {
					return false; // password don;t match
				}
        	}
        	else {
        		$query = "SELECT * FROM ".$this->_joomladbprefix."users".
	        			" WHERE username = '".$user."'".
	        			" AND password = '".md5($pass)."'".
	        			" AND block=".JOOMLA_USER_IS_ACTIVE;
        		$result = $this->_dbc->Execute( $query );
	        	if( !$result ){
	        		return false;
        		}
        	}
	        	
            $ret = ($result->RecordCount() == 1);
            $result->Close();

            if($ret)
                return true;
            else{
                return false;    	
            }
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
        	//Query Joomla db
        	$providerConfig = $this->getProviderConfiguration();
        	if( $providerConfig->getValue( "useNewJoomlaAuth" )) {
        		$query = "SELECT * FROM ".$this->_joomladbprefix."users".
	        			" WHERE username = '".$user."'".
	        			" AND block=".JOOMLA_USER_IS_ACTIVE;
        		$result = $this->_dbc->Execute( $query );
				if( !$result ) { 
	        		return false; // no such user
				}
				$row = $result->FetchRow();
        		list($hash, $salt) = explode(':', $row['password']);
				$cryptpass = md5($pass.$salt);
				if ($hash != $cryptpass) {
					return false; // password don;t match
				}
        	}
        	else {
        		$query = "SELECT * FROM ".$this->_joomladbprefix."users".
	        			" WHERE username = '".$user."'".
	        			" AND password = '".md5($pass)."'".
	        			" AND block=".JOOMLA_USER_IS_ACTIVE;
        		$result = $this->_dbc->Execute( $query );
	        	if( !$result ) {
	        		return false;
	        	}
	        	$row = $result->FetchRow();
        	}
        		        	
            $result->Close();
            
            $userid=$row["id"];
                      	
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
        	//Query Joomla table
	        $query = "SELECT * FROM ".$this->_joomladbprefix."users WHERE username = '".Db::qstr( $username )."'";	       	       
	                  
	        $result = $this->_dbc->Execute( $query );
	        
	        if( !$result ){
	        	return false;
        	}
	        		        	
	        if( $result->RowCount() != 1 ){
                $result->Close();
	        	return false;
            }
	        	
	        $row = $result->FetchRow();
            $result->Close();
            
            $userid=$row["id"];
	        
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
	        
	        $query = "SELECT * FROM ".$this->_joomladbprefix."users WHERE id = '".Db::qstr( $userid )."'";

			//print("user__id = $userid");
	                  
	        $result = $this->_dbc->Execute( $query );
	        
	        if( !$result )
	        	return false;
	        	
	        $row = $result->FetchRow();
            $result->Close();
	        
	        return( $this->_mapUserInfoObject( $row ));
        }
        
        function JoomlaAddBlog( $row )
        {
		    // create a new blog
		    lt_include( PLOG_CLASS_PATH."class/dao/bloginfo.class.php" );				
		    lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
            lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
            lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );
		    
		    $blogs = new Blogs();
		    $blog = new BlogInfo( $row["user"].$this->_blogtitle_postfix,  // name of the new blog
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
                                    $row["id"], 
                                    $newBlogId, 
                                    POST_STATUS_PUBLISHED, 
                                    0, 
                                    Array(), 
                                    "welcome" );
            $t = new Timestamp();
            $article->setDateObject( $t );
            $articles = new Articles();
            $articles->addArticle( $article );	           
        }
        
        /* Gets rows from Joomla user data table and auxiliary LT table and merges
         * them into a single userInfo object
         * 
         * @param row Joomla user data table row, as fetched
         * @param extraInfo Lifetype auxiliary table extra user data, as fetched
         * @return userInfo object, as neede by LT 
         */
        function _mapUserInfoObject( $row, $extraInfo=false)
        {
	        lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );
	        
	        $plogJoomlaData = $this->getpLogJoomlaUserData( $row["id"] );

			//Data fetched from Joomla db
	        //$row["id"] = $row["id"];   //no need to map
	        $row["user"] = $row["username"];
	        $row["password"] = $row["password"];
	        $row["email"] = $row["email"];
	        $row["full_name"] = $row["name"];
	        $row["status"] = ($row["block"] != 1) ? USER_STATUS_ACTIVE : USER_STATUS_DISABLED;
	        
	        //Data fetched from auxiliary table
	        //If no data fetched, these fields will be empty
	        if(!$plogJoomlaData){
		        $row["about"] = "";
		        $row["resource_picture_id"] = 0;
		        $row["properties"] = serialize(Array());
	        } else {
		        $row["about"] = $plogJoomlaData["about"];
		        $row["resource_picture_id"] = $plogJoomlaData["resource_picture_id"];
				if( $row["resource_picture_id"] == "" ) $plogJoomlaData["resource_picture_id"] = 0;
		        $row["properties"] = serialize($plogJoomlaData["properties"]);
	        }
		        
	        // If this is the first time user data is loaded on LT, Joomla Super administrator
	        // will be an LT site admin as well.
	        // Otherwise, get site admin status from auxiliary table	        
	        if(!$plogJoomlaData){
				$row["site_admin"] = ($row["usertype"]==JOOMLA_SITE_ADMIN);
	        }   else {
	        	$row["site_admin"] = $plogJoomlaData["blog_site_admin"];
	        }    
	        	        
	       	// does this Joomla user have a blog yet? If so, create one if the configuration
	        // of the user data provider says so
	        $providerConfig = $this->getProviderConfiguration();
	        if( $providerConfig->getValue( "createBlogIfNotExisting" )) {
		        $userInfo = BaseUserDataProvider::mapRow( $row, true );
		        // check if this user is assigned to any blog
		        $userBlogs = $userInfo->getBlogs();
		        if( empty($userBlogs )) {
					// assign the login_perm permission
					$this->grantLoginPermission( $userInfo );
								
			        $this->JoomlaAddBlog( $row );
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
            $query = "SELECT * FROM ".$this->_joomladbprefix."users ORDER BY id ASC";

            $result = $this->_dbc->Execute( $query, $page, $itemsPerPage );            

            $users = Array();

            while ($info = $result->FetchRow( $result )){
                array_push( $users, $this->_mapUserInfoObject( $info ));
            }
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
	        /* These should be accessible only from Joomla! */
	        /*
	        $query = "UPDATE ".$this->_joomladbprefix."users ".
		        "SET username = '".Db::qstr($userInfo->getUserName()).
		        "', password = '".Db::qstr(md5($userInfo->getPassword())).
		        "', email = '".Db::qstr($userInfo->getEmail()).
		        "', block = '".Db::qstr(($userInfo->getStatus()>USER_STATUS_ACTIVE)? JOOMLA_USER_IS_BLOCKED : JOOMLA_USER_IS_ACTIVE) .
		        "'  WHERE id = ".Db::qstr($userInfo->getId());
	                              
            $result = $this->_dbc->Execute( $query );            
            
            if( !$result ){
	            $this->log->debug("Error while updating joomla table. Query:\n".$query."\n" );
            	return false;
        	}
            
            BaseUserDataProvider::updateUser( $userInfo );
            */
            
			// update plog's joomla_user table
			$result = $this->updatepLogJoomlaUserData( $userInfo );

			return( $result );
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
	        /* 	User registration should be done via Joomla!/Mambo.*/
        }
        
        /**
         * @private
         * Updates the plog-specific user data that is used when the joomla integration is enabled, since
         * plog has some extra information that does not fit anywhere in joomla
         *
         * @param user A UserInfo object
         * @return true if successful or false otherwise
         */
        function updatepLogJoomlaUserData( &$user )
        {
	    	// is the user already there?
	    	if( $this->getpLogJoomlaUserData( $user->getId())) {
		    	// we need to run an UPDATE query...
		    	$query = "UPDATE ".$this->getPrefix().$this->_joomlaauxtable.
		    	         " SET about = '".Db::qstr( $user->getAboutMyself()).
		    	         "', properties = '".Db::qstr( serialize($user->getProperties())).
		    	         "', resource_picture_id = '".Db::qstr( $user->getPictureId()).
		    	         "', blog_site_admin = ".(Db::qstr( $user->isSiteAdmin() ? "1" : "0")).
		    	         "  WHERE joomla_id = ".Db::qstr( $user->getId());    
	    	}
	    	else {
		    	// we need to run an INSERT query...	
		    	$query = "INSERT INTO ".$this->getPrefix().$this->_joomlaauxtable."(joomla_id, about, properties, blog_site_admin, resource_picture_id) ".
		    			  " VALUES (".
		    			  Db::qstr($user->getId()).",'".
		    			  Db::qstr($user->getAboutMyself())."','".
		    	          Db::qstr(serialize($user->getProperties()))."',".
		    	          Db::qstr( $user->isSiteAdmin() ? "1" : "0").",'".
		    	          Db::qstr($user->getPictureId())."')";
	    	}
	    	
	    	$result = $this->Execute( $query );
	    	
			return( true );
        }
        
        /**
         * @private
         * Load the plog-specific joomla user data
         *
         * @param userId
         * @return A row with the extra user data or false otherwise
         */
        function getpLogJoomlaUserData( $userId )
        {
            
	        $query = "SELECT * FROM ".$this->getPrefix().$this->_joomlaauxtable." WHERE joomla_id = '".Db::qstr($userId)."'";
	        $result = $this->Execute( $query );
	        
	        if( !$result )
	        	return false;
	        	
	        if( $result->RowCount() == 0 ){
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
	        $query = "SELECT COUNT(id) AS total FROM ".$this->_joomladbprefix."users";
	        
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
	        $query = "SELECT * FROM ".$this->_joomladbprefix."users WHERE email = '".Db::qstr($email)."'";
	        
	        $result = $this->_dbc->Execute( $query );
	        
	        if( !$result )
	        	return false;
            $ret = ($result->RecordCount() > 0);
            $result->Close();
	        return $ret;
        }
    }
?>
