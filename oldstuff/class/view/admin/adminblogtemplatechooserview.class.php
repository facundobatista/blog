<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/templatesets/templatesets.class.php" );
	
    /**
     * \ingroup View
     * @private
     *	
	 * shows a list with all the template sets that have been added to this blog
	 */
	class AdminBlogTemplateChooserView extends AdminTemplatedView
	{
	
		function AdminBlogTemplateChooserView( $blogInfo ) 
		{
			$this->AdminTemplatedView( $blogInfo, "chooser/blogtemplatechooser" );
		}
		
		function render()
		{
			$ts = new TemplateSets();
			// get all the template sets, without including the global ones
			$blogTemplateSets = $ts->getBlogTemplateSets( $this->_blogInfo->getId(), true );
			$this->setValue( "templates", $blogTemplateSets );
			
			parent::render();
		}
	}
?>