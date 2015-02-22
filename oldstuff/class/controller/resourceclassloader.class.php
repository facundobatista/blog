<?php

	define("DEFAULT_CLASSFILE_SUFFIX", ".class.php");

	/**
	 * \ingroup Controller
	 *
 	 * This class takes care of dynamically loading classes for the controller. 
 	 *
 	 * This is a very simple class that keeps a list of folders where action classes can be found. Whenever
 	 * the controller requests to load an action class, this loader will go one by one through all the folders
 	 * until the class is found, or it will fail if none can be found. 
 	 *
 	 * New folders to be scanned can be added via the static method ResourceClassLoader::addSearchFolder(),
 	 * and the loading of classes takes place in the ResourceClassLoader::load() method.
 	 *
 	 * This object should never be instantiated directly but instead, please use the 
 	 * ResourceClassLoader::getLoader() method which will return a reference to an already existing instance.
	 */
	class ResourceClassLoader 
	{

		var $_paths;
		var $_classFileSuffix;

		/**
		 * initializes the class loader. It is advisable to use the 
		 * static ResourceClassLoader::getLoader() method
		 *
 		 * @param path The starting path where classes can be loaded. It defaults to "./"
		 * @param classFileSuffix default suffix that each class file will have. It defaults to
		 * ".class.php"
		 */
		function ResourceClassLoader( $path = "./", $classFileSuffix = DEFAULT_CLASSFILE_SUFFIX )
		{
			$this->_paths = Array( $path );
			$this->_classFileSuffix = $classFileSuffix;
		}

		/**
		 * static method that returns a single instance of this class. 
		 *
		 * @static
		 * @param path If the object is being created for the first time, this will be passed to the constructor
		 * of the class as the first parameter. If a class instance already exists, this path will be added to the
		 * list of already existing paths to scan.
		 * @return a ResourceClassLoader object
		 */
		function &getLoader( $path = null )
		{
			static $instance;

			if( $instance == null ) {
				// create an instance if it does not exist yet...
				$instance = new ResourceClassLoader();
			}

			// if a path is given and the object already exists, then
			// we can also automatically add it to the list of searched folders...
			if( $path != null ) 
				$instance->addSearchFolder( $path );
		
			return $instance;
		}

		/**
		 * Adds a new folder to the list of folders to be searched
		 * 
		 * @param folder The new folder that will be added
		 * @return always true
		 */
		function addSearchFolder( $folder )
		{		
			$this->_paths[] = $folder;

			return true;
		}

		/**
		 * sets a new suffix for class files, in case our class files do not end with .class.php. Please
		 * note that only <b>one</b> suffix can be used at the same time.
		 *
		 * @param suffix the new suffix
		 * @return always true
		 */
		function setClassFileSuffix( $suffix )
		{
			$this->_classFileSuffix = $suffix;

			return true;
		}
		
		/**
		 * Loads classes from disk using the list of folders that has been provided 
		 * via ResourceClassLoader::addSearchFolder() The class will go through all the folders where 
		 * classes can be located and if it can be found, it will proceed to load it. 
		 * If not, an exception will be thrown
		 * 
		 * @param actionClassName name of the class that we are going to load, <b>without the class suffix</b>
	 	 * @return True if successful
		 */
		function load( $actionClassName )
		{
			lt_include(PLOG_CLASS_PATH . "class/file/file.class.php");			
			
			//foreach( $this->_paths as $path ) {
			$i = 0;
			$loaded = false;
			
			$numPaths = count( $this->_paths );
			while( ($i < $numPaths ) && !$loaded ) {
				// get the current folder
				$path = $this->_paths[$i];
				// build up the file name
				$fileName = $path.strtolower($actionClassName).$this->_classFileSuffix;
				// and see if it exists and can be loaded
				lt_include( PLOG_CLASS_PATH."class/file/file.class.php" );
				if( File::exists( $fileName ) && File::isReadable( $fileName )) {
					lt_include( $fileName );
					$loaded = true;
				}
				// increase the counter
				$i++;
			}

			// did we load anything??
			if( !$loaded ) {
				die( "Could not load $actionClassName!" );
			}

			// otherwise return everything ok!
			return true;
		}
	}
?>
