<?php

	lt_include( PLOG_CLASS_PATH."class/dao/mylink.class.php" );
	
	/**
	 * used for the archive links, it extends it adding an additional method to fetch
	 * the number of posts in that month
	 *
	 * @private
	 *
	 * \ingroup DAO
	 */
	class ArchiveLink extends MyLink
	{
		var $_numPosts;
		
		function ArchiveLink( $name, $description, $url, $blogId, $categoryId, $numPosts, $id = -1)
		{
			$this->MyLink( $name, $description, $url, $blogId, $categoryId, 0, "", $id );
			
			$this->_numPosts = $numPosts;
		}
		
		function getNumArticles()
		{
			return $this->_numPosts;
		}
	}
?>