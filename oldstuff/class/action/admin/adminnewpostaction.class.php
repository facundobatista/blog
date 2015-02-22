<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminnewpostview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );    
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that adds a new post to the database. It also lets the user
     * preview the article before posting it.
     */
    class AdminNewPostAction extends AdminAction 
	{

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminNewPostAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
        	$this->registerFieldValidator( "postText", new StringValidator( true ), true );
        	$this->registerFieldValidator( "sendTrackbacks", new IntegerValidator(), true );

			// security checks
			$this->requirePermission( "add_post" );
        }
                
        /**
         * Carries out the specified action
         */
        function perform()
        {
	        $blogSettings = $this->_blogInfo->getSettings();
	        $categories = new ArticleCategories();
            $blogCategories = $categories->getBlogCategories( $this->_blogInfo->getId() );
            // but make sure that we have at least one!
            if( count($blogCategories) == 0) {
            	$this->_view = new AdminTemplatedView( $this->_blogInfo, "newpostcategory" );
                $this->_view->setSuccessMessage( $this->_locale->tr("error_must_have_one_category"));
                $this->setCommonData();
                return false;
            }	        
	        
        	// initialize the view
        	$this->_view = new AdminNewPostView( $this->_blogInfo );
            $this->_view->setValue( "sendNotification", $blogSettings->getValue( "default_send_notification" ));

			// default global article category id
			$config =& Config::getConfig();
			$this->_view->setValue( "globalArticleCategoryId", $config->getValue( "default_global_article_category_id", 0 ));

			$this->_view->setValue( "sendTrackbacks", $this->_request->getValue( "sendTrackbacks" ) ? 1 : 0);
            
            $postText = $this->_request->getValue( "postText" );
			
            $postText = Textfilter::filterJavaScript( $postText );
            $postText = trim(Textfilter::xhtmlize( $postText ));

			$this->_view->setValue( "postText", $postText );

            $this->setCommonData();


            // better to return true if everything fine
            return true;
        }
    }
?>
