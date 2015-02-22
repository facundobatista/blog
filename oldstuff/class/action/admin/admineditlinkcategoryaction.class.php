<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminlinkcategorieslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/mylinkscategories.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows a form to change the settings of the link category
     */
    class AdminEditLinkCategoryAction extends AdminAction 
	{

    	var $_categoryId;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminEditLinkCategoryAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// data validation
			$this->registerFieldValidator( "categoryId", new IntegerValidator() );

			$view = new AdminLinkCategoriesListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_incorrect_link_category_id"));
			$this->setValidationErrorView( $view );
			
			// permission checks
			$this->requirePermission( "update_link_category" );
        }
        
        /**
         * Carries out the specified action
         */
        function perform()
        {
        	// fetch the category
			$this->_categoryId = $this->_request->getValue( "categoryId" );
            $categories = new MyLinksCategories();
            $category   = $categories->getMyLinksCategory( $this->_categoryId, $this->_blogInfo->getId());
            // show an error if we couldn't fetch the category
            if( !$category ) {
                $this->_view->setErrorMessage( $this->_locale->tr("error_fetching_link_category"));
                $this->setCommonData();

                return false;
            }
			$this->notifyEvent( EVENT_LINK_CATEGORY_LOADED, Array( "linkcategory" => &$category ));
            // otherwise show the form to edit its fields
        	$this->_view = new AdminTemplatedView( $this->_blogInfo, "editlinkcategory" );
			$this->_view->setValue( "linkCategoryName", $category->getName());
			$this->_view->setValue( "linkCategoryId", $category->getId());
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>
