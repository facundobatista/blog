<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Shows a form to add a new locale file
     */
    class AdminNewLocaleAction extends AdminAction 
    {

    	function AdminNewLocaleAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->requireAdminPermission( "add_locale" );
        }

        function perform()
        {
        	$this->_view = new AdminTemplatedView( $this->_blogInfo, "newlocale" );
            $this->setCommonData();

            return true;
        }
    }
?>
