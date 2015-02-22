<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/adminview.class.php" );


    /**
     * \ingroup View
     * @private
     *	
     * Generic view to be used by those classes that do not really need a customized view...
     * which is almost all of them. This one simply loads a template from disk. The name of the template
     * must be the parameter from disk.
     */
    class AdminTemplatedView extends AdminView 
	{

    	var $_templateName;

    	/**
         * This initializes the class, but normally we'll only have to initialize the parent
         */
        function AdminTemplatedView( $blogInfo, $templateName )
        {
        	$this->AdminView( $blogInfo );

            $this->_templateName = $templateName;
        }

        /**
         * Renders the view. It simply gets all the parameters we've been adding to it
         * and puts them in the context of the template renderer so that they can be accessed
         * as normal parameters from within the template
         */
        function render()
        {
			parent::render();
		
        	$template = $this->_templateService->AdminTemplate( $this->_templateName, $this->_blogInfo );
            // assign all the values
            $template->assign( $this->_params->getAsArray());           

            // and send the results after asking plugins if they need to process anything
            $output = $template->fetch();
            $this->notifyEvent( EVENT_PROCESS_BLOG_ADMIN_TEMPLATE_OUTPUT, Array( 'content' => &$output, 'template' => $this->_templateName ));

            print( $output );
        }
    }
?>
