<?php

	lt_include( PLOG_CLASS_PATH."class/summary/action/summaryaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" ); 
	
	/**
	 * by default, how many posts show as recent from this blog
	 */
	define( "SUMMARY_DEFAULT_RECENT_BLOG_POSTS", 5 );

	/**
	 * shows a user profile
	 */
     class BlogProfileAction extends SummaryAction
     {
	 
		var $_blogId;

        function BlogProfileAction( $actionInfo, $request )
        {
            $this->SummaryAction( $actionInfo, $request );
            
            // data validation
            $this->registerFieldValidator( "blogId", new IntegerValidator());
			$view = new SummaryView( "summaryerror" );
			$view->setValue( "message", $this->_locale->tr("error_incorrect_blog_id"));
			$this->setValidationErrorView( $view );
        }

        /**
         * Loads the blog info and show it
         */
        function perform()
        {
			$this->_blogId = $this->_request->getValue( "blogId" );	        
	        
			$this->_view = new SummaryCachedView( "blogprofile", Array( "summary" => "BlogProfile", 
			                                                            "blogId" => $this->_blogId,
			                                                            "locale" => $this->_locale->getLocaleCode()));
			if( $this->_view->isCached()) {
				// nothing to do, the view is cached
				$this->setCommonData();
				return true;
			}
			
			lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );
			lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
			lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
			
			// load some information about the user
			$blogs = new Blogs();
			$blogInfo = $blogs->getBlogInfo( $this->_blogId, true );
			
			// if there was no blog or the status was incorrect, let's not show it!
			if( !$blogInfo || $blogInfo->getStatus() != BLOG_STATUS_ACTIVE ) {
				$this->_view = new SummaryView( "summaryerror" );
				$this->_view->setValue( "message", $this->_locale->tr("error_incorrect_blog_id"));
				return false;
			}
			
			// fetch the blog latest posts
			$posts = Array();
			$articles = new Articles();			
			$t = new Timestamp();
			$posts = $articles->getBlogArticles( $blogInfo->getId(),
			                                     -1,
												 SUMMARY_DEFAULT_RECENT_BLOG_POSTS,
												 0,
												 POST_STATUS_PUBLISHED,
												 0,
												 $t->getTimestamp());
												 
			
			$this->_view->setValue( "blog", $blogInfo );
			$this->_view->setValue( "blogposts", $posts );
			
			$this->setCommonData();			
		
            return true;
        }
     }	 
?>