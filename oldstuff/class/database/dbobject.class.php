<?php    
    
    /**
	 * \ingroup Database
	 * 
	 * The DbObject is the object that ideally all object representing one database row should
	 * extend. It doesn't provide too many features yet but it is planned to provide them in the
	 * future.
     */
    class DbObject
    {
    	var $_table;
    	var $_pk;
    	var $_class;
    	var $_fields;
    	var $_properties;
		var $unserializable;
    
    	function DbObject()
    	{    		    	
    		$this->_table = null;
    		$this->_pk = null;
    		$this->_class = "DbObject";
    		$this->_fields = Array();
    		$this->_properties = Array();
			$this->unserializable = Array();
    	}
    	
    	function setValue( $key, $value )
    	{
    		$this->_properties["$key"] = $value;
    	}
    	
    	function getValue( $key )
    	{
    		return( $this->_properties["$key"] );
    	}
    	
    	function setProperties( $properties )
    	{
    		$this->_properties = $properties;
    	}
    	
    	function getProperties()
    	{
    		return( $this->_properties );
    	}
    	
    	function getClass()
    	{
    		return( $this->_class );
    	}
    	
    	function getPK()
    	{
    		return( $this->_pk );
    	}
    	
    	function getFields()
    	{
    		return( array_keys( $this->_fields ));
    	}
    	
    	function getFieldGetters()
    	{
    		return( $this->_fields );
    	}
		
		/**
		 * No null values are serialized to the session. If there any values in your data class
		 * that need not be serialized to the cache, please implement your own version of __sleep,
		 * set those attributes to null and call parent::__sleep() so that those attributes are not
		 * serialized.
		 *
		 * @private
		 */
		function __sleep()
		{
			$vars = (array)$this;
			foreach ($vars as $key => $val) {
				if (is_null($val)) {
					unset($vars[$key]);
				}
			}
			return( array_keys($vars));
		}
    }
?>