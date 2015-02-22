<?php

	lt_include( PLOG_CLASS_PATH."class/summary/action/summaryaction.class.php" );


	/**
	 * shows the form to reset a password
	 */
	class SummaryShowResetPasswordForm extends SummaryAction
	{	
		function perform()
		{
			$this->_view = new SummaryView( "resetpassword" );
			$this->setCommonData();
			
			return true;
		}
	}
?>