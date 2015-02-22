<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	
    /**
     * \ingroup View
     * @private
     */	
	class AdminUserProfileView extends AdminTemplatedView
	{
		var $_userInfo;
	
		function AdminUserProfileView( $blogInfo, $userInfo )
		{
			$this->AdminTemplatedView( $blogInfo, "usersettings" );
			
			$this->_userInfo = $userInfo;
			
			$this->setValue( "userFullName", $userInfo->getFullName());
			$this->setValue( "userEmail", $userInfo->getEmail());
			// we use 'false' here because we don't want to get the formatting
			$this->setValue( "userAbout", $userInfo->getAboutMyself( false ));
		}
		
		function render()
		{
			$this->setValue( "user", $this->_userInfo );		
			parent::render();
		}
	}
?>