<?php

	lt_include( PLOG_CLASS_PATH."class/summary/action/summaryaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/summary/view/summaryuserlistview.class.php" );    	

	/**
	 * shows a list with all the users, pager included
	 */
     class UserListAction extends SummaryAction
     {
        function UserListAction( $actionInfo, $request )
        {
            $this->SummaryAction( $actionInfo, $request );
        }
		
        /**
         * Loads the posts and shows them.
         */
        function perform()
        {
            $page = View::getCurrentPageFromRequest();
			$this->_view = new SummaryUserListView( Array( "summary" => "UserList", 
			                                               "page" => $page,
			                                               "locale" => $this->_locale->getLocaleCode()));
			if( $this->_view->isCached()) {
				// nothing to do, the view is cached
				$this->setCommonData();
				return true;
			}
			
			$this->setCommonData();
			
			return true;
		}
     }	 
?>