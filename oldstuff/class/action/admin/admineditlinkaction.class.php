<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admineditlinkview.class.php") ;
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminlinkslistview.class.php") ;	
	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/mylinkscategories.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/mylinks.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows a form to change the settings of the link
     */
    class AdminEditLinkAction extends AdminAction 
	{
    	var $_linkId;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminEditLinkAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// data validation stuff
			$this->registerFieldValidator( "linkId", new IntegerValidator());

			$view = new AdminLinksListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_fetching_link" ));
			$this->setValidationErrorView( $view );

			// permission checks
			$this->requirePermission( "update_link" );
        }
        
        /**
         * Carries out the specified action
         */
        function perform()
        {
        	// fetch the link
			$this->_linkId = $this->_request->getValue( "linkId" );
            $links = new MyLinks();
            $link  = $links->getMyLink( $this->_linkId, $this->_blogInfo->getId());
            // show an error if we couldn't fetch the link
            if( !$link ) {
                $this->_view->setErrorMessage( $this->_locale->tr("error_fetching_link"));
                $this->setCommonData();

                return false;
            }
			
			$this->notifyEvent( EVENT_LINK_LOADED, Array( "link" => &$link ));

            // otherwise show the form to edit its fields
        	$this->_view = new AdminEditLinkView( $this->_blogInfo );
            $this->_view->setValue( "linkName", $link->getName());
            $this->_view->setValue( "linkDescription", $link->getDescription());
			$this->_view->setValue( "linkUrl", $link->getUrl());
			$this->_view->setValue( "linkRssFeed", $link->getRssFeed());
			$this->_view->setValue( "linkId", $link->getId());
			$this->_view->setValue( "linkCategoryId", $link->getCategoryId());
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>
