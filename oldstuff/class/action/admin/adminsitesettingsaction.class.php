<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * This one only shows some statistics about the site...
     */
    class AdminSiteSettingsAction extends AdminAction 
    {   
    	function AdminSiteSettingsAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
        }

        function perform()
        {
        	$this->_view = new AdminTemplatedView( $this->_blogInfo, "adminsettings" );
            $this->setCommonData();

            return true;
        }
    }
?>
