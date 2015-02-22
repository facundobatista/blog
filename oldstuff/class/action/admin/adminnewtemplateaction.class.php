<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/template/templatesets/templatesetstorage.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Shows a form to add a new locale file
     */
    class AdminNewTemplateAction extends AdminAction 
	{

    	function AdminNewTemplateAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->requireAdminPermission( "add_template" );
        }

        function perform()
        {
        	$this->_view = new AdminTemplatedView( $this->_blogInfo, "newglobaltemplate" );
        	$this->_view->setValue( "templateFolder", TemplateSetStorage::getBaseTemplateFolder());
            $this->setCommonData();

            return true;
        }
    }
?>
