<?php

	lt_include( PLOG_CLASS_PATH."class/summary/action/registeraction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	lt_include( PLOG_CLASS_PATH."class/summary/view/summaryusercreationview.class.php" );	

	/**
	 * shows a form so that users can register
	 */
    class doReadAgreement extends RegisterAction 
	{
        function perform()
        {
    		if( $this->_config->getValue( "summary_show_agreement" ))
    		    $this->_view = new SummaryView( "registerstep0" );
	    	else
    		    $this->_view = new SummaryUserCreationView();
		    
            $this->setCommonData();		
		    return( true );
        }
    }	 
?>
