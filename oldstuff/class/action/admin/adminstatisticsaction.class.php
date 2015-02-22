<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminreferrersview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows statistics
     */
    class AdminStatisticsAction extends AdminAction 
	{
	
		var $_page;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminStatisticsAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->requirePermission( "view_blog_stats" );
        }
        
        /**
         * Carries out the specified action
         */
        function perform()
        {
			// create the view
			$this->_view = new AdminReferrersView( $this->_blogInfo );
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>
