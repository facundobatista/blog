<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/adminview.class.php" );
	
    /**
     * \ingroup View
     * @private
     */	
	class AdminXmlView extends AdminView
	{
        var $_templateName;

        function AdminXmlView( $blogInfo, $templateName )
        {
            $this->AdminView( $blogInfo );
            $this->_templateName = $templateName;
			
			$this->setContentType( TEXT_XML_CONTENT_TYPE );
        }

        function render()
        {
			parent::render();
		
            $templateService = new TemplateService();
            $template = $templateService->Template( $this->_templateName, "admin/xml" );

            $template->assign( $this->_params->getAsArray());
            print $template->fetch();
        }
	}
?>