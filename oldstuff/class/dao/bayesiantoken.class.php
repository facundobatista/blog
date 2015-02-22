<?php

	lt_include( PLOG_CLASS_PATH."class/database/dbobject.class.php" );

	define("TOKEN_DEFAULT_PROBABILITY", 0.4);
	
    define("TOKEN_SEPARATOR_MARK", "#");
    define("TOKEN_TOPIC_MARK", "Topic" . TOKEN_SEPARATOR_MARK);
    define("TOKEN_URL_MARK", "Url" . TOKEN_SEPARATOR_MARK);
    define("TOKEN_USER_NAME_MARK", "UserName" . TOKEN_SEPARATOR_MARK);
    define("TOKEN_USER_EMAIL_MARK", "UserEmail" . TOKEN_SEPARATOR_MARK);
    define("TOKEN_USER_URL_MARK", "UserUrl" . TOKEN_SEPARATOR_MARK);
    
    /**
	 * \ingroup DAO
	 *
     * Represents a record form the plog_filtered_content table
     *
     * The key of this class is the regexp that will be used to match
     * words against it.
     */
    class BayesianToken extends DbObject 
	{
    	
        var $_id;
        var $_blogId;
        var $_token;
        var $_spamOccurrences;
        var $_nonSpamOccurrences;
        var $_prob;
        
        /**
        * -- Add function info here --
        */
    	function BayesianToken($blogId, $token, $spamOccurrences, $nonSpamOccurrences, $prob, $id = -1)
        {
        	$this->DbObject();

            $this->_id                  = $id;
            $this->_blogId              = $blogId;
            $this->_token               = $token;
            $this->_spamOccurrences     = $spamOccurrences;
            $this->_nonSpamOccurrences  = $nonSpamOccurrences;
            $this->_prob                = $prob;
        }
        
        /**
        * -- Add function info here --
        */
        function getId()
        {
        	return $this->_id;
        }
        
        /**
        * -- Add function info here --
        */
        function getBlogId()
        {
        	return $this->_blogId;
        }
        
        /**
        * -- Add function info here --
        */
        function getToken()
        {
        	return $this->_token;
        }
        
        /**
        * -- Add function info here --
        */
        function getSpamOccurrences()
        {
        	return $this->_spamOccurrences;
        }
        
        /**
        * -- Add function info here --
        */
        function getNonSpamOccurrences()
        {
        	return $this->_nonSpamOccurrences;
        }
        
        /**
        * -- Add function info here --
        */
        function getProb()
        {
        	return $this->_prob;
        }
        
        /**
        * -- Add function info here --
        */
        function setProb($prob)
        {
        	$this->_prob = $prob;
        }
        
        /**
        * -- Add function info here --
        */
        function isSignificant()
        {            
            return ((2 * $this->getNonSpamOccurrences()) + $this->getSpamOccurrences()) > 5;
        }
        
        /**
        * -- Add function info here --
        */
        function isValid($token = false)
        {
        	$config	=& Config::getConfig();
        	
            if ($token === false)
            {
                $token = $this->getToken();
            }
            
            $valid = !ereg("^[0-9-]+$", $token);

            if ($config->getValue("bayesian_filter_min_length_token") > 0)
            {                                            	
                $valid = $valid && strlen($token) >= $config->getValue("bayesian_filter_min_length_token");
            }
            
            if ($config->getValue("bayesian_filter_max_length_token") > 0)
            {
                $valid = $valid && strlen($token) <= $config->getValue("bayesian_filter_max_length_token");
            }
            
            return $valid;
        }
        
        /**
        * -- Add function info here --
        */
        function degenerate($token = false)
        {
        	if ($token === false)
            {
                $token = $this->getToken();
            }
   
            $degenerations = array();
            $curToken = $token;
            $prefix = true;
            
            while ($prefix)
            {
                if (ereg("^(" . TOKEN_TOPIC_MARK . "|" . 
                				TOKEN_URL_MARK . "|" . 
                				TOKEN_USER_NAME_MARK . "|" .
                				TOKEN_USER_EMAIL_MARK . "|" .
                				TOKEN_USER_URL_MARK . ")(.*)$", $curToken, $regs))
            	{
            	    $curToken = $regs[2];
            	    $prefix = $regs[1];
                }
                else
                {
                    $prefix = "";
                }

                $degenerations = array_merge($degenerations, BayesianToken::getBasicsDegeneration($curToken, $prefix));

                if (ereg("([^!]+!)!+$", $curToken, $regs))
                {
                    $degenerations = array_merge($degenerations, BayesianToken::getBasicsDegeneration($regs[1], $prefix));
                }
                
                if (ereg("([^!]+)!+$", $curToken, $regs))
                {   
                    $degenerations = array_merge($degenerations, BayesianToken::getBasicsDegeneration($regs[1], $prefix));
                }   
                
                if ($prefix == "" && $curToken == $token)
                {
                	foreach ($degenerations as $degeneration)
                	{
                		array_push($degenerations, TOKEN_TOPIC_MARK . $degeneration);
                		array_push($degenerations, TOKEN_URL_MARK . $degeneration);
                		array_push($degenerations, TOKEN_USER_NAME_MARK . $degeneration);
                		array_push($degenerations, TOKEN_USER_EMAIL_MARK . $degeneration);
                		array_push($degenerations, TOKEN_USER_URL_MARK . $degeneration);                		
                	}
                }
            }

			return array_unique($degenerations);
        }
        
        /**
        * -- Add function info here --
        */
        function getBasicsDegeneration($token = false, $prefix = "")
        {
            if ($token === false)
            {
                $token = $this->getToken();
            }
            
            $degenerations = array();
            
            $lower = strtolower($token);                 
            array_push($degenerations, $prefix . $token);
            array_push($degenerations, $prefix . strtoupper($token));
            array_push($degenerations, $prefix . $lower);
            array_push($degenerations, $prefix . ucFirst($lower));

            return $degenerations;
        }
    }
?>
