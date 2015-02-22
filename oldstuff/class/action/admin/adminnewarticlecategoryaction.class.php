<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that adds a new category for the articles to the database
     */
    class AdminNewArticleCategoryAction extends AdminAction 
    {

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminNewArticleCategoryAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->requirePermission( "add_category" );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
        	// initialize the view
        	$this->_view = new AdminTemplatedView( $this->_blogInfo, "newpostcategory" );
            $this->setCommonData();

            // fetch the categories
            $categories = new ArticleCategories();
			$blogSettings = $this->_blogInfo->getSettings();
			$categoriesOrder = $blogSettings->getValue( "categories_order" );
            $blogCategories = $categories->getBlogCategories( $this->_blogInfo->getId(), false, $categoriesOrder );
            $this->_view->setValue( "categories", $blogCategories );
			// this field should be true by default
			$this->_view->setValue( "categoryInMainPage", true );

            // better to return true if everything fine
            return true;
        }
    }
?>