<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/adminsiteblogslistview.class.php" );
	
    /**
     * \ingroup View
     * @private
     */	
	class AdminBlogChooserView extends AdminSiteBlogsListView
	{
		function AdminBlogChooserView( $blogInfo, $params = Array())
		{
			$this->_templateName = "chooser/siteblogschooser";
			$this->AdminSiteBlogsListView( $blogInfo, $params );
		}
		
		function render()
		{
			$this->_pagerUrl = "?op=siteBlogsChooser&mode=".$this->getValue("mode", 1 );
			parent::render();
		}
	}
?>