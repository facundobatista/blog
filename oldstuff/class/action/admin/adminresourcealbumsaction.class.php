<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminresourcealbumslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * shows a list of the albums
     */
    class AdminResourceAlbumsAction extends AdminAction
    {
    	var $_albumId;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminResourceAlbumsAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			$this->registerFieldValidator( "albumId", new IntegerValidator());
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
        	$this->_albumId = $this->_request->getValue( "albumId" );
            if( $this->_albumId == "" || $this->_albumId == null )
            	$this->_albumId = 0;		
		
			$this->_view = new AdminResourceAlbumsListView( $this->_blogInfo, Array( "albumId" => $this->_albumId ));
            $this->setCommonData();

            return true;
        }
    }
?>