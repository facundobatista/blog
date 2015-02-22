<?php

      /**
       * GLobal artical Category files added by Ameng(Ameng.vVlogger.com) 2005-06-20
       * version 1.0 
       * Changed from original article category.
       */

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/globalarticlecategories.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/pager/pager.class.php" );
	
    /**
     * \ingroup View
     * @private
     *
     * Action that shows a form to add a link for the blogroll feature
     */
    class AdminGlobalArticleCategoriesListView extends AdminTemplatedView 
	{
		var $_page;

    	  /**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminGlobalArticleCategoriesListView( $blogInfo, $params = Array())
        {
        	$this->AdminTemplatedView( $blogInfo, "globalarticlecategories" );
			
			if( isset( $params['searchTerms'] ))
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
            $categories = new GlobalArticleCategories();
			
			// get the page too
			$this->_page = $this->getCurrentPageFromRequest();			
			
			// retrieve the categories in an paged fashion
			$totalCategories = 	$categories->getNumGlobalArticleCategories();						
            $globalCategories = $categories->getGlobalArticleCategories( $this->_searchTerms,
			                                                             $this->_page, 
																		 DEFAULT_ITEMS_PER_PAGE );
			
			if( !$globalCategories ) {
				$globalCategories = Array();
			}					
			
			// throw the even in case somebody's waiting for it!
			$this->notifyEvent( EVENT_GLOBAL_CATEGORIES_LOADED, Array( "categories" => &$globalCategories ));
            $this->setValue( "categories", $globalCategories );			
			
			// the pager that will be used
			$pager = new Pager( "?op=editGlobalArticleCategories&amp;searchTerms=".$this->_searchTerms."&amp;page=",
			                    $this->_page,
								$totalCategories,
								DEFAULT_ITEMS_PER_PAGE );
								$this->setValue( "pager", $pager );								
			parent::render();			
        }
    }
?>
