<?php

	lt_include( PLOG_CLASS_PATH."class/data/validator/validator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/rules/regexprule.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/rules/nonemptyrule.class.php" );

    define( "VALID_REGEXP_CHARS", "^([a-z0-9._-]+)$");

    /**
     * \ingroup Validator
     *
     * Checks whether the string is a valid template name
     *
     * @see NonEmptyRule
     */
    class TemplateNameValidator extends Validator 
    {
    	function TemplateNameValidator()
        {
        	$this->Validator();
			$this->addRule( new NonEmptyRule());
        	$this->addRule( new RegExpRule(VALID_REGEXP_CHARS, false ));
        }
    }
?>
