<?php

	lt_include( PLOG_CLASS_PATH."class/action/blogaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/usernamevalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/errorview.class.php" );

	define( "VIEW_TRACKBACKS_TEMPLATE", "posttrackbacks" );

    /**
     * \ingroup Action
     * @private
     *
     * It's almost the same as the ViewArticleAction...
     */
	class ViewArticleTrackbacksAction extends BlogAction 
	{
	
        var $_articleId;
		var $_articleName;
		var $_categoryId;
		var $_categoryName;
		var $_userId;
		var $_userName;
		var $_date;

		function ViewArticleTrackbacksAction( $actionInfo, $request )
        {
			$this->BlogAction( $actionInfo, $request );
			
			$this->registerFieldValidator( "articleId", new IntegerValidator(), true );
			$this->registerFieldValidator( "articleName", new StringValidator(), true );
			$this->registerFieldValidator( "postCategoryId", new IntegerValidator(), true );
			$this->registerFieldValidator( "postCategoryName", new StringValidator(), true );
			$this->registerFieldValidator( "userId", new IntegerValidator(), true );
			$this->registerFieldValidator( "userName", new UsernameValidator(), true );

			$this->setValidationErrorView( new ErrorView( $this->_blogInfo, "error_fetching_article" ));			
        }

        function validate()
        {
			if( !parent::validate())
				return false;
	
        	$this->_articleId    = $this->_request->getValue( "articleId" );
        	$this->_articleName  = $this->_request->getValue( "articleName" );
			$this->_categoryId   = $this->_request->getValue( "postCategoryId", -1 );
			$this->_categoryName = $this->_request->getValue( "postCategoryName" );
			$this->_userId       = $this->_request->getValue( "userId", -1 );
			$this->_userName     = $this->_request->getValue( "userName" );
			$this->_date         = $this->_request->getValue( "Date" );
        	$val = new IntegerValidator();
        	if( !$val->validate( $this->_date ) )
            	$this->_date = -1;
			
			// Caculate the correct article date period
			$adjustedDates = $this->_getCorrectedDatePeriod( $this->_date );
			$this->_date = $adjustedDates["adjustedDate"];
			$this->_maxDate = $adjustedDates["maxDate"];
			
            return true;
        }

        function perform()
        {	       
			lt_include( PLOG_CLASS_PATH."class/view/blogview.class.php" );	        
			
        	$this->_view = new BlogView( $this->_blogInfo,
					     VIEW_TRACKBACKS_TEMPLATE, 
					     SMARTY_VIEW_CACHE_CHECK,
					     Array( "articleId" => $this->_articleId,
						    "articleName" => $this->_articleName,
						    "categoryName" => $this->_categoryName,
						    "categoryId" => $this->_categoryId,
						    "userId" => $this->_userId,
						    "userName" => $this->_userName,
						    "date" => $this->_date ));

			 if( $this->_view->isCached()) {
                 $this->setCommonData();
                 return true;
			 }
			 
    		lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
    		lt_include( PLOG_CLASS_PATH."class/dao/trackbacks.class.php" );
			lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
			lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );			 
		
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
                    $this->_view = new ErrorView( $this->_blogInfo );
                    $this->_view->setValue( "message", "error_incorrect_user" );
                    $this->setCommonData();
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
                    $this->_view = new ErrorView( $this->_blogInfo );
                    $this->_view->setValue( "message", "error_fetching_category" );
                    $this->setCommonData();
					return false;				
				}
				// if there was a user, use his/her id
				$this->_categoryId = $category->getId();
			}		
		
            // fetch the article
            $articles = new Articles();
			if( $this->_articleId ) {
				$article  = $articles->getBlogArticle( $this->_articleId, 
				                                       $this->_blogInfo->getId(),
				                                       false, 
													   $this->_date, 
													   $this->_categoryId, 
													   $this->_userId,
				                                       POST_STATUS_PUBLISHED, 
													   $this->_maxDate );
			}
			else {
				$article = $articles->getBlogArticleByTitle( $this->_articleName, 
				                                             $this->_blogInfo->getId(),
				                                             false, 
															 $this->_date, 
															 $this->_categoryId, 
															 $this->_userId,
				                                             POST_STATUS_PUBLISHED, 
															 $this->_maxDate );
			}

            // if the article id doesn't exist, cancel the whole thing...
            if( $article == false ) {
            	$this->_view = new ErrorView( $this->_blogInfo );
                $this->_view->setValue( "message", "error_fetching_article" );
                $this->setCommonData();

                return false;
            }			
			$this->notifyEvent( EVENT_POST_LOADED, Array( "article" => &$article ));
			$this->notifyEvent( EVENT_TRACKBACKS_LOADED, Array( "article" => &$article ));
			
            // if everything's fine, we set up the article object for the view
            $this->_view->setValue( "post", $article );
            $this->_view->setValue( "trackbacks", $article->getTrackbacks( true ));
            $this->setCommonData();

            // and return everything normal
            return true;
        }
    }
?>