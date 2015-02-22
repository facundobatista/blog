<?php

	

	/**
	 * \ingroup Config
	 * 
	 * Class inspired by the java class Properties.
	 */
	class Properties  
	{

		var $_props;
		
		var $_keyFilters;

        /**
         * Constructor.
         *
         * @param values If $values == null, then the object will be initialized empty.
         * If it contains a valid PHP array, all the properties will be initialized at once.
         */
		function Properties( $values = null )
		{
			$this->_keyFilters = Array();
			
            if( $values == null )
				$this->_props = Array();
            else
            	$this->_props = $values;
		}

        /**
         * Sets a value in our hash table.
         *
         * @param key Name of the value in the hash table
         * @param value Value that we want to assign to the key '$key'
         */
		function setValue( $key, $value )
		{
			$this->_props[$key] = $value;
		}

        /**
         * Returns the value associated to a key
         *
         * @param key Key whose value we want to fetch
		 * @param defaultValue value that we should return in case the one we're looking for
		 * is empty or does not exist
		 * @param filterClass An instance of an object implementing the FilterBase interface that
		 * will be used to process the value before returning it.
         * @return Value associated to that key
         */
		function getValue( $key, $defaultValue = null, $filterClass = null )
		{
            if( !isset($this->_props[$key]) ) {
                $value = $defaultValue;
            } else {
			    $value = $this->_props[$key];
            }

			if( $filterClass  || isset( $this->_keyFilters[$key] )) {
				// there's a filter class specified, so we should run the
				// resulting value through it...
				if( isset( $this->_keyFilters[$key] )) {
					$filterClass = $this->_keyFilters[$key];
					//print("using filter: ".get_class($filterClass)." - key = ".$key."<br/>" );					
				}
					
				$value = $filterClass->filter( $value );
			}
			
			return( $value );
		}
		
		/**
		 * This method is an alias for Properties::getValue() but the filter
		 * class is now a mandatory parameter
		 *
         * @param key Key whose value we want to fetch
		 * @param filterClass An instance of an object implementing the FilterBase interface that
		 * will be used to process the value before returning it.
		 * @param defaultValue value that we should return in case the one we're looking for
		 * is empty or does not exist
         * @return Value associated to that key
 		 * @see Properties::getValue()
 		 */
		function getFilteredValue( $key, $filterClass, $defaultValue = null )
		{
			return( $this->getValue( $key, $defaultValue, $filterClass ));
		}
		
		/**
		 * Registers a filter class for the key "$key", so that 
		 * every time Properties::getValue( "$key" ) is called to fetch the requested
		 * value, the filter will be applied automatically without the need to specify
		 * the filter class at every call
		 *
		 * @param key
		 * @param filterClass
		 */
		function registerFilter( $key, &$filterClass ) 
		{
			$this->_keyFilters[$key] = $filterClass;
		}

		/**
		 * Method overwritten from the Object class
         * @return Returns a nicer representation of our contents
		 */
		function toString()
		{
			print_r( $this->_props );
		}

        /**
         * Returns the internal arrary used to store the properties as a PHP array
         * @return Internal array as a PHP array
         */
		function getAsArray()
		{
			return $this->_props;
		}

        /**
         * Returns an array containing all the keys used
         *
         * @return Array containing all the keys
         */
		function getKeys()
		{
			return array_keys( $this->_props );
		}

        /**
         * Returns an array containing the values
         *
         * @return Array containing the values
         */
		function getValues()
		{
			return array_values( $this->_props );
		}
		
		/**
		 * returns whether a given key exists in the table
		 *
		 * @param key The key name
		 * @return True if the key exists, false otherwise
		 */
        function keyExists( $key )
        {
            return( array_key_exists( $key, $this->_props ));
        }
	}
?>
