<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );	
	
    /**
     * \ingroup View
     * @private
     *	
	 * shows the view that allows to edit an album
	 */
	class AdminEditResourceAlbumView extends AdminTemplatedView
	{
	
		function AdminEditResourceAlbumView( $blogInfo )
		{
			$this->AdminTemplatedView( $blogInfo, "editresourcealbum" );
		}
		
		function render()
		{
			// load the nested list of albums
			$albums = new GalleryAlbums();
			$userAlbums = $albums->getNestedAlbumList( $this->_blogInfo->getId());		
			$this->notifyEvent( EVENT_ALBUMS_LOADED, Array( "albums" => &$userAlbums ));			
			$this->setValue( "albums", $userAlbums );
			
			// let the parent view do its work
			parent::render();
		}
	}
?>