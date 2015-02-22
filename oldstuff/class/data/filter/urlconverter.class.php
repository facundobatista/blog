<?php

	lt_include( PLOG_CLASS_PATH."class/data/filter/filterbase.class.php" );

	/**
	 * \ingroup Filter	
	 *
	 * This class extends the FilterBase interface to force the
     * given string to be a proper URL.     
	 */
	class UrlConverter extends FilterBase
	{
		/**
		 * Constructor
		 */
		function UrlConverter()
        {
			$this->FilterBase();
		}
		
		/**
		 * Forces a given string to look at least sort of like a URL
         *   currently all it does is prepend http:// if it isn't there.
		 *
		 * @param data
		 * @return The input string with http:// prepended
		 */
		function filter( $data )
		{
            if((strlen($data) != 0) && ereg('^https?://', $data) == false){
                $data = "http://" . $data;
            }
			return(parent::filter($data));
		}	
	}
?>