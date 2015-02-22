<?php

	lt_include( PLOG_CLASS_PATH."class/summary/view/summarycachedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/globalarticlecategories.class.php" ); 
    lt_include( PLOG_CLASS_PATH."class/data/pager/pager.class.php" );
    lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	lt_include( PLOG_CLASS_PATH."class/summary/dao/summarystats.class.php" );
	
	/**
	 * shows a paged list of blogs
	 */
	class SummaryPostListView extends SummaryCachedView
	{
		var $_numArticlesPerPage;
	
		function SummaryPostListView( $data = Array())
		{
			// get the page
			$this->_page = $this->getCurrentPageFromRequest();
			
			$this->SummaryCachedView( "postslist", $data );
			
            // items per page
            $config =& Config::getConfig();
			$this->_numArticlesPerPage = $config->getValue( "summary_items_per_page", SUMMARY_DEFAULT_ITEMS_PER_PAGE );
		}
		
		function render()
		{
			// do nothing if the contents of our view are cached
			if( $this->isCached()) {
				parent::render();
				return true;
			}
            // get all blog category
            $categories = new GlobalArticleCategories();
            $globalArticleCategories = $categories->getGlobalArticleCategories();

            $min = 0;
            $max = 0;
 
            foreach( $globalArticleCategories as $globalArticleCategory ){
            	$numActiveArticles = $globalArticleCategory->getNumActiveArticles();
            	if( $numActiveArticles < $min ) {
            		$min = $numActiveArticles;
            		continue;
            	}
            	if( $numActiveArticles > $max ) {
            		$max = $numActiveArticles;
            		continue;
            	}
            }
            
            $step = ( $max - $min )/6;
            if($step == 0) 
            	$step = $min + 1;            
            
			// get current globalArticleCategoryId
			$globalArticleCategoryId = $this->_params->getValue( "globalArticleCategoryId" );
			$currentGlobalArticleCategory = $categories->getGlobalArticleCategory( $globalArticleCategoryId );

			if( empty($currentGlobalArticleCategory) )
				$globalArticleCategoryId = ALL_GLOBAL_ARTICLE_CATEGORIES;

			// get the data itself
			$stats = new SummaryStats();
            $posts = $stats->getPostsByGlobalCategory( $globalArticleCategoryId, $this->_page, $this->_numArticlesPerPage );
            $numPosts = $stats->getNumPostsByGlobalCategory( $globalArticleCategoryId );
			
            if( !$posts ) {
                // if there was an error, show the error view
				$posts = Array();
            }
			
			// calculate the links to the different pages
			$pager = new Pager( "?op=PostList&amp;globalArticleCategoryId=".$globalArticleCategoryId."&amp;page=",
			                    $this->_page, 
								$numPosts, 
								$this->_numArticlesPerPage );

			$this->setValue( "recentPosts", $posts );
			$this->setValue( "numRecentPosts", $numPosts );
			$this->setValue( "pager", $pager );
			$this->setValue( "globalArticleCategories", $globalArticleCategories );
			$this->setValue( "currentGlobalArticleCategory", $currentGlobalArticleCategory); 
			$this->setValue( "min", $min );
			$this->setValue( "step", $step );
					
			// let the parent view do its job
			parent::render();
		}
	}
?>
