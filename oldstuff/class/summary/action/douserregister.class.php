<?php

	lt_include( PLOG_CLASS_PATH."class/summary/action/registeraction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/summary/view/summaryusercreationview.class.php" );

	/**
	 * shows a form so that users can register
	 */
    class doUserRegister extends RegisterAction 
	{
        function perform()
        {
           $this->_view = new SummaryUserCreationView();
           $this->setCommonData();

		   // clean the session data
			SessionManager::setSessionValue( "blogName", "" );
			SessionManager::setSessionValue( "blogCategoryId", "" );
			SessionManager::setSessionValue( "blogLocale", "" );
			SessionManager::setSessionValue( "blogSubDomain", "" );
			SessionManager::setSessionValue( "blogMainDomain", "" );
			SessionManager::setSessionValue( "userName", "" );
			SessionManager::setSessionValue( "userPassword", "" );
			SessionManager::setSessionValue( "userEmail", "" );
			SessionManager::setSessionValue( "userFullName", "" );	
           
           return( true );
        }
    }	 
?>
