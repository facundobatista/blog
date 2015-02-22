<?php
	  /**
       * GLobal artical Category files added by Ameng(Ameng.vVlogger.com) 2005-06-20
       * version 1.0 
       * Changed from original article category.
       */
	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/globalarticlecategories.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminglobalarticlecategorieslistview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Deletes an article category from the database
     */
    class AdminDeleteGlobalArticleCategoryAction extends AdminAction 
	{

    	var $_categoryId;
        var $_categoryIds;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminDeleteGlobalArticleCategoryAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			$this->_mode = $actionInfo->getActionParamValue();
        	
        	// get the array that contains the categories we'd like to delete
			if( $this->_mode == "deleteGlobalArticleCategory" ) 
					$this->registerFieldValidator( "categoryId", new IntegerValidator());
			else 
					$this->registerFieldValidator( "categoryIds", new ArrayValidator( new IntegerValidator()));
				
			$view = new AdminGlobalArticleCategoriesListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_incorrect_global_category_id"));
			$this->setValidationErrorView( $view );		
			
			$this->requireAdminPermission( "update_global_category" );
        }

		/**
		 * @private
		 * removes categories from the database
		 */
		function _deleteGlobalArticleCategories()
		{
            $categories = new GlobalArticleCategories();
			
			$errorMessage = "";
			$successMessage = "";
			$totalOk = 0;
			
            foreach( $this->_categoryIds as $categoryId ) {
            	// get the category
            	$category   = $categories->getGlobalArticleCategory( $categoryId );
				
				if( $category ) {
					// get how many articles it has
					$numArticles = $category->getNumArticles( POST_STATUS_ALL );
										
					// fire the pre-event
					$this->notifyEvent( EVENT_PRE_DELETE_GLOBAL_CATEGORY, Array( "category" => &$category ));
					
					// if everything correct, we can proceed and delete it
					if( $numArticles > 0 ) {
						$errorMessage .= $this->_locale->pr( "error_global_category_has_articles", $category->getName())."<br/>";
					}
					else {									
						if( !$categories->deleteGlobalArticleCategory( $categoryId))
							$errorMessage .= $this->_locale->pr("error_deleting_global_category")."<br/>";
						else {
							if( $totalOk < 2 )
								$successMessage .= $this->_locale->pr("global_category_deleted_ok", $category->getName())."<br/>";
							else
								$successMessage = $this->_locale->pr( "global_categories_deleted_ok", $totalOk );
								
							// fire the pre-event
							$this->notifyEvent( EVENT_POST_DELETE_GLOBAL_CATEGORY, Array( "category" => &$category ));
						}
					}
				}
				else {
					$errorMessage .= $this->_locale->pr("error_deleting_global_category2", $categoryId)."<br/>";
				}
        	}
        				
			// prepare the view and all the information it needs to know
			$this->_view = new AdminGlobalArticleCategoriesListView( $this->_blogInfo );
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
			if( $this->_mode == "deleteGlobalArticleCategory" ) {
				$this->_categoryIds = Array();
				$this->_categoryIds[] = $this->_request->getValue( "categoryId" );
			}
			else
				$this->_categoryIds = $this->_request->getValue( "categoryIds" );
			
            return $this->_deleteGlobalArticleCategories();
        }
    }
?>
