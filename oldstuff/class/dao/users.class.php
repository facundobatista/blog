<?php
    
    /**
     * Model representing the users in our application. Provides the methods such as
     * authentication and querying for users.
	 *
	 * \ingroup DAO
     */
    class Users 
    {
	    var $_provider;

        /**
         * Initializes the model
         */
        function Users()
        {
            $this->_loadUserDataProvider();
        }
        
        /** 
         * @private
         * loads the user data provider specified in the config file
         */
        function _loadUserDataProvider()
        {
	        // load the config file
			lt_include( PLOG_CLASS_PATH."class/config/configfilestorage.class.php" );
	        $config = new ConfigFileStorage( Array( "file" => PLOG_CLASS_PATH."config/userdata.properties.php" ));	     
	        
	        // see which class has been configured as the user data provider
	        $providerClass = $config->getValue( "provider" );
	        if( !$providerClass ) {
		     	die( "ERROR: No provider class was specified in userdata.properties.php!" );   
	        }
	        
	        // try to load the class
	        lt_include( PLOG_CLASS_PATH."class/dao/userdata/".strtolower( $providerClass ).".class.php" );
	        
	        // try to check if the class has been defined at all...
	        if( !class_exists( $providerClass )) {
		     	die( "ERROR: Provider class $providerClass has not been defined!" );   
	        }
	        	        
	        // and if so, we can create an instance of it
	        $this->_provider = new $providerClass( $config );	        
	        
	        // all is well...
	        return( true );
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
	        return( $this->_provider->authenticateUser( $user, $pass ));
        }

        /**
         * Retrieves the user information but given only a username
         *
         * @param username The username of the user
         * @return Returns a UserInfo object with the requested information, or false otherwise.
         */
        function getUserInfoFromUsername( $username )
        {
	        return( $this->_provider->getUserInfoFromUsername( $username ));
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
			return( $this->_provider->getUserInfo( $user, $pass ));
        }

        /**
         * Retrieves the user infromation but given only a userid
         *
         * @param userId User ID of the user from whom we'd like to get the information
         * @return Returns a UserInfo object with the requested information, or false otherwise.
         */
        function getUserInfoFromId( $userid, $extendedInfo = false )
        {
	        return( $this->_provider->getUserInfoFromId( $userid, $extendedInfo ));
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
	        return( $this->_provider->getUsersBlogs( $userid, $status ));
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
	        return( $this->_provider->getAllUsers( $status, $searchTerms, $orderBy, $page, $itemsPerPage ));	  
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
	        return( $this->_provider->updateUser( $userInfo ));
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
	        return( $this->_provider->addUser( $user ));
        }

        /**
         * Returns an array with all the users that belong to the given
         * blog.
         *
         * @param blogId The blog identifier.
         * @param includeOwner Wether to include the owner of the blog or not.
         * @return An array with the information about the users who belong in
         * one way or another to that blog.
         */
        function getBlogUsers( $blogId, $includeOwner = true, $status = USER_STATUS_ALL )
        {
			return( $this->_provider->getBlogUsers( $blogId, $includeOwner, $status ));
        }
        
        /**
         * Removes users from the database
         *
         * @param userId The identifier of the user we are trying to remove
         */
        function deleteUser( $userId )
        {
	        return( $this->_provider->deleteUser( $userId ));
        }
        
        /**
         * returns the total number of users
		 *
		 * @param status
		 * @param searchTerms
         *
         * @return total number of users
         */
        function getNumUsers( $status = USER_STATUS_ALL, $searchTerms = "" )
        {
	        return( $this->_provider->getNumUsers( $status, $searchTerms ));
        }

        /**
         * check if the email account has been registered
         * @return true if the email account has been registered
         */
        function emailExists($email)
        {
	        return( $this->_provider->emailExists( $email ));
        }

		/**
		 * @see BaseUserDataProvider::mapRow()
		 */
		function mapRow( $row )
		{
			return( $this->_provider->mapRow( $row ));
		}
		
    }
?>
