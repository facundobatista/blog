<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/adminsiteuserslistview.class.php" );
	
    /**
     * \ingroup View
     * @private
     */	
	class AdminUserChooserView extends AdminSiteUsersListView
	{
		function AdminUserChooserView( $blogInfo, $params = Array())
		{
			$this->_templateName = "chooser/siteuserschooser";
			$this->AdminSiteUsersListView( $blogInfo, $params );
		}
		
		function render()
		{
			$this->_pagerUrl = "?op=siteUsersChooser&mode=".$this->getValue("mode", 1 );
			parent::render();
		}
	}
?>