<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminresourceslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresource.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/cachecontrol.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Deletes a resource from the blog
     */
    class AdminDeleteResourceAction extends AdminAction
    {

    	var $_resourceIds;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminDeleteResourceAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// data validation
			$this->registerFieldValidator( "resourceId", new IntegerValidator());
			$view = new AdminResourcesListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_no_resources_selected"));
			$this->setValidationErrorView( $view );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
        	// load the resource
			$resourceId = $this->_request->getValue( "resourceId" );
        	$resources = new GalleryResources();
			
			// initialize the view we're going to use
			$this->_view = new AdminResourcesListView( $this->_blogInfo );

           	// fetch the resource first, to get some info about it
            $resource = $resources->getResource( $resourceId, $this->_blogInfo->getId());
			if( !$resource ) {
				$this->_view->setErrorMessage( $this->_locale->tr("error_fetching_resource" ));
				$this->setCommonData();
				return false;
			}
			
			// if the resource was loaded ok...
			$this->notifyEvent( EVENT_PRE_RESOURCE_DELETE, Array( "resource" => &$resource ));
			
            // remove it
           	$res = $resources->deleteResource( $resourceId, $this->_blogInfo->getId());
            if( $res ) {
				$this->_view->setSuccessMessage( $this->_locale->pr("resource_deleted_ok", $resource->getFileName()));
				$this->notifyEvent( EVENT_POST_RESOURCE_DELETE, Array( "resource" => &$resource ));

				// clear the cache if everything went fine
				CacheControl::resetBlogCache( $this->_blogInfo->getId(), false );
			}
            else 
				$this->_view->setErrorMessage( $this->_locale->pr("error_deleting_resource", $resource->getFileName()));

            // return the view
            $this->setCommonData();
			
            // better to return true if everything fine
            return true;
        }
    }
?>
