<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/mylinkscategories.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/pager/pager.class.php" );

    /**
     * \ingroup View
     * @private
     *	
	 * shows a list of link categories
	 */
	class AdminLinkCategoriesListView extends AdminTemplatedView
	{
		var $_page;
	
		function AdminLinkCategoriesListView( $blogInfo, $params = Array())
		{
			$this->AdminTemplatedView( $blogInfo, "editlinkcategories" );
			
			// save the parameters and put them in a nicer place after checking them
			if( !isset( $params["searchTerms"] ) ) $params["searchTerms"] = "";
			$this->setValue( "searchTerms", $params["searchTerms"] );

			$this->_page = $this->getCurrentPageFromRequest();
		}
		
		function render()
		{
            // get all the link categories
            $searchTerms = $this->getValue( "searchTerms" );
			$blogSettings = $this->_blogInfo->getSettings();
			$linkCategoriesOrder = $blogSettings->getValue( "link_categories_order", MYLINKS_CATEGORIES_NO_ORDER );			
			// get the link categories
            $linkCategories = new MyLinksCategories();
            $blogLinkCategories = $linkCategories->getMyLinksCategories( $this->_blogInfo->getId(), 
			                                                             $linkCategoriesOrder,
																		 $searchTerms,
																		 $this->_page,
																		 DEFAULT_ITEMS_PER_PAGE );
			// get the total number of link categories
			$numLinkCategories = $linkCategories->getNumMyLinksCategories( $this->_blogInfo->getId(), $searchTerms );
			
			// throw the event
			$this->notifyEvent( EVENT_LINK_CATEGORIES_LOADED, Array( "linkcategories" => &$blogLinkCategories ));
			
			// create the pager
			$pager = new Pager( "?op=editLinkCategories&amp;searchTerms={$searchTerms}&amp;page=",
			                    $this->_page,
								$numLinkCategories,
								DEFAULT_ITEMS_PER_PAGE );

            // create the view and fill the template context
            $this->setValue( "linkcategories", $blogLinkCategories );
			$this->setValue( "searchTerms", $searchTerms );
			$this->setValue( "pager", $pager );
			
			// transfer control to the parent class
			parent::render();
		}
	}
?>