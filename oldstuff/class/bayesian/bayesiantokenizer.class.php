<?php

    lt_include( PLOG_CLASS_PATH."class/dao/bayesiantoken.class.php" );
	lt_include( PLOG_CLASS_PATH."class/bayesian/tokenizer.class.php" );

	define( "SPLIT_REG_EXP", "[^a-zA-Z0-9àáèéíïòóúüÀÁÈÉÍÏÒÓÚÜ'$!,.^-]+");

    /**
     * \ingroup Bayesian
     *
     * This class takes care of splitting a valid html source in the different words that
     * make it up, taking tags into account. The main public method is BayesianTokenizer::tokenize()
     */     
	class BayesianTokenizer extends Tokenizer 
	{
    
        var $_htmlTags = array();
        
        /**
         * constructor, it only calls the parent constructor.
         * @see Tokenizer
         */        
        function BayesianTokenizer()
        {            
            $this->Tokenizer();
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
        	$this->_htmlTags = array();
            $text = $this->_stripHtmlTags($text);
            $tokensFromHtml = $this->_tokenizeHtmlTags($this->_htmlTags);            
            $tokensText = $this->_tokenize($text);            
            $tokens = array_merge($tokensText, $tokensFromHtml);
            
            if ($unique)
            {
                $tokens = array_unique($tokens);
            }
            
            return $tokens;
        }
        
        /**
         * @private
         */
        function _tokenize($text)
        {
            $tokensTemp = split(SPLIT_REG_EXP, $text);            
            $tokens = array();
            
            foreach ($tokensTemp as $token)
            {
                if (strlen($token) > 0 && BayesianToken::isValid($token))
                {
                    if (ereg("\\$[0-9]+[-][0-9]+", $token))
                    {
                        $temp = split("[-]", $token);
                        
                        if (BayesianToken::isValid($temp[0]))
                        {
                        	array_push($tokens, $temp[0]);
                        }
                        
                        if (BayesianToken::isValid("$" . $temp[1]))
                        {
                        	array_push($tokens, "$" . $temp[1]);
                        }                        
                    }
                    else if (!ereg("[0-9]+[,.^][0-9]+", $token))
                    {
                    	$splitted = split("[,.^]", $token);
                    	
                    	foreach ($splitted as $splittedOne)
                    	{
                        	if (BayesianToken::isValid($splittedOne))
	                        {
	                        	array_push($tokens, $splittedOne);
	                        }
						}
                    }
                    else
                    {
                        array_push($tokens, $token);
                    }                        
                }
            }
            
            return $tokens;
        }
        
        /**
         * @private
         */
    	function _getValidHtmlTags($tags)
    	{
    		$validTags = array();
    		
    		foreach ($tags as $tag)
    		{
    			if (eregi("^<a ", $tag) || eregi("^<img ", $tag) || eregi("^<font ", $tag))
    			{
    				array_push($validTags, $tag);
    			}
    		}
    		
    		return $validTags;
    	}
    	
        /**
         * @private
         */
    	function _stripHtmlTags($text)
    	{    	    
            preg_match_all("/(<[^>]+>)/", $text, $regs);
            
            foreach  ($regs[1] as $tag)
            {
                array_push($this->_htmlTags, $tag);
            }
            
            $this->_htmlTags = $this->_getValidHtmlTags($this->_htmlTags);
            
    		return preg_replace("/<[^>]+>/", "", $text);
    	}
    	
        /**
         * @private
         */
    	function _tokenizeHtmlTags($tags)
    	{   
    	    $tokens = array();
    	     	    
            foreach ($tags as $tag)
            {
                $tokens = array_merge($tokens, $this->_tokenizeHtmlTag($tag));
            }
            
            return $tokens;
    	}
    	
        /**
         * @private
         */
    	function _tokenizeHtmlTag($tag)
    	{   
    	    $tokens = array();
    	    
    	    preg_match_all("/([^=]+)=\s*([^\s>]+)/", $tag, $regs);
            $count = count($regs[1]);
            
            //foreach  ($regs[2] as $value)
            for ($i = 0; $i < $count; $i++)
            {
            	$value = $regs[2][$i];
            	$prefix = "";
            	
            	if (eregi("(href|src)", $regs[1][$i]))
            	{
            		$prefix = TOKEN_URL_MARK;
				}
				
                $token = $this->_unquoteToken($value);
                $tokensTemp = $this->_tokenize($token);
                
                foreach  ($tokensTemp as $tokenTemp)
                {
	                if (BayesianToken::isValid($tokenTemp))
	                {
	                    array_push($tokens, $prefix . $tokenTemp);
	                }
				}                
            }
            
            return $tokens;
    	}
    	
        /**
         * @private
         */
    	function _unquoteToken($token)
    	{   
    	    if (ereg("^['\"](.+)['\"]$", $token, $regs))
    	    {
    	        return $regs[1];
            }
            else
            {
    	        return $token;
            }
    	}    	
    	
        /**
         * @private
         */
        function addContextMark($tokens, $mark)
        {
            $count = count($tokens);
            
            for ($i = 0; $i < $count; $i++)
            {
            	if (!eregi("^" . TOKEN_URL_MARK, $tokens[$i]))
            	{
                	$tokens[$i] = $mark . $tokens[$i];
				}
            }
        
            return $tokens;
        }
    }
?>
