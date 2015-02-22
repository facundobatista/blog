<?php

	lt_include( PLOG_CLASS_PATH."class/data/filter/filterbase.class.php" );

	/**
	 * \ingroup Filter	
	 *
	 * This class extends the FilterBase interface to filter all HTML
	 * code in the given string
	 */
	class HtmlFilter extends FilterBase
	{
		/**
		 * Constructor
		 *
		 * @param filterEntities When set to true, characters with an available
		 * HTML entity will be converted after the string has been cleaned up by
		 * the HTML filter. Disabled by default. 
		 */
		function HtmlFilter( $filterEntities = false )
		{
			$this->FilterBase();
			
			if( $filterEntities ) {
				lt_include( PLOG_CLASS_PATH."class/data/filter/htmlentitiesfilter.class.php" );
				$this->addFilter( new HtmlEntitiesFilter());
			}
		}
		
		/**
		 * Filters out all HTML and Javascript code from the given string
		 *
		 * @param data
		 * @return The input string without HTML code
		 */
		function filter( $data )
		{
			lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
			return( parent::filter( Textfilter::filterAllHTML( $data )));
		}	
	}
?>