<?php

    if(PHP_VERSION < 5)
	    lt_include( PLOG_CLASS_PATH."class/object/exception.class.php" );

	lt_include( PLOG_CLASS_PATH."class/logger/loggermanager.class.php" );
	
	/**
	 * \ingroup Core
	 * 
	 * <p><b>NOTE:</b> As of LifeType 1.1, this class is deprecated and all other classes in
	 * LifeType do not longer extend this class. This dependency was removed due to performance
	 * and memory usage.</p>
	 * <p>If you have any custom code that is still extending this class, please make the necessary
	 * changes to remove the dependency as this class will most likely be removed in future versions
     * of LifeType.</p>
	 *
	 * @deprecated
	 */
	class Object 
	{

    	var $_objId;
		var $log;

    	/**
         * Constructor
         */
		function Object()
		{
			// initialize logging -- enable this only for debugging purposes
			$this->log =& LoggerManager::getLogger( "default" );

            if( DEBUG_ENABLED )
                $this->debug =& LoggerManager::getLogger( "debug" );
		}

        function __getObjectId()
        {
        	return $this->_objId;
        }

		/**
		 * Returns a string with a representation of the class
         * @return The string representing the object
		 */
		function toString()
		{
			// returns the name of the class
			$ret_str = get_class( $this )." ".$this->_dumpVars();

			return $ret_str;
		}

		function _dumpVars()
		{
			$vars = get_object_vars( $this );

			$keys = array_keys( $vars );

			$res = "[";

			foreach( $keys as $key )
				$res .= " ".$key."=".$vars[$key];

			$res .= " ]";

			return $res;
		}

		/**
		 * Returns the name of the class
         * @return String with the name of the class
		 */
		function className()
		{
			return get_class( $this );
		}

		/**
		 * Returns the name of the parent class
         * @return String containing the name of the parent class
		 */
		function getParentClass()
		{
			return( get_parent_class( $this ));
		}

		/**
		 * Returns true if the current class is a subclass of the given
		 * class
         * @param $object The object.
         * @return True if the object is a subclass of the given object or false otherwise.
		 */
		function isSubclass( $object )
		{
			return is_subclass_of( $this, $object->className());
		}

		/**
		 * Returns an array containing the methods available in this class
         * @return Array containing all the methods available in the object.
		 */
		function getMethods()
		{
			return get_class_methods( $this );
		}

        /**
         * Returns true if the class is of the given type.
         *
         * @param object Object
         * @return Returns true if they are of the same type or false otherwise.
         */
		function typeOf( $object )
		{
			return is_a( $this, $object->className());
		}
	}
?>