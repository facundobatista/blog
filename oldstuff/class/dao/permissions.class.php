<?php

	lt_include( PLOG_CLASS_PATH."class/dao/model.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/permission.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/dao/daocacheconstants.properties.php" );	
    
	/**
	 * \ingroup DAO
	 *
	 * Model class that manages the 'permissions' table
	 */ 
	class Permissions extends Model
	{
	
		function Permissions()
		{
			$this->Model();
			$this->table = $this->getPrefix()."permissions";
		}
	
		/**
		 * Add a new permission
		 *
		 * @param perm A Permission object
		 * @return True if successful or false otherwise
		 */	
		function addPermission( &$perm )
		{
			if( ($result = $this->add( $perm, Array( CACHE_PERMISSIONS => "getId" )))) {
				$this->_cache->removeData( "_all_", CACHE_PERMISSIONS_ALL );
			}
				
			return( $result );			
		}
		
		/**
		 * Update a permission
		 *
		 * @param perm
		 * @return True if successful or false otherwise
		 */
		function updatePermission( &$perm )
		{
			if( ($result = $this->update( $perm ))) {
				$this->_cache->removeData( $perm->getId(), CACHE_PERMISSIONS );
				$this->_cache->removeData( "_all_", CACHE_PERMISSIONS_ALL );				
			}
			
			return( $result );
		}
		
		/**
		 * Retrieve a permission given its id
		 *
		 * @param id The id of the permission
		 * @return A Permission object if successful or false otherwise
		 */
		function getPermission( $id )
		{
			return( $this->get( "id", $id, CACHE_PERMISSIONS ));			
		}
		
		/**
		 * Retrieve a permission given its name
		 *
		 * @param name The name of the permission
		 * @return A Permission object if successful or false otherwise
		 */		
		function getPermissionByName( $name )
		{
			$perms = $this->getAllPermissions();
			foreach( $perms as $perm ) {
				if( $perm->getName() == $name ) {
					return( $perm );	
				}	
			}
			
			return( false );
		}
		
		/**
		 * Deletes a permission given its id
		 *
		 * @param id The id of the permission
		 * @return True if successful or false otherwise
         * (note, only false on a SQL error, not if the value didn't exist)
		 */		 
		function deletePermission( $id )
		{
			if( ($result = $this->delete( "id", $id ))) {
				$this->_cache->removeData( $id, CACHE_PERMISSIONS );
				$this->_cache->removeData( "_all_", CACHE_PERMISSIONS_ALL );
			}
			
			return( $result );			
		}
		
		/**
		 * Loads all permissions from the database
		 *
		 * @return An array of Permission objects, or an empty array if no
		 * permissions were found
		 */
		function getAllPermissions()
		{
			$permissions = $this->getAll( "all", 
			                             CACHE_PERMISSIONS_ALL, 
			                             Array( CACHE_PERMISSIONS => "getId" ),
			                             Array( "permission" => "ASC" ));
			if( !$permissions )
				return( Array());				
			                             			                             
			return( $permissions );			
		}
		
		/**
		 * @private
		 */
		function mapRow( $row )
		{
			$perm = new Permission( $row["permission"], $row["description"], $row["id"] );
			$perm->setAdminOnlyPermission( $row["admin_only"] );
			$perm->setCorePermission( $row["core_perm"] );
			return( $perm  );	
		}
	}
?>