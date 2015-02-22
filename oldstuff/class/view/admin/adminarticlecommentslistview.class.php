<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articlecomments.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articlecommentstatus.class.php" );	
    lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/data/pager/pager.class.php" );

    /**
     * \ingroup View
     * @private
     *	
	 * shows a list of the comments 
	 */
	class AdminArticleCommentsListView extends AdminTemplatedView
	{
    	var $_article;
		var $_commentStatus;	
		var $_page;
	
		function AdminArticleCommentsListView( $blogInfo, $params = Array(), $type = COMMENT_TYPE_COMMENT )
		{
			if( $type == COMMENT_TYPE_COMMENT )
				$this->AdminTemplatedView( $blogInfo, "editcomments" );
			else
				$this->AdminTemplatedView( $blogInfo, "edittrackbacks" );
			
			$blogSettings = $blogInfo->getSettings();
			$this->_locale =& Locales::getLocale( $blogSettings->getValue( "locale" ), "en_UK" );	
			
			$this->_setParameters( $params );
			
			$this->_type = $type;

			$this->_page = $this->getCurrentPageFromRequest();
		}
		
		/**
		 * @private
		 */
		function _setParameters( $params )
		{
			// fetch the article id
			$this->_article = null;
			if( isset( $params["article"] ))
				$this->_article = $params["article"];
						
			// load the status
			if( isset( $params["showStatus"] ))
				$this->_commentStatus = $params["showStatus"];
				
			if( !ArticleCommentStatus::isValidStatus( $this->_commentStatus )) 
				$this->_commentStatus = COMMENT_STATUS_ALL;	
				
			// laod the search terms
			$this->_searchTerms = "";
			if( isset( $params["searchTerms"] ))			
				$this->_searchTerms = $params["searchTerms"];
		}
		
		/**
		 * show the contents of the view
		 */
		function render()
		{
			// load the comments and throw the correct event
			$comments = new CommentsCommon();
			if( $this->_article ) {
				// load only the comments of the given post
				$postComments = $comments->getPostComments( $this->_article->getId(),
															COMMENT_ORDER_NEWEST_FIRST,
															$this->_commentStatus, 
															$this->_type,
															$this->_page, 
															DEFAULT_ITEMS_PER_PAGE );
				// number of comments
				$numPostComments = $comments->getNumPostComments( $this->_article->getId(), 
				                                                  $this->_commentStatus,
																  $this->_type );
				// id of the article, for the pager...
				$articleId = $this->_article->getId();
			}
			else {
				// load all comments given the current status
				$postComments = $comments->getBlogComments( $this->_blogInfo->getId(),
				                                            COMMENT_ORDER_NEWEST_FIRST,
				                                            $this->_commentStatus,
															$this->_type,
															$this->_searchTerms,
														    $this->_page,
														    DEFAULT_ITEMS_PER_PAGE );
				// number of comments
				$numPostComments = $comments->getNumBlogComments( $this->_blogInfo->getId(),
				                                                  $this->_commentStatus,
																  $this->_type,
																  $this->_searchTerms );
				// no article id...
				$articleId = 0;
			}
			$this->notifyEvent( EVENT_COMMENTS_LOADED, Array( "comments", &$postComments ));
			
			if( $this->_type == COMMENT_TYPE_COMMENT )
				$pagerUrl = "?op=editComments";
			else
				$pagerUrl = "?op=editTrackbacks";

			if( $this->_commentStatus > -1 )
				$pagerUrl .= "&amp;articleId={$articleId}&amp;showStatus=".$this->_commentStatus."&amp;searchTerms=".$this->_searchTerms."&amp;page=";
			else
				$pagerUrl .= "&amp;articleId={$articleId}&amp;searchTerms=".$this->_searchTerms."&amp;page=";
				
			// calculate the pager url
			$pager = new Pager( $pagerUrl,
					    $this->_page,
					    $numPostComments,
					    DEFAULT_ITEMS_PER_PAGE );					
														
			// get a list with all the different comment status
			$statusList = ArticleCommentStatus::getStatusList( true );
			$statusListWithoutAll = ArticleCommentStatus::getStatusList( false );
			
			// and pass all the information to the templates
			$this->setValue( "comments", $postComments);
			$this->setValue( "commentstatus", $statusList );
			$this->setValue( "commentstatusWithoutAll", $statusListWithoutAll );
			$this->setValue( "currentstatus", $this->_commentStatus );
			$this->setValue( "searchTerms", $this->_searchTerms );

			// pass the pager to the view
			$this->setValue( "pager", $pager );+
		
			// pass the common data to the templates
			$this->setValue( "post", $this->_article );
						
			parent::render();
		}
	}
?>