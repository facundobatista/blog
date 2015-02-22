<?php

    lt_include( PLOG_CLASS_PATH."class/security/pipelinefilter.class.php" );
	lt_include( PLOG_CLASS_PATH."class/net/client.class.php" );
	lt_include( PLOG_CLASS_PATH."class/net/client.class.php" );

    define("HIGH_SPAM_PROBABILITY", 1000);

    //
    // these two settings determine what will the filter do with the text of the
    // post when it is considered spam... we can either keep it in the db but marked
    // as spam or we can also throw it away. The second option can be used when our
    // filter has been better trained and we don't care about spam anymore...
    //
    define( "BAYESIAN_FILTER_KEEP_COMMENT_ACTION", 0 );
    define( "BAYESIAN_FILTER_THROW_COMMENT_AWAY_ACTION", 1 );

    /**
     * \ingroup Security
     *
     * Filters the text posted in a comment by a user, to prevent spam-bots. This
     * filter only works if the incoming request has the "op" parameter as
     * "AddComment", because then it means that we're posting a comment. If it's not
     * like that, then we'll quit. Otherwise, the process will continue as normally.
     *
     * This filter uses our implementation of the Bayesian filter from the Bayesian module
     * in order to filter spam comments out. The filter needs some training but after that it should
     * be fairly reliable.
     */
    class BayesianFilter extends PipelineFilter
    {

        function BayesianFilter( $pipelineRequest )
        {
            $this->PipelineFilter( $pipelineRequest );
        }

        /**
        * Processes incoming requests
        *
        * @return A positive PipelineResult object is the comment is not spam or a negative
        * one if it is.
        */
        function filter()
        {
            $config =& Config::getConfig();

            if (!$config->getValue("bayesian_filter_enabled")) {
                return new PipelineResult();
            }

            // get some info
            $blogInfo = $this->_pipelineRequest->getBlogInfo();
            $request  = $this->_pipelineRequest->getHttpRequest();
            $previouslyRejected = $this->_pipelineRequest->getRejectedState();

            // we only have to filter the contents if the user is posting a comment
            // or we're receiving a trackback
            // so there's no point in doing anything else if that's not the case
            if( $request->getValue( "op" ) != "AddComment" && $request->getValue( "op" ) != "AddTrackback" ) {
                return new PipelineResult();
            }

            lt_include( PLOG_CLASS_PATH."class/dao/articlecomments.class.php" );
            lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );

            lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );
            lt_include( PLOG_CLASS_PATH."class/data/filter/urlconverter.class.php" );
            lt_include( PLOG_CLASS_PATH."class/data/filter/allowedhtmlfilter.class.php" );
            lt_include( PLOG_CLASS_PATH."class/data/filter/xhtmlizefilter.class.php" );

            // if it's a trackback, the data is in another place...
            $parentId = "";
            $isTrackback = ($request->getValue( "op" ) == "AddTrackback");
            if( $isTrackback ) {
                $f = new HtmlFilter();
                $userName = $request->getFilteredValue( "blog_name", $f );
                $userEmail = $request->getFilteredValue( "", $f );
                $commentTopic = $request->getFilteredValue( "title", $f );
                $commentText = $request->getFilteredValue( "excerpt", $f );

    			$f = new HtmlFilter();
    			$f->addFilter( new UrlConverter());
    			$userUrl = $request->getFilteredValue( "url", $f );

                $articleId = (int) $request->getValue( "id" );
            }
            else {
                // or else let's assume that we're dealing with a comment
                $f = new HtmlFilter();
                $userName = $request->getFilteredValue( "userName", $f );
                $userEmail = $request->getFilteredValue( "userEmail", $f );
                $commentTopic = $request->getFilteredValue( "commentTopic", $f );

    			$f = new HtmlFilter();
    			$f->addFilter( new UrlConverter());
    			$userUrl = $request->getFilteredValue( "userUrl", $f );

    			$f = new AllowedHtmlFilter();
    			$f->addFilter( new XhtmlizeFilter());
    			$commentText = $request->getFilteredValue( "commentText", $f );

                $articleId = (int) $request->getValue( "articleId" );
                $parentId  = (int) $request->getValue( "parentId" );
            }

			// the two checks below are duplicating some of the code in AddCommentAction
			// and definitely belong to the business logic rather than to the bayesian filter
			// logic, but the problem here is that the filter is run *before* these checks are performed
			// and in some situations, we may end up adding comments for an article that has commenting
			// disabled or for an article that does not even exist

			// does the article even exist?
            $articles = new Articles();
            $article  = $articles->getBlogArticle( $articleId, $blogInfo->getId());
            if(!$article) {
				// if the article to which the articleId parameter refers to doesn't exist, there really
				// is no need to process the whole comments even if it's spam, the request will not be
				// processed by AddCommentAction for this very same reason
                return new PipelineResult();
			}

			// and if it does, are comments enabled for it anyway?
			$blogSettings = $blogInfo->getSettings();
            if( $article->getCommentsEnabled() == false || $blogSettings->getValue ( "comments_enabled" ) == false ) {
				// we let this request pass through although it may be spam, since it will be blocked
				// later on by AddCommentAction because comments aren't enabled
                return new PipelineResult();
			}

            if( $parentId == "" )
                $parentId = 0;

            lt_include( PLOG_CLASS_PATH."class/bayesian/bayesianfiltercore.class.php" );
            if($previouslyRejected){
					// train this as spam
                BayesianFilterCore::train( $blogInfo->getId(), $commentTopic,
                                           $commentText, $userName, $userEmail,
                                           $userUrl, true );
                    // return true, since we didn't check it
                return new PipelineResult();
            }
            else{
                    // check whether this is spam or not, and train appropriately
                $spamicity = $this->getSpamProbability($blogInfo->getId(),
                                                       $commentTopic,
                                                       $commentText,
                                                       $userName, $userEmail,
                                                       $userUrl);

                if ($spamicity >= $config->getValue("bayesian_filter_spam_probability_treshold"))
                {
                        // need this to get the locale
                    $plr = $this->getPipelineRequest();
                    $bi = $plr->getBlogInfo();
                    $locale = $bi->getLocale();
                    
                        // now we need to check what we have to do with this comment... either throw it away
                        // or keep it in the database
                    
                        // this piece of code shouldn't really go here, but it's easier than letting
                        // the AddComment action that there was actually a comment and that it should
                        // still be added but marked as spam and so on... sometimes breaking a few
                        // rules makes things easier :)
                    if( $config->getValue( "bayesian_filter_spam_comments_action" ) == BAYESIAN_FILTER_KEEP_COMMENT_ACTION )
                    {
                        $result = new PipelineResult(false, HIGH_SPAM_PROBABILITY, $locale->tr("error_comment_spam_keep" ));
                        $comments = new ArticleComments();
                        $clientIp = Client::getIp();
                        $comment = new UserComment( $articleId, $blogInfo->getId(), $parentId, $commentTopic, $commentText,
                                                    null, $userName, $userEmail, $userUrl, $clientIp,
                                                    0, COMMENT_STATUS_SPAM );
                            // mark it as a trackback instead of a user comment...
                        
                        if( $isTrackback ) {
                            $comment->setType( COMMENT_TYPE_TRACKBACK );
                        }
                        
                            // add the comment to the db
                        $comments->addComment( $comment );
                    }
                    else {
                            // nothing to do here, simply throw the comment away
                        $result = new PipelineResult(false, HIGH_SPAM_PROBABILITY,
                                                     $locale->tr("error_comment_spam_throw_away" ));
                    }
                    $spam = true;
                }
                else
                {
                    $result = new PipelineResult();
                    $spam = false;
                }

                    // train the filter with the message, be it spam or not...
                BayesianFilterCore::train( $blogInfo->getId(), $commentTopic, $commentText, $userName, $userEmail,
                                           $userUrl, $spam );
                return $result;
            }
        }

        /**
        * @private
        */
        function getSpamProbability($blogId, $topic, $text, $userName, $userEmail, $userUrl)
        {
            lt_include( PLOG_CLASS_PATH."class/bayesian/bayesiantokenizer.class.php" );

            $tokenizer = new BayesianTokenizer();

            $tokensTopic = $tokenizer->addContextMark($tokenizer->tokenize($topic), TOKEN_TOPIC_MARK);
            $tokensText = $tokenizer->tokenize($text);

            $tokensUserName = $tokenizer->addContextMark($tokenizer->tokenize($userName), TOKEN_USER_NAME_MARK);
            $tokensUserEmail = $tokenizer->addContextMark($tokenizer->tokenize($userEmail), TOKEN_USER_EMAIL_MARK);
            $tokensUserUrl = $tokenizer->addContextMark($tokenizer->tokenize($userUrl), TOKEN_USER_URL_MARK);

            $tokens = array_merge($tokensTopic, $tokensText, $tokensUserName, $tokensUserEmail, $tokensUserUrl);
            $significantTokens = BayesianFilter::_getMostSignificantTokens($blogId, $tokens);

            return BayesianFilter::_getBayesProbability($significantTokens);
        }

        /**
        * @private
        */
        function _getMostSignificantTokens($blogId, $tokens)
        {
            lt_include( PLOG_CLASS_PATH."class/dao/bayesiantokens.class.php" );
            lt_include( PLOG_CLASS_PATH."class/dao/bayesianfilterinfos.class.php" );

            $config =& Config::getConfig();

            $bayesianFilterInfos = new BayesianFilterInfos();
            $bayesianFilterInfo  = $bayesianFilterInfos->getBlogBayesianFilterInfo($blogId);

            $totalSpam = $bayesianFilterInfo->getTotalSpam();
            $totalNonSpam = $bayesianFilterInfo->getTotalNonSpam();

            $bayesianTokens = new BayesianTokens();

            foreach ($tokens as $token)
            {
                $bayesianTokens->updateOccurrences($blogId, $token, 0, 0, $totalSpam, $totalNonSpam, false);
            }

            $tokens = $bayesianTokens->getBayesianTokensFromArray($blogId, $tokens);
            $tempArray = array();

            foreach ($tokens as $token)
            {
                if ($token->isSignificant() && $token->isValid())
                {
                    array_push($tempArray, abs($token->getProb() - 0.5));
                }
            }

            arsort($tempArray);
            $significantTokens = array();
            $count = 0;

            foreach ($tempArray as $key => $value)
            {
                array_push($significantTokens, $tokens[$key]);
                $count++;

                if ($count == $config->getValue("bayesian_filter_number_significant_tokens"))
                {
                    break;
                }
            }

            return $significantTokens;
        }

        /**
        * @private
        */
        function _getBayesProbability($significantTokens)
        {
            $productProb   = 1;
            $productNoProb = 1;

            foreach ($significantTokens as $token)
            {
                $productProb   *= $token->getProb();
                $productNoProb *= (1 - $token->getProb());
            }

            return $productProb / ($productProb + $productNoProb);
        }
    }
?>
