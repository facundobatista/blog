<?php

	lt_include( PLOG_CLASS_PATH."class/data/validator/validator.class.php" );

    /**
     * \ingroup Validator
     *
     * Checks that it is really a valid date/time or not.
     *
     * @see DateTimeRule
     */
    class DateTimeValidator extends Validator 
    {
		/**
		 * Constructor.
		 *
		 * @param format To specify data format.
		 */
    	function DateTimeValidator( $format = '' )
        {
        	$this->Validator();
        	
			lt_include( PLOG_CLASS_PATH."class/data/validator/rules/datetimerule.class.php" );			
			$this->addRule( new DateTimeRule( $format ));
        }
    }
?>