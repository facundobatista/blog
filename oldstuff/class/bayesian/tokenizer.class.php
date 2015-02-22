<?php

	

    /**
     * \ingroup Bayesian
     * 
     * Class that defines the interface for classes wishing to implement a tokenizer
     */
    class Tokenizer  
    {

        /**
         * constructor, takes no parameters
         */
    	function Tokenizer()
        {
        	
        }

        /**
         * given an input text, possibly containing HTML tags, it will split it into
         * all the different words that make it up.
         *
         * @param text The text to split
         * @param unique Whether the return array should contain unique items or if the same
         * word is allowed more than once.
         * @return An array where each item is a word from the text
         */
        function tokenize($text, $unique = false)
        {
        	throw(new Exception("Tokenizer::tokenize: This method must be implemented by child classes."));
            die();
        }
    }
?>
