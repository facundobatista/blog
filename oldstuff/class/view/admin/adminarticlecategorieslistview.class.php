<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/pager/pager.class.php" );
	
    /**
     * \ingroup View
     * @private
     *
     * Action that shows a form to add a link for the blogroll feature
     */
    class AdminArticleCategoriesListView extends AdminTemplatedView 
	{
		var $_page;
		var $_searchTerms;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminArticleCategoriesListView( $blogInfo, $params = Array() )
        {
        	$this->AdminTemplatedView( $blogInfo, "editarticlecategories" );

        	// save the parameters and put them in a nicer place after checking them
			if( !isset( $params["searchTerms"] ) )
				$params["searchTerms"] = "";

			$this->_searchTerms = $params["searchTerms"];
			$this->_page = $this->getCurrentPageFromRequest();
        }

        /**
         * Carries out the specified action
         */
        function render()
        {
			// prepare a few parameters
            $categories = new ArticleCategories();
			$blogSettings = $this->_blogInfo->getSettings();
			$categoriesOrder = $blogSettings->getValue( "categories_order" );
			
			// retrieve the categories in an paged fashion
			$totalCategories = $categories->getBlogNumCategories( $this->_blogInfo->getId(), true, $this->_searchTerms );
            $blogCategories = $categories->getBlogCategories( $this->_blogInfo->getId(), 
			                                                  false, 
															  $categoriesOrder,
															  $this->_searchTerms,
															  $this->_page,
															  DEFAULT_ITEMS_PER_PAGE );
			if( !$blogCategories )
				$blogCategories = Array();
			
			// throw the even in case somebody's waiting for it!
			$this->notifyEvent( EVENT_CATEGORIES_LOADED, Array( "categories" => &$blogCategories ));
            $this->setValue( "categories", $blogCategories );
			
			// the pager that will be used
			$pager = new Pager( "?op=editArticleCategories&amp;searchTerms=".$this->_searchTerms."&amp;page=",
			                    $this->_page,
								$totalCategories,
								DEFAULT_ITEMS_PER_PAGE );
			$this->setValue( "pager", $pager );
			$this->setValue( "searchTerms", $this->_searchTerms );
			
			parent::render();
        }
    }
?>
