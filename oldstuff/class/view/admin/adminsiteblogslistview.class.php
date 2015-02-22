<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/pager/pager.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );
		
    /**
     * \ingroup View
     * @private
     *
	 * shows a list with all the blogs from the site
	 */
	class AdminSiteBlogsListView extends AdminTemplatedView
	{
		var $_page;
		var $_status;
		var $_searchTerms;
		var $_pagerUrl;
		
		function AdminSiteBlogsListView( $blogInfo )
		{
			$this->_pagerUrl = "?op=editSiteBlogs";
			if( $this->_templateName == "" )
				$this->_templateName = "siteblogs";
			$this->AdminTemplatedView( $blogInfo, $this->_templateName );
			
			$this->_page = $this->getCurrentPageFromRequest();
		}
		
		/**
		 * retrieves the current status from the request
		 *
		 * @private
		 * @return nothing
		 */
		function getStatusFromRequest()
		{
			$status = $this->_request->getFilteredValue( "status", new HtmlFilter());
			
			// validate the value 
			$val = new IntegerValidator();
			if( !$val->validate( $status ))
				$status = BlogStatus::getDefaultStatus();
				
			// if the value validated, check if it is a valid status
			if( !BlogStatus::isValidStatus( $status ))
				$status = BlogStatus::getDefaultStatus();
				
			return $status;
		}		
		
		function render()
		{			
            // we need to get all the blogs
			// get the data itself
			$this->_status = $this->getStatusFromRequest();
			$this->_searchTerms = $this->_request->getFilteredValue( "searchTerms", new HtmlFilter());
			$blogs = new Blogs();
            $siteBlogs = $blogs->getAllBlogs( $this->_status, ALL_BLOG_CATEGORIES, $this->_searchTerms, $this->_page, DEFAULT_ITEMS_PER_PAGE );
//			print("search terms = ".$this->_searchTerms);
			$numBlogs = $blogs->getNumBlogs( $this->_status, ALL_BLOG_CATEGORIES, $this->_searchTerms );
            if( !$siteBlogs ) {
            	$siteBlogs = Array();
            }
            
            // throw the right event
			$this->notifyEvent( EVENT_BLOGS_LOADED, Array( "blogs" => &$siteBlogs ));            
            
			// calculate the links to the different pages
			$pager = new Pager( $this->_pagerUrl."&amp;searchTerms=".$this->_searchTerms."&amp;status=".$this->_status."&amp;page=",
			                    $this->_page, 
								$numBlogs, 
								DEFAULT_ITEMS_PER_PAGE );

			$this->setValue( "siteblogs", $siteBlogs );
			$this->setValue( "pager", $pager );
			$this->setValue( "currentstatus", $this->_status );
			$this->setValue( "blogstatus", BlogStatus::getStatusList( true ));
			$this->setValue( "searchTerms", $this->_searchTerms );
		
			// let the parent view do its job
			parent::render();                        
		}
	}
?>