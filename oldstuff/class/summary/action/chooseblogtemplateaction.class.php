<?php

	lt_include( PLOG_CLASS_PATH."class/summary/action/registeraction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/templatenamevalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
    lt_include( PLOG_CLASS_PATH."class/summary/view/summaryview.class.php" );


	/**
	 * shows a form where users can choose a new blog template
	 * for their blog, to start with
	 */
    class ChooseBlogTemplateAction extends RegisterAction 
	{
        function ChooseBlogTemplateAction( $actionInfo, $request )
        {
        	$this->RegisterAction( $actionInfo, $request );
        	
        	$this->registerFieldValidator( "templateId", new TemplateNameValidator());
        	$this->setValidationErrorView( new BlogTemplateChooserView());
        }

		function perform()
		{
			// save data to the session
	    	SessionManager::setSessionValue( "templateId", $this->_request->getValue( "templateId" ));			
	
			$this->setCommonData();
		}
    }
?>