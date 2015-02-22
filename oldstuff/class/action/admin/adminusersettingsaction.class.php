<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminuserprofileview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows a form to change the settings of the current user.
     */
    class AdminUserSettingsAction extends AdminAction 
	{

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminUserSettingsAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
        	$this->_view = new AdminUserProfileView( $this->_blogInfo, $this->_userInfo );
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>
