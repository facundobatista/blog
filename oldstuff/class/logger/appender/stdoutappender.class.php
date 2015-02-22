<?php

    lt_include(PLOG_CLASS_PATH."class/logger/appender/appender.class.php");

    /**
     * StdoutAppender logs a message directly to the requesting client.
	 *
	 * \ingroup logger
     */
    class StdoutAppender extends Appender
    {
        /**
         * Create a new FileAppender instance.
         *
         * @param Layout A Layout instance.
         *
         * @access public
         * @since  1.0
         */
        function StdoutAppender($layout, $properties)
        {
            parent::Appender($layout, $properties);
        }

        /**
         * Write a message directly to the client, including the new line.
		 *
		 * @param message The message that we're going to write
         */
        function write ($message)
        {
            echo $message . "<br/>\n";
        }
    }

?>