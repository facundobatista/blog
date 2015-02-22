<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admineditresourceview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminresourceslistview.class.php" );	
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresource.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Shows information about a resource
     */
    class AdminResourceInfoAction extends AdminAction
    {

    	var $_resourceId;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminResourceInfoAction( $actionInfo, $request )
        {	       	        
        	$this->AdminAction( $actionInfo, $request );
			
			// data validation
			$this->registerFieldValidator( "resourceId", new IntegerValidator());
			$view = new AdminResourcesListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr( "error_fetching_resource" ));
			$this->setValidationErrorView( $view );
			
			$this->requirePermission( "update_resource" );			
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
            // load the resource
			$this->_resourceId = $this->_request->getValue( "resourceId" );
            $resources = new GalleryResources();
            $resource = $resources->getResource( $this->_resourceId, $this->_blogInfo->getId());

            if( !$resource ) {
            	$this->_view = new AdminResourcesListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_fetching_resource"));
            }
            else {
            	$this->_view = new AdminEditResourceView( $this->_blogInfo );
				$this->notifyEvent( EVENT_RESOURCE_LOADED, Array( "resource" => &$resource ));
                $this->_view->setValue( "resource", $resource );
				// export some useful information
				$this->_view->setValue( "resourceDescription", $resource->getDescription());
				$this->_view->setValue( "albumId", $resource->getAlbumId());
            }
			
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>
