<?php

    lt_include( PLOG_CLASS_PATH."class/dao/userdata/baseuserdataprovider.class.php" );
    lt_include( PLOG_CLASS_PATH."class/database/db.class.php" );
    
    /**
     * Model representing the users in our application. Provides the methods such as
     * authentication and querying for users.
	 *
	 * \ingroup User_Data_Providers
     */
    class WBBUserDataProvider extends BaseUserDataProvider
    {
	    var $_db;
	    var $_prefix;
      var $_blogtitle_postfix;
      var $_adminusergroups;
        /**
         * Initializes the model
         */
        function WBBUserDataProvider( $providerConfig )
        {
            $this->BaseUserDataProvider( $providerConfig );

            // initialize the database connection based on our parameters
            $config = $this->getProviderConfiguration();
            $user = $config->getValue( "user" );
            $pass = $config->getValue( "password" );
            $host = $config->getValue( "host" );
            $db = $config->getValue( "database" );
            $this->_wbbprefix = $config->getValue( "prefix" );
            
            $this->_dbc =& Db::getNewDb( $host, $user, $pass, $db );                     
            $this->_blogtitle_postfix = $config->getValue( "blogtitle_postfix" );                   
            $this->_adminusergroups = $config->getValue( "admingroup");
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
	        $query = "SELECT * FROM ".$this->_wbbprefix."users WHERE username = '".Db::qstr( $user )."'
	                  AND password = '".md5( $pass )."' AND activation > 0";
	                  
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
	        $query = "SELECT * FROM ".$this->_wbbprefix."users WHERE username = '".Db::qstr( $user )."'
	                  AND password = '".md5( $pass )."'";
	                  
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
	        $query = "SELECT * FROM ".$this->_wbbprefix."users WHERE username = '".Db::qstr( $username )."'";	       	       
	                  
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
	        
	        $query = "SELECT * FROM ".$this->_wbbprefix."users WHERE userid = '".Db::qstr( $userid )."'";

//print("user__id = $userid");
	                  
	        $result = $this->_dbc->Execute( $query );
	        
	        if( !$result )
	        	return false;
	        	
	        $row = $result->FetchRow();
            $result->Close();
	        
	        // fetch the user permissions
	        //$perms = new UserPermissions();
	        //$row["site_admin"] = $perms->isSiteAdmin( $userid );
	        
	        return( $this->_mapUserInfoObject( $row ));
        }
        
        function WBB2AddBlog( $row )
        {
		    // create a new blog
		    lt_include( PLOG_CLASS_PATH."class/dao/bloginfo.class.php" );				
		    lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
            lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
            lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );

            $config =& Config::getConfig();
            $locale =& Locales::getLocale( $config->getValue( "default_locale" ));
		    
		    $blogs = new Blogs();
		    $blog = new BlogInfo( $row["user"].$this->_blogtitle_postfix,  // name of the new blog
		       	                  $row["id"],  // id of the owner
		           	              "",  // no about
		            	          ""); // no properties either
		    $newBlogId = $blogs->addBlog( $blog );
		     	    	     
            // add a default category and a default post            
            $articleCategories = new ArticleCategories();
            $articleCategory = new ArticleCategory( $locale->tr( "register_default_category" ), "", $newBlogId, true );
            $catId = $articleCategories->addArticleCategory( $articleCategory );
            $articleTopic = $locale->tr( "register_default_article_topic" );
            $articleText  = $locale->tr( "register_default_article_text" );
            $article = new Article( $articleTopic, 
                                    $articleText, 
                                    Array( $catId ), 
                                    $row["userid"], 
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
        
        function _mapUserInfoObject( $row, $extraInfo = false )
        {
	        lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );
	        
	        $plogWBB2Data = $this->getpLogWBBUserData( $row["userid"] );

	        $row["user"] = $row["username"];
	        $row["password"] = $row["password"];
	        $row["email"] = $row["email"];
	        $row["about"] = $plogWBB2Data["about"];
	        $row["full_name"] = $plogWBB2Data["full_name"];
	        $row["resource_picture_id"] = $plogWBB2Data["resource_picture_id"];
			if( $row["resource_picture_id"] == "" ) $row["resource_picture_id"] = 0;
	        $row["properties"] = serialize(Array());
	        $row["id"] = $row["userid"];   
	        $row["status"] = ($row["activation"] > 0) ? USER_STATUS_ACTIVE : USER_STATUS_DISABLED;
			    if (in_array($row["groupcombinationid"], $this->_adminusergroups)) $row["site_admin"] = '1';
          else  $row["site_admin"] = '0';
                  	        
	       	// does this wbb user have a blog yet? If so, create one if the configuration
	        // of the user data provider says so
	        $providerConfig = $this->getProviderConfiguration();
	        if( $providerConfig->getValue( "createBlogIfNotExisting" )) {
		        $userInfo = BaseUserDataProvider::mapRow( $row, true );
		        // check if this user is assigned to any blog
		        $userBlogs = $userInfo->getBlogs();
		        if( empty($userBlogs )) {
					// assign the login_perm permission
					$this->grantLoginPermission( $userInfo );			
			
			        $this->WBB2AddBlog( $row );
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
        function getAllUsers( $status = USER_STATUS_ALL, $searchTerms = "", $orderBy = "", $page = DEFAULT_PAGING_ENABLED, $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
        {	        
            $query = "SELECT * FROM ".$this->_wbbprefix."users WHERE userid >= 0 ORDER BY userid ASC";

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
	        $query = "UPDATE ".$this->_wbbprefix."users SET
	                  username = '".Db::qstr($userInfo->getUserName())."',
	                  email = '".Db::qstr($userInfo->getEmail())."',
	                  password = '".md5(Db::qstr($userInfo->getPassword()))."',
	                  sha1_password = '".sha1(Db::qstr($userInfo->getPassword()))."'
	                  WHERE userid = '".Db::qstr($userInfo->getId())."'";
	                              
            $result = $this->_dbc->Execute( $query );            
            
            if( !$result )
            	return false;
            
            BaseUserDataProvider::updateUser( $userInfo );
            
			// update plog's wbb_user table
			$result = $this->updatepLogWBBUserData( $userInfo );

			return( $result );
        }
        
        /**
         * @private
         * Why the hell couldn't they make the user_id field auto-incrementable???
         */
        function getLastWBBUserId()
        {
	       $query = "SELECT MAX(userid)+1 AS next_id FROM ".$this->_wbbprefix."users"; 
	       
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
	        // update the wbb table
	        $password = $user->getPassword();
	        $id = $this->getLastWBBUserId();
	        	
	        $query = "INSERT INTO ".$this->_wbbprefix."users (userid,username,password,sha1_password,email,groupcombinationid,rankid,regdate,lastvisit,lastactivity,usertext,signature,icq,aim,yim,msn,homepage,birthday,gender,showemail,admincanemail,usercanemail,invisible,usecookies,styleid,activation,daysprune,timezoneoffset,startweek,dateformat,timeformat,emailnotify,notificationperpm,receivepm,emailonpm,pmpopup,umaxposts,showsignatures,showavatars,showimages,threadview,langid,rankgroupid,useronlinegroupid,allowsigsmilies,allowsightml,allowsigbbcode,allowsigimages,usewysiwyg,reg_ipaddress) ".
			"VALUES ($id,'".Db::qstr($user->getUserName())."','".md5($user->getPassword())."', '".sha1($user->getPassword())."', '".Db::qstr($user->getEmail())."','4','4','".time()."','".time()."','".time()."','','','','','','','','0000-00-00','0','1','1','1','0','1','0','1','0','1','0','','','0','1','1','0','1','0','1','1','1','0','0','4','4','1','0','1','1','0', '".addslashes($_SERVER['REMOTE_ADDR'])."');";                      

            $result = $this->_dbc->Execute( $query );            

			    $query1 = "INSERT INTO ".$this->_wbbprefix."userfields (userid) VALUES ($id);";
					$result1 = $this->_dbc->Execute( $query1 );   


	        $query2 = "INSERT INTO ".$this->_wbbprefix."user2groups (userid,groupid) VALUES ('".$id."','4');";   
					$result2 = $this->_dbc->Execute( $query2 );   
                
      		$query3 = "UPDATE ".$this->_wbbprefix."stats SET usercount=usercount+1, lastuserid='".$id."';";
          $result3 = $this->_dbc->Execute( $query3 );   

          if( !$result || !$result1 || !$result2 || !$result3)
                return false;	      

			$user->setId( $id );
			
			// update plog's wbb2_user table
			$this->updatepLogWBBUserData( $user );

            return( $id );
        }
        
        /**
         * @private
         * Updates the plog-specific user data that is used when the wbb2 integration is enabled, since
         * plog has some extra information that does not fit anywhere in wbb2
         *
         * @param user A UserInfo object
         * @return true if successful or false otherwise
         */
        function updatepLogWBBUserData( &$user )
        {
	    	// is the user already there?
	    	if( $this->getpLogWBBUserData( $user->getId())) {
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
         * Load the plog-specific wbb2 user data
         *
         * @param userId
         * @return A row with the extra user data or false otherwise
         */
        function getpLogWBBUserData( $userId )
        {
	        $query = "SELECT * FROM ".$this->getPrefix()."phpbb2_users WHERE phpbb_id = '".Db::qstr($userId)."'";
	        
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
	        $query = "SELECT COUNT(id) AS total FROM ".$this->_wbbprefix."users";
	        
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
	        $query = "SELECT * FROM ".$this->_wbbprefix."users WHERE email = '".Db::qstr($email)."'";
	        
	        $result = $this->_dbc->Execute( $query );
	        
	        if( !$result )
	        	return false;
            $ret = ($result->RecordCount() > 0);
            $result->Close();
	        return $ret;
        }
    }
?>
