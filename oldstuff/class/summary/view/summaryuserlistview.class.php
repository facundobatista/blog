<?php

	lt_include( PLOG_CLASS_PATH."class/summary/view/summarycachedview.class.php" );
	
	/**
	 * shows a paged list of users
	 */
	class SummaryUserListView extends SummaryCachedView
	{
		var $_numUsersPerPage;
	
		function SummaryUserListView( $data = Array())
		{
			// get the page id
			$this->_page = $this->getCurrentPageFromRequest();		
			
			// and initialize the cached view
			$this->SummaryCachedView( "userlist", $data );			
		}
		
		function render()
		{
			// do nothing if the contents of our view are cached
			if( $this->isCached()) {
				parent::render();
				return true;
			}
			
		    lt_include( PLOG_CLASS_PATH."class/summary/dao/summarystats.class.php" );	
		    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
		    lt_include( PLOG_CLASS_PATH."class/data/pager/pager.class.php" );
    		lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );						
			
			// items per page
            $config =& Config::getConfig();
			$this->_numUsersPerPage = $config->getValue( "summary_items_per_page", SUMMARY_DEFAULT_ITEMS_PER_PAGE );			
			
			// get the data itself
			$users = new Users();
            $siteUsers = $users->getAllUsers( USER_STATUS_ACTIVE, "", "id desc", $this->_page, $this->_numUsersPerPage );
			
            if( !$siteUsers ) {
                // if there was an error, show the error view
				$siteUsers = Array();
            }

			$numUsers = $users->getNumUsers( USER_STATUS_ACTIVE );
			
			// calculate the links to the different pages
			$pager = new Pager( "?op=UserList&amp;page=",
			                    $this->_page, 
								$numUsers, 
								$this->_numUsersPerPage );

			$this->setValue( "blogActive", BLOG_STATUS_ACTIVE );
			$this->setValue( "users", $siteUsers );
			$this->setValue( "pager", $pager );
		
			// let the parent view do its job
			parent::render();
		}
	}
?>