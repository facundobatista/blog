<?php

	lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfieldvalue.class.php" );
	
	/**
	 * offers methods for dealing with checkboxes
	 *
	 * \ingroup DAO
	 */
	class CustomFieldCheckboxValue extends CustomFieldValue
	{
		/**
		 * constructor
		 *
		 * @see CustomFieldValue
		 */
		function CustomFieldCheckboxValue( $fieldId, $fieldValue, $articleId, $blogId, $id = -1 )
		{
			$this->CustomFieldValue( $fieldId, $fieldValue, $articleId, $blogId, $id );
			
			$this->setValue( $fieldValue );
		}
		
		/**
		 * returns whether the checkbox is enabled or not
		 *
		 * @return boolean
		 */
		function isChecked()
		{
			return( $this->getValue() == "1" );
		}
	}
?>
