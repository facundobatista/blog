<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );

    /**
     * \ingroup Action
     *
     * Extends the AdminAction class in a way that any class subclassing this one,
     * will check if the user has SITE_ADMIN privileges to be here. The implementation is
     * quite simple in the sense that if the UserInfo object that was trying to access
     * this action doesn't have enough privileges, the action will show a really
     * ugly message and all processing will stop. 
     *
     * Hopefully future versions will improve this area and show a friendlier message.
     */
    class SiteAdminAction extends AdminAction 
    {

    	function SiteAdminAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

            // we should check if the user has permissions to be here...
            if( !$this->_userInfo->isSiteAdmin()) {
            	print("Sorry, you don't have enough privileges to access this area." );
                die();
            }
        }
    }
?>
