<?php

	lt_include( PLOG_CLASS_PATH."class/data/filter/filterbase.class.php" );

	/**
	 * \ingroup Filter
	 *
	 * This class extends the FilterBase interface to filter all Javascript
	 * code in the given string
	 */
	class JavascriptFilter extends FilterBase
	{
		/**
		 * Filters out all Javascript code
		 *
		 * @param data
		 * @return The input string without Javascript code
		 */
		function filter( $data )
		{
			lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
			return( parent::filter( Textfilter::filterJavascript( $data )));
		}	
	}
?>