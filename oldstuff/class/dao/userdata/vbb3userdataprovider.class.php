<?php

/*
 * Name:    vbb3userdataprovider (support read user info from vbb)
 * Version: 1.0
 * Author:  Pan Ying(http://www.pactofshadow.com/lifetype/~nest)
 * Contact: panying2000@gmail.com
 * Release: 2006.10.5
 * Download Link:http://www.pactofshadow.com/lifetype/2/articleperma/17.html
 * 
 * Known Issue:
 *    Could not update user info in vbb.
 *    Could not delete user from vbb
 *    Do not support vbb user's Muti-user group , only support main group (todo in future)  
 * 
 *   This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                  
 */


    lt_include( PLOG_CLASS_PATH."class/dao/userdata/baseuserdataprovider.class.php" );
    lt_include( PLOG_CLASS_PATH."class/database/db.class.php" );
    
    /**
     * Model representing the users in our application. Provides the methods such as
     * authentication and querying for users.
	 *
	 * \ingroup User_Data_Providers
     */
    class vbb3UserDataProvider extends BaseUserDataProvider
    {
	    var $_dbc;                   //database connect
	    var $_vbb3prefix;            //vbb database prefix
	    

	    var $_usepasswordsalt;       //vbb3 use password salt
	    var $_allowedusergroups;     //which group in vbb will be active .
	    var $_disallowedusergroups;  //which group in vbb will be not active , if you have block group , set it
	    
	    var $_adminusergroups;       //which group in vbb will have admin permission?
	    var $_adminusers;            //special user in vbb to have admin permission.

        /**
         * Initializes the model
         */
        function vbb3UserDataProvider( $providerConfig )
        {
            $this->BaseUserDataProvider( $providerConfig );

            // initialize the database connection based on our parameters
            $config = $this->getProviderConfiguration();
            $user = $config->getValue( "user" );
            $pass = $config->getValue( "password" );
            $host = $config->getValue( "host" );
            $db = $config->getValue( "database" );
            
            $this->_vbb3prefix = $config->getValue( "prefix" );
            $this->_usepasswordsalt = $config->getValue( "usesalt" );
            $this->_allowedusergroups = $config->getValue( "allowgroup" );
            $this->_disallowedusergroups = $config->getValue( "denygroup" );
            $this->_adminusergroups = $config->getValue( "admingroup");
            $this->_adminusers = $config->getValue( "adminuser");
            
            
            $this->_dbc =& Db::getNewDb( $host, $user, $pass, $db );                     
        }
        
        function vbbAllowed( $row )
        {
        	  //echo "vbbAllowed called".$row['usergroupid'];
        	  if (!in_array($row['usergroupid'], $this->_disallowedusergroups))
        	    if (in_array($row['usergroupid'], $this->_allowedusergroups))
        	      return true;  
        	      
        	  // echo "vbbAllowed return false";      	         	
        	      
        	  return false;
        }
        
        function vbbAdmin( $row )
        {
        	  //echo "vbbAdmin called";
        	  if (in_array($row['usergroupid'], $this->_adminusergroups))
        	     return true;
        	     
        	  if (in_array($row['userid'], $this->_adminusers))
        	     return true;  
        	     
        	  //echo "vbbAdmin return false";     	     
        	     
        	  return false;        	  
        }
        
        function vbbCheckPassword( $pass , $row )
        {
        	 //echo "vbbCheckPassword called";
        	 if ($this->_usepasswordsalt)
        	 {
        	 	if(md5(md5($pass) .  $row['salt']) == $row['password']) return true;
        	 }
        	 else
        	 {
        	 	 if(md5($pass) == $row['password']) return true;
        	 }
        	   
        	 
        	 return false;
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
        	$query = "SELECT * FROM ".$this->_vbb3prefix."user WHERE username = '".Db::qstr( $user )."'";	                  
	                  
	        $result = $this->_dbc->Execute( $query );        
	        
	        
	        if( !$result )
	        	return false;
	        	
          $ret = ($result->RecordCount() == 1);  
                  
          if ($ret) $row = $result->FetchRow();
                    
          $result->Close();    
            

          if($ret && $this->vbbCheckPassword($pass,$row) && $this->vbbAllowed($row))
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
	        $query = "SELECT * FROM ".$this->_vbb3prefix."user WHERE username = '".Db::qstr( $user )."'";
	                  
	                  
	        $result = $this->_dbc->Execute( $query );
	        
	        if( !$result )
	        	return false;
	        	
	        $row = $result->FetchRow();
          $result->Close();
          
          if (!$this->vbbCheckPassword($pass,$row))
            return false;

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
	        $query = "SELECT * FROM ".$this->_vbb3prefix."user WHERE username = '".Db::qstr( $username )."'";	       	       
	                  
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
	        
	        
	        $query = "SELECT * FROM ".$this->_vbb3prefix."user WHERE userid = '".Db::qstr( $userid )."'";

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
        
        function vbb3AddBlog( $row )
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
            $articles = new Articles();
            $articles->addArticle( $article );	           
        }
        
        function _mapUserInfoObject( $row, $extraInfo = false )
        {
	        lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );
	        
	        $plogPhpBB2Data = $this->getpLogPHPBBUserData( $row["userid"] );

	        $row["user"] = $row["username"];
	        //$row["password"] = $row["password"]; //todo
	        $row["email"] = $row["email"];
	        $row["about"] = $plogPhpBB2Data["about"];
	        $row["full_name"] = $plogPhpBB2Data["full_name"];
	        $row["resource_picture_id"] = $plogPhpBB2Data["resource_picture_id"];
			    if( $row["resource_picture_id"] == "" ) 
			      $row["resource_picture_id"] = 0;
	        $row["properties"] = serialize(Array());
	        $row["id"] = $row["userid"];   
	        $row["status"] = $this->vbbAllowed($row) ? USER_STATUS_ACTIVE : USER_STATUS_DISABLED; 
			    $row["site_admin"] = $this->vbbAdmin($row)?1:0;	 
	        	        
	       	// does this vbb3 user have a blog yet? If so, create one if the configuration
	        // of the user data provider says so
	        $providerConfig = $this->getProviderConfiguration();
	        if( $providerConfig->getValue( "createBlogIfNotExisting" )) {
		        $userInfo = BaseUserDataProvider::mapRow( $row, true );
		        // check if this user is assigned to any blog
		        $userBlogs = $userInfo->getBlogs();
		        if( empty($userBlogs )) {
					// assign the login_perm permission
					$this->grantLoginPermission( $userInfo );			
			
			        $this->vbb3AddBlog( $row );
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
	          $where = "";
	        	switch ($status)
	        	{
	        	case user_status_all:
	        	  $where = "";	        	  
	        	  break;        	  
	        	case user_status_active:
	        	  $where = "usergroupid in (".implode(",", $this->_allowedusergroups).")";        	  
	        	  break;
	        	case user_status_unconfirmed:
	        	case user_status_disabled:
	        	  $where = "not(usergroupid in (".implode(",", $this->_allowedusergroups)."))";        	  
	        	  break;       	  
	        	}
	        	
	        	if ($searchTerms != "")
	        	{
	        	  if ($where != "")
	        	    $where = $where." AND ".($this->getSearchConditions($searchTerms));
	        	  else
	        	    $where = $this->getSearchConditions($searchTerms);
	        	}
	        	  
	        	
	        	if ($where != "")
	        	  $where = " where ".$where;
	        	  
	        	$query = "SELECT * FROM ".$this->_vbb3prefix."user".$where." ORDER BY userid ASC";

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
        	BaseUserDataProvider::updateUser( $userInfo );
        	return $this->updatepLogPHPBB2UserData( $userInfo ); //nerver change data in vbb table , just return the updatepLogPHPBB2UserData' return value
        	
	        $query = "UPDATE ".$this->_vbb3prefix."user SET
	                  username = '".Db::qstr($userInfo->getUserName())."',
	                  email = '".Db::qstr($userInfo->getEmail())."',
	                  //user_active = '".Db::qstr($userInfo->getPassword())."'
	                  WHERE userid = '".Db::qstr($userInfo->getId())."'";//todo
	                              
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
	       $query = "SELECT MAX(userid)+1 AS next_id FROM ".$this->_vbb3prefix."user"; 
	       
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
	        //return false; //nerver change data in vbb table , just tell pblog can not do that
	        $password = $user->getPassword();
	        $id = $this->getLastPhpBBUserId();
	        	
	        $query = "INSERT INTO ".$this->_vbb3prefix."user (userid,username,password,useremail)
	                  VALUES ($id, '".Db::qstr($user->getUserName())."','".md5($user->getPassword())."','".
                      Db::qstr($user->getEmail())."');";                      
                      
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
         * Updates the plog-specific user data that is used when the vbb3 integration is enabled, since
         * plog has some extra information that does not fit anywhere in vbb3
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
         * Load the plog-specific vbb3 user data
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
        function getNumUsers( $status = USER_STATUS_ALL , $searchTerms = "" )
        {
        	$where = "";
        	switch ($status)
        	{
        	case user_status_all:
        	  $where = "";        	  
        	  break;        	  
        	case user_status_active:
        	  $where = "usergroupid in (".implode(",", $this->_allowedusergroups).")";        	  
        	  break;
        	case user_status_unconfirmed:
        	case user_status_disabled:
        	  $where = "not(usergroupid in (".implode(",", $this->_allowedusergroups)."))";        	  
        	  break;       	  
        	}
        	
        	if ($searchTerms != "")
        	{
        	  if ($where != "")
        	    $where = $where." AND ".$this->getSearchConditions($searchTerms);
        	  else
        	    $where = $this->getSearchConditions($searchTerms);
        	}
        	  
        	
        	if ($where != "")
        	  $where = " where ".$where;
        	  
        	$query = "SELECT COUNT(userid) AS total FROM ".$this->_vbb3prefix."user".$where; 
	        
	        
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
	        $query = "SELECT * FROM ".$this->_vbb3prefix."user WHERE email = '".Db::qstr($email)."'";
	        
	        $result = $this->_dbc->Execute( $query );
	        
	        if( !$result )
	        	return false;
            $ret = ($result->RecordCount() > 0);
            $result->Close();
	        return $ret;
        }
        
        
        /**
				 * @see Model::getSearchConditions
				 */
				function getSearchConditions( $searchTerms )
				{
					lt_include( PLOG_CLASS_PATH."class/dao/searchengine.class.php" );			
					// prepare the query string
					$searchTerms = SearchEngine::adaptSearchString( $searchTerms );
					
					return( "(username LIKE '%".$searchTerms."%')");
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
          $userids = Array();
          $users = Array();
	        $prefix = $this->getPrefix();       
	          

            // get the information about the owner, if requested so
            if( $includeOwner ) {
                $query = "SELECT {$prefix}blogs.owner_id as userid FROM {$prefix}blogs 
                          WHERE {$prefix}blogs.id = '".Db::qstr($blogId)."';";
                $result = $this->Execute( $query );

                if( !$result )
                    return $users;

                $row = $result->FetchRow();
                $result->Close();               
                
                array_push($userids,$row['userid']);                
            }

            // now get the other users who have permission for that blog.
            $query2 = "SELECT {$prefix}users_permissions.user_id as userid FROM {$prefix}users_permissions 
                       WHERE {$prefix}users_permissions.blog_id = '".Db::qstr($blogId)."';";
            $result2 = $this->Execute( $query2 );
            
            if( $result2 )
            {
            	while( $row = $result2->FetchRow()) {
                array_push($userids,$row['userid']);
	            }
	            $result2->Close();            	
            }
            
            
            if (!is_array($userids)) //return empty value
            {
            	 return $users;            	
            }
            
            
            $where = "";
	        	switch ($status)
	        	{
	        	case user_status_all:
	        	  $where = "";	        	  
	        	  break;        	  
	        	case user_status_active:
	        	  $where = "usergroupid in (".implode(",", $this->_allowedusergroups).")";        	  
	        	  break;
	        	case user_status_unconfirmed:
	        	case user_status_disabled:
	        	  $where = "not(usergroupid in (".implode(",", $this->_allowedusergroups)."))";        	  
	        	  break;       	  
	        	}
	        	
	        	if ($searchTerms != "")
	        	{
	        	  if ($where != "")
	        	    $where = $where." AND ".($this->getSearchConditions($searchTerms));
	        	  else
	        	    $where = $this->getSearchConditions($searchTerms);
	        	}
	        	
	        	if ($where != "")
	        	  $where = $where." AND ";
	        	  
	        	$where = $where." (userid in (".implode(",", $userids)."))";     	
	        	
	        	  
	        	
	        	if ($where != "")
	        	  $where = " where ".$where;
	        	  
	        	$query3 = "SELECT * FROM ".$this->_vbb3prefix."user".$where." ORDER BY userid ASC";        	
	        	

            $result3 = $this->_dbc->Execute( $query3);            

            if($result3){
                while ($info = $result3->FetchRow( $result3 ))
                    array_push( $users, $this->_mapUserInfoObject( $info ));
                $result3->Close();
            }
            return $users;	
        }
    }
?>
