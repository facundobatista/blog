<?php

	lt_include( PLOG_CLASS_PATH.'class/action/admin/adminpostmanagementcommonaction.class.php' );
    lt_include( PLOG_CLASS_PATH.'class/view/viewarticleview.class.php' );
    lt_include( PLOG_CLASS_PATH.'class/view/admin/adminerrorview.class.php' );
    lt_include( PLOG_CLASS_PATH.'class/dao/article.class.php' );

    /**
     * \ingroup Action
     * @private
     *
     * Action that will allow us to preview a post, using the blog's very own
     * template so that it looks and feels exactly like in the real blog, where it will
     * be finally shown when we save it.
     *
     * It works by creating a dummy article that does not exist in the database and then
     * using one of the blog views to show it.
     */
    class AdminPreviewPostAction extends AdminPostManagementCommonAction 
	{

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminPreviewPostAction( $actionInfo, $request )
        {
        	$this->AdminPostManagementCommonAction( $actionInfo, $request );

                // override previous validator, allow it to be blank
        	$this->registerFieldValidator( "postTopic", new StringValidator(), true );

                // TODO: not the best error message, but at least it displays something
			$view = new AdminErrorView( $this->_blogInfo, Array( 'random' => md5(time())));
        	$view->setMessage( $this->_locale->tr("error_adding_post"));
        	$this->setValidationErrorView( $view );
        }
        
        /**
         * loads a bunch of categories given their ids
         *
         * @param categoryIds
         */
        function _loadArticleCategories( $categoryIds )
        {
	    	$articleCategories = new ArticleCategories();
	    	$categories = Array();
	    	foreach( $categoryIds as $categoryId ) {
		    	$category = $articleCategories->getCategory( $categoryId, $this->_blogInfo->getId());
		    	if( $category )
		    		array_push( $categories, $category );
	    	}
	    	
	    	return $categories;
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
	        //print_r($_REQUEST);
	        
	        // fetch all the information that we need for the dummy Article object
			$this->_fetchCommonData();
                // Set Topic if not set
			if(!$this->_postTopic)
                $this->_postTopic = $this->_locale->tr("preview");

			// and now, create a harmless Article object with it
			$postText = Textfilter::xhtmlize($this->_postText);
			// create the main object
			$article  = new Article( $this->_postTopic, 
			                         $postText, 
			                         $this->_postCategories,
									 $this->_userInfo->getId(), 
									 $this->_blogInfo->getId(), 
									 POST_STATUS_PUBLISHED, 
									 0, 
									 Array(), 
									 $this->_postSlug );
			// and a few more properties that we need to know about
			$this->_fetchPostDateInformation();
			$article->setDateObject( $this->_postTimestamp );
            $blogSettings = $this->_blogInfo->getSettings();
            $article->setTimeOffset($blogSettings->getValue("time_offset"));
			// we will not allow comments because it wouldn't work!
			$article->setCommentsEnabled( false );
			$article->setFields( $this->_getArticleCustomFields());
			// the next two fields are also required in order to show an article
			$article->setUserInfo( $this->_userInfo );
			$article->setBlogInfo( $this->_blogInfo );
			$article->setCategories( $this->_loadArticleCategories( $this->_postCategories ));
			// and now trick the ViewArticleView class into thinking that we're showing
			// a real article just fetched from the database (even though it makes no difference
			// to the class itself whence the article came from :)
			
			// throw the EVENT_ARTICLE_PREVIEW event in case a plugin wants to do something with it
			$this->notifyEvent( EVENT_POST_PREVIEW, Array( "article" => &$article ));
			
			// the 'random' parameter in the array is to provide the view with a random view id
			// every time that we run the preview, otherwise when caching is enabled we would always be
			// getting the same page!!
			$this->_view = new ViewArticleView( $this->_blogInfo, Array( 'random' => md5(time())));
			$this->_view->setArticle( $article );
			//$this->setCommonData();
			
			return true;
        }
    }
?>
