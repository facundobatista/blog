<?php

	lt_include( PLOG_CLASS_PATH."class/data/validator/validator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/rules/nonemptyrule.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/rules/nohtmlrule.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/rules/filteredpatternsrule.class.php" );
	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );

    /**
     * \ingroup Validator
     *
     * Checks if a blog name is valid. Usernames have to comply with the following rules:
     *
	 * - They must not be empty
	 * - They must not match any of the regular expressions set by administrators as forbidden_blogname_regexp
     *
     * @see NonEmptyRule
     * @see RegExpRule
     */
    class BlogNameValidator extends Validator 
    {
    	function BlogNameValidator()
        {
        	$this->Validator();
        	
			// it can't be empty
        	$this->addRule( new NonEmptyRule());
            $this->addRule( new NoHtmlRule() );

			// it can't be any of the forbidden ones
			$config =& Config::getConfig();			
			$forbiddenBlognames = $config->getValue( "forbidden_blognames", "" );
			$forbiddenBlognamesArray = explode( " ", $forbiddenBlognames );
			$this->addRule( new FilteredPatternsRule( $forbiddenBlognamesArray, false ));
        }

		function validate( $value )
		{
			if( !parent::validate( $value )) 
				return false;
				
			// in addition to the other rules, the blog name won't be valid if its domainized() version
			// returns empty spaces, so this is what would happen if we set a blog name of
			// things like non-characters for example. We only check this when subdomain enabled.

			lt_include( PLOG_CLASS_PATH."class/net/http/subdomains.class.php" );
			lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );

			if( Subdomains::getSubdomainsEnabled() ) {
				$config =& Config::getConfig();
				if( strstr( $config->getValue( "subdomains_base_url" ), '{blogname}' ) ) {
					return( ( Textfilter::domainize( $value ) ) != "" );
				}
			}

			return true;
		}
    }
?>