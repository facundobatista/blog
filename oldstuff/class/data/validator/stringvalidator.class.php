<?php

	lt_include( PLOG_CLASS_PATH."class/data/validator/validator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/rules/nonemptyrule.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/rules/nohtmlrule.class.php" );

    /**
     * \ingroup Validator
     *
     * Checks that a string is not empty. 
     *
     * @see NonEmptyRule
     */
    class StringValidator extends Validator 
    {
    	function StringValidator( $allowHtml = false )
        {
        	$this->Validator();
        	
        	$this->addRule( new NonEmptyRule());

            if(!$allowHtml){
                $this->addRule( new NoHtmlRule() );
            }
        }
    }
?>