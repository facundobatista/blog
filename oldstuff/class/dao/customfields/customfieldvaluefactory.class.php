<?php

	
	lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfieldvalue.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfielddatevalue.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfieldcheckboxvalue.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfieldlistvalue.class.php" );	
		
	/**
	 * Generates the right CustomFieldValue (or subclass of it)
	 *
	 * \ingroup DAO
	 */
	class CustomFieldValueFactory 
	{
	
	   /**
	    * returns the right class depending on the field type
	    * @private
	    * @static
	    * @param fieldType The field type
	    */
	   function _findConstructorClass( $fieldType )
	   {
			/**
	 		 * this array maps field types to class constructors, see _fillCustomFieldValueInformation
	 		 */		   
			$__fieldTypesConstructors = Array( CUSTOM_FIELD_TEXTBOX => "CustomFieldValue",
	        		                           CUSTOM_FIELD_TEXTAREA => "CustomFieldValue",
											   CUSTOM_FIELD_CHECKBOX => "CustomFieldCheckboxValue",
											   CUSTOM_FIELD_DATE => "CustomFieldDateValue",
											   CUSTOM_FIELD_LIST => "CustomFieldListValue",
											   CUSTOM_FIELD_MULTILIST => "CustomFieldValue",
											   "default" => "CustomFieldValue" );
	       
	       // if the key is incorrect, use the default one
	       if( array_key_exists( $fieldType,  $__fieldTypesConstructors ))
	           $fieldType == "default";
	       
	       // return whatever it is...
	       return $__fieldTypesConstructors[$fieldType];
	   }
	
	   /**
	    * creates and returns the right CustomFieldValuexxx object, or a 
	    * CustomFieldValue object if there is no specific object defined for that type
	    *
	    * @param row
	    * @return A CustomFieldValue object, or a subclass
	    */
	   function getCustomFieldValueObject( $row )
	   {
			// depending on the type, we should return a different object... Just to
			// make things easier for clients of the class! But we can use the array with the
			// mappins to easily figure out the right$ class for the job
			$constructor = CustomFieldValueFactory::_findConstructorClass( (int)$row["field_type"] );

			$value = new $constructor( $row["field_id"],
			                           $row["field_value"],
									   $row["article_id"],
					   			       $row["blog_id"],
 									   $row["id"] );

			return $value;	   
	   }
	   
	   /**
	    * returns the right CustomFieldValuexxx object but this method can be used
	    * in those occasions when we know the fieldId but we do not know its type so what we need to do
	    * is first load the field definition and then work on creating the right object type
	    *
	    * @param fieldId
	    * @param row An array
	    * @see getCustomFieldValueObject
	    */
	   function getCustomFieldValueByFieldId( $fieldId, $row )
	   {
		   // load the field definition first
			lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfields.class.php" );
			$customFields = new CustomFields();
			$customField = $customFields->getCustomField( $fieldId );
			if( !$customField )
				return false;
				
			// if everything went fine, then continue   
			$row["field_id"] = $fieldId;
			$row["field_type"] = $customField->getType();
			$row["field_description"] = $customField->getDescription();
			$row["field_name"] = $customField->getName();
			
			$fieldValueObject = CustomFieldValueFactory::getCustomFieldValueObject( $row );
			
			return( $fieldValueObject );
	   }
	}
?>