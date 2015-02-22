<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/mylinks.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminlinkslistview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows a form to change the settings of the current blog.
     */
    class AdminChangeLinksCategoryAction extends AdminAction 
	{

        var $_linkIds;
		var $_linkCategoryId;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminChangeLinksCategoryAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			$this->registerFieldValidator( "linkIds", new ArrayValidator( new IntegerValidator()));
			$this->registerFieldValidator( "linkCategoryId", new IntegerValidator());
			$view = new AdminLinksListView( $this->_blogInfo );	
			$view->setErrorMessage( $this->_locale->tr("error_no_links_selected"));
			$this->setValidationErrorView( $view );
        }
		
		function perform()
		{
			$this->_linkIds = $this->_request->getValue( "linkIds" );
			$this->_linkCategoryId = $this->_request->getValue( "linkCategoryId" );
				
			$this->_changeLinks();
		}

        /**
         * Carries out the specified action
		 * @private
         */
        function _changeLinks()
        {
            $errorMessage = "";
			$successMessage = "";
			$numOk = 0;

        	// update the link
            $links = new MyLinks();

            foreach( $this->_linkIds as $linkId ) {
            	// load the link
                $link = $links->getMyLink( $linkId, $this->_blogInfo->getId());
				
				if( $link ) {
					// fire the event
					$this->notifyEvent( EVENT_PRE_LINK_UPDATE, Array( "link" => &$link ));

					// update the link category
					$link->setCategoryId( $this->_linkCategoryId );
					$result = $links->updateMyLink( $link );
					
					if( !$result )
						$errorMessage .= $this->_locale->pr("error_updating_link", $link->getName())."<br/>";
					else {
						$numOk++;
						if( $numOk > 1 )
							$successMessage = $this->_locale->pr("links_updated_ok", $numOk );
						else
							$successMessage = $this->_locale->pr("link_updated_ok", $link->getName());
					}
				}
				else {
					$errorMessage .= $this->_locale->pr("error_updating_link2", $linkId)."<br/>";
				}
            }

            $this->_view = new AdminLinksListView( $this->_blogInfo );
            if( $errorMessage != "" ) $this->_view->setErrorMessage( $errorMessage );
			if( $successMessage != "" ) $this->_view->setSuccessMessage( $successMessage );
            $this->setCommonData();
			
			// clear the cache
			CacheControl::resetBlogCache( $this->_blogInfo->getId(), false );

            // better to return true if everything fine
            return true;
        }
    }
?>
