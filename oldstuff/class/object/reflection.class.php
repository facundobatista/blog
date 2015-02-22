<?php

    

    /**
	 * \ingroup Core
	 *
     * <b>Very</b> modest attempt to recreate some kind of Reflection API
     * "a la" Java.
     */
    class Reflection  
	{

        /**
         * Constructor. Does nothing.
         */
        function Reflection()
        {
            
        }

        /**
         * Returns true if the class has a method called $methodName
         *
         * @param class An object of the class we want to check
         * @param methodName Name of the method we want to check
         * @return Returns true if method exists otherwise, false
         */
        function methodExists( &$class, $methodName )
        {
            return method_exists( $class, $methodName );
        }

        /**
         * Returns all the methods available in the class. It returns both the methods from the
         * class itself <b>as well as</b> all
         *
         * @param class The class from which we would like to check the methods
         * @return An array containing all the methods available.
         */
        function getClassMethods( &$class )
        {
            return get_class_methods( $class );
        }
    }
?>