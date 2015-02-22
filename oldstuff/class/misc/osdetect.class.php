<?php

	

    /**
     * \ingroup Misc
     *
     * Operating system detection functions. This class provides a bunch of functions in order to detect
     * on which operating system our php parser is running. Please bear in mind that this has not been
     * thoroughly tested and that at the moment it only provides detection for windows and linux.
     */
    class OsDetect  
    {

    	/**
         * Returns the OS string returned by php_uname
         *
         * @return The OS string.
         * @static
         */
        function getOsString()
        {
        	return PHP_OS;
        }

    	/**
         * Returns true if we are running windows.
         *
         * @return True if we are running windows, false otherwise.
         * @static
         */
        function isWindows()
        {
        	$os = OsDetect::getOsString();

        	if(eregi("win", $os))
            	return true;
            else
            	return false;
        }

        /**
         * Returns true if we are running windows.
         *
         * @return True if we are running windows, false otherwise.
         * @static
         */
        function isLinux()
        {
        	$os = OsDetect::getOsString();

            if(eregi("linux", $os))
            	return true;
            else
            	return false;
        }
    }
?>