<?php

	

	/**
	 * \ingroup Core
	 *
     * Implementation of the Observer pattern. Copied/Inspired ;) from
     * http://www.phppatterns.com/index.php/article/articleview/27/1/1/.
     */
     class Observable  {

        /**
         * @private
         * $observers an array of Observer objects to notify
         */
        var $observers;

        /**
         * @private
         * $state store the state of this observable object
         */
        var $state;

        /**
         * Constructs the Observerable object
         */
        function Observable ()
        {
        	$this->observers=array();
        }

        /**
         * Calls the update() function using the reference to each
         * registered observer - used by children of Observable
         * @return void
         */
        function notifyObservers ()
        {
        	print("notifying observers!");
        	$observers=count($this->observers);
            for ($i=0;$i<$observers;$i++) {
            	$this->observers[$i]->update();
            }
        }

        /**
         * Register the reference to an object object
         * @return void
         */
        function addObserver (& $observer)
        {
        	$this->observers[]=& $observer;
        }

        /**
         * Returns the current value of the state property
         * @return mixed
         */
        function getState ()
        {
        	return $this->state;
        }

        /**
         * Assigns a value to state property
         * @param $state mixed variable to store
         * @return void
         */
        function setState ($state)
        {
        	$this->state=$state;
        }
    }
?>