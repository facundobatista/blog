<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminadduserview.class.php" );

    /**
     * \ingroup Action
     * @private
     */
    class AdminCreateUserAction extends AdminAction 
    {

    	function AdminCreateUserAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->requireAdminPermission( "add_user" );
        }

        function perform()
        {
			// get a list of all the blogs available
            $this->_view = new AdminAddUserView( $this->_blogInfo );
            $this->setCommonData();

            return true;
        }
    }
?>