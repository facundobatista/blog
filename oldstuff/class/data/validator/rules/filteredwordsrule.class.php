<?php

    lt_include( PLOG_CLASS_PATH."class/data/validator/rules/rule.class.php");

    /**
	 * \ingroup Validator_Rules
	 *
	 * Give an array with words, this validation rule checks that the word given as a parameter in the
	 * validate() method does not appear in the list.
	 */
    class FilteredWordsRule extends Rule
    {
		var $_filteredWords;
		
        /**
         * Constructor.
 	 	 * 
		 * @param filteredWords An arary containing the list of words that needs to be checked
         */
        function FilteredWordsRule( $filteredWords )
        {
            $this->Rule();

			$this->_filteredWords = $filteredWords;
        }

        /**
		 * Validates that the given word does not appear in the array given as a parameter to the 
		 * constructor.
		 *
		 * @param value The word to validate
		 * @return True if the word does not appear or false otherwise
         */
        function validate($value)
        {
			$words = explode( " ", $value );
			
			// loop through the words in the string, and for each one of them, see if it's one
			// of the words in the array of "forbidden" words
			foreach( $words as $word ) {
				foreach( $this->_filteredWords as $filteredWord ) {
					if( strcasecmp( $word, $filteredWord ) == 0 )
						return false;
				}
			}
			
			return true;
        }
    }
?>
