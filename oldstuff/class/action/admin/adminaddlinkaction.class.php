<?php

    lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/mylinks.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/mylink.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/httpurlvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminnewlinkview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminlinkslistview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Adds a new link to the database, given some information
     */
    class AdminAddLinkAction extends AdminAction 
	{
        /**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminAddLinkAction( $actionInfo, $request )
        {
            $this->AdminAction( $actionInfo, $request );
			
			// data validation
			$this->registerFieldValidator( "linkName", new StringValidator());
			$this->registerFieldValidator( "linkUrl", new HttpUrlValidator());
			// linkRssFeed will only be validated if it is available in the form
			$this->registerFieldValidator( "linkRssFeed", new HttpUrlValidator(), true );
			$this->registerFieldValidator( "linkCategoryId", new IntegerValidator());
			$this->registerFieldValidator( "linkDescription", new StringValidator(), true );

			$view = new AdminNewLinkView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_adding_link" ));
			$this->setValidationErrorView( $view );
			
			$this->requirePermission( "add_link" );
        }
        
        /**
         * Carries out the specified action
         */
        function perform()
        {
                // fetch the data
            $linkName = Textfilter::filterAllHTML($this->_request->getValue( "linkName" ));
            $linkUrl  = Textfilter::filterAllHTML($this->_request->getValue( "linkUrl" ));
            $linkCategoryId = $this->_request->getValue( "linkCategoryId" );
            $linkDescription = Textfilter::filterAllHTML($this->_request->getValue( "linkDescription" ));
			$linkRss = Textfilter::filterAllHTML($this->_request->getValue( "linkRssFeed" ));
			$properties = Array();
			
            // adds the new link to the database
            $myLinks = new MyLinks();
            $myLink  = new MyLink( $linkName, $linkDescription, $linkUrl, 
			                       $this->_blogInfo->getId(), $linkCategoryId, 
								   0, $linkRss, $properties );
			$this->notifyEvent( EVENT_PRE_LINK_ADD, Array( "link" => &$link ));
            if( !$myLinks->addMyLink( $myLink, $this->_blogInfo->getId())) {
                $this->_view = new AdminNewLinkView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_adding_link"));
                $this->setCommonData();

                return false;
            }
			$this->notifyEvent( EVENT_POST_LINK_ADD, Array( "link" => &$link ));			

			// if the user has no permission to view the links, the we have to find another view
			if( $this->userHasPermission( "view_links" ))
	            $this->_view = new AdminLinksListView( $this->_blogInfo );
			else
                $this->_view = new AdminNewLinkView( $this->_blogInfo );
			
            $this->_view->setSuccessMessage( $this->_locale->pr("link_added_ok", $myLink->getName()));
            $this->setCommonData();
			
			// clear the cache
			CacheControl::resetBlogCache( $this->_blogInfo->getId(), false );

            // better to return true if everything fine
            return true;
        }
    }
?>