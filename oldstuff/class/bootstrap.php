<?php

/**

\mainpage

<b>Welcome to the LifeType API!</b>

<p>This is the starting point of the LifeType API. The API provides an a group of classes that go from 
database abstraction and data representation to templating and data validation.</p>
<p>This documentation is not an in-depth introduction to the LifeType API but instead it should only be used
as a reference when developing plugins or customizing LifeType. Additionally, the most recent version of this
documentation can be found at http://www.lifetype.net/api
 */	 

    /**
     * This file provides all the code needed for bootstraping an application
     * based on the LT framework such as setting error handles, loading base
     * classes, etc
     */

	// load our custom lt_include method to speed up the inclusion of files
	include( PLOG_CLASS_PATH."class/object/loader.class.php" );
    
    // load the Exception class and set the needed error handlers for PHP 4.x
    if(PHP_VERSION < 5)
	    lt_include( PLOG_CLASS_PATH."class/object/exception.class.php" );
	    
    // for performance logging purposes
    lt_include( PLOG_CLASS_PATH."class/misc/info.class.php" );    
?>