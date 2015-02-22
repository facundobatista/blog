<?php

    lt_include( PLOG_CLASS_PATH."class/dao/userdata/baseuserdataprovider.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/userinfo.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/userstatus.class.php" );
    lt_include( PLOG_CLASS_PATH."class/database/db.class.php" );
    
    /**
     * Model representing the users in our application. Provides the methods such as
     * authentication and querying for users.
	 *
	 * \ingroup User_Data_Providers
     */
    class LifeTypeUserDataProvider extends BaseUserDataProvider
    {

        /**
         * Initializes the model
         */
        function LifeTypeUserDataProvider( $providerConfig )
        {
            $this->BaseUserDataProvider( $providerConfig );
            
            $this->table = $this->getPrefix()."users";
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
        	$user = $this->getUserInfoFromUsername( $user );
        	if( $user ) {
	        	return( $user->getPassword() == md5($pass));
	        }
	        else {
	        	return( false );
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
            $userInfo = $this->getUserInfoFromUsername( $user );
            if ( $userInfo && ($userInfo->getPassword() == md5($pass)) ) {
                return $userInfo;
            } else {
                return false;
            }
        }

        /**
         * Retrieves the user information but given only a username
         *
         * @param username The username of the user
         * @return Returns a UserInfo object with the requested information, or false otherwise.
         */
        function getUserInfoFromUsername( $username )
        {
        	$user = $this->get( "user", $username, CACHE_USERIDBYNAME, Array( CACHE_USERINFO => "getId" ));
			if( $user ) {
				if( $user->getUsername() != $username ) {
					$user = false;
				}
			}

			return( $user );
        }

        /**
         * Retrieves the user infromation but given only a userid
         *
         * @param userId User ID of the user from whom we'd like to get the information
         * @return Returns a UserInfo object with the requested information, or false otherwise.
         */
        function getUserInfoFromId( $userid )
        {
        	return( $this->get( "id", $userid, CACHE_USERINFO, Array( CACHE_USERIDBYNAME => "getUsername" )));
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
        function getAllUsers( $status = USER_STATUS_ALL, 
		                      $searchTerms = "",
                              $orderBy = "",
							  $page = DEFAULT_PAGING_ENABLED, 
							  $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
        {   		
	    	$where = "";
	    	
	    	if( $status != USER_STATUS_ALL )
	    		$where = "status = '".Db::qstr($status)."'";

	    	if( $searchTerms != "" ) {
				if( $where != "" )
					$where .= " AND ";
	    	    $where .= $this->getSearchConditions( $searchTerms );
			}
			if( $where != "" )
				$where = "WHERE $where";

            if( $orderBy == "" )
                $orderBy = "id ASC";
			
            $query = "SELECT * FROM ".$this->getPrefix()."users $where ORDER BY $orderBy";

            $result = $this->Execute( $query, $page, $itemsPerPage );
			
            $users = Array();			
			
			if( !$result )
				return $users;
                
            while ($row = $result->FetchRow()) {
				$user = $this->mapRow( $row );
                $users[] = $user;
				// cache the data for later use
				$this->_cache->setData( $user->getId(), CACHE_USERINFO, $user );
				$this->_cache->setData( $user->getUsername(), CACHE_USERIDBYNAME, $user );
			}
            $result->Close();

            return $users;
        }
        
        /**
         * @see Model::buildSearchCondition
         */
        function buildSearchCondition( $searchTerms )
        {
            $searchTerms = trim( $searchTerms );
            $searchCond = "(user LIKE '%".Db::qstr($searchTerms)."%' 
                           OR full_name LIKE '%".Db::qstr($searchTerms)."%' OR 
                           email LIKE '%".Db::qstr($searchTerms)."%')";
            
            return( $searchCond );
        }

        /**
         * Updates the information related to a user
         *
         * @param userInfo An UserInfo object containing the <b>already udpated</b> information of the
         * user we would like to update.
         * @return Returns true if ok or false otherwise.
         */
        function updateUser( $user )
        {
        	$result = $this->update( $user );

			if( $result ) {
				// remove the old data
	            $this->_cache->removeData( $user->getId(), CACHE_USERINFO );
    	        $this->_cache->removeData( $user->getUsername(), CACHE_USERIDBYNAME );
    	    }
            
            BaseUserDataProvider::updateUser( $user );

            return $result;
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
        	$userId = $this->add( $user );

			if( $userId ) {
				// 1. We need to set the password again in this initial UserInfo object, because 
				//    current password is plain password. Through setPassword() we can encrpyt the password
				//    and make the UserInfo object right, then we can cache it. Or user can not login even
				//    we addUser() successfully.
				// 2. Another easy way to solve this is remove the cache code below, don't cache the UserInfo
				//    Object in the first time. Let it cache later.
				$user->setMD5Password( $user->getPassword() );
	        	$this->_cache->setData( $user->getId(), CACHE_USERINFO, $user );
    	    	$this->_cache->setData( $user->getUsername(), CACHE_USERIDBYNAME, $user );
    	    }
        	
        	return( $userId );
        }

        /**
         * Removes users from the database
         *
         * @param userId The identifier of the user we are trying to remove
         */
        function deleteUser( $userId )
        {
            // first, delete all of his/her permissions
            $user = $this->getUserInfoFromId( $userId );
            if( $this->delete( "id", $userId )) {            
	    		lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );
    	        $perms = new UserPermissions();
        	    $perms->revokeUserPermissions( $userId );
        	    $this->_cache->removeData( $userId, CACHE_USERINFO );
        	    $this->_cache->removeData( $user->getUsername(), CACHE_USERIDBYNAME );
                return true;
            }
            
            return false;
        }  
        
        /**
         * returns the total number of users
         *
         * @return total number of users
         */
        function getNumUsers( $status = USER_STATUS_ALL, $searchTerms = "" )
        {
            $table = $this->getPrefix()."users";
			    
			$where = "";
	    	if( $status != USER_STATUS_ALL )
	    		$where = "status = '".Db::qstr($status)."'";

	    	if( $searchTerms != "" ) {
				if( $where != "" )
					$where .= " AND ";
	    	    $where = $this->getSearchConditions( $searchTerms );
			}
				
			return( $this->getNumItems( $table, $where ));
        }

        /**
         * check if the email account has been registered
         * @return true if the email account has been registered
         */
        function emailExists($email) 
		{
            $query = "SELECT email 
                      FROM ".$this->getPrefix()."users 
                      WHERE email = '".Db::qstr($email)."'";

            $result = $this->Execute($query);

            if(!$result)
                return false;

            $count = $result->RecordCount();
            $result->Close(); 
            return ($count >= 1);
        }
		
		/**
		 * @see Model::getSearchConditions
		 */
		function getSearchConditions( $searchTerms )
		{
			lt_include( PLOG_CLASS_PATH."class/dao/searchengine.class.php" );			
			// prepare the query string
			$searchTerms = SearchEngine::adaptSearchString( $searchTerms );
			
			return( "(user LIKE '%".$searchTerms."%' OR full_name LIKE '%".$searchTerms."%')");
		}
    }
?>
