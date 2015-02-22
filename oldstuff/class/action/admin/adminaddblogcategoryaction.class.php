<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/blogcategories.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminblogcategorieslistview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that adds a new article blog category
     */
    class AdminAddBlogCategoryAction extends AdminAction 
	{

    	var $_categoryName;
		var $_categoryDescription;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminAddBlogCategoryAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// register two validators
			$this->registerFieldValidator( "categoryName", new StringValidator());
			$this->registerFieldValidator( "categoryDescription", new StringValidator(), true );
			// and the view we should show in case there is a validation error
			$errorView = new AdminTemplatedView( $this->_blogInfo, "newblogcategory" );
			$errorView->setErrorMessage( $this->_locale->tr("error_adding_blog_category" ));			
			$this->setValidationErrorView( $errorView );

			$this->requireAdminPermission( "add_blog_category" );			
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
			// fetch the data, we already know it's valid and that we can trust it!
        	$this->_categoryName     = $this->_request->getValue( "categoryName" );
			$this->_categoryDescription = $this->_request->getValue( "categoryDescription" );
		
			// create the object...
            $categories = new BlogCategories();
            $category   = new BlogCategory( $this->_categoryName, $this->_categoryDescription );
											   
			// fire the pre event...
			$this->notifyEvent( EVENT_PRE_ADD_BLOG_CATEGORY, Array( "category" => &$category ));

            // once we have built the object, we can add it to the database!
            if( $categories->addBlogCategory( $category )) {
				if( $this->userHasPermission( "view_blog_categories", ADMIN_PERMISSION )) 
					$this->_view = new AdminBlogCategoriesListView( $this->_blogInfo );
				else
					$this->_view = new AdminTemplatedView( $this->_blogInfo, "newblogcategory" );
				
				$this->_view->setSuccess( true );
				$this->_view->setSuccessMessage( $this->_locale->pr("blog_category_added_ok", $category->getName()));
				
				// fire the post event
				$this->notifyEvent( EVENT_POST_ADD_BLOG_CATEGORY, Array( "category" => &$category ));
				
				// clear the cache if everything went fine
				CacheControl::resetBlogCache( $this->_blogInfo->getId(), false );														
				
				$this->setCommonData();
            }
            else {
				// if there was an error, we should say so... as well as not changing the view since
				// we're going back to the original view where we can add the category
				$this->_view->setError( true );
				$this->_view->setErrorMessage( $this->_locale->tr("error_adding_category" ));
				$this->setCommonData( true );
            }

            // better to return true if everything fine
            return true;
        }
    }
?>