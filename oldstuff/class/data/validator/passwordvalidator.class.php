<?php

	lt_include( PLOG_CLASS_PATH."class/data/validator/validator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/rules/nonemptyrule.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/rules/stringrangerule.class.php" );    

    define( "MIN_PASSWORD_LENGTH_DEFAULT", 6 );

    define( "ERROR_PASSWORD_TOO_SHORT", 1 );
    define( "ERROR_PASSWORD_EMPTY", 2 );

    /**
     * \ingroup Validator
     *
     * Validates passwords according to certain rules:
     *
     * - passwords should not be empty
     * - password should have a length between minimun_password_length (if not available, defaults to '6') and a maximum length of 32
     *
     * @see NonEmptyRule
     * @see RangeRule
     */
    class PasswordValidator extends Validator 
    {
    	function PasswordValidator()
        {
        	$this->Validator();
            $config =& Config::getConfig();
            
            $this->addRule( new NonEmptyRule());
            $this->addRule( new StringRangeRule( $config->getValue( "minimum_password_length", MIN_PASSWORD_LENGTH_DEFAULT ), 32 ));
        }
    }
?>
