<?php

	lt_include( PLOG_CLASS_PATH."class/config/properties.class.php" );

	/**
	 * \ingroup Net_HTTP
	 *
	 * Inherits from Properties but just to add some default
	 * values to some settings
	 */
	class SessionInfo extends Properties 
	{

		function SessionInfo()
		{
			$this->Properties();
		}
	}
?>
