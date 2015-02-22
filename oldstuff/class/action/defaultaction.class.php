<?php

	lt_include( PLOG_CLASS_PATH."class/action/blogaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/plugin/pluginmanager.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/usernamevalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );	
    lt_include( PLOG_CLASS_PATH."class/view/errorview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * This class represents the defaut view in our application
     */
	class DefaultAction extends BlogAction 
	{

		var $_config;
        var $_date;
        var $_categoryId;
		var $_categoryName;
		var $_userId;
		var $_userName;
		var $_postAmount;
        var $_searchTerms;

		function DefaultAction( $actionInfo, $request )
        {
			$this->BlogAction( $actionInfo, $request );
			
			$this->registerFieldValidator( "searchTerms", new StringValidator( true ), true );
			$this->registerFieldValidator( "postCategoryId", new IntegerValidator(), true );
			$this->registerFieldValidator( "postCategoryName", new StringValidator( ), true );
			$this->registerFieldValidator( "userId", new IntegerValidator(), true );
			$this->registerFieldValidator( "userName", new UsernameValidator(), true );
			
			$this->setValidationErrorView( new ErrorView( $this->_blogInfo, "error_fetching_articles" ));
        }

        function validate()
        {
			if( !parent::validate())
				return false;
	
            // value of the Date parameter from the request
            $this->_date = $this->_request->getValue( "Date", -1 );
        	$val = new IntegerValidator();
        	if( !$val->validate( $this->_date ) )
            	$this->_date = -1;
			$this->_categoryName = $this->_request->getValue( 'postCategoryName' );
            $this->_categoryId = $this->_request->getValue( 'postCategoryId' );
            if( $this->_categoryId == '' )
            	if( $this->_date == -1 )
                	$this->_categoryId = 0;
                else
                	$this->_categoryId = -1;
					
			$this->_userId = $this->_request->getValue( 'userId', -1 );
			$this->_userName = $this->_request->getValue( 'userName', '' );
            $this->_searchTerms = $this->_request->getFilteredValue( "searchTerms", new HtmlFilter());

            return true;
        }

        /**
         * Executes the action
         */
        function perform()
        {
            lt_include( PLOG_CLASS_PATH."class/view/defaultview.class.php" );
            
        	// first of all, we have to determine which blog we would like to see
			$blogId = $this->_blogInfo->getId();

            // fetch the settings for that blog
            $blogSettings = $this->_blogInfo->getSettings();

            // prepare the view
        	$this->_view = new DefaultView( $this->_blogInfo,
			                                Array( "categoryId" => $this->_categoryId,
							                        "blogId" => $this->_blogInfo->getId(),
							                        "categoryName" => $this->_categoryName,
							                        "date" => $this->_date,
							                        "userName" => $this->_userName,
							                        "userId" => $this->_userId,
													"searchTerms" => $this->_searchTerms,
                                                    "page" => $this->_page,
                                                    "url" => md5($_SERVER["REQUEST_URI"])));
														  
			// check if everything's cached because if it is, then we don't have to
			// do any work... it's already been done before and we should "safely" assume
			// that there hasn't been any change so far
			if( $this->_view->isCached()) {
				$this->setCommonData();
				return true;
			}

            lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );
            lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );
            lt_include( PLOG_CLASS_PATH."class/data/pager/pager.class.php" );
			lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
			lt_include( PLOG_CLASS_PATH."class/config/siteconfig.class.php" );			

            // if we got a category name instead of a category id, then we
            // should first look up this category in the database and see if
            // it exists
            $categories = new ArticleCategories();
            if( $this->_categoryName ) {
                $category = $categories->getCategoryByName( $this->_categoryName, $this->_blogInfo->getId());
                if( !$category ) {
                    $this->_view = new ErrorView( $this->_blogInfo );
                    $this->_view->setValue( 'message', "error_incorrect_category_id" );
                    $this->setCommonData();
                    return false;
                }
                
                // if everything went fine...
                $this->_categoryId = $category->getId();
            }
			else {
				// we don't do anything if the cateogry id is '0' or '-1'
				if( $this->_categoryId > 0 ) {
					$category = $categories->getCategory( $this->_categoryId, $this->_blogInfo->getId());
					if( !$category ) {
						$this->_view = new ErrorView( $this->_blogInfo );
						$this->_view->setValue( 'message', "error_incorrect_category_id" );
						$this->setCommonData();
						return false;
					}
				}
				else {
					// if only to avoid a warning...
					$category = null;
				}
			}
			
			// export the category object in case it is needed
            if( isset($category) )
                $this->_view->setValue( "category", $category );			
			
			lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
            $users = new Users();

            // if we got a user user id, then we should first look up this id
            // user in the database and see if it exists
            if( $this->_userId > 0) {
                $user = $users->getUserInfoFromId( $this->_userId );
                if( !$user ) {
                    $this->_view = new ErrorView( $this->_blogInfo );
                    $this->_view->setValue( 'message', 'error_incorrect_user_id' );
                    $this->setCommonData();
                    return false;
                }
            } 
            else if( $this->_userName ) {
                    // if we got a user name instead of a user id, then we
                    // should first look up this user in the database and see if
                    // it exists
                $user = $users->getUserInfoFromUsername( $this->_userName );
                if( !$user ) {
                    $this->_view = new ErrorView( $this->_blogInfo );
                    $this->_view->setValue( 'message', 'error_incorrect_user_username' );
                    $this->setCommonData();
                    return false;
                }
                
                // if everything went fine...
                $this->_userId = $user->getId();
            }
			else {
				// if only to avoid a warning...
				$user = null;
			}	

			if($blogSettings->getValue( 'show_future_posts_in_calendar')){
				// if posts in the future are to be shown, we shouldn't set a maximum date
				$todayTimestamp = 0;
			}
			else {			
            	$t = new Timestamp();
            	$todayTimestamp = $t->getTimestamp();
       		}
			       		
       		// get the articles...
			$hardLimit = SiteConfig::getHardShowPostsMax();
			$this->_postAmount = $blogSettings->getValue( "show_posts_max" );			
			if( $this->_postAmount > $hardLimit ) $this->_postAmount = $hardLimit;

			$articles = new Articles();
			$blogArticles = $articles->getBlogArticles( 
			                        $blogId, 
			                        $this->_date,
							        $this->_postAmount, 
							        $this->_categoryId,
							        POST_STATUS_PUBLISHED, 
							        $this->_userId, 
							        $todayTimestamp,
							        $this->_searchTerms,
							        $this->_page );				        
			// and the total number based on the conditions given
			$numArticles = $articles->getNumBlogArticles( 
			                        $blogId,
			                        $this->_date,
							        $this->_categoryId,
							        POST_STATUS_PUBLISHED,
							        $this->_userId,
							        $todayTimestamp,
									$this->_searchTerms );

            // if we couldn't fetch the articles, send an error and quit
            if( count($blogArticles) == 0 ) {
            	$this->_view = new ErrorView( $this->_blogInfo );
                $this->_view->setValue( 'message', 'error_fetching_articles' );
            }
            else {
				// ---
				// before finishing, let's see if there's any plugin that would like to do 
				// anything with the post that we just loaded
				// ---
				$pm =& PluginManager::getPluginManager();
				$pm->setBlogInfo( $this->_blogInfo );
				$pm->setUserInfo( $this->_userInfo );
				$result = $pm->notifyEvent( EVENT_POSTS_LOADED, Array( 'articles' => &$blogArticles ));
				$articles = Array();
				foreach( $blogArticles as $article ) {
					$postText = $article->getIntroText();
					$postExtendedText = $article->getExtendedText();
					$pm->notifyEvent( EVENT_TEXT_FILTER, Array( "text" => &$postText ));
					$pm->notifyEvent( EVENT_TEXT_FILTER, Array( "text" => &$postExtendedText ));
					$article->setIntroText( $postText );
					$article->setExtendedText( $postExtendedText );
					array_push( $articles, $article );
				}
				
    	        $this->_view->setValue( 'posts', $articles );
    	        
    	        // build the pager and pass it to the view
    	        $url = $this->_blogInfo->getBlogRequestGenerator();
				$basePageUrl = $url->getCurrentUrl( $category,
				                                    $user,
				                                    $this->_date );
				
    	        $pager = new Pager( $basePageUrl,            // url to the next page
    	                            $this->_page,            // current page
    	                            $numArticles,            // total number of articles
    	                            $this->_postAmount );    	        
    	        $this->_view->setValue( 'pager', $pager );

                    // pass the date onto the template, in case users would like to show it
                if( $this->_date > - 1 ) {
                    $date = str_pad( $this->_date, 14, "0" );
                    $this->_view->setValue( "date", new Timestamp( $date ));
                }
            }           

            $this->setCommonData();
            // save the information about the session for later
            $this->saveSession();

            return true;
        }
    }
?>