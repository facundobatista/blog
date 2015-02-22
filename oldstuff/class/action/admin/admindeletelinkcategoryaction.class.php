<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/mylinkscategories.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminlinkcategorieslistview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that deletes a link category from the database
     */
    class AdminDeleteLinkCategoryAction extends AdminAction 
	{

        var $_categoryIds;
		var $_op;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminDeleteLinkCategoryAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			$this->_op = $actionInfo->getActionParamValue();
			
			$view = new AdminLinkCategoriesListView( $this->_blogInfo );			
			if( $this->_op == "deleteLinkCategory" )
				$this->registerFieldValidator( "categoryId", new IntegerValidator());
			else
				$this->registerFieldValidator( "categoryIds", new ArrayValidator( new IntegerValidator()));
			$view->setErrorMessage( $this->_locale->tr("error_invalid_link_category_id"));			
			$this->setValidationErrorView( $view );
			
			// permission checks
			$this->requirePermission( "update_link_category" );
        }        
		
		function perform()
		{
			if( $this->_op == "deleteLinkCategory" ) {
				$this->_categoryId = $this->_request->getValue( "categoryId" );
				$this->_categoryIds = Array();
				$this->_categoryIds[] = $this->_categoryId;
			}
			else
				$this->_categoryIds = $this->_request->getValue( "categoryIds" );
				
			$this->_deleteLinkCategories();
		}

        /**
         * Carries out the specified action
		 * @static
         */
        function _deleteLinkCategories()
        {
        	// delete the link category, but only if there are no links under it
            $categories = new MyLinksCategories();
            $errorMessage = "";
			$successMessage = "";
			$totalOk = 0;
			
            foreach( $this->_categoryIds as $categoryId ) {
            	// fetch the category
                $linkCategory = $categories->getMyLinksCategory( $categoryId, $this->_blogInfo->getId());
                if( $linkCategory ) {
					if( $linkCategory->getNumLinks() > 0 ) {
                		$errorMessage .= $this->_locale->pr("error_links_in_link_category",$linkCategory->getName())."<br/>";
                	}
                	else {
            			// if all correct, now delete it and check how it went
            			if( !$categories->deleteMyLinksCategory( $categoryId, $this->_blogInfo->getId())) {
                        	$errorMessage .= $this->_locale->pr("error_removing_link_category", $linkCategory->getName());
            			}
                    	else {
							$totalOk++;
							if( $totalOk < 2 )
								$successMessage = $this->_locale->pr( "link_category_deleted_ok", $linkCategory->getName());
							else
								$successMessage = $this->_locale->pr( "link_categories_deleted_ok", $totalOk );
						}
                	}
                }
                else {
                	$errorMessage .= $this->_locale->pr("error_removing_link_category2", $categoryId )."<br/>";
                }
            }
			
            $this->_view = new AdminLinkCategoriesListView( $this->_blogInfo );
            if( $errorMessage != "" ) $this->_view->setErrorMessage( $errorMessage );
			if( $successMessage != "" ) {
				$this->_view->setSuccessMessage( $successMessage );
				// clear the cache
				CacheControl::resetBlogCache( $this->_blogInfo->getId(), false );
			}
				
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>