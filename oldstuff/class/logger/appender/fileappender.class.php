<?php

	lt_include( PLOG_CLASS_PATH."class/logger/appender/appender.class.php" );

    /**
	 * \ingroup logger
	 * 
     * FileAppender allows a logger to write a message to file.
     */
    class FileAppender extends Appender
    {
        /**
         * An absolute file-system path to the log file.
         */
        var $file;

        /**
         * A pointer to the log file.
         *
         * @access private
         * @type   resource
         */
        var $fp;

        /**
         * Create a new qFileAppender instance.
		 *
         *
         * @param Layout A Layout instance.
		 * @param properties An associative array with properties that might be needed by this
		 * appender
         * @access public
         * @since  1.0
		 * @see qAppender
         */
        function FileAppender ($layout, $properties)
        {
            parent::Appender($layout, $properties);

            $this->file    =  $properties["file"];
        }

        /**
         * Open the file pointer.
         *
         * <br/><br/>
         *
         * <note>
         *     This should never be called manually.
         * </note>
         */
        function openFP()
        {		
            $this->fp   = fopen( $this->file, "a+" );
			if( !$this->fp ) {
				throw( new Exception( "Cannot open log file: ".$this->file ));
				die();
			}
			
			return true;
        }

        /**
         * Write a message to the log file.
         *
         * <br/><br/>
         *
         * <note>
         *     This should never be called manually.
         * </note>
         *
         * @param string The message to write.
         */
        function write ($message)
        {
            if( !is_resource($this->fp) )
                $this->openFP();

			fwrite( $this->fp, $message );
        }
    }

?>
