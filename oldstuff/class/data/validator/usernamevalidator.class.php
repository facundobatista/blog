<?php

	lt_include( PLOG_CLASS_PATH."class/data/validator/validator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/rules/nonemptyrule.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/rules/regexprule.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/rules/stringrangerule.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/rules/filteredpatternsrule.class.php" );
	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	
	define( "ONLY_ALPHANUMERIC_REGEXP", "^([a-z0-9]*)$" );

    /**
     * \ingroup Validator
     *
     * Checks if a username is valid. Usernames have to comply with the following rules:
     *
     * - They must not be empty
     * - They must only be made of alphanumeric characters (a-z, A-Z and 0-9)
     * - They must not be any of the forbidden usernames. Forbidden usernames can be configured
     * via the 'forbidden_usernames' configuration parameter.
     *
     * @see NonEmptyRule
     * @see RegExpRule
     * @see FilteredWordsRule
     */
    class UsernameValidator extends Validator 
    {
    	function UsernameValidator()
        {
        	$this->Validator();
        	
        	$this->addRule( new NonEmptyRule());
			$this->addRule( new RegExpRule( ONLY_ALPHANUMERIC_REGEXP ));			
			$this->addRule( new StringRangeRule( 1, 15 ));  // to make sure they're not longer than 15 characters
			$config =& Config::getConfig();
			$forbiddenUsernames = $config->getValue( "forbidden_usernames", "" );
			$forbiddenUsernamesArray = explode( " ", $forbiddenUsernames );
			$this->addRule( new FilteredPatternsRule( $forbiddenUsernamesArray, false ));
        }
    }
?>