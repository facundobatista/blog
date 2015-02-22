<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Defines blog-wide custom fields
     */
    class AdminNewCustomFieldAction extends AdminAction 
	{

        function AdminNewCustomFieldAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->requirePermission( "add_custom_field" );
        }
        
        /**
         * Carries out the specified action
         */
        function perform()
        {
        	$this->_view = new AdminTemplatedView( $this->_blogInfo, "newcustomfield" );
			$this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>
