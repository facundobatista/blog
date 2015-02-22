<?php

	lt_include( PLOG_CLASS_PATH."class/data/validator/validator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/rules/urlformatrule.class.php" );

    /**
     * \ingroup Validator
     *
     * Checks whether the string is a valid http/https url
     *
     */
    class HttpUrlValidator extends Validator 
    {
    	function HttpUrlValidator()
        {
        	$this->Validator();
			$this->addRule( new UrlFormatRule());
        }
    }
?>
