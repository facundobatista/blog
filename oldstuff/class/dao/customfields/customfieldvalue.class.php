<?php

	lt_include( PLOG_CLASS_PATH."class/database/dbobject.class.php" );

	/**
     * Defines a value assigned to a custom field
	 *
	 * \ingroup DAO
     */
	class CustomFieldValue extends DbObject
	{
		var $_fieldId;
		var $_fieldValue;
		var $_id;
		var $_blogId;
		var $_articleId;
		var $_field;
	
		function CustomFieldValue( $fieldId, $fieldValue, $articleId, $blogId, $id = -1 )
		{
			// constructor
			$this->DbObject();			
			// and save some other values
			$this->_fieldId = $fieldId;
			if( $fieldValue == null ) $fieldValue = "";
			$this->_fieldValue = $fieldValue;
			$this->_articleId = $articleId;
			$this->_id = $id;
			$this->_customField = null;
			$this->_blogId = $blogId;
			
			$this->_fields = Array(
			   "field_id" => "getFieldId",
			   "field_value" => "getValue",
			   "normalized_value" => "getNormalizedValue",
			   "blog_id" => "getBlogId",
			   "article_id" => "getArticleId"
			);
		}
		
		/**
		 * @private
		 */
		function _loadFieldDefinition()
		{
			if( $this->_customField == null ) {
				lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfields.class.php" );
				$customFields = new CustomFields();
				$this->_customField = $customFields->getCustomField( $this->_fieldId );
			}
		}
		
		function getBlogId()
		{
			return $this->_blogId;
		}
		
		function getId()
		{
			return $this->_id;
		}
		
		function setId( $id )
		{
			$this->_id = $id;
		}
		
		function getFieldId()
		{
			return $this->_fieldId;
		}
		
		function getArticleId()
		{
			return $this->_articleId;
		}
		
		function getArticle()
		{
			if( $this->_article == null ) {
				lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
				$articles = new Articles();
				$this->_article = $articles->getBlogArticle( $this->_articleId, $this->_blogId );
			}
			
			return( $this->_article );
		}
		
		function getValue()
		{			
			return $this->_fieldValue;
		}
		
		function setValue( $value )
		{
			$this->_fieldValue = $value;
		}
		
		function getNormalizedValue()
		{
			lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
            $filter = new Textfilter();
            return( $filter->normalizeText($this->getValue()));
		}
		
		function getType()
		{
			$this->_loadFieldDefinition();
			return( $this->_customField->getType());
		}
		
		function getDescription()
		{
			$this->_loadFieldDefinition();
			return( $this->_customField->getDescription());
		}
		
		function getName()
		{
			$this->_loadFieldDefinition();
			return( $this->_customField->getName());			
		}
		
		function getFieldDefinition()
		{
			return( $this->_customField );
		}
	}
?>