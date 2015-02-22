<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/adminresourceslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresource.class.php" );
	lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );

    /**
     * \ingroup View
     * @private
     *
	 * we can extend from AdminResourcesListView and we will save a lot of code...
	 */
	class AdminUserPictureSelectView extends AdminResourcesListView
	{
		var $_albumId;
		
		function AdminUserPictureSelectView( $blogInfo, $params = Array())
		{
			$this->AdminResourcesListView( $blogInfo, $params );
		
			$this->_templateName = "chooser/userpictureselect";
			$this->_resourceType = GALLERY_RESOURCE_IMAGE;
		}
		
		function render()
		{
			$this->_pagerUrl = "?op=userPictureSelect&amp;page=";
			
			parent::render();
		}
	}

?>