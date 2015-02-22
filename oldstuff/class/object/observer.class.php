<?php

	

    /**
	 * \ingroup Core
	 * 
     * Implementation of the Observer pattern. Copied/Inspired ;) from
     * http://www.phppatterns.com/index.php/article/articleview/27/1/1/.
     * Base Observer class     
     */
    class Observer {

    	/**
         * @private
         * $subject a child of class Observable that we're observing
         */
        var $subject;

        /**
         * Constructs the Observer
         * @param $subject the object to observe
         */
        function Observer (& $subject)
        {
        	$this->subject=& $subject;

            // Register this object so subject can notify it
            $subject->addObserver($this);
        }

        /**
         * Abstract function implemented by children to repond to
         * to changes in Observable subject
         * @return void
         */
        function update() {
        	//trigger_error ('Update not implemented');
            throw( new Exception( "Observer class: Update method must be implemented by observer classes." ));
        }
    }
?>