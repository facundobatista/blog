<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminglobalsettingslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );	

    /**
     * \ingroup Action
     * @private
     *
     * List of all the available settings for the site
     */
    class AdminGlobalSettingsAction extends AdminAction 
	{
        var $_show;
        
    	function AdminGlobalSettingsAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->requireAdminPermission( "view_global_settings" );
        }

        function validate()
        {
            $this->_show = $this->_request->getValue( "show" );
	        $strVal = new StringValidator();
            if(!$strVal->validate( $this->_show ) ){
                    // view can take care of setting a valid value
                $this->_show = "";
            }

            return (parent::validate());
        }
        
        function perform()
        {
            // if no problem, continue
            $this->_view = new AdminGlobalSettingsListView( $this->_blogInfo, $this->_show );
            $this->setCommonData();

            return true;
        }
    }
?>
