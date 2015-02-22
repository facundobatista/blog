<?php

	lt_include( PLOG_CLASS_PATH."class/summary/view/summaryview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/templatesets/templatesets.class.php" );
	
	/**
	 * shows the final view of the registration process where users get to choose
	 * a template set for their blog
	 */
	class BlogTemplateChooserView extends SummaryView
	{
	
		function BlogTemplateChooserView()
		{
			$this->SummaryView( "registerstep3" );
		}
		
		function render()
		{
			// get a list with all the templates so that users can choose
			$sets = new TemplateSets();
			$templates = $sets->getGlobalTemplateSets();
			$this->setValue( "templates", $templates );
			
			// and render the rest of the contents of the view
			parent::render();
		}
	}
?>