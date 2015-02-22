<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminarticlecategorieslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows a form to add a link for the blogroll feature
     */
    class AdminEditArticleCategoriesAction extends AdminAction 
	{

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminEditArticleCategoriesAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

        	$this->registerFieldValidator( "searchTerms", new StringValidator(), true);
			$this->setValidationErrorView( new AdminArticleCategoriesListView( $this->_blogInfo ) );
        	$this->requirePermission( "view_categories" );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
        	$searchTerms = $this->_request->getFilteredValue( "searchTerms", new HtmlFilter());
        	// create the view, which will take care of fetching the right data
        	$this->_view = new AdminArticleCategoriesListView( $this->_blogInfo, Array( "searchTerms" => $searchTerms ) );
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>
