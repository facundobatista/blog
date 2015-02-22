<?php

	lt_include( PLOG_CLASS_PATH."class/data/filter/filterbase.class.php" );

	/**
	 * \ingroup Filter
	 *
	 * This class extends the FilterBase interface to filter all HTML
	 * code in the given string except the allowed tags
	 */
	class AllowedHtmlFilter extends FilterBase
	{
		/**
		 * Filters out all HTML code except the allowed tags
		 *
		 * @param data
		 * @return The input string without HTML code
		 */
		function filter( $data )
		{
			lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
			$tf = new Textfilter();
			return( parent::filter( $tf->filterHTML( $data )));
		}	
	}
?>