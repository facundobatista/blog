<?php

	lt_include( PLOG_CLASS_PATH."class/database/dbobject.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/userstatus.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/userpermission.class.php" );	

    /**
     * Represents a user in our application. Includes information such as the username,
     * password, email, etc.
	 *
	 * \ingroup DAO
     */
	class UserInfo extends DbObject 
	{

		var $_username;
		var $_password;
		var $_id;
		var $_aboutmyself;
		var $_email;
		var $_blogs;
        var $_siteAdmin;
        var $_fullName;
		var $_resourcePictureId;
		var $_resourcePicture;
		var $_status;
		var $_perms;

		/**
		 * Constructor. Creates a new UserInfo object with the given information.
		 *
		 * @param username Uniquename assigned to this user. Please use the UsernameValidator class before
		 * assigning a username to make sure that the username complies with the rules set by the administrator
		 * of the site and that it has not been taken. This class does not perform any of these checks.
		 * @param password Unencrypted version of this password (passwords are saved encoded as MD5 strings)
		 * @param email Email address of the new user.
		 * @param aboutMyself Some text describing this user, if any.
		 * @param fullName Full name of this user.
		 * @param resourcePictureId Identifier of the picture used to identify this user, defaults to '0' (none)
		 * @param properties Associative array containing extra custom data, defaults to an empty array if not provided.
		 * @param id Identifier of the user, if known. It will be ignored if this is a new user, as the user id will be 
		 * set after calling Users::addUser()
		 * @see Users::addUser()
		 */
		function UserInfo( $username, $password, $email, $aboutMyself, $fullName, $resourcePictureId = 0, $properties = Array(), $id = 0 )
		{
			$this->DbObject();

			$this->setUsername( $username );
			$this->_password = $password;
			$this->_id = $id;
			$this->_aboutmyself = $aboutMyself;
			$this->_email = $email;
			$this->_blogs = "";
			$this->_fullName = $fullName;
            $this->_siteAdmin = 0;
			$this->setPictureId( $resourcePictureId );
			$this->setProperties( $properties );
			$this->_perms = Array();
			
			// by defaults, users are in status "active"
			$this->setStatus( USER_STATUS_ACTIVE );
			
			$this->_pk = "id";
			$this->_fields = Array( 
			   "user" => "getUsername",
			   "password" => "getMD5Password",
			   "email" => "getEmail",
			   "full_name" => "getFullName",
			   "about" => "getUnformattedAboutMyself",
			   "properties" => "getProperties",
			   "status" => "getStatus",
			   "resource_picture_id" => "getPictureId",
			   "site_admin" => "isSiteAdmin"
		    );
		}

		/**
		 * @return returns the username
		 */
		function getUsername()
		{
			return $this->_username;
		}

		/**
		 * Returns the password.
		 *
		 * @param The password
		 */
		function getPassword()
		{
			return $this->_password;
		}

		/**
		 * Returns the password encoded as an MD5 string.
		 *
		 * @param The password encoded as an MD5 string.
		 */		
		function getMD5Password()
		{
			if( strlen( $this->getPassword()) == 32 )
				$md5pass = $this->getPassword();
			else 
				$md5pass = md5( $this->getPassword());
				
			return( $md5pass );
		}

		/** 
		 * @return Returns the identifier assigned to this user.
		 */
		function getId()
		{
			return $this->_id;
		}

		/**
		 * Returns the text that was input in the 'about myself' text box
		 * in the admin interface
		 *
		 * @param format Whether basic formatting should be applied to the text
		 * @return Returns a string
		 */
		function getAboutMyself( $format = true ) 
		{
			$text = $this->_aboutmyself;			
			
			if( $format ) {
				lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
				$text = TextFilter::autoP( $text );
			}
			
			return( $text );
		}
		
		/**
		 * @return Returns the information about this user without any formatting.
		 */
		function getUnformattedAboutMyself()
		{
			return( $this->getAboutMyself( false ));
		}

		function getEmail()
		{
			return $this->_email;
		}

		function getBlogs($status = BLOG_STATUS_ALL)
		{
			if( $this->_blogs == null ) {
				lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
				$users = new Users();
                $this->_blogs = $users->getUsersBlogs($this->getId(), $status);
			}
			
			return( $this->_blogs );
		}

		function getOwnBlogs()
		{
			$this->getBlogs();

			$blogs = array();
			foreach($this->_blogs as $blog) {
				if( $blog->getOwnerId() == $this->getId() )
					array_push( $blogs, $blog );
			}
			
			return( $blogs );
		}
		
		function getFullName()
		{
		  return $this->_fullName;
		}

		function setUsername( $newUsername )
		{
            lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );

			$this->_username = Textfilter::filterAllHTML( $newUsername );
		}

		function setPassword( $newPassword )
		{
			$this->_password = $newPassword;
		}

		function setMD5Password( $newPassword )
		{
			$this->_password = md5($newPassword);
		}		
	
		function setId( $newId )
		{
			$this->_id = $newId;
		}

		function setAboutMyself( $newAboutMyself )
		{
			$this->_aboutmyself = $newAboutMyself;
		}

		function setEmail( $newEmail )
		{
			$this->_email = $newEmail;
		}

		function setBlogs( $blogs )
		{
			$this->_blogs = $blogs;
		}

        function isSiteAdmin()
        {
			if( $this->_siteAdmin == "" ) $this->_siteAdmin = 0;
        	return $this->_siteAdmin;
        }

        function setSiteAdmin( $siteAdmin )
        {
        	$this->_siteAdmin = $siteAdmin;
        }
        
        function setFullName( $fullName )
        {
            $this->_fullName = $fullName;
        }
		
		function setPictureId( $resourceId )
		{
			$this->_resourcePictureId = $resourceId;
			$this->_resourcePicture = null;
		}
		
		function getPictureId()
		{
			return $this->_resourcePictureId;
		}
		
		function setPicture( $resource )
		{
			$this->_resourcePicture = $resource;
			$this->_resourcePictureId = $resource->getId();
		}
		
		/**
		 * returns a GalleryResource object with the picture chosen by the user
		 * as his/her 'avatar'. I think it's better this way than if we just store
		 * a url pointing to an image
		 * 
		 * @return a GalleryResource object
		 */
		function getPicture()
		{
			// only load if this user really has a picture...
			if( !$this->_resourcePicture && $this->hasPicture()) {
				$this->_loadPicture();
			}
			
			return $this->_resourcePicture;
		}
		
		/**
		 * returns true if the user has selected a picture previously
		 *
		 *Ê@return a boolean value, true if there is a picture or false otherwise
		 */
		function hasPicture()
		{
			return( $this->_resourcePictureId != 0 && $this->_resourcePictureId != "" && $this->_loadPicture() != null );
		}
		
		/**
		 * @private
		 * loads the user picture. Returns a GalleryObject if successful or false otherwise
		 */
		function _loadPicture()
		{
			lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresources.class.php" );

			$resources = new GalleryResources();
			$picture = $resources->getResource( $this->_resourcePictureId );
			
			if( !$picture ) 
				$this->_resourcePicture = null;
			else 
				$this->_resourcePicture = $picture;
				
			return( $this->_resourcePicture );
		}
		
		/**
		 * returns the status of the blog
		 *
		 * @return the current status of the blog
		 */
		function getStatus()
		{
			return( $this->_status );
		}
		
		/**
		 * sets the current status of the blog
		 *
		 * @param status
		 * @return always true
		 */
		function setStatus( $status )
		{
			$this->_status = $status;
			
			return true;	
		}
		
		/**
		 * @private
		 */
		function __sleep()
		{
			$this->perms = null;
			$this->_blogs = null;
			
			return( parent::__sleep());
		}
				
		function hasPermission( $permission, $blogId = 0 )
		{
			if( !isset($this->perms[$blogId] )) {
				lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );
				$perms = new UserPermissions();
				$this->perms[$blogId] = $perms->getUserPermissions( $this->getId(), $blogId );
			}
			
			return( isset( $this->perms[$blogId][$permission] ));
		}
		
		function hasPermissionByName( $permName, $blogId = 0 )
		{
			$ok = false;			
			
			if( !isset($this->perms[$blogId] )) {
				lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );
				$perms = new UserPermissions();
				$this->perms[$blogId] = $perms->getUserPermissions( $this->getId(), $blogId );
			}
			
			foreach( $this->perms[$blogId] as $perm ) {
				if( $perm->getPermissionName() == $permName ) {
					$ok = true;
					break;
				}
			}
			
			return( $ok );
		}		
		
		function getPermissions( $blogId = 0 )
		{
			if( !isset($this->perms[$blogId] )) {
				lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );
				$perms = new UserPermissions();
				$this->perms[$blogId] = $perms->getUserPermissions( $this->getId(), $blogId );
			}
			
			return( $this->perms[$blogId] );
		}		
	}
?>
