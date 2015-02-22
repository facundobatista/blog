<?php

	lt_include( PLOG_CLASS_PATH."class/data/validator/validator.class.php" );

    /**
     * \ingroup Validator
     *
     * Empty validation class that always returns true.
     */
    class EmptyValidator extends Validator 
    {  
        /**
         * This method always return true
         *
         * @param value 
         * @return Always true
         */
		function validate( $value )
		{
			return true;
		}
    }
?>