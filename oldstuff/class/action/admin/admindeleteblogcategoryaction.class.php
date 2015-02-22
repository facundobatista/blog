<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/blogcategories.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/emptyvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminblogcategorieslistview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Deletes a blog category from the database
     */
    class AdminDeleteBlogCategoryAction extends AdminAction 
	{

    	var $_categoryId;
        var $_categoryIds;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminDeleteBlogCategoryAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			$this->_mode = $actionInfo->getActionParamValue();
        	// get the array that contains the categories we'd like to delete
			if( $this->_mode == "deleteBlogCategory" ) 
				$this->registerFieldValidator( "categoryId", new IntegerValidator());
			else 
				$this->registerFieldValidator( "categoryIds", new ArrayValidator( new IntegerValidator()));
				
			$view = new AdminBlogCategoriesListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_incorrect_blog_category"));
			$this->setValidationErrorView( $view );	
			
			$this->requireAdminPermission( "update_blog_category" );	
        }

		/**
		 * @private
		 * removes categories from the database
		 */
		function _deleteBlogCategories()
		{
            $categories = new BlogCategories();
			
			$errorMessage = "";
			$successMessage = "";
			$totalOk = 0;
			
            foreach( $this->_categoryIds as $categoryId ) {
				// if there's only one blog category left, we can't do this
				if( $categories->getNumBlogCategories() == 1 ) {
					$errorMessage .= $this->_locale->tr( "error_cannot_delete_last_blog_category" );
					break;
				}
	
            	// get the category
                $category   = $categories->getBlogCategory( $categoryId );
				if( $category ) {
					// get how many blogs it has
					$numBlogs = $category->getNumBlogs( BLOG_STATUS_ALL );
					
					// fire the pre-event
					$this->notifyEvent( EVENT_PRE_DELETE_BLOG_CATEGORY, Array( "category" => &$category ));

					// if it has at least one we can't delete it because then it would break the
					// integrity of our data in the database...
					if( $numBlogs > 0 ) {
						$errorMessage .= $this->_locale->pr( "error_blog_category_has_blogs", $category->getName())."<br/>";
					}
					else {
						// if everything correct, we can proceed and delete it
						if( !$categories->deleteBlogCategory( $categoryId, $this->_blogInfo->getId()))
							$errorMessage .= $this->_locale->pr("error_deleting_blog_category")."<br/>";
						else {
							if( $totalOk < 2 )
								$successMessage .= $this->_locale->pr("blog_category_deleted_ok", $category->getName())."<br/>";
							else
								$successMessage = $this->_locale->pr( "blog_categories_deleted_ok", $totalOk );
								
							// fire the pre-event
							$this->notifyEvent( EVENT_POST_DELETE_BLOG_CATEGORY, Array( "category" => &$category ));
						}
					}
				}
				else {
					$errorMessage .= $this->_locale->pr("error_deleting_blog_category2", $categoryId)."<br/>";
				}
        	}
			
			// prepare the view and all the information it needs to know
			$this->_view = new AdminBlogCategoriesListView( $this->_blogInfo );
			if( $errorMessage != "" ) 
				$this->_view->setErrorMessage( $errorMessage );
			if( $successMessage != "" ) {
				// and clear the cache to avoid outdated information
				CacheControl::resetBlogCache( $this->_blogInfo->getId(), false );			
				$this->_view->setSuccessMessage( $successMessage );
			}
				
			$this->setCommonData();
			
			return true;
		}

        /**
         * Carries out the specified action
         */
        function perform()
        {
			// prepare the parameters.. If there's only one category id, then add it to
			// an array.
			if( $this->_mode == "deleteBlogCategory" ) {
				$this->_categoryIds = Array();
				$this->_categoryIds[] = $this->_request->getValue( "categoryId" );
			}
			else
				$this->_categoryIds = $this->_request->getValue( "categoryIds" );
			
            return $this->_deleteBlogCategories();
        }
    }
?>
