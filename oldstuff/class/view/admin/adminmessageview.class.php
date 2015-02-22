<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );

    /**
     * \ingroup View
     * @private
     *
     * Shows a message to the user
     */
    class AdminMessageView extends AdminTemplatedView 
	{

    	/**
         * This initializes the class, but normally we'll only have to initialize the parent
         */
        function AdminMessageView( $blogInfo )
        {
        	$this->AdminTemplatedView( $blogInfo, "message" );
        }
		
        /**
         * Sets the error message. This method is equivalent of typing
         * $messageView->setValue( "message", "whatever-message-..." ), but using this we
         * type less :)
         *
         * @param message The message we are going to show.
         */
        function setMessage( $message )
        {
        	$this->setValue( "message", $message );
        }
    }
?>
