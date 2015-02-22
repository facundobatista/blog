<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/templatesets/templatesets.class.php" );
	
    /**
     * \ingroup View
     * @private
     */	
	class AdminSiteTemplatesListView extends AdminTemplatedView
	{
	
		function AdminSiteTemplatesListView( $blogInfo )
		{
			$this->AdminTemplatedView( $blogInfo, "sitetemplates" );
		}
		
		function render()
		{
			// get a list with all the global template sets
        	$ts = new TemplateSets();
            $globalTemplates = $ts->getGlobalTemplateSets();
            $this->setValue( "templates", $globalTemplates );
            
            $defaultTs = $ts->getDefaultTemplateSet();
            
            $this->setValue( "defaultTemplate", $defaultTs->getName());
		
			parent::render();
		}
	}
?>