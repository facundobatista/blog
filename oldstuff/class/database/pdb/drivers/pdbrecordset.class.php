<?php

	

    /**
     * \ingroup PDb
     *
     * Abstract representation of a record set. Child classes are expected to extend this class
     * to provide database-specific handling of record sets
     */
	class PdbRecordSet 
	{
		
		var $_dbRes;
	
		function PdbRecordSet( $dbRes = null )
		{
			$this->_dbRes = $dbRes;
		}
		
		/**
		 * Returns a row from the resultset
		 *
		 * @return an associative array
		 */
		function FetchRow()
		{
			// to be implemented by chid classes
		}
		
		/**
		 * Returns the number of rows fetched in the last SELECT operation
		 *
		 * @return an integer
		 */
		function RecordCount()
		{
			
		}
		
		/**
		 * Alias for RecordCount()		 
		 *
		 * @see RecordCount
		 */
		function RowCount()
		{
		    return( $this->RecordCount());
		}		
		
		/**
		 * Generate a 2-dimensional array of records from the current cursor position, indexed from 0 to $nRows - 1. 
		 * If $nRows iundefined, till EOF.
		 *
		 * @param nRows
		 * @return Array
		 */
		function &GetArray($nRows = -1)
		{
		    $cnt = 0;
		    $results = array();
		    while ( ($row = $this->FetchRow($this->_dbRes)) && $nRows != $cnt) {
		        $results[] = $row;
		        $cnt++;
		    }
		    return $results;
		}		
	}
?>