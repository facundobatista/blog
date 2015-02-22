<?php
	
	lt_include( PLOG_CLASS_PATH."class/summary/action/summaryaction.class.php" );

	/**
	 * a very stupid action
	 */
	class SummaryRegistrationAction extends SummaryAction
	{
		function perform()
		{
			lt_include( PLOG_CLASS_PATH."register.php" );
			die();
		}
	}
?>