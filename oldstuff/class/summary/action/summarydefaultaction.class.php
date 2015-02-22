<?php

	lt_include( PLOG_CLASS_PATH."class/summary/action/summaryaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/summary/dao/summarystatsconstants.class.php" );  
	lt_include( PLOG_CLASS_PATH."class/summary/net/summaryrequestgenerator.class.php" );      

     /**
      * This is the one and only default action. It simply fetches all the most recent
      * posts from the database and shows them. The default locale is the one specified
      * in the configuration file and the amount of posts shown in this page is also
      * configurable through the config file.
      */
     class SummaryDefaultAction extends SummaryAction
     {
        function SummaryDefaultAction( $actionInfo, $request )
        {
            $this->SummaryAction( $actionInfo, $request );
        }

        /**
         * Loads the posts and shows them.
         */
        function perform()
        {
            // get the blogCategoryId from request
			$globalArticleCategoryId = $this->_request->getValue( "globalArticleCategoryId" );
			$val = new IntegerValidator();
			if( !$val->validate( $globalArticleCategoryId ))
				$globalArticleCategoryId = ALL_GLOBAL_ARTICLE_CATEGORIES;
				
			$this->_view = new SummaryCachedView( "index", 
												  Array( "summary" => "default",
												  "globalArticleCategoryId" => $globalArticleCategoryId,
												  "locale" => $this->_locale->getLocaleCode()));
												
			if( $this->_view->isCached()) {
				// if the view is already cached... move along! nothing to see here
				$this->setCommonData();
				return true;
			}

            lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
            lt_include( PLOG_CLASS_PATH."class/dao/globalarticlecategories.class.php" );
            lt_include( PLOG_CLASS_PATH."class/summary/dao/summarystats.class.php" );
            $blogs       = new Blogs();
            $stats       = new SummaryStats();

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
			
			if( $globalArticleCategoryId != ALL_GLOBAL_ARTICLE_CATEGORIES )
			{
				$currentGlobalArticleCategory = $categories->getGlobalArticleCategory( $globalArticleCategoryId );
				$this->_view->setValue( "currentGlobalArticleCategory", $currentGlobalArticleCategory);
			}
			
			// export the value for global article categories
			$this->_view->setValue( "summaryStats", $stats );
			$this->_view->setValue( "globalArticleCategories", $globalArticleCategories );
			$this->_view->setValue( "min", $min );
			$this->_view->setValue( "step", $step );
			$this->_view->setValue( "globalArticleCategoryId", $globalArticleCategoryId );
		
			// summary request generator
			$this->_view->setValue( "url", new SummaryRequestGenerator());
			
			$this->setCommonData();

            return true;
        }
     }
?>