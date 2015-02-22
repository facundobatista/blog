<?php

	
	
	/** 
	 * \ingroup Gallery_resizer
	 *
	 * Implementes the detectGd method which returns true if the GD library
	 * is available in this host
	 *
	 */
	class GdDetector 
	{
		function GdDectector()
		{
			
		}
		
		/**
		 * Returns true if the GD library is available in this host, or false
		 * otherwise.
		 *
		 * @return true if available or false otherwise
		 * @static
		 */
		function detectGd()
		{
			// the imagecreate() method has been in GD since PHP 3 so it's
			// a safe bet to consider GD available if this function is available
			return( function_exists( "imagecreate" ));
		}
	}