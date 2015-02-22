<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminsitelocaleslistview.class.php" );
	
    /**
     * \ingroup Action
     * @private
     *
	 * displays a list with all the locales available in this site
	 */
    class AdminSiteLocalesAction extends AdminAction 
	{

    	function AdminSiteLocalesAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->requireAdminPermission( "view_locales" );
        }

        function perform()
        {
            $this->_view = new AdminSiteLocalesListView( $this->_blogInfo );
            $this->setCommonData();
            
            return true;
        }
    }
?>