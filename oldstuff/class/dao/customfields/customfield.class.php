<?php

	lt_include( PLOG_CLASS_PATH."class/database/dbobject.class.php" );

	/**
     * Defines a custom field
	 *
	 * \ingroup DAO
     */
	class CustomField extends DbObject 
	{
		var $_name;
		var $_type;
		var $_description;
		var $_id;
		var $_blogId;
		var $_searchable;
		var $_hidden;
		var $_defaultValue;
		var $_fieldValues;
	
		function CustomField( $name, $description, $type, $blogId, $hidden = false, $searchable = true, $id = -1 )
		{
			$this->DbObject();
			$this->setName( $name );
			$this->_description = $description;
			$this->_type = $type;
			$this->_id = $id;
			$this->_blogId = $blogId;
			$this->_searchable = $searchable;
			$this->_hidden = $hidden;
			$this->_defaultValue = "";
			$this->_fieldValues = Array();
			
			$this->_fields = Array(
				"field_name" => "getName",
				"blog_id" => "getBlogId",
				"field_description" => "getDescription",
				"field_type" => "getType",
				"hidden" => "getHidden",
				"searchable" => "getSearchable",
				"field_values" => "getFieldValues"
			);
		}
		
		function getBlogId()
		{
			return $this->_blogId;
		}
		
		function setBlogId( $blogId )
		{
			$this->_blogId = $blogId;
		}		
		
		function getId()
		{
			return $this->_id;
		}
		
		function setId( $id )
		{
			$this->_id = $id;
		}
		
		function getName()
		{
			return $this->_name;
		}
		
		function setName( $name )
		{
	        lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );	

			$this->_name = Textfilter::filterAllHTML( $name );
		}
		
		function getType()
		{
			return $this->_type;
		}
		
		function setType( $type )
		{
			$this->_type = $type;
		}
		
		function getDescription()
		{
			return $this->_description;
		}
		
		function setDescription( $description )
		{
			$this->_description = $description;
		}
		
		function getSearchable()
		{
			if( $this->_searchable == 1 )
				return 1;
			else
				return 0;
		}
		
		function isSearchable()
		{
			return $this->getSearchable();
		}
		
		function getHidden()
		{
			if( $this->_hidden == 1 )
				return 1;
			else
				return 0;
		}
		
		function isHidden()
		{
			return $this->getHidden();
		}
		
		function setHidden( $hidden )
		{
			$this->_hidden = $hidden;
		}
		
		function setSearchable( $searchable )
		{
			$this->searchable = $searchable;
		}
		
		function getDefaultValue()
		{
			return $this->_defaultValue;	
		}
		
		function getFieldValues()
		{
			return( $this->_fieldValues );
		}
		
		function setFieldValues( $values )
		{
			$this->_fieldValues = $values;
		}
	}
?>
