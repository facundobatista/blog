<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/blogcategories.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/pager/pager.class.php" );
	
    /**
     * \ingroup View
     * @private
     *
     * Action that shows a form to add a link for the blogroll feature
     */
    class AdminBlogCategoriesListView extends AdminTemplatedView 
	{
		var $_page;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminBlogCategoriesListView( $blogInfo, $params = Array())
        {
        	$this->AdminTemplatedView( $blogInfo, "blogcategories" );
			
			if( isset( $params['searchTerms']  )) 
				$this->_searchTerms = $params['searchTerms'];
			else
				$this->_searchTerms = "";
        }

        /**
         * Carries out the specified action
         */
        function render()
        {
			// prepare a few parameters
            $categories = new BlogCategories();
			
			// get the page too
			$this->_page = $this->getCurrentPageFromRequest();
			
			// retrieve the categories in an paged fashion
			$totalCategories = $categories->getNumBlogCategories( $this->_searchTerms );
            $blogCategories = $categories->getBlogCategories( $this->_searchTerms, $this->_page, DEFAULT_ITEMS_PER_PAGE );
			if( !$blogCategories ) {
				$blogCategories = Array();
			}					
			
			// throw the even in case somebody's waiting for it!
			$this->notifyEvent( EVENT_BLOG_CATEGORIES_LOADED, Array( "blogcategories" => &$blogCategories ));
            $this->setValue( "blogcategories", $blogCategories );
			
			// the pager that will be used
			$pager = new Pager( "?op=editBlogCategories&amp;searchTerms=".$this->_searchTerms."&amp;page=",
			                    $this->_page,
								$totalCategories,
								DEFAULT_ITEMS_PER_PAGE );
			$this->setValue( "pager", $pager );
			$this->setValue( "searchTerms", $this->_searchTerms );
						
			parent::render();
        }
    }
?>