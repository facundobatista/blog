<?php

    /**
     * \defgroup Bayesian
     *
     * The Bayesian module provides the logic that implements a spam filter based
     * on Bayesian filtering techniques. The filter is trained via the user's own posts (they are
     * considered "safe" content) and the negative food is provided via the posts marked as spam by
     * users via the admin interface.
     */


	
    lt_include( PLOG_CLASS_PATH."class/bayesian/bayesiantokenizer.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/bayesianfilterinfos.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/bayesiantokens.class.php" );

    /**
     * \ingroup Bayesian
     *
     * This class provides the 'train' and 'untrain' method that are crucial to get our
     * filter to differentiate between spam and non-spam. 
     *
     * The filter needs a bit of training before it can recognize spam content so please use the 
     * BayesianFilterCore::train() method for giving "bad" content to the filter and the 
     * BayesianFilterCore::untrain() for giving "good" content to the filter.
     *
     * This class is the base for the BayesianFilter object that works as a PipelineFilter in the
     * Pipeline.
     *
     * Client classes will rarely need to use this class.
     *
     * @see BayesianFilter
     */
	class BayesianFilterCore 
    {

    	function BayesianFilterCore()
        {
        	
        }

        /**
         * Trains the filter to recognize comments like this as spam
         *
         * @param blogId The blog id
         * @param topic The topic of the comment/article that we're using to train the filter
         * @param text The text of the comment/articles that we're usingn to train the filter
         * @param userName Name of the user posting this comment/article
         * @param userEmail Email address of the user posting this comment/article
         * @param userUrl URL of the user posting this comment/article
         * @param spam Wether we should set this message as spam or not. The content will be marked
         * as non-spam by default
         * @static
         * @return true
         */
        function train($blogId, $topic, $text, $userName, $userEmail, $userUrl, $spam = false)
        {
            $tokenizer = new BayesianTokenizer();
            
            $tokensTopic = $tokenizer->addContextMark($tokenizer->tokenize($topic), TOKEN_TOPIC_MARK);
            $tokensText = $tokenizer->tokenize($text);
            $tokensUserName = $tokenizer->addContextMark($tokenizer->tokenize($userName), TOKEN_USER_NAME_MARK);
            $tokensUserEmail = $tokenizer->addContextMark($tokenizer->tokenize($userEmail), TOKEN_USER_EMAIL_MARK);
            $tokensUserUrl = $tokenizer->addContextMark($tokenizer->tokenize($userUrl), TOKEN_USER_URL_MARK);
            
            $tokens = array_merge($tokensTopic, $tokensText, $tokensUserName, $tokensUserEmail, $tokensUserUrl);
            
            $bayesianFilterInfos = new BayesianFilterInfos();
            $bayesianFilterInfo  = $bayesianFilterInfos->getBlogBayesianFilterInfo($blogId);
            
            $totalSpam = $bayesianFilterInfo->getTotalSpam();
            $totalNonSpam = $bayesianFilterInfo->getTotalNonSpam();
            
            $bayesianTokens = new BayesianTokens();
            
            if ($spam)
            {
                $totalSpam++;
                $bayesianFilterInfos->incTotalSpam($bayesianFilterInfo->getId());
                $bayesianTokens->incSpamOccurrencesFromTokensArray($blogId, $tokens, $totalSpam, $totalNonSpam);
            }
            else
            {	
                $totalNonSpam++;
                $bayesianFilterInfos->incTotalNonSpam($bayesianFilterInfo->getId());
                $bayesianTokens->incNonSpamOccurrencesFromTokensArray($blogId, $tokens, $totalSpam, $totalNonSpam);
            }
            
            return true;
        }
        
        /**
         * Given an Article object, trains the filter based on the article data
         *
         * @param article An Article object
         * @return true
         * @static
         */
        function trainWithArticle( $article )
        {
	    	return( BayesianFilterCore::train( $article->getBlog(),
	                                           $article->getTopic(),
	                                           $article->getText(),
	                                           "",
	                                           "",
	                                           "",
	                                           false ));
        }

        /**
         * untrains the filter
         *
         * @param blogId The blog id
         * @param topic The topic of the comment/article that we're using to untrain the filter
         * @param text The text of the comment/articles that we're usingn to untrain the filter
         * @param userName Name of the user posting this comment/article
         * @param userEmail Email address of the user posting this comment/article
         * @param userUrl URL of the user posting this comment/article
         * @param spam Wether we should unmark these contents as spam or not. The content will be unmarked
         * as non-spam by default
         * @static
         * @see train
         */
        function untrain($blogId, $topic, $text, $userName, $userEmail, $userUrl, $spam = false)
        {
            $tokenizer = new BayesianTokenizer();
            
            $tokensTopic = $tokenizer->addContextMark($tokenizer->tokenize($topic), TOKEN_TOPIC_MARK);
            $tokensText = $tokenizer->tokenize($text);
            $tokensUserName = $tokenizer->addContextMark($tokenizer->tokenize($userName), TOKEN_USER_NAME_MARK);
            $tokensUserEmail = $tokenizer->addContextMark($tokenizer->tokenize($userEmail), TOKEN_USER_EMAIL_MARK);
            $tokensUserUrl = $tokenizer->addContextMark($tokenizer->tokenize($userUrl), TOKEN_USER_URL_MARK);
            
            $tokens = array_merge($tokensTopic, $tokensText, $tokensUserName, $tokensUserEmail, $tokensUserUrl);
            
            $bayesianFilterInfos = new BayesianFilterInfos();
            $bayesianFilterInfo  = $bayesianFilterInfos->getBlogBayesianFilterInfo($blogId);
            
            $totalSpam = $bayesianFilterInfo->getTotalSpam();
            $totalNonSpam = $bayesianFilterInfo->getTotalNonSpam();
            
            $bayesianTokens = new BayesianTokens();
            
            if ($spam)
            {
                $bayesianTokens->decSpamOccurrencesFromTokensArray($blogId, $tokens, $totalSpam, $totalNonSpam);
                $bayesianFilterInfos->decTotalSpam($bayesianFilterInfo->getId());
            }
            else
            {
                $bayesianTokens->decNonSpamOccurrencesFromTokensArray($blogId, $tokens, $totalSpam, $totalNonSpam);
                $bayesianFilterInfos->decTotalNonSpam($bayesianFilterInfo->getId());
            }
            
            return true;
        }
        
        /** 
         * untrains the filter based on information from the given Article object
         *
         * @param Article and Article object
         * @return always true
         * @static
         * @see untrain
         */
        function untrainWithArticle( $article )
        {
	    	return( BayesianFilterCore::untrain( $article->getBlog(),
	    	                                     $article->getTopic(),
	    	                                     $article->getText(),
	    	                                     "",
	    	                                     "",
	    	                                     "",
	    	                                     false ));
        }
   }
?>
