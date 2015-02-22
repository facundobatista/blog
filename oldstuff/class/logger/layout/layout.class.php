<?php

	
	
    /**
     * Layout provides a customizable way to format data for an appender. A Layout specifies
	 * the format of the messages that are going to be written to a target via an appender.
	 *
	 * @see Appender
	 * @see Logger
	 * @see LoggerManager
	 * \ingroup logger	 
     */
    class Layout
    {
        /**
         * Create a new Layout instance.
         */
        function Layout()
        {
			// nuttin' :)
        }

        /**
         * Format a message.
         *
         * <br/><br/>
         *
         * <note>
         *     This should never be called manually.
         * </note>
         *
         * @param Message A LoggedMessage instance.
         */
        function &format (&$message)
        {
            throw(new Exception("Layout::format(&$message) must be overridden"));
            die();
        }
    }

?>