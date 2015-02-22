<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminresourceslistview.class.php" );	
	
    /**
     * \ingroup View
     * @private
     *	
	 * shows the view that allows to see more information of a resource
	 */
	class AdminEditResourceView extends AdminTemplatedView
	{
		var $_resourceId;
	
		function AdminEditResourceView( $blogInfo )
		{
			$this->AdminTemplatedView( $blogInfo, "resourceinfo" );
		}
		
		function getResourceId( $resourceId )
		{
			return( $this->_resourceId );
		}
		
		function render()
		{
			// load the nested list of albums
            // fetch the albums for this blog
            $albums = new GalleryAlbums();
            $blogAlbums = $albums->getNestedAlbumList( $this->_blogInfo->getId());
			$this->notifyEvent( EVENT_ALBUMS_LOADED, Array( "albums" => &$blogAlbums ));
			$this->setValue( "albums", $blogAlbums );
			
			parent::render();
		}
	}
?>