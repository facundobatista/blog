<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminxmlview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that adds a new article category to the database.
     */
    class AdminAddArticleCategoryAjaxAction extends AdminAction 
	{

    	var $_categoryName;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminAddArticleCategoryAjaxAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

            $this->registerFieldValidator( "categoryName", new StringValidator());

	        $view = new AdminXmlView( $this->_blogInfo, "response" );				
	        $view->setValue( "method", "addCategoryAjax" );
            $view->setValue( "success", "0" );
            $view->setValue( "message", $this->_locale->tr("error_adding_article_category") );

            $this->setValidationErrorView( $view );

			$this->requirePermission( "add_category" );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
            // fetch the input values
        	$this->_categoryName     = Textfilter::filterAllHTML($this->_request->getValue( "categoryName" ));

                // create the object...
            $categories = new ArticleCategories();
            $category   = new ArticleCategory( $this->_categoryName,
                                               "",
                                               $this->_blogInfo->getId(),
                                               1,
											   $this->_categoryName,
											   0,
											   Array() );
											   
			// fire the pre event...
			$this->notifyEvent( EVENT_PRE_CATEGORY_ADD, Array( "category" => &$category ));

			$this->_view = new AdminXmlView( $this->_blogInfo, "response" );				
			$this->_view->setValue( "method", "addCategoryAjax" );	
            
            // once we have built the object, we can add it to the database!
            $catId = $categories->addArticleCategory( $category );

            // once we have built the object, we can add it to the database
            $this->_view = new AdminXmlView( $this->_blogInfo, "response" );				
            $this->_view->setValue( "method", "addCategoryAjax" );
            if( $catId )
            {
            	$this->_view->setValue( "success", "1" );
            	$this->_view->setValue( "message", $this->_locale->pr("category_added_ok", $this->_categoryName) );           	
            	
                $result = '<id>'.$catId.'</id>';
                $result .= '<name>'.$this->_categoryName.'</name>';
                $this->_view->setValue( "result", $result );
				
				// fire the post event
				$this->notifyEvent( EVENT_POST_CATEGORY_ADD, Array( "category" => &$category ));
				
				// clear the cache if everything went fine
				CacheControl::resetBlogCache( $this->_blogInfo->getId(), false );							
			}             
            else
            {
            	$this->_view->setValue( "success", "0" );
            	$this->_view->setValue( "message", $this->_locale->tr("error_adding_article_category") );              	 
            }
                
            return true;	
        }
    }
?>