<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admincustomfieldslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfields.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Custom fields that have been defined so far 
     */
    class AdminBlogCustomFieldsAction extends AdminAction 
	{

        function AdminBlogCustomFieldsAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->requirePermission( "view_custom_fields" );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
			// the view will do everything for us anyway... :)
			$this->_view = new AdminCustomFieldsListView( $this->_blogInfo );
			$this->setCommonData();
        }
    }
?>