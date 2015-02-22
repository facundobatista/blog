<?php

	lt_include( PLOG_CLASS_PATH."class/data/validator/validator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/rules/nonemptyrule.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/rules/nohtmlrule.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/rules/filteredwordsrule.class.php" );
	
    /**
     * \ingroup Validator
     *
     * Checks if a domain is valid. Domains have to comply with the following rules:
     *
     * - They must not be empty
     * - They must not be any of the forbidden usernames. Forbidden domains
     *   (and usernames) can be configured via the 'forbidden_usernames'
     *   configuration parameter.
     *
     * @see NonEmptyRule
     * @see FilteredWordsRule
     */
    class DomainValidator extends Validator 
    {
    	function DomainValidator()
        {
        	$this->Validator();
        	$this->addRule( new NonEmptyRule());
            $this->addRule( new NoHtmlRule() );
			$config =& Config::getConfig();
			$forbiddenDomainNames = $config->getValue( "forbidden_usernames", "" );
			$forbiddenDomainNamesArray = explode( " ", $forbiddenDomainNames );
			$this->addRule( new FilteredWordsRule( $forbiddenDomainNamesArray ));
        }
    }
?>