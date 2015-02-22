<?php

	lt_include( PLOG_CLASS_PATH."class/data/validator/validator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/data/validator/rules/regexprule.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/rules/filteredwordsrule.class.php" );
	lt_include( PLOG_CLASS_PATH."class/file/file.class.php" );

	/**
	 * custom summary page validator that does:
	 *
	 * 1) check whether the page is a valid string
	 * 2) whether it has the correct characters
	 * 3) whether the page exists under templates/summary/ and it is readable
	 *
	 * If all the checks above are successful, it will return true
	 */
	class CustomSummaryPageValidator extends Validator
	{
	
		function CustomSummaryPageValidator()
		{
			// check that the page has the right characters
			$this->addRule( new RegExpRule( "[A-Za-z0-9\-_]" ));
			// and that it is a string at all
			$this->addValidator( new StringValidator());
			// and that it is not any of the default templates...
			$this->addRule( new FilteredWordsRule( Array( "summaryerror",
			                                              "agreement",
														  "email_confirm",
														  "registerfinished",
														  "blogprofile",
														  "formvalidate",
														  "pager",
														  "userlist",
														  "userprofile",
														  "validate",
														  "changepassword",
														  "message",
														  "resetpassword",
														  "resetpasswordemail",
														  "searchresults",
														  "bloglist",
														  "index",
														  "recent",
														  "registererror",
														  "footer",
														  "header",
														  "summary" )));														
		}
		
		function validate( $data )
		{
			// check the rules created in the constructor
			if( !parent::validate( $data ))
				return false;
				
			// and if they succeeded, check if the file exists. If not, return false
			// and go to the main page...
			$filePath = "templates/summary/$data.template";
			return( File::isReadable( $filePath ));
		}		
	}
?>