<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminblogsettingsview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows a form to change the settings of the current blog.
     */
    class AdminBlogSettingsAction extends AdminAction 
	{

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminBlogSettingsAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->requirePermission( "update_blog" );
        }
        
        /**
         * Carries out the specified action
         */
        function perform()
        {
			$this->_view = new AdminBlogSettingsView( $this->_blogInfo );
            $this->setCommonData();
			
            // better to return true if everything fine
            return true;
        }
    }
?>
