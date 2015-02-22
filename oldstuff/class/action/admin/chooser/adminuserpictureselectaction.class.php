<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/chooser/adminuserpictureselectview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Deletes a resource from the blog
     */
    class AdminUserPictureSelectAction extends AdminAction
    {
		
    	var $_albumId;
		
    	/**
		* Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminUserPictureSelectAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
        	$this->registerFieldValidator( "albumId", new IntegerValidator(), true );
            
        	// and the view we should show in case there is a validation error
			$view = new AdminUserPictureSelectView( $this->_blogInfo );
			$this->setValidationErrorView( $view );
        }

        /**
			* Carries out the specified action
         */
        function perform()
        {
            // fetch the albumId
            $this->_albumId = $this->_request->getValue( "albumId" );
            if( $this->_albumId == "" )
            	$this->_albumId = 0;
			
            $this->_view = new AdminUserPictureSelectView( $this->_blogInfo, Array( "albumId" => $this->_albumId ));
            $this->setCommonData();
			
            return true;
        }
    }
?>