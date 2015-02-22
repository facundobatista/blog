<?php

	lt_include( PLOG_CLASS_PATH."class/xml/tree/Node.php" );
    
    /** 
     * \ingroup Template
     *
     * Wraps around an XML_Tree_Node object to represent a node from the
     * menu tree
     */
    class MenuEntry extends XML_Tree_Node
    {
       
        /**
         * constructor
         *
         * @param entryId The name of the node/menu entry
         * @param entryAttrs an array containing the attributes of the menu entry (locale id,
         * use bread&crumbs, etc)
         */ 
        function MenuEntry( $entryId, $entryAttrs = Array()) 
        {
            $this->XML_Tree_Node( $entryId, '', $entryAttrs );
        }
        
        /**
         * adds a subentry. Reimplemented from XML_Tree_Node, it seems to behave a bit weird...
         *
         * @param entry
         * @return Returns true if successful or false otherwise
         */
        function addEntry( &$entry, $entryOrder = -1 )
        {
            // calculate the right position
            if ($entryOrder < 0) {
                $entryOrder = count($this->children);
            }
            
            // and then simply add the node
            $this->children[$entryOrder] = &$entry;
        }
    }
?>