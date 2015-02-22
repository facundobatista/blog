<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articlecategory.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminarticlecategorieslistview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that adds a new article category to the database.
     */
    class AdminAddArticleCategoryAction extends AdminAction 
	{

    	var $_categoryName;
		var $_categoryDescription;
		var $_categoryInMainPage;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminAddArticleCategoryAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// register two validators
			$this->registerFieldValidator( "categoryName", new StringValidator());
			$this->registerFieldValidator( "categoryDescription", new StringValidator(), true );
			$this->registerFieldValidator( "categoryInMainPage", new IntegerValidator(), true );

			// and the view we should show in case there is a validation error
			$errorView = new AdminTemplatedView( $this->_blogInfo, "newpostcategory" );
			$errorView->setErrorMessage( $this->_locale->tr("error_adding_article_category" ));			
			$this->setValidationErrorView( $errorView );
			
			$this->requirePermission( "add_category" );

        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
			// fetch the data, we already know it's valid and that we can trust it!
        	$this->_categoryName     = Textfilter::filterAllHTML($this->_request->getValue( "categoryName" ));
            $this->_categoryInMainPage = Textfilter::checkboxToBoolean($this->_request->getValue( "categoryInMainPage" ));
			$this->_categoryDescription = Textfilter::filterAllHTML($this->_request->getValue( "categoryDescription" ));
		
			// create the object...
            $categories = new ArticleCategories();
            $category   = new ArticleCategory( $this->_categoryName,
                                               "",
                                               $this->_blogInfo->getId(),
                                               $this->_categoryInMainPage,
											   $this->_categoryDescription,
											   0,
											   Array() );
											   
			// fire the pre event...
			$this->notifyEvent( EVENT_PRE_CATEGORY_ADD, Array( "category" => &$category ));

            // once we have built the object, we can add it to the database!
            if( $categories->addArticleCategory( $category )) {
				// if everything went fine, transfer the execution flow to the action that
				// lists all the article categories... without forgetting that we should let the
				// next class know that we actually added a category alongside a message
				// and the category that we just added!
				if( $this->userHasPermission( "view_categories" )) 
					$this->_view = new AdminArticleCategoriesListView( $this->_blogInfo );
				else
					$this->_view = new AdminTemplatedView( $this->_blogInfo, "newpostcategory" );
					
				$this->_view->setSuccess( true );
				$this->_view->setSuccessMessage( $this->_locale->pr("category_added_ok", $category->getName()));
				
				// fire the post event
				$this->notifyEvent( EVENT_POST_CATEGORY_ADD, Array( "category" => &$category ));
				
				// clear the cache if everything went fine
				CacheControl::resetBlogCache( $this->_blogInfo->getId(), false );														
				
				$this->setCommonData();				
            }
            else {
                    // display an error
                $this->_view = new AdminTemplatedView( $this->_blogInfo, "newpostcategory" );
				$this->_view->setError( true );
				$this->_view->setErrorMessage(
                    $this->_locale->tr("error_adding_article_category" ));
				$this->setCommonData( true );
                return false;
            }

            // better to return true if everything fine
            return true;
        }
    }
?>