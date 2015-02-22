<?php

	lt_include( PLOG_CLASS_PATH."class/database/dbobject.class.php" );

	/**
	 * represents a permission from the database
	 *
	 * \ingroup DAO
	 */	 	
	class UserPermission extends DbObject 
	{

    	var $_userId;
        var $_blogId;
        var $_permId;
        var $_id;
        var $_perm;
		var $_user;

    	function UserPermission( $userId, $blogId, $permId, $id = -1 )
        {
			$this->DbObject();
        	$this->_userId = $userId;
            $this->_blogId = $blogId;
            $this->_permId = $permId;
            $this->_id     = $id;
            $this->_perm   = null;
			$this->_user   = null;
			
			$this->_pk = "id";
			$this->_fields = Array (
				"blog_id" => "getBlogId",
				"user_id" => "getUserId",
				"permission_id" => "getPermissionId" 
			);
        }

        function getId()
        {
        	return $this->_id;
        }
		
		function setId( $id )
		{
			$this->_id = $id;
		}

        function getBlogId()
        {
        	return $this->_blogId;
        }
		
		function setBlogId( $blogId )
		{
			$this->_blogId = $blogId;
		}
		
		function getBlogInfo()
		{
			if( $this->_blog === null ) {
				lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
				$blogs = new Blogs();
				$this->_blog = $blogs->getBlogInfo( $this->getBlogId());
			}
			
			return( $this->_blog );	
		}

        function getPermissionId()
        {
        	return $this->_permId;
        }
		
		function setPermission( $perm )
		{
			$this->_perm = $perm;
			$this->_permId = $perm->getId();			
		}
		
		function getPermission()
		{
			if( $this->_perm === null ) {
				lt_include( PLOG_CLASS_PATH."class/dao/permissions.class.php" );
				$perms = new Permissions();	
				$this->_perm = $perms->getPermission( $this->_permId );
			}
			
			return( $this->_perm );
		}
		
		function getPermissionName()
		{
			$perm = $this->getPermission();
			return( $perm->getName());	
		}
		
		function getPermissionDescription()
		{
			$perm = $this->getPermission();
			return( $perm->getDescription());					
		}

        function getUserId()
        {
        	return $this->_userId;
        }
		
		function setUserId( $userId )
		{
			$this->_userId = $userId;
		}
		
		function getUserInfo()
		{
			if( $this->_user === null ) {
				lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
				$users = new Users();
				$this->_user = $users->getUserInfoFromId( $this->getUserId());
			}
			
			return( $this->_user );				
		}
    }
?>
