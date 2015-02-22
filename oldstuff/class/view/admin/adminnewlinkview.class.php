<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/mylinkscategories.class.php" );    


    /**
     * \ingroup View
     * @private
     *
     * shows the view to add a new link
     */
    class AdminNewLinkView extends AdminTemplatedView 
	{
		var $_templateName;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminNewLinkView( $blogInfo, $params = Array())
        {

        	$this->AdminTemplatedView( $blogInfo, "newlink" );
        }

        /**
         * Carries out the specified action
         */
        function render()
        {
            // get all the link categories but we have to respect the order that the user asked
			$blogSettings = $this->_blogInfo->getSettings();
			$order = $blogSettings->getValue( 'link_categories_order', MYLINKS_CATEGORIES_NO_ORDER );			
            $linkCategories = new MyLinksCategories();
            $blogLinkCategories = $linkCategories->getMyLinksCategories( $this->_blogInfo->getId(), $order );
			$this->notifyEvent( EVENT_LINK_CATEGORIES_LOADED, Array( "linkcategories" => &$blogLinkCategories ));

			// put the data in the view
            $this->setValue( "linkcategories", $blogLinkCategories );

			parent::render();
        }
    }
?>