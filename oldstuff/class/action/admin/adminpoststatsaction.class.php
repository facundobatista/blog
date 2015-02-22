<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminpostslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminarticlereferrersview.class.php" );	
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows a page with the statistics about a form
     */
    class AdminPostStatsAction extends AdminAction 
	{
    	var $_postId;
		var $_page;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminPostStatsAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// data validatdion
			$this->registerFieldValidator( "postId", new IntegerValidator());
			$view = new AdminPostsListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_incorrect_article_id"));
			$this->setValidationErrorView( $view );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
        	$this->_postId = $this->_request->getValue( "postId" );	
        	$this->_page = View::getCurrentPageFromRequest();
		
            // fetch the post itself
            $posts = new Articles();
            $post  = $posts->getBlogArticle( $this->_postId, $this->_blogInfo->getId());
            if( !$post ) {
            	$this->_view = new AdminPostsListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_fetching_article"));
                $this->setCommonData();
                return false;
            }

        	// generate the view
			$this->_view = new AdminArticleReferrersView( $this->_blogInfo, Array( "page" => $this->_page, "article" => $post ));
            $this->setCommonData();
            return true;
        }
    }
?>