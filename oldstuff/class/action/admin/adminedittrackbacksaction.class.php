<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/admineditcommentsaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminarticletrackbackslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminpostslistview.class.php" );
	
    /**
     * \ingroup Action
     * @private
     *
     * Action that shows a list of all the trackbacks for a given post
     */
    class AdminEditTrackbacksAction extends AdminEditCommentsAction 
	{

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminEditTrackbacksAction( $actionInfo, $request )
        {
        	$this->AdminEditCommentsAction( $actionInfo, $request );
			
			$this->_viewClass = "AdminArticleTrackbacksListView";
			
			$this->requirePermission( "view_trackbacks" );
        }
    }
?>
