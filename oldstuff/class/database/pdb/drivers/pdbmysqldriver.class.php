<?php

	lt_include( PLOG_CLASS_PATH."class/database/pdb/drivers/pdbdriverbase.class.php" );
	lt_include( PLOG_CLASS_PATH."class/database/pdb/drivers/pdbmysqlrecordset.class.php" );
    /**
     * \ingroup PDb
     *
     * MySQL driver for PDb
     */
	class PDbMySQLDriver extends PDbDriverBase
	{
		
		var $_res;
		var $_dbname;
		var $_charset;
	
	    /**
	     * Constructor of the driver. Doesn't do much.
	     */
		function PDbMySQLDriver()
		{
			$this->PDbDriverBase();
			
			// the driver name
			$this->_type = 'mysql';	
			
			// character set, 'default' until one is explicitely set
			$this->_charset = 'default';
		}
		
		/**
		 * @see PDbDriverBase::Execute()
		 */
		function Execute( $query, $page = -1, $itemsPerPage = 15 )
		{
			global $__pdb_num_queries;
		
		    if( $page > -1 ) {
                $start = (($page - 1) * $itemsPerPage);
                $limits = " LIMIT $start, $itemsPerPage";
                $query .= " $limits";
            }
				
			// execute the query and see whether it was incorrect
			$this->_debugQuery( $query );
			
			// as per the comments in http://www.php.net/manual/en/function.mysql-select-db.php, looks like
			// in situations where we've got more than one table in the same server using the same
			// connection parameters, we either need to select the database *everytime* we want to make 
			// a query or use slightly different connection paramters. I am not sure if this has any
			// performance hit, though.
			mysql_select_db( $this->_dbname, $this->_res );
			
			// increment the number of queries executed so far, regardless of what they were
			$__pdb_num_queries++;
			
			$result = mysql_query( $query, $this->_res );
			if( !$result ) {
			    if( $this->_debug ) {
			       print("<hr/>ERROR MESSAGE: ".$this->ErrorMsg()."<br/>");
			    } 
				return false;
            }
				
			// if not, create a RecordSet based on it
			$rs = new PdbMySQLRecordSet( $result );
			return( $rs );
		}
		
		/**
		 * @see PDbDriverBase::Connect()
		 */		
		function Connect( $host, $username, $password, $dbname = null, $dbcharset = null )
		{
			PDbDriverBase::Connect( $host, $username, $password, $dbname );
			
			// try to connect and quit if unsuccessful
			$this->_res = mysql_connect( $host, $username, $password );			
			if( !$this->_res )
				return false;
				
			// set the right character encoding for mysql 4.1+ client, connection and collation
			if( !empty( $dbcharset ) && $dbcharset != "default" ) {
	           	mysql_query( "SET NAMES ".$dbcharset, $this->_res );
				$this->_charset = $dbcharset;
			}
				
			// continue otherwise and try to select our db
			if( $dbname )
				return( mysql_select_db( $dbname, $this->_res ));
			else
				return( true );
		}
		
		/**
		 * @see PDbDriverBase::PConnect()
		 */		
		function PConnect( $host, $username, $password, $dbname = null, $dbcharset = null )
		{
			PDbDriverBase::Connect( $host, $username, $password, $dbname );			
			
			// try to connect and quit if unsuccessful
			$this->_res = mysql_pconnect( $host, $username, $password );			
			if( !$this->_res )
				return false;				
				
			// set the right character encoding for mysql 4.1+ client, connection and collation
			if( !empty( $dbcharset ) && $dbcharset != "default" ) {
	           	mysql_query( "SET NAMES ".$dbcharset, $this->_res );
				$this->_charset = $dbcharset;	
			}

			// continue otherwise and try to select our db
			if( $dbname )
				return( mysql_select_db( $dbname, $this->_res ));
			else
				return( true );
		}
		
		/**
		 * @see PDbDriverBase::Close()
		 */		
		function Close()
		{
		    return( mysql_close( $this->_res ));
		}
		
		/**
		 * @see PDbDriverBase::ErrorMsg()
		 */		
		function ErrorMsg()
		{
			return( mysql_error( $this->_res ));	
		}
		
		/**
		 * @see PDbDriverBase::Insert_ID()
		 */		
		function Insert_ID()
		{
			return( mysql_insert_id( $this->_res ));
		}
		
		/**
		 * @see PDbDriverBase::Affected_Rows()
		 */		
		function Affected_Rows()
		{
		    return( mysql_affected_rows( $this->_res ));
		}
		
		/**
		 * @see PDbDriverBase::getDriverDataDictionary()
		 */		
        function getDriverDataDictionary()
        {
            return( PDbDriverBase::getDriverDataDictionary( 'mysql' ));
        }

		/**
		 * Returns true if the current database supports FULLTEXT searches. This is currently 
		 * configured in the database configuration file, config/config.properties.php:
		 *
		 * <pre>
		 *  $config['db_options'] = Array( "enable_mysql_fulltext_search" => false );
		 * </pre>
		 *
		 * @return true if FULLTEXT is supported
		 */
		function isFullTextSupported()
		{			
			isset( $this->_opts["enable_mysql_fulltext_search"] ) ? $enableFullText = $this->_opts["enable_mysql_fulltext_search"] : $enableFullText = false;
			
			return( $enableFullText );
		}
		
		/**
		 * Return the name of the character set currently being used
		 *
		 * @see PDbDriverBase::getDbCharacterSet()
		 */
		function getDbCharacterSet()
		{
			return( $this->_charset );
		}
	}
?>