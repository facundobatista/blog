<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminlinkcategorieslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows the list link categories
     */
    class AdminEditLinkCategoriesAction extends AdminAction 
	{

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminEditLinkCategoriesAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
        	$this->registerFieldValidator( "searchTerms", new StringValidator(), true );
			$this->setValidationErrorView( new AdminLinkCategoriesListView( $this->_blogInfo ) );
        	
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
            // get all the link categories
			$searchTerms = $this->_request->getFilteredValue( "searchTerms", new HtmlFilter());
            $this->_view = new AdminLinkCategoriesListView( $this->_blogInfo, Array( "searchTerms" => $searchTerms ) );
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>
