<?php

	lt_include( PLOG_CLASS_PATH."class/summary/action/registeraction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
	lt_include( PLOG_CLASS_PATH."class/summary/view/summaryxmlview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/usernamevalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that adds a new article category to the database.
     */
    class CheckUserNameAjaxAction extends RegisterAction 
	{

    	var $_userName;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function CheckUserNameAjaxAction( $actionInfo, $request )
        {
        	$this->RegisterAction( $actionInfo, $request );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
			$this->_userName = Textfilter::filterAllHTML($this->_request->getValue( "userName" ));
			
            // once we have built the object, we can add it to the database
            $this->_view = new SummaryXmlView( "response" );				
            $this->_view->setValue( "method", "checkUserNameAjax" );
            
			$uv = new UsernameValidator();
			if( !$uv->validate( $this->_userName ) )
			{
            	$this->_view->setValue( "success", "0" );
            	$this->_view->setValue( "message", $this->_locale->tr("error_incorrect_username") );
            	
            	return true;  
			}
			
			// create the object...
            $users = new Users();
            $userInfo = $users->getUserInfoFromUsername( $this->_userName );
            if( !$userInfo )
            {
            	$this->_view->setValue( "success", "1" );
            	$this->_view->setValue( "message", $this->_locale->tr("check_username_ok") );   
            }
            else
            {
            	$this->_view->setValue( "success", "0" );
            	$this->_view->setValue( "message", $this->_locale->tr("error_username_exist") );  
            }
                
            return true;	
        }
    }
?>