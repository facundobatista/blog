<?php

	lt_include( PLOG_CLASS_PATH."class/summary/view/summarycachedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/pager/pager.class.php" );
    lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	lt_include( PLOG_CLASS_PATH."class/summary/dao/summarystats.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/blogcategories.class.php" ); 	
	
	/**
	 * shows a paged list of blogs
	 */
	class SummaryBlogListView extends SummaryCachedView
	{
		var $_numBlogsPerPage;
	
		function SummaryBlogListView( $data = Array())
		{
			// get the page
			$this->_page = $this->getCurrentPageFromRequest();
			
			$this->SummaryCachedView( "blogslist", $data );
			
			// items per page
            $config =& Config::getConfig();
			$this->_numBlogsPerPage = $config->getValue( "summary_items_per_page", SUMMARY_DEFAULT_ITEMS_PER_PAGE );
		}
		
		function render()
		{
			// do nothing if the contents of our view are cached
			if( $this->isCached()) {
				parent::render();
				return true;
			}
            // get all blog category
            $categories = new BlogCategories();
            $blogCategories = $categories->getBlogCategories();
            
            $min = 0;
            $max = 0;
 
            foreach( $blogCategories as $blogCategory ){
            	$numActiveBlogs = $blogCategory->getNumActiveBlogs();
            	if( $numActiveBlogs < $min ) {
            		$min = $numActiveBlogs;
            		continue;
            	}
            	if( $numActiveBlogs > $max ) {
            		$max = $numActiveBlogs;
            		continue;
            	}
            }
            
            $step = ( $max - $min )/6;
            if($step == 0) 
            	$step = $min + 1;                
            
			// get current blogCategory
			$blogCategoryId = $this->_params->getValue( "blogCategoryId" );
			$currentBlogCategory = $categories->getBlogCategory( $blogCategoryId );
			if( !$currentBlogCategory )
				$blogCategoryId = ALL_BLOG_CATEGORIES;

			// get the data itself
			$blogs = new Blogs();						
            $siteBlogs = $blogs->getAllBlogs( BLOG_STATUS_ACTIVE, $blogCategoryId, "", $this->_page, $this->_numBlogsPerPage );
			$numBlogs = $blogs->getNumBlogs( BLOG_STATUS_ACTIVE, $blogCategoryId );		
			
            if( !$siteBlogs ) {
                // if there was an error, show the error view
				$siteBlogs = Array();
            }
			
			// calculate the links to the different pages
			$pager = new Pager( "?op=BlogList&amp;blogCategoryId=".$blogCategoryId."&amp;page=",
			                    $this->_page, 
								$numBlogs, 
								$this->_numBlogsPerPage );

			$this->setValue( "blogs", $siteBlogs );
			$this->setValue( "pager", $pager );
			$this->setValue( "blogCategories", $blogCategories );
			$this->setValue( "currentBlogCategory", $currentBlogCategory);
			$this->setValue( "min", $min );
			$this->setValue( "step", $step );

			// let the parent view do its job
			parent::render();
		}
	}
?>