<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/referers.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/data/pager/pager.class.php" );
	
	/**
	 * shows a list of the referrers collected by this site
	 */
	class AdminReferrersView extends AdminTemplatedView
	{
	
		function AdminReferrersView( $blogInfo )
		{
			$this->AdminTemplatedView( $blogInfo, "statistics" );
		}
		
		function render()
		{
        	$referers = new Referers();
			$totalReferers = $referers->getBlogTotalReferers( $this->_blogInfo->getId());
			// get the current page
			$this->_page = $this->getCurrentPageFromRequest();

            $blogReferers = $referers->getBlogReferers( $this->_blogInfo->getId(), 
			                                            $this->_page,
														DEFAULT_ITEMS_PER_PAGE );
														
            if( !$blogReferers ) {
				$blogReferers = Array();
            }
			
			// calculate the links to the different pages
			$pager = new Pager( "?op=Stats&amp;page=", 
			                    $this->_page, 
								$totalReferers, 
								DEFAULT_ITEMS_PER_PAGE );

            $this->setValue( "referrers", $blogReferers );
			$this->setValue( "pager", $pager );
		
			parent::render();
		}
	}
?>