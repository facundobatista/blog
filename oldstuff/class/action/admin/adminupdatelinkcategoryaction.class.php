<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminlinkcategorieslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/mylinkscategories.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );    

    /**
     * \ingroup Action
     * @private
     *
     * Updates an article category.
     */
    class AdminUpdateLinkCategoryAction extends AdminAction 
	{

    	var $_categoryName;
        var $_categoryId;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminUpdateLinkCategoryAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// data validation
			$this->registerFieldValidator( "linkCategoryName", new StringValidator());
			$this->registerFieldValidator( "linkCategoryId", new IntegerValidator());
			$errorView = new AdminTemplatedView( $this->_blogInfo, "editlinkcategory" );
			$errorView->setErrorMessage( $this->_locale->tr("error_updating_link_category"));
			$this->setValidationErrorView( $errorView );
			
			// permission checks
			$this->requirePermission( "update_link_category" );
        }
        
        /**
         * Carries out the specified action
         */
        function perform()
        {
        	// fetch the category we're trying to update
			$this->_categoryId = $this->_request->getValue( "linkCategoryId" );
			$this->_categoryName = Textfilter::filterAllHTML($this->_request->getValue( "linkCategoryName" ));
            $categories = new MyLinksCategories();
            $category   = $categories->getMyLinksCategory( $this->_categoryId, $this->_blogInfo->getId());
            if( !$category ) {
            	$this->_view = new AdminLinkCategoriesListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_fetching_link_category"));
                $this->setCommonData();

                return false;
            }

            // update the fields
            $category->setName( $this->_categoryName );
			$this->notifyEvent( EVENT_PRE_LINK_CATEGORY_UPDATE, Array( "linkcategory" => &$category ));
            if( !$categories->updateMyLinksCategory( $category )) {
            	$this->_view = new AdminLinkCategoriesListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_updating_link_category"));
                $this->setCommonData();

                return false;
            }
			$this->notifyEvent( EVENT_POST_LINK_CATEGORY_UPDATE, Array( "linkcategory" => &$category ));			
			
			// clear the cache
			CacheControl::resetBlogCache( $this->_blogInfo->getId(), false );

            $this->_view = new AdminLinkCategoriesListView( $this->_blogInfo );
            $this->_view->setSuccessMessage( $this->_locale->pr("link_category_updated_ok", $category->getName()));
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>