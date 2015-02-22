<?php

    lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminnewlinkview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/mylinkscategories.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/mylinkscategory.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows a form to add a link for the blogroll feature
     */
    class AdminNewLinkAction extends AdminAction 
    {

        /**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminNewLinkAction( $actionInfo, $request )
        {
            $this->AdminAction( $actionInfo, $request );

			$this->requirePermission( "add_link" );
        }
        
        /**
         * Carries out the specified action
         */
        function perform()
        {
            // fetch all the link categories
			$blogSettings = $this->_blogInfo->getSettings();
			$linkCategoriesOrder = $blogSettings->getValue( "link_categories_order", MYLINKS_CATEGORIES_NO_ORDER );
            $linkCategories = new MyLinksCategories();
            $blogLinkCategories = $linkCategories->getMyLinksCategories( $this->_blogInfo->getId(), $linkCategoriesOrder );			

            // if there is none, we should not be allowed to add any link!
            if( empty($blogLinkCategories)) {
                $this->_view = new AdminTemplatedView( $this->_blogInfo, "newlinkcategory" );
                $this->_view->setErrorMessage( $this->_locale->tr("error_must_have_one_link_category" ));
                $this->setCommonData();

                return false;
            }
			
			$this->notifyEvent( EVENT_LINK_CATEGORIES_LOADED, Array( "linkcategories" => &$blogLinkCategories ));			

            // else, put that in the template and continue
            $this->_view = new AdminNewLinkView( $this->_blogInfo );
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }        
    }
?>
