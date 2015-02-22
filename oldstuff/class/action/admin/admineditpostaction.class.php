<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminpostslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admineditpostview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows a form to update a post
     */
    class AdminEditPostAction extends AdminAction
	{

    	var $_postId;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminEditPostAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
        	
        	// data validation
        	$this->registerFieldValidator( "postId", new IntegerValidator());

        	$view = new AdminPostsListView( $this->_blogInfo );
        	$view->setErrorMessage( $this->_locale->tr("error_incorrect_article_id"));
        	$this->setValidationErrorView( $view );

			$this->requirePermission( "update_post" );
        }
        
        /**
         * Carries out the specified action
         */
        function perform()
        {
	        // fetch the post id that has already been validated
	     	$this->_postId = $this->_request->getValue( "postId" );   
	        
        	// fetch the post from the database
            $posts = new Articles();
            $post  = $posts->getBlogArticle( $this->_postId, $this->_blogInfo->getId(), false );

            // if the article does not exist, quit
            if( !$post ) {
            	$this->_view = new AdminPostsListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_fetching_article" ));
                $this->setCommonData();

                return false;
            }

	        // if the user does not have the 'view_all_user_articles' permission, then
            // we have to check whether the original poster of the article and the current
            // user match
            if( !$this->userHasPermission( "view_all_user_articles" )) {
	            if( $post->getUserId() != $this->_userInfo->getId()) {
	            	$this->_view = new AdminPostsListView( $this->_blogInfo );
	                $this->_view->setErrorMessage( $this->_locale->tr("error_can_only_view_own_articles" ));
	                $this->setCommonData();

	                return false;		            
	            }
            }

            
    		
            // throw the event
			$this->notifyEvent( EVENT_POST_LOADED, Array( "article" => &$post, "from" => "editPost" ));			

			// and create the view where we will edit the post
			$this->_view = new AdminEditPostView( $this->_blogInfo );			
			$this->_view->setArticle( $post );
			$this->_view->setUserInfo( $this->_userInfo );
            $this->setCommonData();

            return true;
        }
    }
?>