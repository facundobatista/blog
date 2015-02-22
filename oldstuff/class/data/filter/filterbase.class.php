<?php

	/**
	 * \defgroup Filter
	 *
	 * Filters in Lifetype can be used to perform filtering operations on data, although
	 * they will be used most of the times as input filters to filter the values of
	 * incoming parameters.
	 *
	 * @see Properties::getValue()
	 */
	
	/**
	 * \ingroup Filter
	 *
	 * This class is the base class that defines the interface for 
	 * filter classes. You should probably not be instantiating objects of this class
	 * but instead, extend it and implement the FilterBase::filter() method.
	 *
	 * It is also possible to chain multiple validators, in a way that the output of
	 * the previous one becomes the input of the next one. Please see the
	 * FilterBase::addFilter() method
	 */
	class FilterBase
	{
		var $_filters;
		
		/**
		 * Constructor of the class
		 */
		function FilterBase()
		{
			$this->_filters = Array();
		}
		
		/**
		 * Appends a validator to the current one. Validators appended to this one
		 * are chained <b>at the end</b> so they always get executed
		 * <b>after</b> this one is.
		 *
		 * @param filterInstance An instance of a class that implements the FilterBase interface
		 * @return Always true
		 */
		function addFilter( &$filterInstance )
		{
			$this->_filters[] = $filterInstance;
			
			return( true );
		}
		
		/**
		 * This is the main method that takes care of the processing of the input data
		 *
		 * @param data Unfiltered data
		 * @return The filtered data
		 */
		function filter( $data )
		{
			foreach( $this->_filters as $filterClass ) {
				$data = $filterClass->filter( $data );
			}
			
			return( $data );
		}
	}
?>