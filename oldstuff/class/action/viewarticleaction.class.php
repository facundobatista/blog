<?php

	lt_include( PLOG_CLASS_PATH."class/action/blogaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/data/validator/usernamevalidator.class.php" );	
    lt_include( PLOG_CLASS_PATH."class/view/errorview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/config/siteconfig.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * This class represents the defaut view in our application
     */
	class ViewArticleAction extends BlogAction
    {

    	var $_config;
        var $_articleId;
		var $_articleName;
		var $_date;
		var $_maxDate;
		var $_userId;
		var $_userName;
		var $_categoryId;
		var $_categoryName;
		var $_article;

		function ViewArticleAction( $actionInfo, $request )
        {
			$this->BlogAction( $actionInfo, $request );
			
			$this->registerFieldValidator( "articleId", new IntegerValidator(), true );
			$this->registerFieldValidator( "articleName", new StringValidator(), true );
			$this->registerFieldValidator( "postCategoryId", new IntegerValidator(), true );
			$this->registerFieldValidator( "postCategoryName", new StringValidator(), true );
			$this->registerFieldValidator( "userId", new IntegerValidator(), true );
			$this->registerFieldValidator( "userName", new UsernameValidator(), true );
            $this->registerFieldValidator( "Date", new IntegerValidator(), true );
            
			$this->setValidationErrorView( new ErrorView( $this->_blogInfo, "error_fetching_article" ));
        }
        
        // checks that the articleId is valid
        function validate()
        {
			if( !parent::validate())
				return( false );
	
        	$this->_articleId = $this->_request->getValue( "articleId" );
			$this->_articleName = $this->_request->getValue( "articleName" );
			// find some other additional parameters and use some 'null' values
			// in case they're empty
			$this->_categoryId = $this->_request->getValue( "postCategoryId", -1 );
			$this->_categoryName = $this->_request->getValue( "postCategoryName" );
			$this->_userId = $this->_request->getValue( "userId", -1 );
			$this->_userName = $this->_request->getValue( "userName" );
			$this->_date = $this->_request->getValue( "Date", -1 );
        	$val = new IntegerValidator();
        	if( !$val->validate( $this->_date ) )
            	$this->_date = -1;
			$this->_isCommentAdded = ($this->_request->getValue( "op" ) == "AddComment" );
			
			// Calculate the correct article date period
            $adjustedDates = $this->_getCorrectedDatePeriod( $this->_date );
            $this->_date = $adjustedDates["adjustedDate"];
            $this->_maxDate = $adjustedDates["maxDate"];
//			if( $this->_maxDate == -1 ) $this->_maxDate = 0;
            
            return true;
        }
		
		function _setErrorView()
		{
			$this->_view = new ErrorView( $this->_blogInfo );
			$this->_view->setValue( "message", "error_fetching_article" );
			if( $this->_config->getValue( 'request_format_mode' ) != NORMAL_REQUEST_MODE ) 
			{
				$this->_view->_headers[0] = "HTTP/1.0 404 Not Found";
			}
			$this->setCommonData();
		}
		
		/**
		 * @private
		 * updates the article referrers given an article
		 */
		function _updateArticleReferrers($article){
			$this->_updateArticleReferrersById($article->getId());
		}
		/**
		 * @private
		 * updates the article referrers given an id
		 */
        function _updateArticleReferrersById($articleId)
        {
    		lt_include( PLOG_CLASS_PATH."class/dao/referers.class.php" );	        
	        
            if ( array_key_exists( 'HTTP_REFERER', $_SERVER ) )
            {
                $referrers = new Referers();
                $referrers->addReferer( $_SERVER['HTTP_REFERER'], 
                                        $articleId, $this->_blogInfo->getId());
            }
        }
		/**
		 * @private
		 * updates the article referrers, given a slug
		 */
		function _updateArticleReferrersByTitle($slug)
		{
    		lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );						
			$articles = new Articles();
			$article = $articles->getBlogArticleByTitle( $slug, $this->_blogInfo->getId());
			$article ? $id = $article->getId() : $id = 0;
			// if the article isn't found, we will save a referrer to
		    // the main page, since $id will be 0.
			$this->_updateArticleReferrersById( $id );
		}
		
		/**
		 * @private
		 * updates the number of times that an article has been read in the db
		 * 
		 * @param articleId
		 * @return always true
		 */
		function updateNumArticleReads( $articleId )
		{
			$articles = new Articles();
			$articles->updateArticleNumReads( $articleId );
			
			return( true );
		}
		
        function perform()
        {		
	        lt_include( PLOG_CLASS_PATH."class/view/viewarticleview.class.php" );
    		lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );	       		     

        	$this->_view = new ViewArticleView( $this->_blogInfo, 
                                                   Array( "articleId" => $this->_articleId,
                                                          "articleName" => $this->_articleName,
                                                          "categoryId" => $this->_categoryId,
                                                          "categoryName" => $this->_categoryName,
                                                          "userId" => $this->_userId,
                                                          "userName" => $this->_userName,
                                                          "date" => $this->_date,
                                                          "page" => $this->_page ));

			if( $this->_view->isCached()) {
				if( $this->_config->getValue( 'update_cached_article_reads', false )) {
					$articles = new Articles();
					if( $this->_articleId ){
						$articles->updateArticleNumReads( $this->_articleId );
                        if( $this->_config->getValue( "referer_tracker_enabled" )) {
                            $this->_updateArticleReferrersById( $this->_articleId );
                        }
                    }
 					else if($this->_articleName){
						$articles->updateArticleNumReadsByName( $this->_articleName );
                        if( $this->_config->getValue( "referer_tracker_enabled" )) {
                            $this->_updateArticleReferrersByTitle($this->_articleName );
                        }
                    }
                    else{
                            // print "Can't update referrers without an id or a name...";
                    }
				}
				
				$this->setCommonData();
				return true;
			}			
			

			lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );
    		lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
    		lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );      
			lt_include( PLOG_CLASS_PATH.'class/data/pager/pager.class.php' );
			lt_include( PLOG_CLASS_PATH.'class/dao/articlecomments.class.php' );
														  
			// ---
			// if we got a category name or a user name instead of a category
			// id and a user id, then we have to look up first those
			// and then proceed
			// ---
			// users...
			if( $this->_userName ) {
				$users = new Users();
				$user = $users->getUserInfoFromUsername( $this->_userName );
				if( !$user ) {
					$this->_setErrorView();
					return false;				
				}
				// if there was a user, use his/her id
				$this->_userId = $user->getId();
			}
			
			// ...and categories...
			if( $this->_categoryName ) {
				$categories = new ArticleCategories();
				$category = $categories->getCategoryByName( $this->_categoryName, $this->_blogInfo->getId());
				if( !$category ) {
					$this->_setErrorView();
					return false;
				}
				// if there was a user, use his/her id
				$this->_categoryId = $category->getId();
			}			
		
            // fetch the article
            // the article identifier can be either its internal id number or its mangled topic
            if( $this->_articleId ) { 
                $articles = new Articles();
                $article  = $articles->getBlogArticle( $this->_articleId, 
                                                       $this->_blogInfo->getId(), 
                                                       false, 
                                                       $this->_date, 
                                                       $this->_categoryId, 
                                                       $this->_userId,
                                                       POST_STATUS_PUBLISHED,
                                                       $this->_maxDate);
            } else if($this->_articleName){
                $articles = new Articles();
                $article  = $articles->getBlogArticleByTitle( $this->_articleName, 
                                                              $this->_blogInfo->getId(), 
                                                              false,
                                                              $this->_date, 
                                                              $this->_categoryId, 
                                                              $this->_userId,
                                                              POST_STATUS_PUBLISHED,
                                                              $this->_maxDate);
            }
            else{
                    // print "No name or ID - fetch by date/category/etc?";
                $article = null;
            }

            // if the article id doesn't exist, cancel the whole thing...
            if( !$article ) {
                $this->_setErrorView();
                return false;
            }

			$this->_article = $article;
			
            // check if we have to update how many times an article has been read
            if( $this->_config->getValue( "update_article_reads" )) {
				$this->updateNumArticleReads( $article->getId());
            }
			
			// update the referrers, if needed
            if( $this->_config->getValue( "referer_tracker_enabled" )) {
                $this->_updateArticleReferrers( $article );
            }
						
            // if everything's fine, we set up the article object for the view
            $this->_view->setArticle( $article );
			// and the comments
			$blogSettings = $this->_blogInfo->getSettings();			
			$hardLimit = SiteConfig::getHardShowCommentsMax();
			$commentsPerPage = $blogSettings->getValue( "show_comments_max", $this->_config->getValue( "show_comments_max" ));	
			if( $commentsPerPage > $hardLimit ) $commentsPerPage = $hardLimit;						

			$comments = new ArticleComments();
            $order = $blogSettings->getValue( "comments_order", COMMENT_ORDER_NEWEST_FIRST );
			$postComments = $comments->getPostComments( $article->getId(), 
			                                            $order,
			                                            COMMENT_STATUS_NONSPAM, 
			                                            $this->_page, 
			                                            $commentsPerPage );
			$this->_view->setValue( 'comments', $postComments );
	        // build the pager and pass it to the view
	        $url = $this->_blogInfo->getBlogRequestGenerator();	
	        $pager = new Pager( $url->postPermalink( $article ).$url->getPageSuffix(),
	                            $this->_page,  // current page
	                            $article->getNumComments(),  // total number of articles
	                            $commentsPerPage );  // number of comments per page
	        $this->_view->setValue( 'pager', $pager );
	
            $this->setCommonData();
			
            // and return everything normal
            return true;
        }
    }
?>
