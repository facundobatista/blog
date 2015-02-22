<?php

    /**
     * \defgroup DAO
     *    
     * DAO stands for "Data Access Object" and represents a data model 
     * according to the MVC architecture. 
     *
     * DAO classes isolate developers of all the intricacies of the 
     * database structure, so that for example loading a post from the 
     * dabase is as easy as:
     *
     * <pre>
     *   $articles = new Articles();
     *   $userPost = $arcticles->getBlogArticle( 15 );
     * </pre>
     *
     * Otherwise, developers would need to write an SQL query every time we 
     * need to load an article from the database. In general, DAO classes 
     * provide access to reading, updating and removing data from the database.
     * In pLog, we usually have two classes per entity: a smaller one that 
     * contains no database access logic and that only contains the information
     * necessary (usually, it represents a row from the database), and the 
     * second will be a bigger class that includes SQL code and database logic 
     * and that provides all the methods outlined above (read, update and 
     * remove from the database) Examples of this are Articles and Article, 
     * or Users and UserInfo. 
     *
     * Other relevant DAO classes are ArticleComments and UserComment, 
     * MyLink and MyLinks, etc.
     *
     * All classes that extend the base Model class, automatically inherit 
     * an open connection to the database * (via the private attribute 
     * Model::_db) and several other database-related methods. 
     *
     * Furthermore all classes that extend the base Model class gets a 
     * reference to the global disk-based cache. More information on how
     * to use the cache can be found in the Cache Group
     *
     * If you need to implement some kind of data access, please extend 
     * from Model.
     *
     */

    /**
     * default database prefix, if none other specified
     */
    define( "DEFAULT_DB_PREFIX", "plog_" );

    /**
     * whether database-level debugging is enabled
     */
    define( "DAO_DEBUG_ENABLED", false );

    /**
     * how many items per page by default, when paging is enabled
     */
    define( "DEFAULT_ITEMS_PER_PAGE", 15 );
    
    /**
     * whether we're going to use paging or not.
     */
    define( "DEFAULT_PAGING_ENABLED", -1 );
	
	/**
	 * enable or disable the data cache
	 */
	define( "DATA_CACHE_ENABLED", true );

    /**
     * the names of the tables used in pLog
     */
    define( 'BLOGS_TABLENAME', 'blogs' );
    define( 'ARTICLES_TABLENAME', 'articles' );
    define( 'ARTICLETEXTS_TABLENAME', 'articles_text' );
    define( 'ARTICLE_CATEGORIES_RELATIONSHIP_TABLENAME',
            'article_categories_link' );
    define( 'CUSTOMFIELD_VALUES', 'custom_fields_values' );


    /**
     * \ingroup DAO
     *
     * This class provides all the classes extending it with a database 
     * connection so that classes don't have to 
     * worry about that. Later on, the Model classes will be used by 
     * the corresponding action object.
     */
    class Model
    {

        var $_db;        
        var $_prefix = null;
        var $_cache;
        var $_dbInitialized =  false;
        var $_lastError = "";

        /**
         * Basic constructor, setting up a cache for all classes extending Model
         *
         * @param useCache Some object might not need a cache and can disable it by passing false
         */
        function Model( $cacheEnabled = DATA_CACHE_ENABLED )
        {
            // allow a cache for all dao objects
            lt_include( PLOG_CLASS_PATH . "class/cache/cachemanager.class.php" );
            $this->_cache =& CacheManager::getCache( $cacheEnabled );
        }

        /**
         * executes a query with certain limits (for paging, for ex.)
         *
         * @param query The query to be executed
         * @param page enable paging, pass page number or -1 to disable paging
         * @param itemsPerPage number of items on a page
         * @see Execute
         * @return A ResultSet or false if errors
         */        
        function Execute( $query, 
                          $page = DEFAULT_PAGING_ENABLED, 
                          $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
        {
            // initialize the db when we have to execute the first query, 
            // not earlier. 
            $this->_initializeDb();
        
            // see PDbDriverBase (or one of its drivers like PDbMySQLDriver 
            // for details)
            $result = $this->_db->Execute( $query, $page, $itemsPerPage );

            // if the query generated an error, write a message to the sql 
            // error log file
            if( !$result ) {
                $this->_lastError = $this->_db->ErrorMsg();
                lt_include( PLOG_CLASS_PATH . "class/logger/loggermanager.class.php" );

                $log =& LoggerManager::getLogger( "sqlerr" );
                $error = $this->DbError();
                $log->error( "The following query = \n" .
                             "\"$query\"" .
                             "\ngenerated the following error message = \n" .
                             "\"$error\"" );
            }
                
            return( $result );
        }

        /**
         * Returns the last error message from the database.
		 *
		 * @param driverError Set this field to true if interested in getting the error message as reported by
		 * the driver. Keep in mind that the error message might be gone after we've closed the connection
		 * (by calling the Close() method at the driver level) The only way to get the error message in 
		 * that case (if any at all) is by setting this parameter to 'false'. If done so, this class will save
		 * the last error message and make it available via this method. This parameter defaults to false.
		 * @return A string containing the error message
         */
        function DbError( $driverError = false )
        {
            if( $driverError )
                return( $this->_db->ErrorMsg());
            else
                return( $this->_lastError );
        }
        
		/**
		 * Retrieves one single row/object from the database
		 *
		 * @param field
		 * @param value
		 * @param cacheId
		 * @param caches
		 * @return
		 */
        function get( $field, $value, $cacheId, $caches = null )
        {
        	$dbObject = $this->_cache->getData( $value, $cacheId );

        	if( !$dbObject ) {
	        	$query = "SELECT * FROM ".$this->table." WHERE {$field} = '".Db::qstr( $value )."'";
	        	
	        	$result = $this->Execute( $query );
	        	
	        	if( !$result )
	        		return false;
	        	
	        	if( $result->RowCount() == 0 ){
                    $result->Close();
	        		return false;
                }
	        		
	        	$row = $result->FetchRow();
                $result->Close();

	        	$dbObject = $this->mapRow( $row );
	        	
	        	$this->_cache->setData( $value, $cacheId, $dbObject );
	        	if( $caches ) {
	        		foreach( $caches as $cache => $getter ) {
	        			$this->_cache->setData( $dbObject->$getter(), $cache, $dbObject );
	        		}
	        	}
	        }
	        
	        return( $dbObject );
        	
        }
                
		/**
		 * Retrieves all the rows from the given table
		 *
		 * @param key
		 * @param cacheId
		 * @param itemCaches
		 * @param sorting
		 * @param searchTerms
		 * @param page
		 * @param itemsPerPage
		 * @return
		 */		 
        function getAll( $key, $cacheId, $itemCaches = null, $sorting = Array(), $searchTerms ="", $page = -1, $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
        {
        	return( $this->getMany( $key, "_all_", $cacheId, $itemCaches, $sorting, $searchTerms, $page, $itemsPerPage ));
        }      
		
		/**
		 * This method must be reimplemented by child classes wishing to provide
		 * search functionalities
		 *
		 * @param searchTerms
		 * @return search string to be used in an SQL query
		 */
		function getSearchConditions( $searchTerms )
		{
			return( "" );
		}
        
		/**
		 * Retrieves a set of rows of the given table using the given (simple) conditions.
		 *
		 * @param key
		 * @param value
		 * @param cacheId
		 * @param itemCaches
		 * @param sorting
		 * @param searchTerms
		 * @param page
		 * @param itemsPerPage
		 * @return
		 */		 		
        function getMany( $key, $value, $cacheId, $itemCaches = null, $sorting = Array(), $searchTerms = "", $page = -1, $itemsPerPage = DEFAULT_ITEMS_PER_PAGE )
        {
			// if we previously used the search terms, then we did not cache the data
			if( $searchTerms == "" )
				$dbObjects = $this->_cache->getData( $value, $cacheId );
			else
				$dbObjects = null;
				
        	if( !$dbObjects ) {
        		$query = "SELECT * FROM ".$this->table;	
				$where = "";
				// if $value is 'all' or null, then we are going to cache the whole thing so let's not
				// include these as parameters. 
        		if( $value != "_all_" && $value != null ) {
        			$where .= " WHERE {$key} = '".Db::qstr($value)."'";					
				}
				if( $searchTerms != "" ) {
					// get the table-dependent search string
					$search = $this->getSearchConditions( $searchTerms );
					// add the search terms, if any
					if( $where ) $where .= " AND ( $search )";
					else
						if( $search) $where = " WHERE ".$search;
					// remove the last 'AND' in case it's the last thing in the string
					if( substr($where, strlen( $where ) - 3 , 3) == 'AND' )
						$where = substr( $where, 0, strlen( $where ) - 3 );
				}
				$orderBy = "";
        		if( count( $sorting ) > 0 ) {
        			// check the sorting...
        			$orderBy = " ORDER BY ";
        			foreach( $sorting as $field => $dir ) {
        				$orderBy .= "$field $dir";
        			}
        		}

				// build the query including all parts
				$query = $query.$where.$orderBy;
				
	        	$result = $this->Execute( $query );
	        	
	        	if( !$result )
	        		return false;
	        	
	        	if( $result->RowCount() == 0 ){
                    $result->Close();
	        		return false;
                }
	        		
	        	$dbObjects = Array();
	        	while( $row = $result->FetchRow()) {        	
		        	$dbObject = $this->mapRow( $row );
		        	$dbObjects[] = $dbObject;
		        	if( $itemCaches ) {
		        		foreach( $itemCaches as $cache => $getter ) {
	        				$this->_cache->setData( $dbObject->$getter(), $cache, $dbObject );
	        			}
	        		}
		        }
                $result->Close();

				// do not cache data if using search terms!
				if( $searchTerms == "" ) 
					$this->_cache->setData( $value, $cacheId, $dbObjects );
        	}
        	
        	if( $page > -1 ) {
        		// return only a subset of the items
				$start = (($page - 1) * $itemsPerPage );
                $dbObjects = array_slice( $dbObjects, $start, $itemsPerPage );        		
        	}
        	
        	return( $dbObjects );
        }
        
        /**
		 * Adds a row to the current table
		 *
		 * @param dbObject
		 * @param cacheId
		 * @return True if successful or false if error
		 */
        function add( &$dbObject, $cacheId = null ) 
        {
        	$fields = $dbObject->getFieldGetters();
        	$fieldsString = '';
        	$fieldsValuesString = '';

            $sql    = "INSERT INTO `".$this->table."` (";

            foreach ($fields as $field => $getter)
            {
            	if( $field != "id" ) {
            		// let's ignore the primary key!
	            	$fieldsString .= $field.", ";
            	
        	    	$value = $dbObject->$getter();
        	    	
            		if( is_array( $value )) $value = serialize( $value );     
					elseif( is_bool( $value ))  $value = (int)$value;  // convert the bool to '1' or '0'
            		elseif( is_object( $value )) {
                		if( strtolower(get_class( $value )) == "timestamp" )
            			    $value = $value->getTimestamp();
                        else
                            $value = serialize( $value );
            		}
	                $value = Db::qstr($value);
    	            $fieldsValuesString .= "'" . $value . "', ";
    	        }
            }

            $sql .= substr($fieldsString, 0, -2) . ") VALUES (".substr($fieldsValuesString, 0, -2).")";            
            
            $result = $this->Execute( $sql );            
            if( !$result )
            	return false;
            	
            $dbObject->setId( $this->_db->Insert_ID());            
            
            if( $cacheId ) {
            	foreach( $cacheId as $cache => $getter ) {
            		$this->_cache->setData( $dbObject->{$getter}(), $cache, $dbObject );
            	}
            }           

			return( $dbObject->getId());
        }
        
		/** 
		 * Updates an object/row in the database
		 *
		 * @param dbObject
		 * @return True if successful or false if error
		 */
        function update( &$dbObject )
        {
        	lt_include( PLOG_CLASS_PATH."class/database/db.class.php" );
        
            $fields = $dbObject->getFieldGetters();
            $sql    = "UPDATE `".$this->table."` SET ";

            foreach ($fields as $field => $getter)
            {
            	$value = $dbObject->$getter();
            	if( is_array( $value )) $value = serialize( $value );            	
            	elseif( strtolower(@get_class( $value )) == "timestamp" )
            		$value = $value->getTimestamp();
            	elseif( is_object( $value )) $value = serialize( $value );
            	
                $value = Db::qstr($value);
                $sql  .= "`" . $field . "`='" . $value . "', ";
            }

            $sql = substr($sql, 0, -2) . " WHERE id = '".Db::qstr($dbObject->getId())."'";            
            
            $result = $this->Execute( $sql );            
            
            if( !$result )
            	return false;

            return true;
        }
        
		/**
		 * Deletes a row from the database, using the field and value parameters as the condition
		 *
		 * @param field
		 * @param value
		 * @return True if successful or false otherwise
		 */
        function delete( $field, $value )
        {
			$query = "DELETE FROM ".$this->table." WHERE {$field} = '{$value}'";
			$result = $this->Execute( $query );								
			return( $result );
        }

        /**
         * returns the current database prefix
         *
         * @deprecated the prefix should be set/added by the Db Classes, not by the model
         * @return the current database prefix
         */
        function getPrefix()
        {
            if ( $this->_prefix === null ) {
                lt_include( PLOG_CLASS_PATH."class/database/db.class.php" );

                $this->_prefix = Db::getPrefix();
            }

            return $this->_prefix;
        }

        /**
         * retrieves the number of records in a table given an optional condition
         *
         * @param table
         * @param cond
         * @return the number of items or 0 if none or error
         */
        function getNumItems( $table, $cond = "", $pk = "id" )
        {
            // build up the query
            if( $cond != "" ) $cond = "WHERE $cond";
            $query = "SELECT COUNT($pk) AS total FROM $table $cond";

            // execute it
            $result = $this->Execute( $query );

            if( !$result )
                return 0;
            
            // if no error, get the result
            $row = $result->FetchRow();
            $result->Close();			

            $total = $row["total"];
            if( $total == "" ) $total = 0;

            return $total;
        }

        /**
         * Initialize the Db and set the table-prefix.
		 *
		 * @private
         */
        function _initializeDb()
        {
            if ( !$this->_dbInitialized ) {
                lt_include( PLOG_CLASS_PATH."class/database/db.class.php" );

                $this->_db =& Db::getDb();

                $this->_dbInitialized = true;
            }
        }
    }
?>