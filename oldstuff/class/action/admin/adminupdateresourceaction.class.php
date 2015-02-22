<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminresourceslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admineditresourceview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresources.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Updates a resource in the database
     */
    class AdminUpdateResourceAction extends AdminAction
    {

    	var $_resourceId;
        var $_resourceDescription;
        var $_albumId;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminUpdateResourceAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// data validation
			$this->registerFieldValidator( "albumId", new IntegerValidator());
			$this->registerFieldValidator( "resourceId", new IntegerValidator());
			$this->registerFieldValidator( "resourceDescription", new StringValidator(), true);
			$this->registerFieldValidator( "regenerate", new StringValidator(), true);

			$view = new AdminEditResourceView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_updating_resource" ));
			$this->setValidationErrorView( $view );
        }
		
		function validate()
		{
            if(parent::validate())
                return true;
            
                // If it didn't validate, customize the view returned based on inputs
            if($this->_form->isFieldValid("resourceId")){
                $resources = new GalleryResources();
                $resource = $resources->getResource( $this->_request->getValue("resourceId"), $this->_blogInfo->getId());
                if( !$resource ) {	
                    $this->_view = new AdminResourcesListView( $this->_blogInfo );
                    $this->_view->setErrorMessage( $this->_locale->tr("error_updating_resource" ));
                    $this->setCommonData();
                    return false;
                }
            }
            $this->_view->setValue( "resource", $resource );
            return false;
		}

        /**
         * Carries out the specified action
         */
        function perform()
        {
        	// load the resource
			$this->_resourceDescription = Textfilter::filterAllHTML($this->_request->getValue( "resourceDescription" ));
			$this->_albumId = $this->_request->getValue( "albumId" );
			$this->_resourceId = $this->_request->getValue( "resourceId" );
        	$resources = new GalleryResources();
            $resource = $resources->getResource( $this->_resourceId, $this->_blogInfo->getId());
            // update the fields we'd like to update
            $resource->setAlbumId( $this->_albumId );
            $resource->setDescription( $this->_resourceDescription );
			// send the event
			$this->notifyEvent( EVENT_PRE_RESOURCE_UPDATE, Array( "resource" => &$resource ));						
            // and update it in the db
            $result = $resources->updateResource( $resource );

            if( !$result ) {
            	$this->_view = new AdminResourcesListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_updating_resource"));
            }
            else {
                // check which submit button was pressed
                if($this->_request->getValue("regenerate" ) != ""){
                    return Controller::setForwardAction( "regeneratePreview" );
                }

                $this->_view = new AdminResourcesListView( $this->_blogInfo );
                $this->_view->setSuccessMessage( $this->_locale->pr("resource_updated_ok", $resource->getFileName()));
                $this->notifyEvent( EVENT_POST_RESOURCE_UPDATE, Array( "resource" => &$resource ));				
                
                // clear the cache
                CacheControl::resetBlogCache( $this->_blogInfo->getId(), false );
            }

            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>
