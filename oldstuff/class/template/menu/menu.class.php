<?php

	lt_include( PLOG_CLASS_PATH."class/xml/tree/Tree.php" );
	lt_include( PLOG_CLASS_PATH."class/template/menu/menuentry.class.php" );
	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	
	define( "DEFAULT_MENU_FILE", "/admin/menus.xml" );

    /**
     *  \ingroup Template
     *
     * Implements support for dealing with xml tree-based menus.
     *
     * There is more information about the menu API and how to use it here:
     * http://wiki.lifetype.net/index.php/Menu_api
     *
     * If you need to get the global instance of the menu, use the Menu::getMenu() singleton method. 
     *
     * New entries can be dynamically added to the menu via the Menu::addEntry() method, and the whole
     * menu can be rendered by using the MenuRenderer class.
     *
     * @see MenuEntry
     * @see MenuRenderer
     */
    class Menu extends XML_Tree
    {
    
		/**
		 * constructor
		 *
		 * @param menuFile
		 * @return nothing
		 */
        function Menu( $menuFile = "")
        {
			// generate the path to the menu file
			if( $menuFile == "" ) {
				$config =& Config::getConfig();
				$menuFile = $config->getValue( "template_folder" ).DEFAULT_MENU_FILE;			
			}
			
			// generate the tree
            $this->XML_Tree( $menuFile );
            $this->getTreeFromFile();
        }
		
		/**
		 * returns a valid instance of the global menu
		 *
		 * @static
		 * @return A valid Menu object
		 */
		function &getMenu( $menuFile = "" )
		{
			static $instance;
			
			// create a new instance of the menu if it does not exist yet...
			if( $instance == null )
				$instance = new Menu( $menuFile );
				
			// once we have it, return this instance
			return $instance;
		}
        
        /**
         * @private
         * @see findNode
         * Helper function that helps to find a certain node in the menu tree
         */
        function _findNode( $node, $nodeId )
        {
            //print("node = $node->name<br/>");
            if( $node->name == $nodeId )
                return $node;
            else {
                $i=0;
                $found=false;
                while( !$found && $i < count($node->children)) {
                    $found = $this->_findNode( $node->children[$i], $nodeId );
                    $i++;
                }
            }

            return $found;				
        }
        
        /**
         * finds a node in the tree
         *
         * @param nodeId
         * @return Returns an XML_Tree_Node object or null if not found
         */
        function findEntry( $nodeId ) 
        {
            $root = $this->getRoot();
            return $this->_findNode( $root, $this->_prepareEntryPath($nodeId));
        }
        
        /**
         * @private
         * @see nodePath
         */
        function _nodePath( $node, $nodeId, $stack = Array())
        {
            //print("node = $node->name<br/>");
            if( $node->name == $nodeId ) {
                while( $tmp = array_pop($stack))
                    $path = $tmp->name."/".$path;
                $path = $path.$node->name;

                return $path;
            }
            else {
                $i=0;
                $found=false;
                while( !$found && $i < count($node->children)) {
                    //print("i=$i<br/>");
                    array_push( $stack, $node );
                    $found = $this->_nodePath( $node->children[$i], $nodeId, $stack );
                    $i++;
                    array_pop( $stack );
                }
            }

            return $found;
        }
        
        /**
         * @private
         * Removes the "/" from the beginning of a path, if it exists at all
         * @return The cleaned up version of the path
         */
        function _prepareEntryPath( $entryPath )
        {
            if( $entryPath[0] == "/" )
                $entryPath = substr($entryPath, 1, strlen($entryPath)-1);
                
            return $entryPath;
        }

        /**
         * returns the path to a menu entry
         *
         * @param nodeId The id of the node
         * @param node Optional, a name of the starting node if it's not the root
         * @return An XML_Tree_Node object or null if not found
         */
        function entryPath( $nodeId, $node = null )
        {
            if( $node == null )
                $node = $this->getRoot();

            return $this->_nodePath( $node, $nodeId );
        }
        
        /**
         * adds a menu entry to the menu
         * 
         * @param entryPath
         * @param entryId
         * @param entryAttrs
         * @param entryOrder
         */
        function addEntry( $entryPath, $entry, $entryOrder = -1 )
        {
            // insertChild will return a reference to the node or PEAR_Error if there was
            // a problem
            $node = $this->insertChild( $this->_prepareEntryPath($entryPath), // path to the entry
                                        $entryOrder,  // order, use '-1' to specify that it should be the last
                                        $entry // object 
                                        );

			//
			// this is a bit tricky, but the problem is that when we adde a new menu entry (a new node to the tree)
			// the nodes above it should also inherit the new permission that is required to view this entry. Otherwise
			// if a new node is added with let's say permission 'manage_plugins' but the nodes above it only have 'view_links' and 
			// 'edit_links', then that entry and all the ones above it would not rendered by the MenuRenderer class. 
			// By means of the code below, we're replicating the needed permissions to all the parent nodes so that we can get the
			// upper level entries to be displayed
			//
			$path = split( "/", $entryPath );
			$currentPath = "";
			foreach( $path as $step ) {
				if( $step != "" ) {
					//print($currentPath."/".$step."<br/>");
					$currentPath = $currentPath."/".$step;
					$node =& $this->getNodeAt( $this->_prepareEntryPath( $currentPath ));
					// add the new node's orPerms and andPerms
					$curOrPerms = $node->getAttribute( "orPerms" );
					if( $curOrPerms != "" ) {
						$node->setAttribute( "orPerms", $curOrPerms.",".$entry->getAttribute( "orPerms" ));
					}
					$curAndPerms = $node->getAttribute( "andPerms" );					
					if( $curAndPerms != "" ) {
						$node->setAttribute( "andPerms", $curAndPerms.",".$entry->getAttribute( "andPerms" ));
					}
				}
			}

            
            $ok = $this->isError( $node );
            
            return $ok;
        }
        
        /**
         * alias for getNodeAt
         *
         * @param path
         */
        function getEntryAt( $path )
        {
            return $this->getNodeAt( $path );
        }
        
        /**
         * returns all the "brother" nodes or in other words, all the nodes that have
         * the same parent
         *
         * @param path
         * @return An array of nodes
         */
        function getEntryBrothers( $path )
        {
            // no brothers or sister if the path is pointing to the root!
            if( $path == $this->root->name )
                return Array();
                
            // if not, then we can proceed...
            $node = $this->getEntryAt( $path );
            
            if( $node )
                return $node->parent->children;
            else
                return Array();
        }
		
		/**
		 * returns whether there is an entry in the given path or not
		 *
		 * @param entryPath
		 * @return True if it exists or false otherwise
		 */
		function entryExists( $entryPath )
		{
			$entryExists = ( $this->getNodeAt( $this->_prepareEntryPath($entryPath)) != null );
			
			return $entryExists;
		}
    }
?>