<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/globalarticlecategories.class.php" );

    /**
     * \ingroup View
     * @private
     *	
	 * shows the view that will allow to edit an article
	 */
	class AdminNewGlobalArticleCategoryView extends AdminTemplatedView
	{
		
		var $_article;
	
		function AdminNewGlobalArticleCategoryView( $blogInfo )
		{
			$this->AdminTemplatedView( $blogInfo, "newglobalarticlecategory" );
		}
	}
?>