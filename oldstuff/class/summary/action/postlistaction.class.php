<?php

	lt_include( PLOG_CLASS_PATH."class/summary/action/summaryaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/summary/view/summarypostlistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );       

	/**
	 * shows a list with all the blogs, pager included
	 */
     class PostListAction extends SummaryAction
     {
        function PostListAction( $actionInfo, $request )
        {
            $this->SummaryAction( $actionInfo, $request );
        }

        /**
         * Loads the posts and shows them.
         */
        function perform()
        {
            // get the blogCategoryId from request
			$globalArticleCategoryId = $this->_request->getValue( "globalArticleCategoryId" );
			$val = new IntegerValidator();
			if( !$val->validate( $globalArticleCategoryId ))
				$globalArticleCategoryId = ALL_GLOBAL_ARTICLE_CATEGORIES;

            // this is a bit hackish but it works...
            $page = View::getCurrentPageFromRequest();
            
            // create the view
			$this->_view = new SummaryPostListView( Array( "summary" => "PostList", 
													"globalArticleCategoryId" => $globalArticleCategoryId,
			                                        "page" => $page, 
			                                        "locale" => $this->_locale->getLocaleCode() ));
			
			if( $this->_view->isCached()) {
				// nothing to do, the view is cached
				$this->setCommonData();
				return true;
			}
			
			$categories = new GlobalArticleCategories();
			if( $globalArticleCategoryId != ALL_GLOBAL_ARTICLE_CATEGORIES )
			{
				$currentGlobalArticleCategory = $categories->getGlobalArticleCategory( $globalArticleCategoryId );
				$this->_view->setValue( "currentGlobalArticleCategory", $currentGlobalArticleCategory);
			}			
			$this->_view->setValue( "globalArticleCategoryId", $globalArticleCategoryId );
			
			$this->setCommonData();
			
			return true;
        }
     }	 
?>