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
    class AdminDeleteLinkAction extends AdminAction 
	{

        var $_linkIds;
		var $_op;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminDeleteLinkAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			$this->_op = $actionInfo->getActionParamValue();
			
			$view = new AdminLinksListView( $this->_blogInfo );
			if( $this->_op == "deleteLink" ) {
				$this->registerFieldValidator( "linkId", new IntegerValidator());
				$view->setErrorMessage( $this->_locale->tr("error_incorrect_link_id"));	
			}
			else {
				$this->registerFieldValidator( "linkIds", new ArrayValidator( new IntegerValidator()));
				$view->setErrorMessage( $this->_locale->tr("error_no_links_selected"));
			}
			$this->setValidationErrorView( $view );
			
			// permission checks
			$this->requirePermission( "update_link" );
        }        
		
		function perform()
		{
			if( $this->_op == "deleteLink" ) {
				$this->_linkIds = Array();
				$this->_linkId = $this->_request->getValue( "linkId" );
				$this->_linkIds[] = $this->_linkId;
			}
			else
				$this->_linkIds = $this->_request->getValue( "linkIds" );
				
			$this->_deleteLinks();
		}

        /**
         * Carries out the specified action
		 * @private
         */
        function _deleteLinks()
        {
        	// delete the link
            $links = new MyLinks();

            $errorMessage = "";
			$successMessage = "";
			$numOk = 0;
            foreach( $this->_linkIds as $linkId ) {
            	// load the link
                $link = $links->getMyLink( $linkId, $this->_blogInfo->getId());
				if( $link ) {
					if( !$links->deleteMyLink( $linkId, $this->_blogInfo->getId()))
						$errorMessage .= $this->_locale->pr("error_removing_link", $link->getName())."<br/>";
					else {
						$numOk++;
						if( $numOk > 1 )
							$successMessage = $this->_locale->pr("links_deleted_ok", $numOk );
						else
							$successMessage = $this->_locale->pr("link_deleted_ok", $link->getName());
					}
				}
				else {
					$errorMessage .= $this->_locale->pr("error_removing_link2", $linkId)."<br/>";
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
