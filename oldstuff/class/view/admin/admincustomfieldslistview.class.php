<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfields.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/pager/pager.class.php" );

    /**
     * \ingroup View
     * @private
     *	
	 * displays the list of custom fields that have been added
	 * to the blog, or an empty list of none has been added!
	 */
	class AdminCustomFieldsListView extends AdminTemplatedView
	{
		var $_page;
	
		function AdminCustomFieldsListView( $blogInfo )
		{
			$this->AdminTemplatedView( $blogInfo, "customfields" );

			// get the current page from the request
			$this->_page = $this->getCurrentPageFromRequest();
		}
		
		/**
		 * load the fields and pass them to the view
		 */
		function render()
		{
			// load the custom fields that have been defined so far
			$customFields = new CustomFields();
			$blogFields = $customFields->getBlogCustomFields( $this->_blogInfo->getId(), true, $this->_page, DEFAULT_ITEMS_PER_PAGE );
			$this->notifyEvent( EVENT_CUSTOM_FIELDS_LOADED, Array( "fields" => &$blogFields ));
			// and the total number of them too
			$numBlogFields = $customFields->getNumBlogCustomFields( $this->_blogInfo->getId());

			// create the pager
			$pager = new Pager( "?op=blogCustomFields&amp;page=",
					    $this->_page,
					    $numBlogFields,
					    DEFAULT_ITEMS_PER_PAGE );
			
			// and show them
			$this->setValue( "fields", $blogFields );
			$this->setValue( "pager", $pager );
			
			return parent::render();
		}
	}
?>