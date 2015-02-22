<?php

	lt_include( PLOG_CLASS_PATH."class/data/validator/validator.class.php" );

    /**
     * \ingroup Validator
     *
     * Checks that it is really an integer value.
     *
     * @see UIntRule
     */
    class IntegerValidator extends Validator 
    {
		/**
		 * Constructor.
		 *
		 * @param signed Whether to allow signed integers or not. For compatibility reasons,
		 * signed integers are not allowed by default.
		 */
    	function IntegerValidator( $signed = false )
        {
        	$this->Validator();
        	
			if( $signed ) {
				lt_include( PLOG_CLASS_PATH."class/data/validator/rules/intrule.class.php" );
				$this->addRule( new IntRule());
			}
			else {
				lt_include( PLOG_CLASS_PATH."class/data/validator/rules/uintrule.class.php" );			
				$this->addRule( new UIntRule());
			}
        }
    }
?>