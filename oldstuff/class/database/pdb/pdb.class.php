<?php
    /**
     * \defgroup PDb
	 *
	 * Since LifeType 1.1, ADOdb is no longer part of LifeType and now all database interaction is handled via PDb,
	 * a much tighter implementation of a database abstraction layer inspired on ADOdb (PDb is like ADOdb, with the
	 * only difference that only those methods from ADOdb that were used by LifeType's code were implemented)
	 *
	 * The only noticeable difference that may make your code incompatible with this implementation is that the
	 * function NewDataDictionary() used to get a data dictionary in ADOdb has been renamed to 
	 * NewPDbDataDictionary() to avoid problems where both ADOdb and PDb have to coexist.
     */
    
    define( "PDB_DRIVER_FOLDER", PLOG_CLASS_PATH."class/database/pdb/drivers/" );
    
    /**
     * PDb
     *
     * LifeType's own lightweight database abstraction layer modelled after ADOdb but only implementing
 	 * those methods that are really needed.
     *
     * \ingroup PDb
     */
    class PDb 
    {
		/** 
		 * Constructor of the class
		 */
        function PDb()
        {            
        }
        
        /**
         * return the right driver type
         *
         * @param driver the driver name. Supported types in LifeType 1.1 are:
         *
         * - mysql
         *
         * @return An object of a class extending the PDbDriverBase class that implements the requested
         * database access.
         * @see PDbDriverBase
         */
        function getDriver( $driver )
        {
            $validDrivers = PDb::getValidDrivers();

            if( array_key_exists($driver, $validDrivers) ) {
                // load the driver class
                $driverPath = PDB_DRIVER_FOLDER.strtolower( $validDrivers[$driver] ).".class.php";
                lt_include( $driverPath );
                
                // create an instance of it
                $driverClass = new $validDrivers[$driver]();
                
                return( $driverClass );
            } else {
                return false;
            }
        }

		/** 
		 * Returns an array containing the names of the drivers available
		 *
		 * @return An associative array, where the key is the name of the driver and the
		 * value is the name of the class implementing the driver
		 */
        function getValidDrivers() 
		{
            $_drivers = Array( "mysql"    => "PDbMySQLDriver" );           
    
            return $_drivers;
        }

		/**
		 * Returns an array with only the driver names, which are the only suitable names for the call
		 * to PDb::getDriver
		 *
		 * @return An array with the driver names
		 * @see PDb::getDriver()
		 */
        function listDrivers() 
		{
            return array_keys( PDb::getValidDrivers() );
        }
    }
    
    /**
     * ADOdb compatibility, although this method was renamed to NewLifeTypeDataDictionary() not
     * to collide with ADOdb's own in those system where these two libraries may be loaded at the
     * same time (i.e. when the gallery2 plugin is loaded)
     *
     * @param driver A driver class whose data dictionary class we'd like to get. This method is obsolete
     * and you should call PDbDriverBase::getDriverDataDictionary(). This method is only here for compatibility
     * reasons.
     *
     * @see PDbDriverBase::getDriverDataDictionary()     
     * @deprecated
     */
    function NewPDbDataDictionary( $driver )
    {
        return( $driver->getDriverDataDictionary());
    }
?>
