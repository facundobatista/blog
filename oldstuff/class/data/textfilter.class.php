<?php

    /**
     * \defgroup Data
     *
     * The Data module includes all sorts of functions that deal with data manipulation such as Validator
     * classes, text formatters or pagers
     */

	
	lt_include( PLOG_CLASS_PATH."class/data/stringutils.class.php" );
	
	/**
	 * default character used as the word separator, instead of blank spaces
	 * when creating permalinks and blog names (for subdomains)
	 */
	define( 'URLIZE_WORD_SEPARATOR_DEFAULT', '_' ); 

    /**
     * \ingroup Data
     *
	 * Implements some text filters that can be used to remove Javascript code, or convert
	 * non-XHTML code into valid XHTML code, etc.
	 */
	class TextFilter  
	{

		var $htmlAllowedTags;

        /**
         * Constructor.
         */
		function TextFilter()
		{
            lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
			$config =& Config::getConfig();
			$this->htmlAllowedTags = $config->getValue( "html_allowed_tags_in_comments" );
		}

		/**
		 * Removes the Javascript code from a string.
		 * Original function from http://www.zend.com/tips/tips.php?id=124&single=1
         *
         * @param text The text we want to filter
         * @return Returns the filtered text
		 * @static
		 */
        function filterJavaScript( $text )
        {
            lt_include( PLOG_CLASS_PATH."class/data/inputfilter.class.php" );		      
            $if = new InputFilter(Array(), Array(), 1, 1);
            $text = $if->process($text);
            return $text;
        }

		/**
		 * Filters a string with html code removing all the non-allowed tags
		 * (see the 'html_allowed_tags_in_comments' from the configuration file
		 * config.properties)
		 * It also removes the Javascript code if any.
         *
         * @param string The text we would like to filter
         * @return Returns the filtered text.
		 */
        function filterHTML( $string )
        {
			$tmp = strip_tags( $string, $this->htmlAllowedTags );
			// y luego eliminamos el javascript
			$filteredString = $this->filterJavaScript( $tmp );

			return $filteredString;
        }

		/**
		 * Works like the one above but it simply removes *all* html code
		 * from a string
         *
         * @param string The string we would like to filter
         * @return The filtered text.
		 * @static
		 */
        function filterAllHTML( $string )
        {
			$tmp = strip_tags( $string );
			// y luego eliminamos el javascript
			$filteredString = Textfilter::filterJavaScript( $tmp );

			return( trim($filteredString));
        }

		/**
		 * Converts special characters to HTML entities
		 * Works as a wrapper to the htmlentities() function from the PHP API
         * @param string The string we would like to process
         * @return The same string but with the HTML/XML entities encoded to their representation
		 */
		function filterHTMLEntities( $string )
		{
			return htmlentities( $string );
		}

        /**
         * Alias for filterHTMLEntities
         *
         * @see filterHTMLEntities
         */
		function filterXMLEntities( $string )
		{
			return $this->filterHTMLEntities($string);
		}
		
		/**
		 * removes characters from a string based on the input array
		 *
		 * @param string
		 * @param characters
		 * @returns the filtered string
		 */
		function filterCharacters( $string, $characters = Array()) 
		{
			foreach( $characters as $char ) {
				$string = str_replace( $char, "", $string );
			}
			
			return $string;
		}
		
        /**
         * Texturize function borrowed from http://photomatt.net/tools/texturize
         *
         * Takes care of "beautifying" code typed by users.
         */
        function texturize($text)
        {
            $textarr = preg_split("/(<.*>)/U", $text, -1, PREG_SPLIT_DELIM_CAPTURE); // capture the tags as well as in between
            $stop = count($textarr); $next = true; // loop stuff
            for ($i = 0; $i < $stop; $i++) {
            	$curl = $textarr[$i];
                if (!strstr($_SERVER['HTTP_USER_AGENT'], 'Gecko')) {
                	$curl = str_replace('<q>', '&#8220;', $curl);
                    $curl = str_replace('</q>', '&#8221;', $curl);
                }
                if ('<' != $curl{0} && $next) { // If it's not a tag
                	$curl = str_replace('---', '&#8212;', $curl);
                    $curl = str_replace('--', '&#8211;', $curl);
                    $curl = str_replace("...", '&#8230;', $curl);
                    $curl = str_replace('``', '&#8220;', $curl);

                    $curl = preg_replace("/'s/", "&#8217;s", $curl);
                    $curl = preg_replace("/'(\d\d(?:&#8217;|')?s)/", "&#8217;$1", $curl);
                    $curl = preg_replace('/(\s|\A|")\'/', '$1&#8216;', $curl);
                    $curl = preg_replace("/(\d+)\"/", "$1&Prime;", $curl);
                    $curl = preg_replace("/(\d+)'/", "$1&prime;", $curl);
                    $curl = preg_replace("/(\S)'([^'\s])/", "$1&#8217;$2", $curl);
                    $curl = preg_replace('/"([\s.]|\Z)/', '&#8221;$1', $curl);
                    $curl = preg_replace('/(\s|\A)"/', '$1&#8220;', $curl);
                    $curl = preg_replace("/'([\s.]|\Z)/", '&#8217;$1', $curl);
                    $curl = preg_replace("/\(tm\)/i", '&#8482;', $curl);
                    $curl = preg_replace("/\(c\)/i", '&#169;', $curl);
                    $curl = preg_replace("/\(r\)/i", '&#174;', $curl);

                    $curl = str_replace("''", '&#8221;', $curl);
                    $curl = preg_replace('/&([^#])(?![a-z]{2,8};)/', '&#038;$1', $curl);

                    $curl = preg_replace('/(d+)x(\d+)/', "$1&#215;$2", $curl);
               } elseif (strstr($curl, '<code') || strstr($curl, '<pre') || strstr($curl, '<kbd' || strstr($curl, '<style') || strstr($curl, '<script'))) {
               		// strstr is fast
            		$next = false;
        	  } else {
            		$next = true;
        	  }
        	  $output .= $curl;
    	}

    	return $output;
	}

        /**
         * Texturize function borrowed from http://photomatt.net/tools/texturize
         *
         * Takes care of "beautifying" text, by adding <p> tags to blocks of texts without the users
         * needing to know any html.
         *
         * @param pee
         * @param br
         */
		function autoP($pee, $br=1)
        {
			$pee = preg_replace("/(\r\n|\n|\r)/", "\n", $pee); // cross-platform newline
            $pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
        	$pee = preg_replace('/\n?(.+?)(\n\n|\z)/s', "<p>$1</p>\n", $pee); // make paragraphs, including one at the end
        	$pee = preg_replace('/<p>(<(?:table|[ou]l|pre|select|form|blockquote)>)/', "$1", $pee);
        	$pee = preg_replace('!(</?(?:table|[ou]l|pre|select|form|blockquote)>)</p>!', "$1", $pee);
        	if ($br) $pee = preg_replace('|(?<!</p>)\s*\n|', "<br />\n", $pee); // optionally make line breaks
        	$pee = preg_replace('!(</(?:table|[ou]l|pre|select|form|blockquote)>)<br />!', "$1", $pee);
        	$pee = str_replace('<p><p>', '<p>', $pee);
        	$pee = str_replace('</p></p>', '</p>', $pee);

        	return $pee;
		}
		
		/**
		 * This function works in the opposite way of htmlentities, by translating html entities
		 * into their character representations. PHP >= 4.3 has html_entity_decode() but this
		 * method below works fine regardless of the php version.
		 * 
		 * Obtained from http://fi.php.net/manual/en/function.html-entity-decode.php
		 *
		 * @param htmlString The original string with html entities encoded
		 * @param quoteStyle
		 * @return The decoded html string
		 * @static
		 */
		function htmlDecode( $htmlString, $quote_style = ENT_QUOTES )
		{
            // replace numeric entities
            $htmlString = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $htmlString);
            $htmlString = preg_replace('~&#([0-9]+);~e', 'chr(\\1)', $htmlString);
            // get the entity translation table from PHP (current encoding is ISO-8859-1)
            $trans_table = get_html_translation_table( HTML_ENTITIES, $quote_style );
            // when we want to decode the input string to normalized string, there are two factors 
            // we need to take into consideration:
            //  - Input string encoding
            //  - MySQL default-character-set encoding
            // No matter what input string encoding does, the normalized text saved to MySQL should 
            // follow MySQL data validation. If we don't follow the constraint, then MySQL will raise 
            // an error for us. (It only happend in MySQL5 strict mode)
            // Therefore, we need to check the db_character_set in our config file to see we should
            // use the UTF-8 translation table or ISO-8859-1 translation table
            // This should fixed the CJK/UTF-8 characters break by Jon's original modification.
            //
            // If possible, I really hope we can accept UTF-8 encoding only, it will make our life easier.
            lt_include( PLOG_CLASS_PATH . "class/config/configfilestorage.class.php" );
			$config = new ConfigFileStorage();
			if( $config->getValue( 'db_character_set' ) == 'utf8' ) {
				// Convert the ISO-8859-1 translation table to UTF-8
				foreach ( $trans_table as $key => $value ){
					$new_trans_table[$value] = utf8_encode( $key );
				}
			} else {
				// Keep original ISO-8859-1 translation table, just flip it
            	$new_trans_table = array_flip($trans_table);
			}
            return strtr( $htmlString, $new_trans_table );
		} 
		
		/**
		 * Normalizes the given text. By 'normalizing', it means removing all html markup from the text as well
		 * as all punctuation signs, commas, colons, semicolons, ellipsis and everything else... This method
		 * is used when storing a normalized version of the text of a post, so that all these characters do not
		 * interfere with the search engine provided by mysql. Otherwise, searching for things like
		 * 'href' will return strange results (it will return all posts where there were html links), or things
		 * like "welcome!" or "whatever..." wouldn't be returned when searching for "welcome" and "whatever"
		 *
		 * @param text The original text.
		 * @return Returns the normalized version of the original text.
		 * @static
		 */
		function normalizeText( $text )
		{
			  lt_include( PLOG_CLASS_PATH."class/dao/article.class.php" ); // because of the POST_EXTENDED_TEXT_MODIFIER constant
		      // remove all html code
		      $result = TextFilter::filterAllHtml( $text );
		      // remove the "extended post modifier"
		      $result = str_replace( POST_EXTENDED_TEXT_MODIFIER, "", $result );
		      // put all the html entities back to what they should be
		      $result = TextFilter::htmlDecode( $result );
		      // and remove everything which is not letters or numbers
		      $result = ereg_replace( "/[^A-Za-z0-9_]/", " ", $result );
		      // finally, remove the unnecessary spaces
		      $result = preg_replace( "/ +/", " ", $result );
		      
		      return $result;
		}
                
        /**
         * balanceTags
         *
         * Balances Tags of string using a modified stack.
         *
         * @param text      Text to be balanced
         * @return          Returns balanced text
         * @author          Leonard Lin (leonard@acm.org)
         * @version         v1.1
         * @date            November 4, 2001
         * @license         GPL v2.0
         * @notes           
         * @changelog       
         *            1.2  ***TODO*** Make better - change loop condition to $text
         *            1.1  Fixed handling of append/stack pop order of end text
         *                 Added Cleaning Hooks
         *            1.0  First Version
         */
        function balanceTags($text, $is_comment = 0) 
        {        
            $tagstack = array(); $stacksize = 0; $tagqueue = ''; $newtext = '';
        
            # WP bug fix for comments - in case you REALLY meant to type '< !--'
            $text = str_replace('< !--', '<    !--', $text);
            # WP bug fix for LOVE <3 (and other situations with '<' before a number)
            $text = preg_replace('#<([0-9]{1})#', '&lt;$1', $text);
        
            while (preg_match("/<(\/?\w*)\s*([^>]*)>/",$text,$regex)) {
                $newtext .= $tagqueue;
        
                $i = strpos($text,$regex[0]);
                $l = strlen($regex[0]);
        
                // clear the shifter
                $tagqueue = '';
                // Pop or Push
                if ($regex[1][0] == "/") { // End Tag
                    $tag = strtolower(substr($regex[1],1));
                    // if too many closing tags
                    if($stacksize <= 0) { 
                        $tag = '';
                        //or close to be safe $tag = '/' . $tag;
                    }
                    // if stacktop value = tag close value then pop
                    else if ($tagstack[$stacksize - 1] == $tag) { // found closing tag
                        $tag = '</' . $tag . '>'; // Close Tag
                        // Pop
                        array_pop ($tagstack);
                        $stacksize--;
                    } else { // closing tag not at top, search for it
                        for ($j=$stacksize-1;$j>=0;$j--) {
                            if ($tagstack[$j] == $tag) {
                            // add tag to tagqueue
                                for ($k=$stacksize-1;$k>=$j;$k--){
                                    $tagqueue .= '</' . array_pop ($tagstack) . '>';
                                    $stacksize--;
                                }
                                break;
                            }
                        }
                        $tag = '';
                    }
                } else { // Begin Tag
                    $tag = strtolower($regex[1]);
        
                    // Tag Cleaning
        
                    // If self-closing or '', don't do anything.
                    if((substr($regex[2],-1) == '/') || ($tag == '')) {
                    }
                    // ElseIf it's a known single-entity tag but it doesn't close itself, do so
                    elseif ($tag == 'br' || $tag == 'img' || $tag == 'hr' || $tag == 'input') {
                        $regex[2] .= '/';
                    } else {	// Push the tag onto the stack
                        // If the top of the stack is the same as the tag we want to push, close previous tag
                        if (($stacksize > 0) && ($tag != 'div') && ($tagstack[$stacksize - 1] == $tag)) {
                            $tagqueue = '</' . array_pop ($tagstack) . '>';
                            $stacksize--;
                        }
                        $stacksize = array_push ($tagstack, $tag);
                    }
        
                    // Attributes
                    $attributes = $regex[2];
                    if($attributes) {
                        $attributes = ' '.$attributes;
                    }
                    $tag = '<'.$tag.$attributes.'>';
                    //If already queuing a close tag, then put this tag on, too
                    if ($tagqueue) {
                        $tagqueue .= $tag;
                        $tag = '';
                    }
                }
                $newtext .= substr($text,0,$i) . $tag;
                $text = substr($text,$i+$l);
            }  
        
            // Clear Tag Queue
            $newtext .= $tagqueue;
        
            // Add Remaining text
            $newtext .= $text;
        
            // Empty Stack
            while($x = array_pop($tagstack)) {
                $newtext .= '</' . $x . '>'; // Add remaining tags to close
            }
        
            // WP fix for the bug with HTML comments
            $newtext = str_replace("< !--","<!--",$newtext);
            $newtext = str_replace("<    !--","< !--",$newtext);
        
            return $newtext;
        }
        
        /** 
         * Given a string, convert it into something that can be used in a URL (it probably doesn't work very
         * well with non iso-8859-X strings) It will remove the following characters:
         *
         * ; / ? : @ & = + $ ,
         *
         * It will convert accented characters such as ˆ, , ’, etc to their non-accented counterparts (a, e, i) And
         * any other non-alphanumeric character that hasn't been removed or replaced will be thrown away.
         *
         * @param string The string that we wish to convert into something that can be used as a URL
         */
        function urlize( $string, $domainize = false )
        {
            lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
			$config =& Config::getConfig();
            $separator = $config->getValue( "urlize_word_separator", URLIZE_WORD_SEPARATOR_DEFAULT );

            // remove unnecessary spaces and make everything lower case
		    $string = preg_replace( "/ +/", " ", strtolower($string) );

            // removing a set of reserved characters (rfc2396: ; / ? : @ & = + $ ,)
            $string = str_replace(array(';','/','?',':','@','&','=','+','$',','),
                                  $separator, $string);

            // replace some characters to similar ones
            $search  = array(' ', 'ä', 'ö', 'ü','é','è','à','ç', 'à', 'è', 'ì',
                             'ò', 'ù', 'á', 'é', 'í', 'ó', 'ú', 'ë', 'ï' );
            $replace = array( $separator, 'a','o','u','e','e','a','c', 'a', 'e', 'i',
                              'o', 'u', 'a', 'e', 'i', 'o', 'u', 'e', 'i' );
            if($domainize){
                    // domains shouldn't have underscores, and we'll convert
                    // hyphens to the user-customizable $separator to be
                    // consistent
                $search[] = '-';
                $search[] = '_';
                $replace[] = $separator;
                $replace[] = $separator;
            }
            $string = str_replace($search, $replace, $string);
            
                // and everything that is still left that hasn't been
                // replaced/encoded, throw it away
                // NOTE: need double backslash to pass the escape to preg_replace
            $good_characters = "a-z0-9.\\".$separator;
            if(!$domainize){
                $good_characters .= "_\\-";
            }
            $string = preg_replace( '/[^'.$good_characters.']/', '', $string );        
            
                // remove doubled separators
            $string = preg_replace("/[".$separator."]+/", $separator, $string);
                // remove starting and trailing separator chars
            $string = trim($string, $separator);
            if($domainize){
                // remove trailing dots - LT will add them back in if appropriate
                $string = trim($string, ".");
            }
            
            return $string;            
        }
		
        /** 
         * Given a string, convert it into something that can be used in the domain part of a URL
         * (it probably doesn't work very
         * well with non iso-8859-X strings) It will remove the following characters:
         *
         * ; / ? : @ & = + $ ,
         *
         * It will convert accented characters such as ˆ, , ’, etc to
         * their non-accented counterparts (a, e, i) And
         * any other non-alphanumeric character that hasn't been removed
         * or replaced will be thrown away.
         *
         * @param string The string that we wish to convert into something that can be used as a URL
 	     * @static
         */
        function domainize( $string )
        {
            return Textfilter::urlize($string, true);
        }

		/**
		 * xhtml-izes a string. It uses the KSes filter for the task as long as the configuration parameter
		 * xhtml_converter_enabled is enabled. If xhtml_converter_aggreesive_mode_enabled is also enabled,
		 * KSes will be set into "aggressive" mode and it will try to fix even more problems with the XHTML
		 * markup (but it can also introduce more mistakes)
		 *
		 * @param string
		 * @return the xhtml-ized string
		 */
		function xhtmlize( $string )
		{
		      // use kses in the "xhtml converter" mode
            lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
            $config =& Config::getConfig();
            if( $config->getValue( "xhtml_converter_enabled" )) {
                lt_include( PLOG_CLASS_PATH."class/data/kses.class.php" );
                $kses = new kses( true, $config->getValue( "xhtml_converter_aggresive_mode_enabled"));
                $string = $kses->Parse( $string );
            }
            
            return $string;
		}


		/**
		 * slugifies a string, which is to say that it urlizes it but
         *  additionally only uses characters allowed in a postname, as
         *  defined by the linkparser.
		 *
		 * @param string
		 * @return the xhtml-ized string
		 */
        function slugify( $string ){
            lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
            lt_include( PLOG_CLASS_PATH.'class/net/linkparser.class.php' );

			$config =& Config::getConfig();
            $separator = $config->getValue( "urlize_word_separator", URLIZE_WORD_SEPARATOR_DEFAULT );
                // remove characters not allowed by the link parser
            $lp = new LinkParser("");
            $regexp = $lp->getValidTag("{postname}");
            $start_bracket = strpos($regexp, "[");
            $end_bracket = strrpos($regexp, "]");
            $validChars = false;
            if($start_bracket !== false && $end_bracket !== false){
                $validChars = substr($regexp, $start_bracket+1,
                                     $end_bracket-$start_bracket-1);
            }
                // link format doesn't contain brackets, or is not what we
                // were expecting using default
            if($validChars === false){
                $validChars = "_0-9a-zA-Z.-";
            }
                // remove "bad" characters
            $string = preg_replace("/[^".$validChars."]/", $separator, strip_tags(Textfilter::htmlDecode($string)));
                // remove doubled separators
            $string = preg_replace("/[".$separator."]+/", $separator, $string);
                // remove starting and trailing separator chars
            $string = trim($string, $separator);
				// and convert to lowercase
			$string = strtolower( $string );

            return $string;
		}


        function recursiveStripSlashes($obj){
            foreach($obj as $key => $value){
                if(is_array($value)){
                    $obj[$key] = Textfilter::recursiveStripSlashes($value);
                }
                else{
                    $obj[$key] = stripslashes($value);
                }
            }
            return $obj;
        }
            
        
		/**
		 * @private
		 */
		function checkboxToBoolean( $value )
		{
			if( $value == "1" || $value == "on" )
				return true;
			else
				return false;
		}
    }
?>
