<?php

	lt_include( PLOG_CLASS_PATH."class/view/view.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/templatesets/templatesetstorage.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/templateservice.class.php" );	
	
    /**
     * \ingroup View
     * @private
     *
     * Provides XML-based responses to trackback requests
     */
    class TrackbackView extends View
    {
    
        var $_profile;

    	function TrackbackView( $message, $error = false )
        {
			$this->View();
		
			$ts = new TemplateSetStorage();

			// we need to overwrite the $this->_template object with the Template object of our choice...
			$templateService = new TemplateService();
            $this->_template = $templateService->Template( 'trackback', 'misc' );
			
			// set the correct content type
            $this->setContentType( 'text/xml' );
			
			$this->setValue( "message", $message );
			if( $error ) $errorCode = 1;
			else $errorCode = 0;
			$this->setValue( "error", $errorCode );
        }
		
		/**
		 * View::render() does not implement any rendering logic so we'll have to provide our own
		 * @private
		 */
		function render()
		{
			parent::render();
		
			// pass all the data to the template
			$this->_template->assign( $this->_params->getAsArray());
			// and render it
			$response = $this->_template->fetch();
			
			AddTrackbackAction::tblog( "*** Sending response ***" );
			AddTrackbackAction::tblog(  $response );
			
			print( $response );
		}
    }
?>