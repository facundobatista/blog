<?php

    /** 
      V4.54 5 Nov 2004  (c) 2000-2004 John Lim (jlim@natsoft.com.my). All rights reserved.
      Released under both BSD license and Lesser GPL library license. 
      Whenever there is any discrepancy between the two licenses, 
      the BSD license will take precedence.
    */
    
    

    // compatibility stuff    
    if (!function_exists('ctype_alnum')) {
        function ctype_alnum($text) {
            return preg_match('/^[a-z0-9]*$/i', $text);
        }
    }

    
    /**
     * Base class for data dictionaries. Data dictionaries allow to use a meta-language to describe a database schema, which
     * will be processed by the data dictionary and which will allow to automatically modify a database. This meta-language
	 * is independent of the database being used, so it can be used in different database environments.
     *
     * Data dictionaries in PDb work in exactly the same way as in ADOdb, please see more details about data dictionaries here:
     * http://phplens.com/lens/adodb/docs-datadict.htm.
     *
     * \ingroup PDb
     */    
    class PDbBaseDataDict  
    {
        var $connection;
        var $debug = false;
        var $dropTable = 'DROP TABLE %s';
        var $renameTable = 'RENAME TABLE %s TO %s'; 
        var $dropIndex = 'DROP INDEX %s';
        var $addCol = ' ADD';
        var $alterCol = ' ALTER COLUMN';
        var $dropCol = ' DROP COLUMN';
        var $renameColumn = 'ALTER TABLE %s RENAME COLUMN %s TO %s';	// table, old-column, new-column, column-definitions (not used by default)
        var $nameRegex = '\w';
        var $schema = false;
        var $serverInfo = array();
        var $autoIncrement = false;
        var $dataProvider;
		var $upperName;
        var $invalidResizeTypes4 = array('CLOB','BLOB','TEXT','DATE','TIME'); // for changetablesql
        var $blobSize = 100; 	/// any varchar/char field this size or greater is treated as a blob
                                /// in other words, we use a text area for editting.
                                
    
        /**
         *  Parse arguments, treat "text" (text) and 'text' as quotation marks.
         *  To escape, use "" or '' or ))
         *   
         *  Will read in "abc def" sans quotes, as: abc def
         *  Same with 'abc def'.
         *  However if `abc def`, then will read in as `abc def`
         *   
         *  @param endstmtchar    Character that indicates end of statement
         *  @param tokenchars     Include the following characters in tokens apart from A-Z and 0-9 
         *  @returns 2 dimensional array containing parsed tokens.
         */
        function Lens_ParseArgs($args,$endstmtchar=',',$tokenchars='_.-')
        {
            $pos = 0;
            $intoken = false;
            $stmtno = 0;
            $endquote = false;
            $tokens = array();
            $tokarr = array();
            $tokens[$stmtno] = array();
            $max = strlen($args);
            $quoted = false;
            
            while ($pos < $max) {
                $ch = substr($args,$pos,1);
                switch($ch) {
                case ' ':
                case "\t":
                case "\n":
                case "\r":
                    if (!$quoted) {
                        if ($intoken) {
                            $intoken = false;
                            $tokens[$stmtno][] = implode('',$tokarr);
                        }
                        break;
                    }
                    
                    $tokarr[] = $ch;
                    break;
                
                case '`':
                    if ($intoken) $tokarr[] = $ch;
                case '(':
                case ')':	
                case '"':
                case "'":
                    
                    if ($intoken) {
                        if (empty($endquote)) {
                            $tokens[$stmtno][] = implode('',$tokarr);
                            if ($ch == '(') $endquote = ')';
                            else $endquote = $ch;
                            $quoted = true;
                            $intoken = true;
                            $tokarr = array();
                        } else if ($endquote == $ch) {
                            $ch2 = substr($args,$pos+1,1);
                            if ($ch2 == $endquote) {
                                $pos += 1;
                                $tokarr[] = $ch2;
                            } else {
                                $quoted = false;
                                $intoken = false;
                                $tokens[$stmtno][] = implode('',$tokarr);
                                $endquote = '';
                            }
                        } else
                            $tokarr[] = $ch;
                            
                    }else {
                    
                        if ($ch == '(') $endquote = ')';
                        else $endquote = $ch;
                        $quoted = true;
                        $intoken = true;
                        $tokarr = array();
                        if ($ch == '`') $tokarr[] = '`';
                    }
                    break;
                    
                default:
                    
                    if (!$intoken) {
                        if ($ch == $endstmtchar) {
                            $stmtno += 1;
                            $tokens[$stmtno] = array();
                            break;
                        }
                    
                        $intoken = true;
                        $quoted = false;
                        $endquote = false;
                        $tokarr = array();
            
                    }
                    
                    if ($quoted) $tokarr[] = $ch;
                    else if (ctype_alnum($ch) || strpos($tokenchars,$ch) !== false) $tokarr[] = $ch;
                    else {
                        if ($ch == $endstmtchar) {			
                            $tokens[$stmtno][] = implode('',$tokarr);
                            $stmtno += 1;
                            $tokens[$stmtno] = array();
                            $intoken = false;
                            $tokarr = array();
                            break;
                        }
                        $tokens[$stmtno][] = implode('',$tokarr);
                        $tokens[$stmtno][] = $ch;
                        $intoken = false;
                    }
                }
                $pos += 1;
            }
            if ($intoken) $tokens[$stmtno][] = implode('',$tokarr);
            
            return $tokens;
        }

		/** 
		 * Given a field, return its metatype based on its real type, i.e. 'VARCHAR' would return 'C', 
		 * 'TIMESTAMP' would return 'T' and so on. This method must be implemented by child classes as each
		 * database has its own data types.
		 * 
		 * @param t The type
		 * @param len the field length
		 * @param fieldobj
		 * @return Returns a string with the meta type
		 */
        function MetaType($t,$len=-1,$fieldobj=false)
		{
			// to be implemented by child classes
		}
		
		/** 
		 * Returns information about the tables in the current connection (a list of them)
		 *
		 * @return An array with the tables
		 */
        function &MetaTables()
        {
            if (!$this->connection->IsConnected()) return array();
            return $this->connection->MetaTables();
        }

		/** 
		 * Returns information (the table schema) about the given table
		 *
		 * @param tab Name of the table
		 * @param upper
		 * @param schema
		 * @return An array with the table schema
		 */        
        function &MetaColumns($tab, $upper=true, $schema=false)
        {
            if (!$this->connection->IsConnected()) return array();
            return $this->connection->MetaColumns($this->TableName($tab), $upper, $schema);
        }
        
		/** 
		 * Returns information about primary keys in the given table
		 *
		 * @param tab Name of the table
		 * @param upper
		 * @param schema
		 * @return An array with information about the primary keys
		 */        
        function &MetaPrimaryKeys($tab,$owner=false,$intkey=false)
        {
            if (!$this->connection->IsConnected()) return array();
            return $this->connection->MetaPrimaryKeys($this->TableName($tab), $owner, $intkey);
        }

		/** 
		 * Returns information about indexes in the given table
		 *
		 * @param tab Name of the table
		 * @param upper
		 * @param schema
		 * @return An array with information about the indexes
		 */                
        function &MetaIndexes($table, $primary = false, $owner = false)
        {
            if (!$this->connection->IsConnected()) return array();
            return $this->connection->MetaIndexes($this->TableName($table), $primary, $owner);
        }
        
		/** 
		 * Adds quotes around a name if necessary
		 *
		 * @param name The name that should be quoted
		 * @return The quoted name if it needed any quotes, or unquoted if it didn't
		 */
        function NameQuote($name = NULL)
        {
            if (!is_string($name)) {
                return FALSE;
            }
            
            $name = trim($name);
            
            if ( !is_object($this->connection) ) {
                return $name;
            }
            
            $quote = $this->connection->nameQuote;
            
            // if name is of the form `name`, quote it
            if ( preg_match('/^`(.+)`$/', $name, $matches) ) {
                return $quote . $matches[1] . $quote;
            }
            
            // if name contains special characters, quote it
            if ( !preg_match('/^[' . $this->nameRegex . ']+$/', $name) ) {
                return $quote . $name . $quote;
            }
            
            return $name;
        }
        
		/**
		 * tbd
		 */
        function TableName($name)
        {
            if ( $this->schema ) {
                return $this->NameQuote($this->schema) .'.'. $this->NameQuote($name);
            }
            return $this->NameQuote($name);
        }
        
        /** 
         * Executes the sql array returned by GetTableSQL and GetIndexSQL
 	 	 * 
		 * @param sql The SQL code to be executed, passed as an array of queries
		 * @param continueOnError Whether to stop after an error or not
		 * @return True if successful or false otherwise
		 */
        function ExecuteSQLArray($sql, $continueOnError = true)
        {
            $rez = 2;
            $conn = &$this->connection;
            //$saved = $conn->debug;
            foreach($sql as $line) {
                
                if ($this->debug) $conn->debug = true;
                $ok = $conn->Execute($line);
                //$conn->debug = $saved;
                if (!$ok) {
                    if ($this->debug) print($conn->ErrorMsg());
                    if (!$continueOnError) return 0;
                    $rez = 1;
                }
            }
            return $rez;
        }        

		/**
		 * Given a metatype, return its equivalent database type
         *   
		 * @param meta The meta-type:
         * - C:  varchar
         * - X:  CLOB (character large object) or largest varchar size if CLOB is not supported
         * - C2: Multibyte varchar
         * - X2: Multibyte CLOB 
         * - B:  BLOB (binary large object) 
         * - D:  Date
         * - T:  Date-time 
         * - L:  Integer field suitable for storing booleans (0 or 1)
         * - I:  Integer
         * - F:  Floating point number
         * - N:  Numeric or decimal number
		 * @return Returns a string with the real type
         */        
        function ActualType($meta)
        {
            return $meta;
        }
        
		/**
		 * Returns the SQL code necessary to create the given database
		 *
		 * @param dbname Name of the new database
		 * @param options Any additional options needed to create the database, or empty as default
		 * @return An array with SQL code needed to create the database
		 */
        function CreateDatabase($dbname,$options=false)
        {
            $options = $this->_Options($options);
            $sql = array();
            
            $s = 'CREATE DATABASE ' . $this->NameQuote($dbname);
            if (isset($options[$this->upperName]))
                $s .= ' '.$options[$this->upperName];
            
            $sql[] = $s;
            return $sql;
        }
        
        /**
         * Generates the SQL to create index.
 		 *
		 * @param idxname Name of the index
		 * @param tabname Name of the table where the index will be created
		 * @param flds Names of the fields on which the index will work
		 * @param idxoptions Options needed to create the index
		 * @return Returns an array of sql strings.
         */
        function CreateIndexSQL($idxname, $tabname, $flds, $idxoptions = false)
        {
            if (!is_array($flds)) {
                $flds = explode(',',$flds);
            }
            
            foreach($flds as $key => $fld) {
                $flds[$key] = $this->NameQuote($fld);
            }
            
            return $this->_IndexSQL($this->NameQuote($idxname), $this->TableName($tabname), $flds, $this->_Options($idxoptions));
        }
        
		/**
		 * Removes an index from a table
		 * 
		 * @param idxname Name of the index
		 * @param tabname Name of the table in which the index exists
		 * @return Returns an array of SQL strings needed to remove an index
		 */
        function DropIndexSQL ($idxname, $tabname = NULL)
        {
            return array(sprintf($this->dropIndex, $this->NameQuote($idxname), $this->TableName($tabname)));
        }
        
		/**
		 * tbd
		 */
        function SetSchema($schema)
        {
            $this->schema = $schema;
        }
        
		/**
		 * Returns SQL code needed to add a colum to the database table
		 * 
		 * @param tabname Name of the table
		 * @param flds An array with all the fields that will be added to the database
		 * @return An array with SQL code
        function AddColumnSQL($tabname, $flds)
        {
            $tabname = $this->TableName ($tabname);
            $sql = array();
            list($lines,$pkey) = $this->_GenFields($flds);
            $alter = 'ALTER TABLE ' . $tabname . $this->addCol . ' ';
            foreach($lines as $v) {
                $sql[] = $alter . $v;
            }
            return $sql;
        }
        
        /**
         * Change the definition of one column
         *
         * As some DBM's can't do that on there own, you need to supply the complete defintion of the new table,
         * to allow, recreating the table and copying the content over to the new table
         * @param string $tabname table-name
         * @param string $flds column-name and type for the changed column
         * @param string $tableflds='' complete defintion of the new table, eg. for postgres, default ''
         * @param array/string $tableoptions='' options for the new table see CreateTableSQL, default ''
         * @return array with SQL strings
         */
        function AlterColumnSQL($tabname, $flds, $tableflds='',$tableoptions='')
        {
            $tabname = $this->TableName ($tabname);
            $sql = array();
            list($lines,$pkey) = $this->_GenFields($flds);
            $alter = 'ALTER TABLE ' . $tabname . $this->alterCol . ' ';
            foreach($lines as $v) {
                $sql[] = $alter . $v;
            }
            return $sql;
        }
        
        /**
         * Rename one column
         *
         * Some DBM's can only do this together with changeing the type of the column (even if that stays the same, eg. mysql)
         * @param string $tabname table-name
         * @param string $oldcolumn column-name to be renamed
         * @param string $newcolumn new column-name
         * @param string $flds='' complete column-defintion-string like for AddColumnSQL, only used by mysql atm., default=''
         * @return array with SQL strings
         */
        function RenameColumnSQL($tabname,$oldcolumn,$newcolumn,$flds='')
        {
            $tabname = $this->TableName ($tabname);
            if ($flds) {
                list($lines,$pkey) = $this->_GenFields($flds);
                list(,$first) = each($lines);
                list(,$column_def) = split("[\t ]+",$first,2);
            }
            return array(sprintf($this->renameColumn,$tabname,$this->NameQuote($oldcolumn),$this->NameQuote($newcolumn),$column_def));
        }
            
        /**
         * Drop one column
         *
         * Some DBM's can't do that on there own, you need to supply the complete defintion of the new table,
         * to allow, recreating the table and copying the content over to the new table
         * @param string $tabname table-name
         * @param string $flds column-name and type for the changed column
         * @param string $tableflds='' complete defintion of the new table, eg. for postgres, default ''
         * @param array/string $tableoptions='' options for the new table see CreateTableSQL, default ''
         * @return array with SQL strings
         */
        function DropColumnSQL($tabname, $flds, $tableflds='',$tableoptions='')
        {
            $tabname = $this->TableName ($tabname);
            if (!is_array($flds)) $flds = explode(',',$flds);
            $sql = array();
            $alter = 'ALTER TABLE ' . $tabname . $this->dropCol . ' ';
            foreach($flds as $v) {
                $sql[] = $alter . $this->NameQuote($v);
            }
            return $sql;
        }

        /**
         * Generate the SQL to drop a table
		 *
		 * @param tabname Name of the table
		 * @return An array with SQL code to drop the table.
         */        
        function DropTableSQL($tabname)
        {
            return array (sprintf($this->dropTable, $this->TableName($tabname)));
        }

        /**
         * Generate the SQL to rename a table
		 *
		 * @param tabname Name of the table
		 * @return An array with SQL code to rename the table.
         */                
        function RenameTableSQL($tabname,$newname)
        {
            return array (sprintf($this->renameTable, $this->TableName($tabname),$this->TableName($newname)));
        }	
        
        /**
         * Generate the SQL to create table. Returns an array of sql strings.
		 *
		 * @param tabname Name of the table
		 * @param flds Table schema
		 * @param tableoptions Any extra options needed to create the table
		 * @return An array with SQL code to create the table according to the schema
         */
        function CreateTableSQL($tabname, $flds, $tableoptions=false)
        {
            if (!$tableoptions) $tableoptions = array();
            
            list($lines,$pkey) = $this->_GenFields($flds, true);
            
            $taboptions = $this->_Options($tableoptions);
            $tabname = $this->TableName ($tabname);
            $sql = $this->_TableSQL($tabname,$lines,$pkey,$taboptions);
			$idxs = $this->_IndexesSQL($tabname,$flds);
            
            $tsql = $this->_Triggers($tabname,$taboptions);
            foreach($tsql as $s) $sql[] = $s;
			foreach($idxs as $i) $sql[] = $i;
            
            return $sql;
        }
		
		/**
		 * @private
		 */		
		function _Triggers($tabname,$taboptions)
		{
			return array();
		}		
        
		/**
		 * @private
		 */
        function _array_change_key_case($an_array)
        {
            if (is_array($an_array)) {
                $new_array = array();
                foreach($an_array as $key=>$value)
                    $new_array[strtoupper($key)] = $value;
        
                   return $new_array;
           }
        
            return $an_array;
        }
        
		/**
		 * @private
		 */
        function _GenFields($flds,$widespacing=false)
        {
            if (is_string($flds)) {
                $padding = '     ';
                $txt = $flds.$padding;
                $flds = array();
                $flds0 = PDbBaseDataDict::Lens_ParseArgs($txt,',');
                $hasparam = false;
                foreach($flds0 as $f0) {
				
					// ignore index fields
					if( in_array( $f0[0], Array( "INDEX", "FULLTEXT", "HASH", "UNIQUE", "CLUSTERED", "BITMAP", "DROP" )))
						continue;
				
                    $f1 = array();
                    foreach($f0 as $token) {
                        switch (strtoupper($token)) {
                        case 'CONSTRAINT':
                        case 'DEFAULT': 
                            $hasparam = $token;
                            break;
                        default:
                            if ($hasparam) $f1[$hasparam] = $token;
                            else $f1[] = $token;
                            $hasparam = false;
                            break;
                        }
                    }
                    $flds[] = $f1;
                    
                }
            }
            $this->autoIncrement = false;
            $lines = array();
            $pkey = array();
            foreach($flds as $fld) {
			
                $fld = PDbBaseDataDict::_array_change_key_case($fld);
            
                $fname = false;
                $fdefault = false;
                $fautoinc = false;
                $ftype = false;
                $fsize = false;
                $fprec = false;
                $fprimary = false;
                $fnoquote = false;
                $fdefts = false;
                $fdefdate = false;
                $fconstraint = false;
                $fnotnull = false;
                $funsigned = false;
                
                //-----------------
                // Parse attributes
                foreach($fld as $attr => $v) {
                    if ($attr == 2 && is_numeric($v)) $attr = 'SIZE';
                    else if (is_numeric($attr) && $attr > 1 && !is_numeric($v)) $attr = strtoupper($v);
                    
                    switch($attr) {
                    case '0':
                    case 'NAME': 	$fname = $v; break;
                    case '1':
                    case 'TYPE': 	$ty = $v; $ftype = $this->ActualType(strtoupper($v)); break;
                    
                    case 'SIZE': 	
                                    $dotat = strpos($v,'.'); if ($dotat === false) $dotat = strpos($v,',');
                                    if ($dotat === false) $fsize = $v;
                                    else {
                                        $fsize = substr($v,0,$dotat);
                                        $fprec = substr($v,$dotat+1);
                                    }
                                    break;
                    case 'UNSIGNED': $funsigned = true; break;
                    case 'AUTOINCREMENT':
                    case 'AUTO':	$fautoinc = true; $fnotnull = true; break;
                    case 'KEY':
                    case 'PRIMARY':	$fprimary = $v; $fnotnull = true; break;
                    case 'DEF':
                    case 'DEFAULT': $fdefault = $v; break;
                    case 'NOTNULL': $fnotnull = $v; break;
                    case 'NOQUOTE': $fnoquote = $v; break;
                    case 'DEFDATE': $fdefdate = $v; break;
                    case 'DEFTIMESTAMP': $fdefts = $v; break;
                    case 'CONSTRAINT': $fconstraint = $v; break;
                    } //switch
                } // foreach $fld
                
                //--------------------
                // VALIDATE FIELD INFO
                if (!strlen($fname)) {
                    if ($this->debug) print("Undefined NAME");
                    return false;
                }
                
                $fid = strtoupper(preg_replace('/^`(.+)`$/', '$1', $fname));
                $fname = $this->NameQuote($fname);
                
                if (!strlen($ftype)) {
                    if ($this->debug) print("Undefined TYPE for field '$fname'");
                    return false;
                } else {
                    $ftype = strtoupper($ftype);
                }
                
                $ftype = $this->_GetSize($ftype, $ty, $fsize, $fprec);
                
                if ($ty == 'X' || $ty == 'X2' || $ty == 'B') $fnotnull = false; // some blob types do not accept nulls
                
                if ($fprimary) $pkey[] = $fname;
                
                // some databases do not allow blobs to have defaults
                if ($ty == 'X') $fdefault = false;
                
                //--------------------
                // CONSTRUCT FIELD SQL
                if ($fdefts) {
                    if (substr($this->connection->_type,0,5) == 'mysql') {
                        $ftype = 'TIMESTAMP';
                    } else {
                        $fdefault = $this->connection->sysTimeStamp;
                    }
                } else if ($fdefdate) {
                    if (substr($this->connection->_type,0,5) == 'mysql') {
                        $ftype = 'TIMESTAMP';
                    } else {
                        $fdefault = $this->connection->sysDate;
                    }
                } else if (strlen($fdefault) && !$fnoquote) {
                    if ($ty == 'C' or $ty == 'X' or 
                        ( substr($fdefault,0,1) != "'" && !is_numeric($fdefault)))
                        if (strlen($fdefault) != 1 && substr($fdefault,0,1) == ' ' && substr($fdefault,strlen($fdefault)-1) == ' ') 
                            $fdefault = trim($fdefault);
                        else if (strtolower($fdefault) != 'null') {
                            //$fdefault = $this->connection->qstr($fdefault);
							$fdefault = "'".$fdefault."'";
						}
				}
                $suffix = $this->_CreateSuffix($fname,$ftype,$fnotnull,$fdefault,$fautoinc,$fconstraint,$funsigned);
                
                if ($widespacing) $fname = str_pad($fname,24);
                $lines[$fid] = $fname.' '.$ftype.$suffix;
                
                if ($fautoinc) $this->autoIncrement = true;
            } // foreach $flds
            
            return array($lines,$pkey);
        }
		
		/**
		 * @private
		 */		
        function _IndexesSQL($tabname,$flds)
        {
            if (is_string($flds)) {
                $padding = '     ';
                $txt = $flds.$padding;
                $flds = array();
                $flds0 = PDbBaseDataDict::Lens_ParseArgs($txt,',');
                $hasparam = false;
                foreach($flds0 as $f0) {
				
					// ignore non-index fields
					if( !in_array( $f0[0], Array( "INDEX", "FULLTEXT", "HASH", "UNIQUE", "CLUSTERED", "BITMAP", "DROP" )))
						continue;
						
                    $flds[] = $f0;
                }
            }
            $lines = array();
            foreach($flds as $fld) {
				$typeStr = $fld[0];
				if( $typeStr == "INDEX" )
					$type = false;
				else
					$type = Array( $typeStr );			
									
                $fld = PDbBaseDataDict::_array_change_key_case($fld);
				$sql = $this->CreateIndexSQL( $fld[1], $tabname, $fld[2], $type );
				
				foreach( $sql as $s )
					$lines[] = $s;
			}
                        
            return($lines);
        }
				
		/**
		 * @private
		 */
        function _GetSize($ftype, $ty, $fsize, $fprec)
        {
            if (strlen($fsize) && $ty != 'X' && $ty != 'B' && strpos($ftype,'(') === false) {
                $ftype .= "(".$fsize;
                if (strlen($fprec)) $ftype .= ",".$fprec;
                $ftype .= ')';
            }
            return $ftype;
        }
        
        
        // return string must begin with space
        function _CreateSuffix($fname,$ftype,$fnotnull,$fdefault,$fautoinc,$fconstraint)
        {	
            $suffix = '';
            if (strlen($fdefault)) $suffix .= " DEFAULT $fdefault";
            if ($fnotnull) $suffix .= ' NOT NULL';
            if ($fconstraint) $suffix .= ' '.$fconstraint;
            return $suffix;
        }

		/**
		 * @private
		 */        
        function _IndexSQL($idxname, $tabname, $flds, $idxoptions)
        {
            $sql = array();
            
            if ( isset($idxoptions['REPLACE']) || isset($idxoptions['DROP']) ) {
                $sql[] = sprintf ($this->dropIndex, $idxname);
                if ( isset($idxoptions['DROP']) )
                    return $sql;
            }
            
            if ( empty ($flds) ) {
                return $sql;
            }
            
            $unique = isset($idxoptions['UNIQUE']) ? ' UNIQUE' : '';
        
            $s = 'CREATE' . $unique . ' INDEX ' . $idxname . ' ON ' . $tabname . ' ';
            
            if ( isset($idxoptions[$this->upperName]) )
                $s .= $idxoptions[$this->upperName];
            
            if ( is_array($flds) )
                $flds = implode(', ',$flds);
            $s .= '(' . $flds . ')';
            $sql[] = $s;
            
            return $sql;
        }

		/**
		 * @private
		 */        
        function _DropAutoIncrement($tabname)
        {
            return false;
        }
        
		/**
		 * @private
		 */
        function _TableSQL($tabname,$lines,$pkey,$tableoptions)
        {
            $sql = array();
            
            if (isset($tableoptions['REPLACE']) || isset ($tableoptions['DROP'])) {
                $sql[] = sprintf($this->dropTable,$tabname);
                if ($this->autoIncrement) {
                    $sInc = $this->_DropAutoIncrement($tabname);
                    if ($sInc) $sql[] = $sInc;
                }
                if ( isset ($tableoptions['DROP']) ) {
                    return $sql;
                }
            }
            $s = "CREATE TABLE $tabname (\n";
            $s .= implode(",\n", $lines);
            if (sizeof($pkey)>0) {
                $s .= ",\n                 PRIMARY KEY (";
                $s .= implode(", ",$pkey).")";
            }
            if (isset($tableoptions['CONSTRAINTS'])) 
                $s .= "\n".$tableoptions['CONSTRAINTS'];
            
            if (isset($tableoptions[$this->upperName.'_CONSTRAINTS'])) 
                $s .= "\n".$tableoptions[$this->upperName.'_CONSTRAINTS'];
            
            $s .= "\n) ";
            if (isset($tableoptions[$this->upperName])) $s .= $tableoptions[$this->upperName];
            $sql[] = $s;
            
            return $sql;
        }
        
        /**
         * Sanitize options, so that array elements with no keys are promoted to keys
		 *
		 * @private
         */
        function _Options($opts)
        {
            if (!is_array($opts)) return array();
            $newopts = array();
            foreach($opts as $k => $v) {
                if (is_numeric($k)) $newopts[strtoupper($v)] = $v;
                else $newopts[strtoupper($k)] = $v;
            }
            return $newopts;
        }
        
        /**
         * Generate the SQL to modify the schema of a table
		 *
		 * @param tabname Name of the table
		 * @param flds The new table schema
		 * @param tableoptions Any extra options needed
		 * @return An array with SQL code to modify the schema of the given table
         */        
        function ChangeTableSQL($tablename, $flds, $tableoptions = false)
        {
            // check table exists
            $cols = &$this->MetaColumns($tablename);
            if ( empty($cols)) { 
                return $this->CreateTableSQL($tablename, $flds, $tableoptions);
            }
            
            // already exists, alter table instead
            list($lines,$pkey) = $this->_GenFields($flds);
            $alter = 'ALTER TABLE ' . $this->TableName($tablename);
            foreach ( $lines as $id => $v ) {
                if ( isset($cols[$id]) && is_object($cols[$id]) ) {
                
                    $flds = PDbBaseDataDict::Lens_ParseArgs($v,',');
                    
                    //  We are trying to change the size of the field, if not allowed, simply ignore the request.
                    if ($flds && in_array(strtoupper(substr($flds[0][1],0,4)),$this->invalidResizeTypes4)) continue;	 
                 
                    $sql[] = $alter . $this->alterCol . ' ' . $v;
                } else {
                    $sql[] = $alter . $this->addCol . ' ' . $v;
                }
            }
            
            return $sql;
        }		
    }
?>