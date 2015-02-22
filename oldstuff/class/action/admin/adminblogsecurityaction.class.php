<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Does nothing yet :)
     */
    class AdminBlogSecurityAction extends AdminAction {

        function AdminBlogSecurityAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
            $this->_view = new AdminTemplatedView( $this->_blogInfo, "blogsecurity" );
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>
