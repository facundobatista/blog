<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminpostslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Fetches all the posts and offers them for edition or deletion.
     */
    class AdminEditPostsAction extends AdminAction 
	{
    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminEditPostsAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

 			// field validation
        	$this->registerFieldValidator( "showCategory", new IntegerValidator( true ), true );
			$this->registerFieldValidator( "showStatus", new IntegerValidator( true ), true );
			$this->registerFieldValidator( "showUser", new IntegerValidator(), true );
			$this->registerFieldValidator( "showMonth", new IntegerValidator( true ), true );
			$this->registerFieldValidator( "searchTerms", new StringValidator(), true );
        	$this->setValidationErrorView( new AdminPostsListView( $this->_blogInfo ) );
        	
			$this->requirePermission( "view_posts" );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
			$this->_searchTerms = $this->_request->getFilteredValue( "searchTerms", new HtmlFilter());
			// create the view with the right parameters... 
        	$this->_view = new AdminPostsListView( $this->_blogInfo, 
			                                       Array( "showCategory" => $this->_request->getValue( "showCategory" ),
												          "showStatus" => $this->_request->getValue( "showStatus" ),
														  "showUser" => $this->_request->getValue( "showUser" ),
														  "showMonth" => $this->_request->getValue( "showMonth" ),
														  "searchTerms" => $this->_searchTerms ));
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>