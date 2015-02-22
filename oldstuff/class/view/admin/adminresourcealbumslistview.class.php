<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );	
    /**
     * \ingroup View
     * @private
     */	
	class AdminResourceAlbumsListView extends AdminTemplatedView
	{
		var $_albumId;
	
		function AdminResourceAlbumsListView( $blogInfo, $params = Array())
		{
			$this->AdminTemplatedView( $blogInfo, "resourcealbums" );
				
			// fetch the album id
			$this->_albumId = $params["albumId"];
			if( $this->_albumId == "" ) $this->_albumId = 0;
		}
		
		function render()
		{
        	// fetch the child albums of the current top level album
        	$galleryAlbums = new GalleryAlbums();
            $albums = $galleryAlbums->getChildAlbums( $this->_albumId, $this->_blogInfo->getId());

            // get some info about the parent album if it's different from the
            // top level album
            if( $this->_albumId > 0 )
            	$album = $galleryAlbums->getAlbum( $this->_albumId, $this->_blogInfo->getId());	

            // fetch the albums for this blog
            $this->setValue( "albums", $albums );
            $this->setValue( "albumid", $this->_albumId );
            $this->setValue( "album", $album );
			
			parent::render();
		}
	}
?>