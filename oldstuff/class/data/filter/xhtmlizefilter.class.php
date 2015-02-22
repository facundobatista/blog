<?php

	lt_include( PLOG_CLASS_PATH."class/data/filter/filterbase.class.php" );

	/**
	 * \ingroup Filter
	 *
	 * This class extends the FilterBase interface to try to "fix"
	 * and upconvert HTML strings to XHTML
	 */
	class XhtmlizeFilter extends FilterBase
	{
		/**
		 * Filters out all HTML code except the allowed tags
		 *
		 * @param data
		 * @return The input string converted to XHTML
		 */
		function filter( $data )
		{
			lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
			$t = new Textfilter();
			return( parent::filter( $t->xhtmlize( $data )));
		}	
	}
?>