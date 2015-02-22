<?php

	lt_include( PLOG_CLASS_PATH."class/dao/model.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfield.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfieldsvalues.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/daocacheconstants.properties.php" );	
	
	/**
	 * different custom field types available
	 *
	 * \ingroup DAO
	 */
	define( "CUSTOM_FIELD_TEXTBOX", 1 );
	define( "CUSTOM_FIELD_TEXTAREA", 2 );
	define( "CUSTOM_FIELD_CHECKBOX", 3 );
	define( "CUSTOM_FIELD_DATE", 4 );
	define( "CUSTOM_FIELD_LIST", 5 );
	define( "CUSTOM_FIELD_MULTILIST", 6 );
	
	/**
     * Model for the custom fields
     */
	class CustomFields extends Model 
	{
	
		function CustomFields()
		{
			$this->Model();
			$this->table = $this->getPrefix()."custom_fields_definition";
		}
		
		/**
		 * returns a custom field given its id
		 *
		 * @param id The id of the custom field
		 * @return A CustomField object with information about the custom field
		 */
		function getCustomField( $id )
		{
			return( $this->get( "id", $id, CACHE_CUSTOMFIELDS ));
		}

		/**
		 * returns a custom field given its name and the blog where it belongs
		 *
		 * @param blogId The id of the blog where this field belongs
		 * @param fieldName The name of the custom field
		 * @return A CustomField object with information about the custom field
		 */		
		function getCustomFieldByName( $blogId, $fieldName )
		{
			$blogFields = $this->getMany( "blog_id", 
			                              $blogId, 
			                              CACHE_CUSTOMFIELDS_BLOG,
			                              Array( CACHE_CUSTOMFIELDS => "getId" ),
										  Array( "field_name" => "ASC" ));
			if( !$blogFields )
				return false;
			foreach( $blogFields as $field ) {
				if( $field->getName() == $fieldName ) {
					// we can return right away, no need to bother...
					return( $field );
				}
			}
		}
		
		/**
		 * returns all the custom fields defined in a blog
		 *
		 * @param blogId The id of the blog
		 * @param includeHidden Whether to return the fields that have been marked as
		 * hidden or not.
		 * @param page
		 * @param itemsPerPage
		 * @return An array of CustomField objects with information about the different
		 * custom fields defined.
		 */
		function getBlogCustomFields( $blogId, $includeHidden = true, $page = -1, $itemsPerPage = 15 )
		{
			$blogFields = $this->getMany( "blog_id",
			                              $blogId,
			                              CACHE_CUSTOMFIELDS_BLOG,
			                              Array( CACHE_CUSTOMFIELDS => "getId" ),
										  Array( "field_name" => "ASC" ),
										  "",
			                              $page, 
			                              $itemsPerPage );
			                              
			if( !$blogFields )
				return( Array());
			                          
			// if we have to include the hidden fields, we can return the whole array as 
			// those are already there
			if( $includeHidden )
				return( $blogFields );
			                          
			// if not, filter out the non-hidden
			$result = Array();
			foreach( $blogFields as $field ) {
				if( !$field->isHidden())
					$result[] = $field;
			}
			
			return( $result );
		}

		/**
 		 * returns the number of custom fields defined for the blog
		 *
		 * @param blogId
		 * @param includeHidden
		 */
		function getNumBlogCustomFields( $blogId, $includeHidden = true )
		{
			return( count( $this->getBlogCustomFields( $blogId, $includeHidden )));
		}
		
		/**
		 * adds a custom field to the database
		 *
		 * @param field A CustomField object
		 * @return True if successful or false otherwise.
		 */
		function addCustomField( &$field )
		{		
			// does the field already exist?
			$existingField = $this->getCustomFieldByName( $field->getBlogId(), $field->getName());
			if( $existingField ) // it already exists, we cannot add it!
				return false;
				
			if( $result = $this->add( $field )) {
				// clean the caches
				$this->_cache->removeData( $field->getId(), CACHE_CUSTOMFIELDS );
				$this->_cache->removeData( $field->getBlogId(), CACHE_CUSTOMFIELDS_BLOG );
			}
			return( $result );
		}
		
		/**
		 * removes a custom field, but also all the values that have been created
		 * based on this field and that have been assigned to different articles.
		 * Otherwise, we would have data which is not linked to any article... but if still
		 * needed, set the second parameter to false
		 *
		 * @param id
		 * @param deleteValues
		 * @return Returns true if successful or false otherwise
		 */
		function removeCustomField( $id, $deleteValues = true )
		{
			$field = $this->getCustomField( $id );
			
			if( !$field )
				return false;
			
			if( !$this->delete( "id", $id ))
				return false;
				
			// clean the caches
			$this->_cache->removeData( $field->getId(), CACHE_CUSTOMFIELDS );
			$this->_cache->removeData( $field->getBlogId(), CACHE_CUSTOMFIELDS_BLOG );
				
			if( !$deleteValues )
				return true;
			
			// remove the values that were associated to this field
			$fieldValues = new CustomFieldsValues();
			return( $fieldValues->removeCustomFieldValues( $id ));
		}
		
		/**
		 * update a field in the database
		 *
		 * @param field
		 * @return True if successful or false otherwise
		 */
		function updateCustomField( $field )
		{
			if( ($result = $this->update( $field ))) {
				// clean the caches
				$this->_cache->removeData( $field->getId(), CACHE_CUSTOMFIELDS );
				$this->_cache->removeData( $field->getBlogId(), CACHE_CUSTOMFIELDS_BLOG );
			}
			return( $result );
		}
		
		/**
		 * @private
		 */
		function mapRow( $row )
		{
			$field = new CustomField( $row["field_name"],
			                          $row["field_description"],
					 				  $row["field_type"],
					   				  $row["blog_id"],
									  $row["hidden"],
									  $row["searchable"],
									  $row["id"] );
			// set the field with the possible values, but check first if it can be unserialized before we get an error
			!isset( $row["field_values"] ) ? $values = Array() : $values = unserialize( $row["field_values"] );
			
			
			$field->setFieldValues( $values );
									  
			return $field;
		}
	}
?>