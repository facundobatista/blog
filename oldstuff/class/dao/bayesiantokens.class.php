<?php

	lt_include( PLOG_CLASS_PATH."class/dao/model.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/bayesiantoken.class.php" );

    /**
     * Takes care of dealing with fetching filtered contents from the database
	 * \ingroup DAO
     */
	class BayesianTokens extends Model 
	{

    	function BayesianTokens()
        {
        	$this->Model();
        }

        /**
        * -- Add function info here --
        */
        function getBayesianTokenFromId($id)
        {
        	$query = "SELECT * FROM " . $this->getPrefix() . "bayesian_tokens WHERE id = $id";

            return $this->getBayesianTokenFromQuery( $query );
        }
        
        /**
        * -- Add function info here --
        */
        function getBayesianTokenFromToken($blogId, $token)
        {
            $query = "SELECT * FROM " . $this->getPrefix() . "bayesian_tokens WHERE token = '" . addSlashes($token) . "' AND blog_id = '$blogId'";

            return $this->getBayesianTokenFromQuery($query, $token);
        }
        
        /**
        * -- Add function info here --
        */
        function getBayesianTokensFromArray($blogId, $tokens, $degenerate = true)
        {
            $bayesianTokens = array();
            
            foreach ($tokens as $token)
            {
                $bayesianToken = $this->getBayesianTokenFromToken($blogId, $token);                

                if (!$bayesianToken)
                {
                	if ($degenerate)
                	{   
                		$bayesianToken = $this->getFarthestToken($blogId, BayesianToken::degenerate($token));
					}
					else
                	{
                    	$bayesianToken = new BayesianToken($blogId, $token, 0, 0, TOKEN_DEFAULT_PROBABILITY);
					}
                }
                
				array_push($bayesianTokens, $bayesianToken);
            }
        	        	        	        	
        	return $bayesianTokens;
        }
        
        /**
        * -- Add function info here --
        */
        function getFarthestToken($blogId, $tokens)
        {
        	$bayesianTokens = new BayesianTokens();            
            $tokens = $bayesianTokens->getBayesianTokensFromArray($blogId, $tokens, false);            
            
            $tempArray = array();

            foreach ($tokens as $token)
            {
				array_push($tempArray, abs($token->getProb() - 0.5));
            }
            
            arsort($tempArray);
            $keys = array_keys($tempArray);
            $key = $keys[0];
			
			return $tokens[$key];
        }
        
        /**
        * -- Add function info here --
        */
        function updateFromQuery($query)
        {
            $result = $this->Execute( $query );

            if( !$result )
            	return false;

            return true;
        }
        
        /**
        * -- Add function info here --
        */
        function getBayesianTokenFromQuery($query, $token = false)
        {
            $result = $this->Execute($query);

            if( !$result )	// return an empty array if error
            	return false;

			if ($token === false)
			{
	            $row = $result->FetchRow();
                $result->Close();			

	            if(!$row)
	            	return false;
	            	
				return $this->_mapRowToObject($row);
			}
			else
			{
				while ($row = $result->FetchRow())
				{
					if ($row["token"] == $token)
					{
                        $result->Close();			
						return $this->_mapRowToObject($row);
					}					
				}
                $result->Close();			

				return false;
			}
        }
        
        /**
        * -- Add function info here --
        */
        function _mapRowToObject($row)
        {
            return new BayesianToken($row["blog_id"], $row["token"], $row["spam_occurrences"], $row["nonspam_occurrences"], $row["prob"], $row["id"]);
        }
        
        /**
        * -- Add function info here --
        */
        function _calcProb($spamOccurrences, $nonSpamOccurrences, $totalSpam, $totalNonSpam)
        {
            return ($spamOccurrences / $totalSpam) / (2 * ($nonSpamOccurrences / $totalNonSpam) + ($spamOccurrences / $totalSpam));
        }
        
        /**
        * -- Add function info here --
        */
        function incSpamOccurrencesFromTokensArray($blogId, $tokens, $totalSpam, $totalNonSpam)
        {
            $this->updateOccurrencesFromTokensArray($blogId, $tokens, 1, 0, $totalSpam, $totalNonSpam);
        }
    
        /**
        * -- Add function info here --
        */
        function incNonSpamOccurrencesFromTokensArray($blogId, $tokens, $totalSpam, $totalNonSpam)
        {
            $this->updateOccurrencesFromTokensArray($blogId, $tokens, 0, 1, $totalSpam, $totalNonSpam);
        }
        
        /**
        * -- Add function info here --
        */
        function decSpamOccurrencesFromTokensArray($blogId, $tokens, $totalSpam, $totalNonSpam)
        {
            $this->updateOccurrencesFromTokensArray($blogId, $tokens, -1, 0, $totalSpam, $totalNonSpam );
        }
    
        /**
        * -- Add function info here --
        */
        function decNonSpamOccurrencesFromTokensArray($blogId, $tokens, $totalSpam, $totalNonSpam)
        {
            $this->updateOccurrencesFromTokensArray($blogId, $tokens, 0, -1, $totalSpam, $totalNonSpam);
        }
        
        /**
        * -- Add function info here --
        */
        function updateOccurrencesFromTokensArray($blogId, $tokens, $spamAddition, $nonSpamAddition, $totalSpam, $totalNonSpam )
        {                        
            foreach ($tokens as $token)
            {
                $result = $this->updateOccurrences($blogId, $token, $spamAddition, $nonSpamAddition, $totalSpam, $totalNonSpam );
                
                if (!$result)
                {
                    throw(new Exception("BayesianTokens::updateOccurrencesFromTokensArray: Cannot update occurrences of token '$token'."));
                    die();
                }
            }
        }
        
        /**
        * -- Add function info here --
        */
        function updateOccurrences($blogId, $token, $spamAddition, $nonSpamAddition, $totalSpam, $totalNonSpam, $insert = true)
        {          
        	$prob = 0;
        	  
            if ($tk = $this->getBayesianTokenFromToken($blogId, $token))
            {
                $id = $tk->getId();
                $spamOccurrences = $tk->getSpamOccurrences() + $spamAddition;
                $nonSpamOccurrences = $tk->getNonSpamOccurrences() + $nonSpamAddition;
                
                if ($spamOccurrences == 0 && $nonSpamOccurrences == 0)
                {
                	return $this->delete($id);
                }
                
                if ($totalSpam == 0 || $totalNonSpam == 0)
                {
                    if ($spamAddition == 1)
                    {
                        $prob = 0.99;
                    }
                    else
                    {
                        $prob = 0.01;
                    }
                }
                else
                {
                    $prob = $this->_calcProb($spamOccurrences, $nonSpamOccurrences, $totalSpam, $totalNonSpam);
                    
                    if ($prob == 1)
                    {
                    	$prob = 0.99;
					}
					else if ($prob == 0)
                    {
                    	$prob = 0.01;
					}
                }
                
                $query = "UPDATE " . $this->getPrefix() . "bayesian_tokens SET " .
                         "spam_occurrences = $spamOccurrences, " .
                         "nonspam_occurrences = $nonSpamOccurrences, " .
                         "prob = $prob" .
                         " WHERE id=$id;";
                
                return $this->updateFromQuery($query);
            }
            else if ($insert)
            {
                if ($spamAddition == 1)
                {
                    return $this->insert($blogId, $token, 1, 0, 0.99);
                }
                else
                {
                    return $this->insert($blogId, $token, 0, 1, 0.01);
                }
            }
        }
        
        /**
        * -- Add function info here --
        */
        function updateProbabilities($blogId, $totalSpam, $totalNonSpam)
        {          
			$query = "UPDATE " . $this->getPrefix() . "bayesian_tokens SET " .
					 "prob = (spam_occurrences / $totalSpam) / (2 * (nonspam_occurrences / $totalNonSpam) + (spam_occurrences / $totalSpam))" .
					 " WHERE blog_id = $blogId;";

			if (!$this->updateFromQuery($query))
			{
				return false;
			}
            
            $query = "UPDATE " . $this->getPrefix() . "bayesian_tokens SET " .
					 "prob = 0.99" .
					 " WHERE prob = 1 AND blog_id = $blogId;";
                
			if (!$this->updateFromQuery($query))
			{
				return false;
			}

			$query = "UPDATE " . $this->getPrefix() . "bayesian_tokens SET " .
					 "prob = 0.01" .
					 " WHERE prob = 0 AND blog_id = $blogId;";
                
			return $this->updateFromQuery($query);
        }
        
        /**
        * -- Add function info here --
        */
        function insert($blogId, $token, $spamOccurrences, $nonSpamOccurrences, $prob)
        {
            $token = addSlashes($token);
            $query = "INSERT INTO " . $this->getPrefix() . "bayesian_tokens (blog_id, token, spam_occurrences, nonspam_occurrences, prob) VALUES " .
                     "('$blogId', '$token', '$spamOccurrences', '$nonSpamOccurrences', '$prob')";
            
            $result = $this->Execute($query);

            if(!$result)
            	return false;

            return true;
        }
        
        /**
        * -- Add function info here --
        */
        function delete($id)
        {
            $query = "DELETE FROM " . $this->getPrefix() . "bayesian_tokens WHERE id=$id";
            
            $result = $this->Execute($query);

            if(!$result)
            	return false;

            return true;
        }

        /**
        * -- Add function info here --
        */
        function deleteBayesianTokensByBlogId( $blogId )
        {
            $query = "DELETE FROM " . $this->getPrefix() . "bayesian_tokens WHERE blog_id=$blogId";
            
            $result = $this->Execute($query);

            if(!$result)
            	return false;

            return true;
        }        
    }
?>
