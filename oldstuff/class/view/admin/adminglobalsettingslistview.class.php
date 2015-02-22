<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
	
	define( "ADMINISTRATOR_BLOG", 1 );
	
    /**
     * \ingroup View
     * @private
     *	
	 * shows a list with all the global parameters
	 */
	class AdminGlobalSettingsListView extends AdminTemplatedView
	{
		var $_show;
		
	    // list with the defined shows
	
		function AdminGlobalSettingsListView( $blogInfo, $show = "all" )
		{
			$this->AdminTemplatedView( $blogInfo, "globalsettings" );
			$this->_show = $show;
			
			// array with the defined shows
			$this->_shows = Array( "general", "summary", "templates", "urls", "email",
	                                  "upload", "helpers", "interfaces", "security",
	                                  "bayesian", "resources", "search" );			
		}
		
		function render()
		{
			// if the show is not correct, then use the default "general"
			if( !in_array( $this->_show, $this->_shows ))
				$this->_show = "general";
			 
			// export all the config parameters
            $config =& Config::getConfig();
            $settings = $config->getAsArray();
            $this->setValue( "settings", $settings );
            foreach( $settings as $key => $value ) {
            	$this->setValue( $key, $value );
            }			
			
            // set the show too
            $this->setValue( "show", $this->_show );
            
            parent::render();
		}
	}
?>