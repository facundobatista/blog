<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/templatesets/templatesets.class.php" );
	
    /**
     * \ingroup View
     * @private
     *	
	 * shows a list with all the template sets that have been added to this blog
	 */
	class AdminBlogTemplateSetsListView extends AdminTemplatedView
	{
	
		function AdminBlogTemplateSetsListView( $blogInfo ) 
		{
			$this->AdminTemplatedView( $blogInfo, "blogtemplates" );
		}
		
		function render()
		{
			$ts = new TemplateSets();
			// get all the template sets, without including the global ones
			$blogTemplateSets = $ts->getBlogTemplateSets( $this->_blogInfo->getId(), false );
			$this->setValue( "templates", $blogTemplateSets );
			
			parent::render();
		}
	}
?>