<?php
	
	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminblogcategorieslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );
	
	class AdminBlogCategoriesAction extends AdminAction
	{
			
		function AdminBlogCategoriesAction( $actionInfo, $request )
		{
			$this->AdminAction( $actionInfo, $request );

			$this->registerFieldValidator( "searchTerms", new StringValidator(), true);
			$this->setValidationErrorView( new AdminBlogCategoriesListView( $this->_blogInfo ) );
			
			$this->requireAdminPermission( "view_blog_categories" );
		}
		
		function perform()
		{
			$searchTerms = $this->_request->getFilteredValue( "searchTerms", new HtmlFilter());
			$this->_view = new AdminBlogCategoriesListView( $this->_blogInfo, Array( "searchTerms" => $searchTerms ));
			$this->setCommonData();
		}
	}
?>