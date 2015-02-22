<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/referers.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/data/pager/pager.class.php" );
	
    /**
     * \ingroup View
     * @private
     *
	 * shows a list of the referrers collected by this site for a particular post
	 */
	class AdminArticleReferrersView extends AdminTemplatedView
	{
	
		function AdminArticleReferrersView( $blogInfo, $params )
		{
			$this->AdminTemplatedView( $blogInfo, "statistics" );
			
			$this->_page = $params["page"];
			$this->_article = $params["article"];
			if( $this->_page == "" ) $this->_page = 1;
		}
		
		function render()
		{
        	$referers = new Referers();
			$totalReferers = $referers->getBlogTotalReferers( $this->_blogInfo->getId(), $this->_article->getId());
            $postReferers = $referers->getArticleReferers( $this->_article->getId(), 
			                                               $this->_blogInfo->getId(), 
														   $this->_page,
														   DEFAULT_ITEMS_PER_PAGE );

            if( !$postReferers ) {
				$postReferers = Array();
            }
			
			// calculate the links to the different pages
			$pager = new Pager( "?op=postStats&amp;postId=".$this->_article->getId()."&amp;page=", 
			                    $this->_page, 
								$totalReferers, 
								DEFAULT_ITEMS_PER_PAGE );

            $this->setValue( "referrers", $postReferers );
			$this->setValue( "pager", $pager );
			$this->setValue( "post", $this->_article );
		
			parent::render();
		}
	}
?>