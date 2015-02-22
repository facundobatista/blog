<?php

    /**
     * \defgroup Controller
     *
     * The controller is the central piece of the MVC pattern, and the object that takes care of
     * transferrign the process flow to the right action class based on a certain value in the 
     * incoming request.
     *
     * This package includes the basic Controller class, as well as the BlogController and
     * AdminController ones, which implement a bit of extra logic for loading the maps and so on.
     *
     * There is also a dynamic class loader (ResourceClassLoader) that takes care of loading action classes 
     * from a certain set of folders.
     */
    
    lt_include( PLOG_CLASS_PATH."class/action/action.class.php" );
	lt_include( PLOG_CLASS_PATH."class/controller/resourceclassloader.class.php" );

    //
    // various constants that will come handy
    //
    define("DEFAULT_ACTION_PARAM", "op");
    define("DEFAULT_ACTION_NAME", "Default");

    //
    // global array to hold the mappings between action keys and action classes. It is implemented as a
    // global arry because the Controller::registerAction is a static method and PHP4 does not implement
    // class-level static methods, and therefore, this is the only way have to accomplish this
    // functionality
    //
    $_plogController_actionMap = array();

    //
    // Pretty much the same as above...
    //
    $_plogController_forwardAction = array();

    /**
     * \ingroup Controller
     *
     * This is how MVC works, using the pattern of the 'Front Controller'. With this pattern, we have
     * a single controller class that receives all the requests from the client, identifies the
     * action to be taken and the relays the execution of the action to the most suitable Action class.
     * The 'Action' class then will use the application business logic (the Model) to carry out the
     * operations necessary.
     *
     * (according to http://java.sun.com/blueprints/guidelines/designing_enterprise_applications_2e/web-tier/web-tier5.html)
     *
     *  The controller receives a POST from the client.
     *  The controller creates an Action corresponding to the requested operation (as described in the previous section).
     *  The controller calls the Action's perform method.
     *    perform calls a model business method.
     *  The controller calls the screen flow manager to select the next view to display.
     *  The screen flow manager determines the next view and returns its name to the controller.
     *  The controller forwards the request to the templating service, which assembles and delivers the selected view to the client.
     * 
     * The Controller uses an action map file that maps action parameters to action classes. This is file is
     * nothing more than an associative PHP array where the key of the array is the value of the action
     * parameter and the key is the name of an action class. The default action parameter is "op" and the default
     * action is "Default" unless otherwise specified.
     *
     * This is an example of a map file:
     *
     * <pre>
     * $actions["Default"] = "AdminDefaultAction";
     * $actions["Login"] = "AdminLoginAction";
     * $actions["blogSelect"] = "AdminMainAction";
     * $actions["Dashboard"] = "AdminMainAction";
     * $actions["Manage"] = "AdminManageAction";
     * </pre>
     *
     * It is not necesary to specify the full extension of the file, and by default it is considered to be
     * ".class.php". In case this is not the case, please the ResourceClassLoader::setClassFileSuffix()
     * method.
     *
     * Classes can be dynamically loaded (meaning that we
     * do not need to load all of them via include() or lt_include() at the top of our code), via the 
     * ResourceClassLoader class. This class will scan a set of pre-defined folders looking for the action
     * class and load it if it's the one we were looking for.
     *
     * In order to create a Controller object and process a request, we would use something like:
     *
     * <pre>
     *  $controller = new Controller( "myactionmap.properties.php" );
     *  $controller->process( $_REQUEST );
     * </pre>
     *
     * It is also possible to register new actions dynamically at run time via the static method
     * Controller::registerAction() which takes an action class name and an action id as parameters. This is the
     * method used by plugins to register new actions (although via the wrapper PluginBase::registerAction())
	 *     
     * In our particular implementation, each action class should extend the Action class or a child of it
     * such as BlogAction, AdminAction, etc. Each one of this action classes should at least implement
     * the Action::perform() method and include there its business logic (which can be whatever) Action classes
     * are expected to generate a valid View class that the Controller will use to render the contents that
     * will be sent back to the client. The Controller will get the right view via the Action::getView() method, and
     * views can be set via the Action::_view attribute or the Action::setView() method.
     *
     * Another method that the controller will call is the validate() method that can be used for basic
     * data validation logic. The base Action class already provides some code in this method, which is tied to
     * the FormValidator and Validator classes so in case of data validation, it is advisable to use those
     * methods. However if required, feel free to reimplement Action::validate(), keeping in mind that if the controller
     * receives a positive value, it will continue processing the Action::perform() and if not, it will stop
     * processing and call Action::getView() right away.
     *
     * Actions can also forward the process flow to another action without generating a view by calling
     * the static method Controller::setForwardAction(), which takes an action class identifier as a parameter. If
     * after processing action the controller detects that there is a forwarding in place, it will <b>not</b>
     * call Action::getView() but instead, start the process again and call validate() and process() in the next
     * action in line.
     *
	 * @see BlogController
	 * @see AdminController
     * @see Action
     * @see BlogAction
     * @see AdminAction
     * @see FormValidator
     * @see Validator
     */
    class Controller 
    {
        var $_actionParam;
		
		/**
		 * @public
		 * Determines the base path from where action files can be dynamically loaded
		 */
		var $actionFolderPath;
		
		/**
		 * @private
		 */
		var $_cannotPerformAction;
		
		/**
		 * @private
		 */		
		var $_forwardAction; 
		
		/**
		 * @private
		 */		
		var $_loader;		

        /**
         * $ActionsMap is an associative array of the form:
         *
         * ( $actionName, $actionClassName )
         *
         * Where for every different possible value of the 'action' parameter in the request,
         * there is an object inheriting form the Action class that will take care of
         * that requested action.
         *
         * @param actionMap is the associative array with the mappings
         * @param actionParam is the name of the parameter in the request that will be used
         * @param loadActionClasses By default set to 'true', enables dynamic loading of the
         * action classes from disk. Set it to false if the action classes that are going
         * to be needed by the controller have already been loaded and there is no need to
         * do it again.
         * to identify the action to be taken
         */
        function Controller( $actionMap, $actionParam = DEFAULT_ACTION_PARAM )
        {           
            global $_plogController_actionMap;
            if( !is_array($_plogController_actionMap))
                 $_plogController_actionMap = Array();

            $_plogController_actionMap = $actionMap;

            $this->_actionParam = $actionParam;
            $this->_forwardAction = null;
			
			// default folder where actions are located
			$this->actionFolderPath = PLOG_CLASS_PATH.'class/action/';

			// get a resource loader so that we can dynamically load classes if they
			// have not been loaded yet!
			$this->_loader =& ResourceClassLoader::getLoader( $this->actionFolderPath );
			
			// no action defined in case we cannot perform
			$this->_cannotPerformAction = null;
        }
		
		/**
		 * sets the folder where action classes can be dynamically loaded
		 *
		 * @param newActionFolderPath absolute or relative path to the folder
		 */
		function setActionFolderPath( $newActionFolderPath )
		{
			$this->_loader->addSearchFolder( $newActionFolderPath );
		}

        /**
         * @static
         */
        function registerAction( $actionKey, $actionClass )
        {
            global $_plogController_actionMap;
            if( !is_array($_plogController_actionMap))   // make sure that we have an array
                 $_plogController_actionMap = Array();
            $_plogController_actionMap[ $actionKey ] = $actionClass;

            return true;
        }

        /**
         * check action exist in controller map or not
         *
         * @param actionName Name of the action
         * @return true if action exist
         * @static
         */
        function checkActionExist( $actionName )
        {
            global $_plogController_actionMap;
            return (array_key_exists( $actionName, $_plogController_actionMap ) );
        }

        /**
         * Add function info here
         *
         * @private
         */
        function _getActionClassName( $actionName )
        {
            global $_plogController_actionMap;
            $actionMap = $_plogController_actionMap;

            if(!$actionName || !is_string($actionName) || !array_key_exists($actionName, $actionMap)) {
                $actionName = DEFAULT_ACTION_NAME;
            }

            if (!empty($actionMap)) {
                $actionClassName = $actionMap[$actionName];
            }
            else {
                $actionClassName = $actionName . 'Action';
            }

            return $actionClassName;
        }

        /**
         * Sets the action to which we will forward the process
         *
         * @param forwardAction Name of the action to which we will forward the current
         * workflow.
		 * @param previousActionObject
         * @static
         */
        function setForwardAction( $forwardAction, $previousActionObject = null )
        {
            global $_plogController_forwardAction;
			global $_plogController_previousAction;

            $_plogController_forwardAction = $forwardAction;
			$_plogController_previousAction = $previousActionObject;
        }

        /**
         * Loads an action class from disk. I have refactored it and put this little bit in its
         * own method because doing so, applications that want to load the action classes from
         * somewhere else than PLOG_CLASS_PATH/class/action/, or have a different naming scheme
         * can extend this class and reimplement this method at will.
         *
         * @param actionClass The name of the action class that is to be loaded.
         * @return Always true.
         */
        function loadActionClass( $actionClass )
        {
            if( !class_exists($actionClass)) {
				$this->_loader->load( $actionClass );
            }

            return true;
        }
        
        /** 
         * Specific controllers should use this method to set a class that will be used in case
         * Action::canPerform() return false. The controller will then load this class and execute
         * it as if it was a normal action.
         * This feature can be used to display a view with an error message in case our controller 
         * and actions are working together to provide permission-based access: each action checks whether
         * the credentials of the current user allow him to execute the current action or not in
         * the Action::canPeform() method and if it returns true, then the action specified in this method
         * call takes over and displays whatever error message needs to be displayed (or does some
         * cleanup, etc, whatever needed)
         *
         * @param actionClass A string with the name of the class that should be loaded when
         * Action::canPerform() returns false. Please note that this is the name of the class, not the
         * class object itself!
         */
        function setCannotPerformAction( $actionClass )
        {
	    	$this->_cannotPerformAction = $actionClass;   
        }

        /**
         * Processess the HTTP request sent by the client
         *
         * @param httpRequest HTTP request sent by the client
         */
        function process( $httpRequest )
        {
            lt_include( PLOG_CLASS_PATH."class/net/request.class.php" );

            // get the name of the action
            $request = new Request( $httpRequest );

            $i = 0;
            $performed = false;

            while( !$performed ) {
                // get the value of this variable, every loop
                global $_plogController_forwardAction;
				global $_plogController_previousAction;

                if ($i == 0) {
                        // if this is the first iteration, then we have to take this path...
                    // since we will use the http request to determine which action to
                    // use next
                    $actionName = $request->getValue($this->_actionParam );
                    $actionClass = $this->_getActionClassName($request->getValue($this->_actionParam));
                }
                elseif (!empty($_plogController_forwardAction)) {
                    // if we're forwarding the current execution flow to another action, then
                    // we'll go this way
                    $actionName = $_plogController_forwardAction;
                    $actionClass = $this->_getActionClassName($_plogController_forwardAction);
                    $httpRequest = HttpVars::getRequest();
                    $_plogController_forwardAction = null;
                }
                else {
                    // if there's nothing else to do, finish
                    $performed = true;
                }

                if( !$performed ) {
                    lt_include( PLOG_CLASS_PATH."class/action/actioninfo.class.php" );

                    // load the class if it hasn't been loaded yet
                    $this->loadActionClass( $actionClass );

                    $actionInfo   = new ActionInfo( $this->_actionParam, $actionName );
                    $actionObject = new $actionClass( $actionInfo, $httpRequest );
					$actionObject->setPreviousAction( $_plogController_previousAction );
					
					if( $actionObject->canPerform()) {
	                    // we can use the validate method to check the values of the form variables. If validate()
	                    // returns 'true', then we call the 'perform' method. If not, then we won't :)
	                    if( $actionObject->validate()) {
	                        if( $actionObject->perform())
	                        	$actionObject->setSuccess( true );
	                        else
	                        	$actionObject->setSuccess( false );
	                    }
                	}
                	else {
	                	// check that we have an action defined for this kind of situations
	                	if( $this->_cannotPerformAction === null ) {
		                	throw( new Exception( "Action ".$actionName." was not allowed to execute and there is no fallback action to execute" ));
		                	die();
	                	}
	                	$actionClass = $this->_cannotPerformAction;
	                	$this->loadActionClass( $actionClass );
	                	$actionObject = new $actionClass( $actionInfo, $httpRequest );
						$actionObject->perform();
                	}
                }

                $i++;
            }

            // once the operation has been carried out, fetch and send the view
            // to the client
            $view = $actionObject->getView();

            // this is not good...
            if( empty( $view )) {
                $e = new Exception( 'The view is empty after calling the perform method.' );
                throw( $e );
            }
            else
                $view->render();
        }
    }
?>
