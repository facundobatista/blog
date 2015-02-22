<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows an empty admin page, just in case... :)
     */
    class AdminEmptyAction extends AdminAction 
	{

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminEmptyAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
        	// initialize the view
        	$this->_view = new AdminTemplatedView( $this->_blogInfo, "main" );
            $this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>
