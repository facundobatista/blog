<?php
	/**
     * GLobal artical Category files added by Ameng(Ameng.vVlogger.com) 2005-06-20
     * version 1.0 
     * Changed from original article category.
     */
	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminglobalarticlecategorieslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows a form to add a link for the blogroll feature
     */
    class AdminEditGlobalArticleCategoriesAction extends AdminAction 
	{

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminEditGlobalArticleCategoriesAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			$this->registerFieldValidator( "searchTerms", new StringValidator(), true);
			$this->setValidationErrorView( new AdminGlobalArticleCategoriesListView( $this->_blogInfo ) );
			
			$this->requireAdminPermission( "view_global_categories" );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
        	$searchTerms = $this->_request->getFilteredValue( "searchTerms", new HtmlFilter() );
        	// create the view, which will take care of fetching the right data
        	$this->_view = new AdminGlobalArticleCategoriesListView( $this->_blogInfo, Array( "searchTerms" => $searchTerms ));
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>