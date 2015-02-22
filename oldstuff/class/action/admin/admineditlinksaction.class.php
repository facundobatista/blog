<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminlinkslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows the list of user defined links (blogroll)
     */
    class AdminEditLinksAction extends AdminAction 
	{

    	var $_categoryId;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminEditLinksAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// data validation. In this case if the element is not correct we won't
			// bother doing anything... we'll just show the whole list of categories
			$this->registerFieldValidator( "showCategory", new IntegerValidator(), true);
			$this->registerFieldValidator( "searchTerms", new StringValidator(), true);
			$blogSettings = $this->_blogInfo->getSettings();
			$this->_linkCategoriesOrder = $blogSettings->getValue( "link_categories_order", MYLINKS_CATEGORIES_NO_ORDER );
			$this->setValidationErrorView( new AdminLinksListView( $this->_blogInfo ) );
																
																
			$this->requirePermission( "view_links" );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
			// get the id of the category we'd like to load
        	$this->_categoryId = $this->_request->getValue( "showCategory" );		
			$this->_searchTerms = $this->_request->getFilteredValue( "searchTerms", new HtmlFilter());
			
			// and create the view with all the parameters we'll need
			$this->_view = new AdminLinksListView( $this->_blogInfo, Array( "showCategory" => $this->_categoryId,
																            "searchTerms" => $this->_searchTerms, 			
			                                                                "showOrder" => $this->_linkCategoriesOrder )); 
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>