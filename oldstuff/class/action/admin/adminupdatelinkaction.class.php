<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminlinkslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/admineditlinkview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/mylinks.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/httpurlvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Updates a link in the database
     */
    class AdminUpdateLinkAction extends AdminAction 
	{
    	var $_linkName;
        var $_linkUrl;
        var $_linkId;
        var $_linkDescription;
        var $_linkCategoryId;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminUpdateLinkAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// set up the data validators
			// data validation
			$this->registerFieldValidator( "linkName", new StringValidator());
			$this->registerFieldValidator( "linkUrl", new HttpUrlValidator());
			// linkRssFeed will only be validated if it is available in the form
			$this->registerFieldValidator( "linkRssFeed", new HttpUrlValidator(), true );
			$this->registerFieldValidator( "linkCategoryId", new IntegerValidator());
			$this->registerFieldValidator( "linkDescription", new StringValidator(), true );
			$this->registerFieldValidator( "linkId", new IntegerValidator());
			$view = new AdminEditLinkView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_updating_link" ));
			$this->setValidationErrorView( $view );

			// permission checks
			$this->requirePermission( "update_link" );			
        }
        
        /**
         * Carries out the specified action
         */
        function perform()
        {
			// data is fine, we have already validated it
        	$this->_linkName = Textfilter::filterAllHTML($this->_request->getValue( "linkName" ));
            $this->_linkDescription = Textfilter::filterAllHTML($this->_request->getValue( "linkDescription" ));
            $this->_linkUrl  = Textfilter::filterAllHTML($this->_request->getValue( "linkUrl" ));
            $this->_linkCategoryId = $this->_request->getValue( "linkCategoryId" );
            $this->_linkId = $this->_request->getValue( "linkId" );
			$this->_linkFeed = Textfilter::filterAllHTML($this->_request->getValue( "linkRssFeed" ));
		
        	// fetch the link we're trying to update
            $links = new MyLinks();
            $link  = $links->getMyLink( $this->_linkId, $this->_blogInfo->getId());
            if( !$link ) {
            	$this->_view = new AdminLinksListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_fetching_link"));
                $this->setCommonData();

                return false;
            }

            // update the fields
            $link->setName( $this->_linkName );
            $link->setDescription( $this->_linkDescription );
            $link->setCategoryId( $this->_linkCategoryId );
            $link->setUrl( $this->_linkUrl );
			$link->setRssFeed( $this->_linkFeed );
			$this->notifyEvent( EVENT_PRE_LINK_UPDATE, Array( "link" => &$link ));
            // and now update it in the database
            if( !$links->updateMyLink( $link )) {
            	$this->_view = new AdminLinksListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_updating_link"));
                $this->setCommonData();

                return false;
            }
			$this->notifyEvent( EVENT_POST_LINK_UPDATE, Array( "link" => &$link ));			
			
			// clear the cache
			CacheControl::resetBlogCache( $this->_blogInfo->getId(), false );
			
			// and go back to the view with the list of links
            $this->_view = new AdminLinksListView( $this->_blogInfo );
            $this->_view->setSuccessMessage( $this->_locale->pr("link_updated_ok", $link->getName()));
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>
