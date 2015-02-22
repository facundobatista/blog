<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminnewalbumview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Adds a new album
     */
    class AdminNewResourceAlbumAction extends AdminAction 
	{

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminNewResourceAlbumAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
        	
        	$this->requirePermission( "add_album" );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
            $this->_view = new AdminNewAlbumView( $this->_blogInfo );
            $this->setCommonData();
            return true;
        }
    }
?>
