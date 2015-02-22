<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/emptyvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminarticlecategorieslistview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Deletes an article category from the database
     */
    class AdminDeleteArticleCategoryAction extends AdminAction 
	{

    	var $_categoryId;
        var $_categoryIds;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminDeleteArticleCategoryAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			$this->_mode = $actionInfo->getActionParamValue();
        	// get the array that contains the categories we'd like to delete
			if( $this->_mode == "deleteArticleCategory" ) 
				$this->registerFieldValidator( "categoryId", new IntegerValidator());
			else 
				$this->registerFieldValidator( "categoryIds", new ArrayValidator( new IntegerValidator()));
				
			$view = new AdminArticleCategoriesListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_incorrect_category_id"));
			$this->setValidationErrorView( $view );		
        }

		/**
		 * @private
		 * removes categories from the database
		 */
		function _deleteArticleCategories()
		{
            $categories = new ArticleCategories();
			
			$errorMessage = "";
			$successMessage = "";
			$totalOk = 0;
			
            foreach( $this->_categoryIds as $categoryId ) {
            	// get the category
                $category   = $categories->getCategory( $categoryId, $this->_blogInfo->getId());
				if( $category ) {
					// get how many articles it has
					//$numArticles = $categories->getNumArticlesCategory( $categoryId );
					$numArticles = $category->getNumArticles( POST_STATUS_ALL );
					
					// fire the pre-event
					$this->notifyEvent( EVENT_PRE_CATEGORY_DELETE, Array( "category" => &$category ));

					// if it has at least one we can't delete it because then it would break the
					// integrity of our data in the database...
					if( $numArticles > 0 ) {
						$errorMessage .= $this->_locale->pr( "error_category_has_articles", $category->getName())."<br/>";
					}
					else {
						// if everything correct, we can proceed and delete it
						if( !$categories->deleteCategory( $categoryId, $this->_blogInfo->getId()))
							$errorMessage .= $this->_locale->pr("error_deleting_category")."<br/>";
						else {
							if( $totalOk < 2 )
								$successMessage .= $this->_locale->pr("category_deleted_ok", $category->getName())."<br/>";
							else
								$successMessage = $this->_locale->pr( "categories_deleted_ok", $totalOk );
								
							// fire the pre-event
							$this->notifyEvent( EVENT_POST_CATEGORY_DELETE, Array( "category" => &$category ));
						}
					}
				}
				else {
					$errorMessage .= $this->_locale->pr("error_deleting_category2", $categoryId)."<br/>";
				}
        	}
			
			// prepare the view and all the information it needs to know
			$this->_view = new AdminArticleCategoriesListView( $this->_blogInfo );
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
			if( $this->_mode == "deleteArticleCategory" ) {
				$this->_categoryIds = Array();
				$this->_categoryIds[] = $this->_request->getValue( "categoryId" );
			}
			else
				$this->_categoryIds = $this->_request->getValue( "categoryIds" );
			
            return $this->_deleteArticleCategories();
        }
    }
?>
