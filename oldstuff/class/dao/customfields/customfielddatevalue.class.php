<?php

	lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfieldvalue.class.php" );
	
	/**
	 * offers methods for dealing with custom fields that represent dates
	 *
	 * \ingroup DAO
	 */
	class CustomFieldDateValue extends CustomFieldValue
	{
		var $_timestamp;
		
		/**
		 * constructor
		 *
		 * @see CustomFieldValue
		 */
		function CustomFieldDateValue( $fieldId, $fieldValue, $articleId, $blogId, $id = -1 )
		{
			$this->CustomFieldValue( $fieldId, $fieldValue, $articleId, $blogId, $id );
			
			$this->setValue( $fieldValue );
		}
		
		/**
		 * returns a Timestamp object based on what we have in the field. If the string in the
		 * field does not represent a valid date, we might get unexpected results...
		 * 
		 * @return a Timestamp object
		 */
		function getDateObject()
		{
			return( $this->_timestamp );
		}
		
		/**
		 * formats the date accordingly
		 *
		 * @param value
		 */
		function setValue( $value )
		{
	        lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );

            $this->_timestamp = new Timestamp();
            $dateTimeParts = explode(" ", $value );
            $dateParts = explode( "/", $dateTimeParts[0] );
            $timeParts = explode( ":",$dateTimeParts[1] );
            $this->_timestamp->setDay( $dateParts[0] );
            $this->_timestamp->setMonth( $dateParts[1] );
            $this->_timestamp->setYear( $dateParts[2] );
            $this->_timestamp->setHour( $timeParts[0] );
            $this->_timestamp->setMinutes( $timeParts[1] );
			
			parent::setValue( $value );
			
			return true;
		}
		
		/**
		 * shortuct for formatting the date in the same way that it is expected by
		 * the "date picker" javascript calendar
		 *
		 * @return a string
		 */
		function getDateFormatted()
		{
			lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );
			$locale = Locales::getLocale( "en_UK" );
			
			$dateFormatted = $locale->formatDate( $this->getDateObject(), "%d/%m/%Y %H:%M" );
			
			return $dateFormatted;
		}
		
		/**
		 * returns the current date
		 *
		 * @param a default value
		 */
		function getDefaultValue()
		{
	        lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );

			return( new Timestamp());	
		}
	}
	
?>
