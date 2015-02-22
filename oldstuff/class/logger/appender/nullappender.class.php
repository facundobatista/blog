<?php

    lt_include(PLOG_CLASS_PATH."class/logger/appender/appender.class.php");

    /**
	 * Dummy appender that does nothing.
	 *
	 * \ingroup logger	 
     */
    class NullAppender extends Appender
    {
        /**
		 * This method does nothing since this is a dummy appender anyway!
         */
        function write ($message)
        {
			return( true );
        }
    }

?>