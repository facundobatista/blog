<?php

    lt_include( PLOG_CLASS_PATH."class/database/dbobject.class.php" );

    /**
     * \ingroup DAO
     *
     * Object that maps a row of the permissions table
     */
	class Permission extends DbObject
	{
		var $_id;
		var $_name;
		var $_description;
		var $_corePerm;
		var $_adminOnly;
	
		/**
		 * Constructor
		 *
		 * @param name Name of the permission
		 * @param description Description of the permission. Please use a locale id/key
		 * instead of a text string. Translation will be provided later on in the user interface
		 * based on this key
		 * @param id of the permission
		 */
		function Permission( $name, $description, $id = -1 )
		{
			$this->DbObject();
			
			$this->pk = "id";
			
			$this->_name = $name;
			$this->_description = $description;
			$this->_id = $id;
			$this->_adminOnly = false;
			$this->_corePerm = false;
			
			$this->_fields = Array(
				"permission" => "getName",
				"description" => "getDescription",
				"core_perm" => "isCorePermission",
				"admin_only" => "isAdminOnlyPermission",
				"id" => "getId"
			);
		}
		
		function getId()
		{
			return( $this->_id );
		}
		
		function setId( $id )
		{
			$this->_id = $id;	
		}
		
		function getName()
		{
			return( $this->_name );
		}
		
		function setName( $name )
		{
			$this->_name = $name;
		}		
		
		function getDescription()
		{
			return( $this->_description );	
		}
		
		function setDescription( $desc )
		{
			$this->_description = $desc;
		}
		
		function isCorePermission()
		{
			return( $this->_corePerm );	
		}
		
		function setCorePermission( $corePerm )
		{
			$this->_corePerm = $corePerm;	
		}
		
		function isAdminOnlyPermission()
		{
			return( $this->_adminOnly );	
		}
		
		function setAdminOnlyPermission( $adminOnly )
		{
			$this->_adminOnly = $adminOnly;
		}
		
		/**
		 * Returns the number of users who have been granted this permission
		 *
		 * @return An integer
		 */
		function getNumUsersWithPermission()
		{
			lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );
			$perms = new UserPermissions();
			return( $perms->getNumUsersWithPermission( $this->getId()));
		}
	}
?>