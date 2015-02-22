<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/referers.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminreferrersview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Allows to remove referrers
     */
    class AdminDeleteReferrerAction extends AdminAction 
	{

    	var $_articleId;
        var $_referrerIds;
		var $_mode;
		var $_referrerId;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminDeleteReferrerAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// data validation
			$this->_mode = $actionInfo->getActionParamValue();
			if( $this->_mode == "deleteReferrer" )
				$this->registerFieldValidator( "referrerId", new IntegerValidator());
			else
				$this->registerFieldValidator( "referrerIds", new ArrayValidator( new IntegerValidator()));
			$view = new AdminReferrersView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_no_items_selected" ));
			$this->setValidationErrorView( $view );
			
			$this->requirePermission( "update_blog_stats" );
        }
		
		function perform()
		{
			// collect the data and call the right method...
			if( $this->_mode == "deleteReferrer" ) {
				$this->_referrerId = $this->_request->getValue( "referrerId" );
				$this->_referrerIds = Array();
				$this->_referrerIds[] = $this->_referrerId;
			}
			else
				$this->_referrerIds = $this->_request->getValue( "referrerIds" );
			
			$this->_deleteReferrers();
			
			return true;
		}

        /**
         * Carries out the specified action
         */
        function _deleteReferrers()
        {
            $referrers = new Referers();
            $errorMessage = "";
			$successMessage = "";
			$totalOk = 0;
			
            foreach( $this->_referrerIds as $referrerId ) {
            	// fetch the referrer
				$referrer = $referrers->getBlogReferer( $referrerId, $this->_blogInfo->getId());
				
				// fire the pre-event
				$this->notifyEvent( EVENT_PRE_REFERRER_DELETE, Array( "referrer" => &$referrer ));
				
				if( !$referrer ) {
					$errorMessage .= $this->_locale->pr("error_deleting_referrer2", $referrerId )."<br/>";
				}
				else {
					if( !$referrers->deleteBlogReferer( $referrerId, $this->_blogInfo->getId()))
						$errorMessage .= $this->_locale->pr("error_deleting_referrer", $referrer->getUrl())."<br/>";
					else {
						$totalOk++;
						if( $totalOk < 2 )
							$successMessage = $this->_locale->pr("referrer_deleted_ok", $referrer->getUrl());
						else
							$successMessage = $this->_locale->pr("referrers_deleted_ok", $totalOk );
						// fire the post-event
						$this->notifyEvent( EVENT_POST_REFERRER_DELETE, Array( "referrer" => &$referrer ));
						
						$clearCache = true;
					}
				}			
            }
			
			if( $clearCache ) {
				// clear the cache if needed
				CacheControl::resetBlogCache( $this->_blogInfo->getId(), false );			
			}

            $this->_view = new AdminReferrersView( $this->_blogInfo );
            if( $errorMessage != "" ) $this->_view->setErrorMessage( $errorMessage );
			if( $successMessage != "" ) $this->_view->setSuccessMessage( $successMessage );
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>