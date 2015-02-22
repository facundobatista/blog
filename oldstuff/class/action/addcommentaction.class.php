<?php

	lt_include( PLOG_CLASS_PATH."class/action/blogaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articlenotifications.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/usernamevalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/emailvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/httpurlvalidator.class.php" );    
    lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/filter/javascriptfilter.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/filter/urlconverter.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/filter/allowedhtmlfilter.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/filter/xhtmlizefilter.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/errorview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/redirectview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/bayesian/bayesianfiltercore.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/cachecontrol.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Takes care of validating the form to add new comments to an article
     */
    class AddCommentAction extends BlogAction 
	{

    	var $_articleId;
        var $_blogId;
        var $_userName;
        var $_userEmail;
        var $_userUrl;
        var $_commentText;
        var $_commentTopic;
        var $_parentId;

    	/**
         * Constructor
         */
        function AddCommentAction( $blogInfo, $request )
        {
        	$this->BlogAction( $blogInfo, $request );

			// input filters
			$f = new HtmlFilter();
			$this->_request->registerFilter( "userEmail", $f );
			$this->_request->registerFilter( "userName", $f );
			$this->_request->registerFilter( "commentTopic", $f );

            // userUrl is a regular HTML filter, in addition to
            // being forced to look like a URL
			$f = new HtmlFilter();
			$f->addFilter( new UrlConverter());
			$this->_request->registerFilter( "userUrl", $f );

			$f = new AllowedHtmlFilter();
			$f->addFilter( new JavascriptFilter());
			$f->addFilter( new XhtmlizeFilter());
			$this->_request->registerFilter( "commentText", $f );			

			// change the validation mode of the form
			$this->registerFieldValidator( "articleId", new IntegerValidator());
			$this->_form->setFieldErrorMessage( "articleId", $this->_locale->tr("error_incorrect_article_id" ));
			$this->registerFieldValidator( "blogId", new IntegerValidator());
			$this->_form->setFieldErrorMessage( "blogId", $this->_locale->tr("error_incorrect_blog_id" ));
			$this->registerFieldValidator( "parentId", new IntegerValidator(), true );
			$this->_form->setFieldErrorMessage( "parentId", $this->_locale->tr("error_incorrect_article_id" ));
			$this->registerFieldValidator( "userEmail", new EmailValidator(), true );
			$this->_form->setFieldErrorMessage( "userEmail", $this->_locale->tr("error_incorrect_email_address" ));
			$this->registerFieldValidator( "userName", new StringValidator());
			$this->_form->setFieldErrorMessage( "userName", $this->_locale->tr("error_comment_without_name" ));
			$this->registerFieldValidator( "commentText", new StringValidator( true ));
			$this->_form->setFieldErrorMessage( "commentText", $this->_locale->tr("error_comment_without_text"));
			$this->registerFieldValidator( "userUrl", new HttpUrlValidator(), true );
			$this->_form->setFieldErrorMessage( "userUrl", $this->_locale->tr("invalid_url" ));
			$view = new ErrorView( $this->_blogInfo );
			$view->setErrorMessage( "There has been an error validating the data!" );
			$this->setValidationErrorView( $view );

            $this->_fetchFields();
        }

        function _fetchFields()
        {
            lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );

			$f = new HtmlFilter( true );

            $this->_articleId = $this->_request->getValue( "articleId" );
            $this->_blogId    = $this->_request->getValue( "blogId" );
            $this->_parentId  = $this->_request->getValue( "parentId" );
            if( $this->_parentId == null || $this->_parentId == "" )
                $this->_parentId = 0;
            $this->_userEmail = $this->_request->getValue( "userEmail" );
            $this->_userUrl   = $this->_request->getValue( "userUrl" );
            $this->_userName  = $this->_request->getValue( "userName" );
            $this->_commentText = trim($this->_request->getValue( "commentText" ));
            $this->_commentTopic = $this->_request->getValue( "commentTopic" );

            // now, if the option is set, we 'beautify' the text typed by users
            if( $this->_config->getValue( "beautify_comments_text" )) {
            	$tf = new TextFilter();	
            	$this->_commentText = $tf->autop($this->_commentText);
            }
        }

        /**
         * Since this function contains this method, the controller will automatically
         * call it before calling perform()
         *
         * @return True if all fields ok or false otherwise.
         */
        function validate()
        {
            // check if comments are enabled
            $blogSettings = $this->_blogInfo->getSettings();
            if( !$blogSettings->getValue( "comments_enabled" )) {
            	$this->_view = new ErrorView( $this->_blogInfo, "error_comments_not_enabled" );
                $this->setCommonData();

                return false;
            }
			
			return( parent::validate());
        }
		
		/**
		 * prepare a nicer error message. This method is only going to be executed whenver a validation
		 * error happens (see Action::validate())
		 *
		 * @see Action::validate()
		 */
		function validationErrorProcessing()
		{
            $this->_view = new ErrorView( $this->_blogInfo);

			// collect all the errors from all the fields and for those that failed validation,
			// put them in a nicer string.
			$results = $this->_form->getFormValidationResults();
			$errorMessage = "";
			foreach( $results as $field => $result ) {
				if( !$result ) {
					$errorMessage .= $this->_form->getFieldErrorMessage( $field )."<br/><br/>";
				}
			}
			
			$this->_view->setErrorMessage( $errorMessage );
			$this->setCommonData();
			
			return true;
		}

        /**
         * Carries out the action
         */
        function perform()
        {
            lt_include( PLOG_CLASS_PATH."class/dao/articlecomments.class.php" );
            lt_include( PLOG_CLASS_PATH."class/dao/usercomment.class.php" );
            lt_include( PLOG_CLASS_PATH."class/net/client.class.php" );

        	// need to check the ip of the client
            $clientIp = Client::getIp();

            // fetch the same article again so that we can have all the comments from
            // the database, plus this last one
            $articles = new Articles();
            $article  = $articles->getBlogArticle( $this->_articleId, $this->_blogInfo->getId());
            if(!$article) {
                 $this->_view = new ErrorView( $this->_blogInfo );
                 $this->_view->setValue( "message", $this->_locale->tr("error_incorrect_article_id"));
                 $this->setCommonData();
                 return false;
            }
			
			$this->notifyEvent( EVENT_POST_LOADED, Array( "article" => &$article ));

            // check if the user wanted to receive comments for this article
            // or not...
            if( $article->getCommentsEnabled() == false ) {
                 $this->_view = new ErrorView( $this->_blogInfo );
                 $this->_view->setValue( "message", "Comments have been disabled for this article." );
                 $this->setCommonData();
                 return false;
            }

        	// we have already checked all the data, so we are sure that everything's in place
            $comments = new ArticleComments();
			
			$comment = new UserComment( $this->_articleId, 
			                            $this->_blogInfo->getId(),
			                            $this->_parentId, 
			                            $this->_commentTopic, 
			                            $this->_commentText,
			                            null, 
			                            $this->_userName, 
			                            $this->_userEmail, 
			                            $this->_userUrl,
									    $clientIp );			
									
			// check if the comment was being posted by an authenticated user...
			if( $this->_userInfo ) {
				// ...and if so, save the user data in the UserComment object
				$comment->setUser( $this->_userInfo );
			} else {
				$comment->setUserId( 0 );
			}

                // check if there is already a comment with the same text, topic and made from the same
                // IP already in the database because if so, then we will not add the comment that
                // the user is trying to add (a reload or submit button mistake, perhaps?)
            if( $comments->getIdentical( $comment )){
                $this->_view = new ErrorView( $this->_blogInfo );
                $this->_view->setValue( "message", "error_adding_comment" );
                $this->setCommonData();
                return false;
            }

                // fire an event
            $this->notifyEvent( EVENT_PRE_COMMENT_ADD, Array( "comment" => &$comment ));
            
            if( !$comments->addComment( $comment )) {
                    // show an error message if problems
                $this->_view = new ErrorView( $this->_blogInfo );
                $this->_view->setValue( "message", "error_adding_comment" );
                $this->setCommonData();
                return false;
            }
            
            // finally, check if there was any user who wanted to be notified of new comments
            // to this post...
            $notifier = new ArticleNotifications();
            $notifier->notifyUsers( $article->getId(), $this->_blogInfo);
			
			// fire the post event...
			$this->notifyEvent( EVENT_POST_COMMENT_ADD, Array( "comment" => &$comment ));

			//
			// clear caches. This should be done in a more granular way, because right now
			// we're either removing *all* of them or none. I guess we should only remove the 
			// cache whose identifier corresponds with the blog and article that we just removed, 
			// but let's leave it as it is for the time being...
			//
			CacheControl::resetBlogCache( $this->_blogInfo->getId());
			
			// clean up the request, there's a parameter called 'userName' also used by
			// ViewArticleAction but that doesn't have the same meaning so we better remove it
			// before it's too late! We also need to add a new request commentUserName to replace
			// original userName, in case developer need it in filter or event plugin.
			$request = HttpVars::getRequest();
			$request["commentUserName"] = $this->_userName;
			$request["userName"] = "";
			HttpVars::setRequest( $request ); 					
			
			// calculate the final URL
			$rg = $this->_blogInfo->getBlogRequestGenerator();
			$rg->setXHTML( false );
			$postPermalink = $rg->postPermalink( $article );
						
			// and pass it to the redirect view to perform the redirection
			$this->_view = new RedirectView( $postPermalink );
			
			return( true );
        }
    }
?>
