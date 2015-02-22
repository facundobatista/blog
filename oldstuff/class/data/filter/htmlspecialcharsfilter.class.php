<?php

	lt_include( PLOG_CLASS_PATH."class/data/filter/filterbase.class.php" );

	/**
	 * \ingroup Filter
	 *
	 * This class extends the FilterBase interface to filter all HTML
	 * code in the given string
	 */
	class HtmlSpecialCharsFilter extends FilterBase
	{
		/**
		 * Filters out all HTML and Javascript code from the given string
		 *
		 * @param data
		 * @return The input string without HTML code
		 */
		function filter( $data )
		{
			return( parent::filter( htmlspecialchars( $data )));
		}	
	}
?>