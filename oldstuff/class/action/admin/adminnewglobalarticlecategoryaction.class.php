<?php
      /**
       * GLobal artical Category files added by Ameng(Ameng.vVlogger.com) 2005-06-20
       * version 1.0 
       * Changed from original article category.
       */
     

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that adds a new category for the articles to the database
     */
    class AdminNewGlobalArticleCategoryAction extends AdminAction 
    {

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminNewGlobalArticleCategoryAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->requireAdminPermission( "add_global_category" );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
        	// initialize the view
          $this->_view = new AdminTemplatedView( $this->_blogInfo, "newglobalarticlecategory" );
          $this->setCommonData();
        }
    }
?>