<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );	

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows a form to add a link for the blogroll feature
     */
    class AdminControlCenterAction extends AdminAction 
	{

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminControlCenterAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
        	// create the view, which will take care of fetching the right data
        	$this->_view = new AdminTemplatedView( $this->_blogInfo, "controlcenter" );
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>
