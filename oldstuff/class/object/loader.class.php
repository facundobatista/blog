<?php

	function lt_include( $filename )
	{ 
		if(!isset($GLOBALS['included_files'][$filename] )) { 
			$GLOBALS['included_files'][$filename] = TRUE; 
			include($filename);
		}
	}

?>