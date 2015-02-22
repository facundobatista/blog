<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminblogcategorieslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/blogcategories.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows a form to change the settings of the article category
     */
    class AdminEditBlogCategoryAction extends AdminAction 
	{

    	var $_categoryId;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminEditBlogCategoryAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// stuff for the data validation
			$this->registerFieldValidator( "categoryId", new IntegerValidator());
			$errorView = new AdminBlogCategoriesListView( $this->_blogInfo );
			$errorView->setErrorMessage( $this->_locale->tr("error_incorrect_category_id"));
			$this->setValidationErrorView( $errorView );
		
			$this->requireAdminPermission( "update_blog_category" );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
        	// fetch the category
			$this->_categoryId = $this->_request->getValue( "categoryId" );
            $categories = new BlogCategories();
            $category   = $categories->getBlogCategory( $this->_categoryId);
            // show an error if we couldn't fetch the category
            if( !$category ) {
            	$this->_view = new AdminBlogCategoriesListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_fetching_category") );
				$this->_view->setError( true );
                $this->setCommonData();
                return false;
            }
			
			$this->notifyEvent( EVENT_BLOG_CATEGORY_LOADED, Array( "category" => &$category ));			
            // otherwise show the form to edit its fields
        	$this->_view = new AdminTemplatedView( $this->_blogInfo, "editblogcategory" );
            $this->_view->setValue( "category", $category );
			$this->_view->setValue( "categoryName", $category->getName());
			$this->_view->setValue( "categoryDescription", $category->getDescription());
			$this->_view->setValue( "categoryId", $category->getId());
            $this->setCommonData();

            // better to return true if everything fine
           return true;
        }
    }
?>
