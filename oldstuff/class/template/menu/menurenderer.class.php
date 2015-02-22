<?php

    
    lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );
    lt_include( PLOG_CLASS_PATH."class/template/templateservice.class.php" );	
    lt_include( PLOG_CLASS_PATH."class/template/menu/menu.class.php" );	
    lt_include( PLOG_CLASS_PATH."class/template/menu/menuentry.class.php" );
	lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );
    
    /**
     * \ingroup Template
     *
     * dumps the contents of the menu in a tree-fashion way using unordered html lists. It is necessary
     * to use some additional CSS styling to give some formatting to the menu.
     *
     * This class is only used at the presentation layer of pLog to generate the menu structure,
     * and class users should never need to create an instance of this class.
     *
     * See the "Menu API reference" document in the wiki for more information:
     * http://wiki.plogworld.net/index.php/PLog_1.0/The_Menu_API
     */
    class MenuRenderer 
    {
        var $_menu;
		var $_blogInfo;
		var $_userInfo;
		
		/**
		 * Initializes the renderer
		 *
		 * @param menu A vaid Menu object
		 * @param blogInfo A reference to a BlogInfo object containing information of the blog for whom
		 * we are going to render this menu tree.
		 * @param userInfo A reference to a UserInfo object containing information of the user for whom
		 * we are going to render this menu tree, because we need to know which options he/she is allowed
		 * to see and which not.
		 */
        function MenuRenderer( $menu, $blogInfo, $userInfo )
        {
            
            $this->_menu = $menu;
			$this->_blogInfo = $blogInfo;
			$this->_userInfo = $userInfo;
			
			$config =& Config::getConfig();			
			
			// load the right locale
			if( $blogInfo != null ) {
				$this->_locale =& $blogInfo->getLocale();
			}
			else { 
				$localeCode = $config->getValue( "default_locale" );
				$this->_locale =& Locales::getLocale( $localeCode );
			}
        }
        
        /**
         * renders the contents of the menu
         *
         * @param depth How many depth levels we should generate.
         * @param renderFunc render method for menu tree , the value is "JavaScript" and "Plain"         
         */
        function generate( $depth = 99, $renderFunc = "Plain" )
        {
            $node = $this->_menu->getRoot();
            
            if ( $renderFunc != "JavaScript" ) {
            	return $this->_render( $node, $depth );
            } else {
            	$menu = "<script type=\"text/javascript\">\n";
            	$menu .= "<!-- \n";
            	$menu .= "var mainMenu = [\n";
            	$menu .= $this->_renderJS( $node, $depth );
            	$menu .= "];\n";
				$menu .= "cmDraw ('menu', mainMenu, 'hbr', cmThemeOffice, 'ThemeOffice');\n";
				$menu .= "-->\n";
				$menu .= "</script>\n";
            	
            	return $menu;
        	}
        }
		
		/**
		 * Generates a tree starting from another node which can be different than the
		 * root node
		 *
		 * @param nodeId
		 * @param depth
		 * @param activeOpt
		 * @param renderFunc render method for menu tree , the value is "JavaScript" and "Plain"		 
		 */
		function generateAt( $nodeId, $depth = 1, $activeOpt = "", $renderFunc = "Plain"  )
		{
			$nodePath = $this->_menu->entryPath( $nodeId );
			$node = $this->_menu->getEntryAt( $nodePath );
			$start = 0;
            
			if ( $renderFunc == "JavaScript" ) {
            	$menu = "<script type=\"text/javascript\">\n";
            	$menu .= "<!-- \n";
            	$menu .= "var mainMenu = [\n";
            	$menu .= $this->_renderJS( $node, $depth, $start, $activeOpt );
            	$menu .= "];\n";
				$menu .= "cmDraw ('menu', mainMenu, 'hbr', cmThemeOffice, 'ThemeOffice');\n";
				$menu .= "-->\n";
				$menu .= "</script>\n";
            	return $menu;
            } else {
            	return $this->_render( $node, $depth, $activeOpt );
        	}
		}

		/**
		 * returns true if the user has enough permissions to see this node from the menu
		 *
		 * @param node
		 * @param userInfo
		 * @return true if the user can, false otherwise
		 */
		function userCanSee( $node )
		{
			// if the user is the blog owner, then he can see
			if( $node->getAttribute( "admin" ) != "1" ) {
				if( $this->_userInfo->getId() == $this->_blogInfo->getOwnerId())
					return true;
			}
			// if the user has the 'admin mode' permission, then he can see too
			if( $this->_userInfo->hasPermissionByName( "edit_blog_admin_mode", 0 )) {
				return( true );
			}
				
			// get the AND permissions for the node but if there are no permissions assigned, then we can see
			$nodeAndPerms = $node->getAttribute("andPerms");
			if( $nodeAndPerms != "" ) {
				//print("here? node: ".$node->name."<br/>");				
				// we can specify more than one permissions separated with a comma
				$perms = explode( ",", $nodeAndPerms );
				// and check if the current user has such permission in this blog
				foreach( $perms as $perm ) {
					$perm = trim( $perm );
					if( (!$this->_userInfo->hasPermissionByName( $perm, $this->_blogInfo->getId())) &&
					    (!$this->_userInfo->hasPermissionByName( $perm, 0 ))) {
						return false;
					}
				}
			}
			else {
				// get the OR permissions for the node but if there are no permissions assigned, then we can see
				$nodeOrPerms = $node->getAttribute("orPerms");
				if( $nodeOrPerms == "" )
					return true;
				
				// we can specify more than one permissions separated with a comma
				$perms = explode( ",", $nodeOrPerms );
				// and check if the current user has such permission in this blog
				foreach( $perms as $perm ) {
					$perm = trim( $perm );		
					if( ($this->_userInfo->hasPermissionByName( $perm, $this->_blogInfo->getId())) ||
					    ($this->_userInfo->hasPermissionByName( $perm, 0 ))) {
						return true;
					}
				}
				
				return false;
			}			

			// if none of the above is true, then the user does not have enough permissions!
			return true;
		}
        
        /**
         * @private
         * Used by render() to really render the tree
         */
        function _render( $node, $depth = 0, $activeOpt = "", $menuTop = true )
        {
            $result = ($menuTop === true) ? "<ul class=\"menuTop\">" : "<li><ul class=\"menuTop\">";
            $depth--;
            foreach( $node->children as $child ) {
                if( $child->name != "" ) {
					// check whether the user has the right permissions to see this
					if( $this->userCanSee( $child )) {
						$url = $child->getAttribute( "url" );
						$localeId = $this->getLocaleId( $child );
						$cssClass = "Level_".$depth;
						if( $url != "" ) 
							$result .= "<li class=\"$cssClass\"><a href=\"".$child->getAttribute("url")."\">".$this->_locale->tr($localeId)."</a></li>";
						else
							$result .= "<li class=\"$cssClass\">".$this->_locale->tr($child->name)."</li>";
						
						if( $depth > 0 ) {					
							$result .= $this->_render( $child, $depth, $activeOpt, false );
					    }
					}
				}
			}
			$result .= ($menuTop === true) ? "</ul>" : "</ul></li>";
			
			return $result;
        }

        /**
         * @private
         * Used by renderJS() to really render the Javascriipt menu tree
         */
        function _renderJS( $node, $depth = 0, $start = 0, $activeOpt = "" )
        {
			$result  = "";
            $depth--;
            foreach( $node->children as $child ) {
                if( $child->name != "" ) {
					// check whether the user has the right permissions to see this
					if( $this->userCanSee( $child )) {
		            	if( $start == 0 ) {
		            		$result .= "[";
		            		$start = 1;
		            	} else {
				    		$result .= ",[";
				    	}
				    	
						$url = $child->getAttribute( "url" );
						$localeId = $this->getLocaleId( $child );
										
						// escape the string for javascript
						$menuText = strtr($this->_locale->tr( $localeId ), array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
						
						if( $url != "" ) 
							$result .= "null, '".$menuText."', '".$child->getAttribute("url")."', '_self', null";
						else
							$result .= "null, '".$menuText."', '#', '_self', null";
						
						if( $depth > 0 )
							$result .= $this->_renderJS( $child, $depth, $start, $activeOpt );

						$result .= "]";
					}
				}
			}
			
			return $result;
        }        
		
		/**
		 * gets the locale id, given a node. The locale id can be either the value of the
		 * "localeId" attribute if specified or the name of the tag otherwise
		 *
		 * @param entry A valid menu node
		 * @return the locale identifier
		 */
		function getLocaleId( $entry )
		{
			$localeId = $entry->getAttribute("localeId");
			if( $localeId == "" )
				$localeId = $entry->name;
				
			return $localeId;			
		}
		
        /**
         * simple version of a "breadcrumbs" feature
         *
         * @param entryId
         */
        function breadCrumbs( $entryId )
        {
           $nodePath = $this->_menu->entryPath( $entryId );
		   $path = "";
		   
           if( $nodePath ) {
                $parts = explode( "/", $nodePath );
				$totalParts = count($parts);
				if( $totalParts == 1 ) $insertBlogLink = true;
				else $insertBlogLink = false;
				
				$count=0;
                foreach( $parts as $part ) {
					$path .= $part;
					$menuEntry = $this->_menu->getEntryAt( $path );
					if( $menuEntry->getAttribute("ignoreBreadCrumbs") != "1" ) {
						$localeId = $this->getLocaleId( $menuEntry );
						if( $entryId == $part ) $result .= "<b>";
						if( $menuEntry->getAttribute("url") != "" )
							$result .= "<a href=\"".$menuEntry->getAttribute("url")."\">".$this->_locale->tr($localeId)."</a>";
						else
							$result .= $this->_locale->tr($part);
						if( $entryId == $part ) $result .= "</b>";						
						if( $count == 0 ) $result .= " &raquo; <a href=\"?op=blogSelect&amp;blogId=".$this->_blogInfo->getId()."\">".$this->_blogInfo->getBlog()."</a>";
						if( $count < $totalParts-1 ) $result .= " &raquo; ";
					}
					$count++;
					$path .= "/";
				}
           }
		   
		   return $result;
        }
		
		/**
	 	 * @param entryId
		 */
	   function getOpts( $entryId) 
	   {
			$nodePath = $this->_menu->entryPath( $entryId );
			$parts = explode( "/", $nodePath );
			array_pop( $parts );	
			$parentPath = implode( "/", $parts );
			
			$parentNode = $this->_menu->getNodeAt( $parentPath );
			
			$children = $parentNode->children;
			
			// the children of my parent are... my brothers :)))
			return $children;
	   }
    }
?>
