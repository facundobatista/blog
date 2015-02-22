<?php

	lt_include( PLOG_CLASS_PATH.'class/view/view.class.php' );
    lt_include( PLOG_CLASS_PATH.'class/template/templateservice.class.php' );
    lt_include( PLOG_CLASS_PATH.'class/net/requestgenerator.class.php' );
	
    /**
     * \ingroup View
     *
     * Extends the View class to add some commont methods and attributes that will be shared
     * by all the views of the administrative interface. This is the main basic view that all
     * view classes from the admin interface should extend.
     * 
     * This class has support for loading templates from the templates/admin folder, as well as
     * being able to use locales and even to throw events if needed. 
     *
     * The AdminView::render() method implements some logic of its own so it must be called
     * at some point in our custom classes extending AdminView.
     *
     * As of pLog 1.0, AdminView classes do not have support for cached views.
     */
    class AdminView extends View 
	{
        var $_pm;
    	var $_templateService;
        var $_blogInfo;
        var $_userInfo;

        /**
         * Calls the parent constructor and initializes the template service used
         * to fetch the templates
         *
         * @param blogInfo A valid BlogInfo object
         */
    	function AdminView( $blogInfo )
        {
        	$this->View();

            $this->_templateService = new TemplateService();

            $this->_blogInfo = $blogInfo;
            $this->setValue( 'url', RequestGenerator::getRequestGenerator($blogInfo));
			
            $blogSettings = $this->_blogInfo->getSettings();
			
			// initialize the plugin manager, so that we can throw events from views too!
			$this->_pm =& PluginManager::getPluginManager();
			$this->_pm->setBlogInfo( $this->_blogInfo );			
			
			// set the character set in the request based on the blog locale
			$locale = $this->_blogInfo->getLocale();
			$this->setCharset( $locale->getCharset());
			$this->addHeaderResponse( 'Cache-Control: no-cache, must-revalidate' );
			$this->addHeaderResponse( 'Last-Modified: ' . gmdate( "D, d M Y H:i:s" ) . ' GMT' );
			$this->addHeaderResponse( 'Expires: now' );
			$this->addHeaderResponse( 'Pragma: no-cache' );
        }
        
        /**
         * some views need to know who is executing them... but since it's not mandatory
         * for all, we'll have to use AdminView::setUserInfo whenever needed
         *
         * @param userInfo a UserInfo object with information about the user who is currently
         * executing this view
         */
        function setUserInfo( $userInfo )
        {
	    	$this->_userInfo = $userInfo;
        }
        
        /**
         * notifies of a throwable event
         *
         * @param eventType The code of the event we're throwing
         * @param params Array with the event parameters
         */
		function notifyEvent( $eventType, $params = Array())
		{
			$params[ 'from' ] = get_class( $this );
								
			return $this->_pm->notifyEvent( $eventType, $params );
		}        
		
		/**
		 * call the View::render() method.
		 */
		function render()
		{
			lt_include( PLOG_CLASS_PATH.'class/template/menu/menu.class.php' );
			lt_include( PLOG_CLASS_PATH.'class/template/menu/menurenderer.class.php' );		
		
			// set a few common parametres...
			$config =& Config::getConfig();
            $this->setValue( 'baseurl', $config->getValue( 'base_url'));			
            $this->setValue( 'version', Version::getVersion());
            $this->setValue( 'uploads_enabled', $config->getValue( 'uploads_enabled' ));			
            $this->setValue( 'bayesian_filter_enabled', $config->getValue( 'bayesian_filter_enabled' ));			

			//
			// stuff to generate the menu on the left
			//
            $menu =& Menu::getMenu();
			// initialize the menu renderer, passing as parameters the original menu structure,
			// the current logged in user (so that we can check permissions and so on)
			// and the current value of the 'op' parameter so that we can now which option is the
			// current active one
            $menuRenderer = new MenuRenderer( $menu, $this->_blogInfo, $this->_userInfo );
			//$this->setValue( "menus", $menuRenderer->generateAt("Manage"));
			
			$this->setValue( 'menu', $menuRenderer );

			parent::render();
		}
    }
?>
