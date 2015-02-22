<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );
	
    /**
     * \ingroup View
     * @private
     */	
	class AdminNewAlbumView extends AdminTemplatedView
	{
	
		function AdminNewAlbumView( $blogInfo )
		{
			$this->AdminTemplatedView( $blogInfo, "newresourcealbum" );
		}
		
		function render()
		{
			// get all the albums
			$albums = new GalleryAlbums();
			$userAlbums = $albums->getNestedAlbumList( $this->_blogInfo->getId());
			$this->notifyEvent( EVENT_ALBUMS_LOADED, Array( "albums" => &$userAlbums ));
			$this->setValue( "albums", $userAlbums );
			
			// transfer control to the parent class
			parent::render();
		}
	}
?>