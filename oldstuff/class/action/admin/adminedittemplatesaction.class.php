<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminsitetemplateslistview.class.php" );

    /**
     * \ingroup Action
     * @private
     */
    class AdminEditTemplatesAction extends AdminAction 
    {

        function AdminEditTemplatesAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			$this->requireAdminPermission( "view_templates" );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
			$this->_view = new AdminSiteTemplatesListView( $this->_blogInfo );
            $this->setCommonData();

            return true;
        }
    }
?>
