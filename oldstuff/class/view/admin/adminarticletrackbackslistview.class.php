<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/adminarticlecommentslistview.class.php" );
	
    /**
     * \ingroup View
     * @private
     *
	 * shows a list with all the trackbacks received for a certain article
	 */
	class AdminArticleTrackbacksListView extends AdminArticleCommentsListView
	{
		function AdminArticleTrackbacksListView( $blogInfo, $params = Array())
		{
			$this->AdminArticleCommentsListView( $blogInfo, $params, COMMENT_TYPE_TRACKBACK );
		}
	}
?>