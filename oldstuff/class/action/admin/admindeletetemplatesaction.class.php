<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/template/templatesets/templatesetstorage.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminsitetemplateslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );	

    /**
     * \ingroup Action
     * @private
     *
     * Removes global templates from disk.
     */
    class AdminDeleteTemplatesAction extends AdminAction
    {

    	var $_templateIds;
    	var $_op;

        function AdminDeleteTemplatesAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			// data validation stuff
        	$this->_op = $actionInfo->getActionParamValue();
        	if( $this->_op == "deleteTemplate" )
        		$this->registerFieldValidator( "templateId", new StringValidator());
        	else
        		$this->registerFieldValidator( "templateIds", new ArrayValidator( new StringValidator()));
        	$view = new AdminSiteTemplatesListView( $this->_blogInfo );
        	$view->setErrorMessage( $this->_locale->tr("error_no_templates_selected"));
        	$this->setValidationErrorView( $view );

			$this->requireAdminPermission( "update_template" );
        }

        function perform()
        {
        	if( $this->_op == "deleteTemplate" ) {
        		$templateId = $this->_request->getValue( "templateId" );
        		$this->_templateIds = Array();
        		$this->_templateIds[] = $templateId;
        	}
        	else
        		$this->_templateIds = $this->_request->getValue( "templateIds" );

        	// carry out the
        	$this->_deleteTemplates();
        }

        function _deleteTemplates()
        {
        	$ts = new TemplateSetStorage();

        	$errorMessage = "";
        	$successMessage = "";
        	$totalOk = 0;
			$f = new HtmlFilter();	

        	// get the id of the default template
        	$defaultTemplate = $this->_config->getValue( "default_template" );

            foreach( $this->_templateIds as $templateId ) {
				$templateId = $f->filter( $templateId );
	
            	// we can't remove the default template
            	if( $defaultTemplate ==$templateId )
            		$errorMessage .=$this->_locale->pr( "error_template_is_default", $templateId)."<br/>";
            	else {
            		// if it's not the default, then try to really remove it from disk
					if( !$ts->removeGlobalTemplate( $templateId ))
						$errorMessage .= $this->_locale->pr("error_removing_template", $templateId )."<br/>";
					else {
						$totalOk++;
						if( $totalOk < 2 )
							$successMessage = $this->_locale->pr("template_removed_ok", $templateId);
						else
							$successMessage = $this->_locale->pr( "templates_removed_ok", $totalOk );
					}
				}
            }

            // create the view and show some feedback
            $this->_view = new AdminSiteTemplatesListView( $this->_blogInfo );
			if( $errorMessage != "" ) $this->_view->setErrorMessage( $errorMessage );
			if( $successMessage != "" ) $this->_view->setSuccessMessage( $successMessage );
            $this->setCommonData();

            return true;
        }
    }
?>
