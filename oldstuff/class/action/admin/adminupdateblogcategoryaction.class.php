<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/blogcategories.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminblogcategorieslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Updates an article category.
     */
    class AdminUpdateBlogCategoryAction extends AdminAction 
	{

    	var $_categoryName;
        var $_categoryUrl;
        var $_categoryId;
		var $_categoryDescription;     

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminUpdateBlogCategoryAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// data validation settings
			$this->registerFieldValidator( "categoryName", new StringValidator());
			$this->registerFieldValidator( "categoryId", new IntegerValidator());
			$this->registerFieldValidator( "categoryDescription", new StringValidator(), true );
			$errorView = new AdminTemplatedView( $this->_blogInfo, "editblogcategory" );
			$errorView->setErrorMessage( $this->_locale->tr("error_updating_article_category" ));
			$this->setValidationErrorView( $errorView );
			
			$this->requireAdminPermission( "update_blog_category" );			
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
			// get the data from the form
        	$this->_categoryName = $this->_request->getValue( "categoryName" );
            $this->_categoryId   = $this->_request->getValue( "categoryId" );
			$this->_categoryDescription = $this->_request->getValue( "categoryDescription" );
		
        	// fetch the category we're trying to update
            $categories = new BlogCategories();
            $category   = $categories->getBlogCategory( $this->_categoryId );
            if( !$category ) {
            	$this->_view = new AdminBlogCategoriesListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_fetching_category"));
                $this->setCommonData();

                return false;
            }
			
			// fire the pre-event
			$this->notifyEvent( EVENT_PRE_UPDATE_BLOG_CATEGORY, Array( "category" => &$category ));			

            // update the fields
            $category->setName( $this->_categoryName );
			$category->setDescription( $this->_categoryDescription );
			
			// this is view we're going to use to show our messages
			$this->_view = new AdminBlogCategoriesListView( $this->_blogInfo );			
			
            if( !$categories->updateBlogCategory( $category )) {
                $this->_view->setErrorMessage( $this->_locale->tr("error_updating_article_category"));
            }
			else {
				// if everything fine, load the list of categories
				$this->_view->setSuccessMessage( $this->_locale->pr("article_category_updated_ok", $category->getName()));
				
				// fire the post-event
				$this->notifyEvent( EVENT_POST_UPDATE_BLOG_CATEGORY, Array( "category" => &$category ));			
				
				// clear the cache
				CacheControl::resetBlogCache( $this->_blogInfo->getId());			
			}
			
			$this->setCommonData();			
			
            // better to return true if everything fine
            return true;
        }
    }
?>