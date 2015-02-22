<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminblogtemplatesetslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/templatesets/templatesetstorage.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );
		
    /**
     * \ingroup Action
     * @private
     *
     * Removes a global template from disk.
     */
    class AdminDeleteBlogTemplateAction extends AdminAction 
	{

    	var $_templateIds;

        function AdminDeleteBlogTemplateAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			$this->_op = $actionInfo->getActionParamValue();
			if( $this->_op == "deleteBlogTemplate" )
				$this->registerFieldValidator( "templateId", new StringValidator());
			else
				$this->registerFieldValidator( "templateIds", new ArrayValidator( new StringValidator()));
			$view = new AdminBlogTemplateSetsListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_no_templates_selected" ));
			$this->setValidationErrorView( $view );
			
			$this->requirePermission( "update_blog_template" );
        }
		
		function perform()
		{
			if( $this->_op == "deleteBlogTemplate" ) {
				$this->_templateId = $this->_request->getValue( "templateId" );
				$this->_templateIds = Array();
				$this->_templateIds[] = $this->_templateId;
			}
			else
				$this->_templateIds = $this->_request->getValue( "templateIds" );
				
			$this->_deleteTemplates();
		}

        /**
         * Carries out the specified action
		 * @private
         */
        function _deleteTemplates()
        {
        	$ts = new TemplateSetStorage();
			$errorMessage = "";
			$successMessage = "";
			$totalOk = 0;
			$blogTemplate = $this->_blogInfo->getTemplateSet();
			$f = new HtmlFilter();

            foreach( $this->_templateIds as $templateId ) {
				$templateId = $f->filter( $templateId );
	
				// we can't remove the current template!
				if( $blogTemplate->getName() == $templateId ) {
					$errorMessage .= $this->_locale->pr("error_template_is_current", $templateId )."<br/>";
				}
				else {
					// otherwise, let's proceed
					if( !$ts->removeTemplate( $templateId, $this->_blogInfo->getId() ))
						$errorMessage .= $this->_locale->pr("error_removing_template", $templateId )."<br/>";
					else {
						$totalOk++;					
						if( $totalOk < 2 )
							$successMessage = $this->_locale->pr("template_removed_ok", $templateId);
						else
							$successMessage = $this->_locale->pr("templates_removed_ok", $totalOk );
					}
				}
            }

            $this->_view = new AdminBlogTemplateSetsListView( $this->_blogInfo );
            if( $errorMessage != "" ) $this->_view->setErrorMessage( $errorMessage );
            if( $successMessage != "" ) $this->_view->setSuccessMessage( $successMessage );			
            $this->setCommonData();
            
            // We should update the session too, or we will get data dirty
            lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
            $blogs = new Blogs();
            $blogInfo = $blogs->getBlogInfo( $this->_blogInfo->getId() );
            $this->_session->setValue( "blogInfo", $blogInfo );
            $this->saveSession();

            return true;
        }
    }
?>