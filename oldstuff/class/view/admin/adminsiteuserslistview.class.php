<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/pager/pager.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/userstatus.class.php" ); 
    lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );   
	
    /**
     * \ingroup View
     * @private
     *
	 * shows a list with the users in the blog
	 */
	class AdminSiteUsersListView extends AdminTemplatedView
	{
		var $_status;
		var $_page;
		var $_templateName;
	
		/**
		 * @param blogInfo
		 * @param chooserMode Whether this view should act in chooser mode or not. If in chooser mode,
		 * it will load a lighter template
		 */
		function AdminSiteUsersListView( $blogInfo )
		{
			if( $this->_templateName == "" )
				$this->_templateName = "siteusers";	
				
			$this->AdminTemplatedView( $blogInfo, $this->_templateName );
			
			$this->_pagerUrl = "?op=editSiteUsers";
		}
		
		/**
		 * retrieves the current status from the request
		 *
		 * @private
		 * @return a string with the status code as it came from the request
		 */
		function getStatusFromRequest()
		{
			$status = $this->_request->getFilteredValue( "status", new HtmlFilter() );
			
			// validate the value 
			$val = new IntegerValidator();
			if( !$val->validate( $status ))
				$status = UserStatus::getDefaultStatus();
				
			// if the value validated, check if it is a valid status
			if( !UserStatus::isValidStatus( $status ))
				$status = UserStatus::getDefaultStatus();
				
			return $status;
		}
		
		/**
		 * @private
		 */
		
		function render()
		{
			// get the current page
			$this->_page = $this->getCurrentPageFromRequest();
			$this->_status = $this->getStatusFromRequest();
			$this->_searchTerms = $this->_request->getFilteredValue( "searchTerms", new HtmlFilter());
			
        	// get the users of the blog
            $users = new Users();
            $siteUsers = $users->getAllUsers( $this->_status, $this->_searchTerms, "", $this->_page, DEFAULT_ITEMS_PER_PAGE );
            $numUsers = $users->getNumUsers( $this->_status, $this->_searchTerms );
            
            // in case of problems, empty array...
            if( !$siteUsers )
            	$siteUsers = Array();
            
            // notify the event
            $this->notifyEvent( EVENT_USERS_LOADED, Array( "users" => &$blogUsers ));

			$this->_pagerUrl = $this->_pagerUrl."&amp;searchTerms=".$this->_searchTerms."&amp;status=".$this->_status."&amp;page=";
			$pager = new Pager( $this->_pagerUrl,
								$this->_page, 
								$numUsers, 
								DEFAULT_ITEMS_PER_PAGE );				
            
            // and generate the view
            $this->setValue( "siteusers", $siteUsers );	
            $this->setValue( "userstatus", UserStatus::getStatusList( true ));
            $this->setValue( "pager", $pager );
            $this->setValue( "currentstatus", $this->_status );
            $this->setValue( "searchTerms", $this->_searchTerms );
			parent::render();
		}
	}
	
?>