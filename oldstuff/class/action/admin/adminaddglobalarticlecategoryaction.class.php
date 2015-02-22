<?php

 	  /**
       * GLobal artical Category files added by Ameng(Ameng.vVlogger.com) 2005-06-20
       * version 1.0 
       * Changed from original article category.
       */

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/globalarticlecategories.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminglobalarticlecategorieslistview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that adds a new article category to the database.
     */
    class AdminAddGlobalArticleCategoryAction extends AdminAction 
	{

    	var $_categoryName;
		var $_categoryDescription;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminAddGlobalArticleCategoryAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// register two validators
			$this->registerFieldValidator( "categoryName", new StringValidator());
			$this->registerFieldValidator( "categoryDescription", new StringValidator(), true );
			// and the view we should show in case there is a validation error
			$errorView = new AdminTemplatedView( $this->_blogInfo, "newglobalarticlecategory" );
			$errorView->setErrorMessage( $this->_locale->tr("error_adding_global_article_category" ));			
			$this->setValidationErrorView( $errorView );
				
			$this->requireAdminPermission( "add_global_category" );				
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
            $categories = new GlobalArticleCategories();
            $category   = new GlobalArticleCategory( $this->_categoryName,
                                              	     $this->_categoryDescription );
											   
			// fire the pre event...
			$this->notifyEvent( EVENT_PRE_ADD_GLOBAL_CATEGORY, Array( "category" => &$category ));

            // once we have built the object, we can add it to the database!
             if( $categories->addGlobalArticleCategory( $category )) {
				// if everything went fine, transfer the execution flow to the action that
				// lists all the article categories... without forgetting that we should let the
				// next class know that we actually added a category alongside a message
				// and the category that we just added!
				if( $this->userHasPermission( "view_global_categories", ADMIN_PERMISSION ))
					$this->_view = new AdminGlobalArticleCategoriesListView( $this->_blogInfo );
				else
					$this->_view = new AdminTemplatedView( $this->_blogInfo, "newglobalarticlecategory" );
				$this->_view->setSuccess( true );
				$this->_view->setSuccessMessage( $this->_locale->pr("global_category_added_ok", $category->getName()));
				
				// fire the post event
				$this->notifyEvent( EVENT_POST_ADD_GLOBAL_CATEGORY, Array( "category" => &$category ));
				
				$this->setCommonData();				
            }
            else {
				// if there was an error, we should say so... as well as not changing the view since
				// we're going back to the original view where we can add the category
				$this->_view->setError( true );
				$this->_view->setErrorMessage( $this->_locale->tr("error_adding_global_article_category" ));
				$this->setCommonData( true );
            }

            // better to return true if everything fine
            return true;
        }
    }
?>