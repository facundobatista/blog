<?php

	lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfieldvalue.class.php" );
	
	/**
	 * offers methods for dealing with custom fields that use drop-down lists that
	 * allows to select one of the values
	 *
	 * \ingroup DAO
	 */
	class CustomFieldListValue extends CustomFieldValue
	{		
		/**
		 * constructor
		 *
		 * @see CustomFieldValue
		 */
		function CustomFieldListValue( $fieldId, $fieldValue, $articleId, $blogId, $id = -1 )
		{
			$this->CustomFieldValue( $fieldId, $fieldValue, $articleId, $blogId, $id );
			$this->setValue( $fieldValue );
		}
		
		function getFieldValues()
		{
			$field = $this->getCustomField();
			return( $this->getFieldValues());
		}
	}
	
?>
