<?php

	lt_include( PLOG_CLASS_PATH."class/controller/controller.class.php" );
	
	$_plogBlogController_running = null;

    /**
     * \ingroup Controller
     *
     * Extends the Controller class so that the operation of loading the
     * file with the mappings is done automatically. The default action parameter becomes
     * "op" and the file class/controller/controllermap.class.php will be used as the default
     * action class map.
     *
     * You will rarely need to use this class in real life, see the main Controller class.
 	 *
 	 * @see Controller
     */
    class BlogController extends Controller 
    {

    	/**
         * Constructor. Automatically loads the maps
         */
		function BlogController()
        {
			$actionMap = $this->_loadActionMaps();
            $defaultActionParam = "op";
            
            global $_plogBlogController_running;
            $_plogBlogController_running = true;            

            $this->Controller( $actionMap, $defaultActionParam );
			$this->setActionFolderPath( PLOG_CLASS_PATH."class/action/" );
        }
        
        /**
         * If a plugin adds an action
         * for the public side and an action for the admin side, the action will be accessible via
         * both index.php?op=theActionName and admin.php?op=theActionName. We will use
         * use a global variable that is initialized when the constructor of
         * this class is called. If that variable is set to 'true', then it means that our constructor
         * was initialized and that we can go ahead and add the action. If not, then we skip the whole
         * thing and the action is not added.
         *
         * This method should therefore be called when adding new actions but <b>only for the public side</b>. In
         * case of plugins, please use PluginBase::registerBlogAction() and PluginBase::registerAdminAction()
         * which will nicely hide all these things from you.
         *         
         * @param actionKey Action key to add
         * @param actionClass Action class to which the class will map
         * @see Controller
         */        
        function registerAction( $actionKey, $actionClass )
        {
            global $_plogBlogController_running;
            
            if( $_plogBlogController_running )
                Controller::registerAction( $actionKey, $actionClass );
            
            return true;
        }        

        /**
         * Loads the maps
         *
         * @private
         */
        function _loadActionMaps()
        {
                // Note: this is an acceptable use of include()
			include( PLOG_CLASS_PATH."class/controller/controllermap.properties.php" );

            return $actions;
        }
    }
?>
