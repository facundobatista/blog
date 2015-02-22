<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminblogtemplatesetslistview.class.php" );

    /**
     * \ingroup Action
     * @private
     */
    class AdminEditBlogTemplatesAction extends AdminAction 
	{

        function AdminEditBlogTemplatesAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
        	
        	$this->requirePermission( "view_blog_templates" );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
            $this->_view = new AdminBlogTemplateSetsListView( $this->_blogInfo );
            $this->setCommonData();

            return true;
        }
    }
?>
