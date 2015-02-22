<?php

    /**
     * Appender allows you to log messags to any location. Every appender has its own layout
	 * that specifies the format of the messages, and every appender has a target location for messages.
	 * This way, we can have an appender that writes log messages to a database, another one that writes
	 * messages to a file, etc.
	 *
	 * @see LoggerManager
	 * @see Logger
	 * \ingroup logger	 
     */
    class Appender
    {
        /**
         * The layout to be used for this appender.
         */
        var $layout;
		
		/**
		 * used to keep the properties
		 * @private
		 */
		var $_properties;

        /**
         * Create a new Appender instance.
         *
         * @param Layout A Layout instance.
         */
        function Appender(&$layout, $properties)
        {
            $this->layout =& $layout;
			
			$this->_properties = $properties;
        }

        /**
         * Retrieve the layout this appender is using.
         *
         * @return Layout A Layout instance.
         */
        function & getLayout ()
        {
            return $this->layout;
        }

        /**
         * Set the layout this appender will use.
         *
         * @param Layout A Layout instance.
         */
        function setLayout (&$layout)
        {
            $this->layout =& $layout;
        }

        /**
         * Write to this appender.
         *
         * <br/><br/>
         *
         * <note>
         *     This should never be called manually.
         * </note>
         *
         * @param message The message to write.
         */
        function write ($message)
        {
            throw(new Exception("Appender::write: This method must be implemented by child classes."));
            die();
        }
    }

?>