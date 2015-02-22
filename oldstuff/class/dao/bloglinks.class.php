<?php
	lt_include( PLOG_CLASS_PATH."class/database/dbobject.class.php" );

    /**
     * \ingroup DAO
     *
     * Cache Object to cache all links of a blog, this information will not be stored in the db
     * but only in the disk-cache.
     */
    class BlogLinks  {

        var $_blogId;
        var $_links = array();
        var $_linksById = array();
        var $_categories = array();
   
        /**
         * Constructor
         *
         * @param blogId The blogId of the links to fetch
         * @links array containing link objects
         */
        function BlogLinks( $blogId, $blogLinks )
        {
            $this->_blogId = $blogId;

            foreach ($blogLinks as $link) {
                $this->_links[]                   = $link;
                $this->_linksById[$link->getId()] = $link;
                $this->_categories[$link->getCategoryId()][] = $link;
            }
        }

        /**
         * Get the BlogId for the links stored in this object
         */
        function getBlogId()
        {
            return $this->_blogId;
        }

        /**
         * Get all Links for current blog
         */
        function getLinks()
        {
            return $this->_links;
        }

        /** 
         * Get Links for a specific category, use categoryId = 0 to get all links.
         *
         * @param categoryId Category of the links to get
         * @return array of link objects
         */

        function getLinksForCategory( $categoryId )
        {
            if ( $categoryId == 0 )
                return $this->getLinks();
            elseif ( is_array( $this->_categories[$categoryId] ) )
                return $this->_categories[$categoryId];
            else 
                return array();
        }

        /**
         * Get specific link of current blog
         *
         * :TODO: this method is not yet used, we might change a few admin panels to use this method
         *        instead of myLinks->getMyLink(). 
         *                                              (2005-05-22 ork@devel.plogworld.net)
         *
         * @param linkId The id of the link to get
         * @return link object of false
         */

        function getLink( $linkId )
        {
            if ( isset($this->_links[$linkId]) )
                return $this->_links[$linkId];
            else 
                return false;
        }
    }
?>
