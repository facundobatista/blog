<?php

	lt_include( PLOG_CLASS_PATH."class/summary/action/summaryaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/summary/view/summarybloglistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );       

	/**
	 * shows a list with all the blogs, pager included
	 */
     class BlogListAction extends SummaryAction
     {
        function BlogListAction( $actionInfo, $request )
        {
            $this->SummaryAction( $actionInfo, $request );
        }

        /**
         * Loads the posts and shows them.
         */
        function perform()
        {
            // get the blogCategoryId from request
			$blogCategoryId = $this->_request->getValue( "blogCategoryId" );
			$val = new IntegerValidator();
			if( !$val->validate( $blogCategoryId ))
				$blogCategoryId = ALL_BLOG_CATEGORIES;

            // this is a bit hackish but it works...
            $page = View::getCurrentPageFromRequest();
            
            // create the view
			$this->_view = new SummaryBlogListView( Array( "summary" => "BlogList", 
													"blogCategoryId" => $blogCategoryId,
			                                        "page" => $page, 
			                                        "locale" => $this->_locale->getLocaleCode() ));
			
			if( $this->_view->isCached()) {
				// nothing to do, the view is cached
				$this->setCommonData();				
				return true;
			}
			
			$categories = new BlogCategories();
			if( $blogCategoryId != ALL_BLOG_CATEGORIES )
			{
				$currentBlogCategory = $categories->getBlogCategory( $blogCategoryId );
				$this->_view->setValue( "currentBlogCategory", $currentBlogCategory);
			}			

			$this->_view->setValue( "blogCategoryId", $blogCategoryId );			
			$this->setCommonData();
			
			return true;
        }
     }	 
?>