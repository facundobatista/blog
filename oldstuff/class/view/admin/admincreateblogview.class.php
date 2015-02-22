<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/blogcategories.class.php" );
	lt_include( PLOG_CLASS_PATH."class/net/http/subdomains.class.php" );
	
    /**
     * \ingroup View
     * @private
     */	
	class AdminCreateBlogView extends AdminTemplatedView
	{
	
		function AdminCreateBlogView( $blogInfo )
		{
			$this->AdminTemplatedView( $blogInfo, "createblog" );			
		}
		
		function render()
		{
			// get a list of blog categories, so we can let user to choose
			$blogCategories = new BlogCategories();
			$categories = $blogCategories->getBlogCategories();
			$this->setValue( "blogCategories", $categories );
			
			// enable or disable the drop-down list to select subdomains
			if( Subdomains::getSubdomainsEnabled()) {
				$this->setValue( "blogDomainsEnabled", true );
				$this->setValue( "blogAvailableDomains", Subdomains::getAvailableDomains());
			}			

			return( parent::render());
		}
	}
?>