<?php

	lt_include( PLOG_CLASS_PATH."class/data/validator/validator.class.php" );

    /**
     * \ingroup Validator
     *
     * Checks that it is really a float value.
     *
     * @see IntRule
     */
    class FloatValidator extends Validator 
    {
		/**
		 * Constructor.
		 *
		 * @param signed Whether to allow signed float or not.
		 */
    	function FloatValidator()
        {
        	$this->Validator();
        	
            lt_include( PLOG_CLASS_PATH."class/data/validator/rules/floatrule.class.php" );
            $this->addRule( new FloatRule());
        }
    }
?>