<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminpostmanagementcommonaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminxmlview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php");
    lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );

    /**
     * \ingroup Action
     * @private
     */
	class AdminSaveDraftArticleAjaxAction extends AdminPostManagementCommonAction
	{
		function AdminSaveDraftArticleAjaxAction( $actionInfo, $request )
		{
			$this->AdminPostManagementCommonAction( $actionInfo, $request );

            $view = new AdminXmlView( $this->_blogInfo, "response" );
            $view->setValue( "method", "saveDraftArticleAjax" );
            $view->setValue( "success", "0" );
            $view->setValue( "message", $this->_locale->tr( "error_saving_draft" ) );
        	$this->setValidationErrorView( $view );

			$this->requirePermission( "add_post" );
        }

		function perform()
		{
            $this->_fetchCommonData();
            
            $status = POST_STATUS_DRAFT;
            $articles = new Articles();
            
            $article  = new Article( $this->_postTopic, $this->_postText,
                                     $this->_postCategories, $this->_userInfo->getId(),
                                     $this->_blogInfo->getId(), $status, 0, Array(), $this->_postSlug );
            // set also the date before it's too late
            $article->setDateObject( $this->_postTimestamp );
            $blogSettings = $this->_blogInfo->getSettings();
            $article->setTimeOffset($blogSettings->getValue("time_offset"));
            $article->setCommentsEnabled( $this->_commentsEnabled );
            // prepare the custom fields
            $fields = Array();
            if( is_array($this->_customFields)) {
                lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfieldvalue.class.php" );
                foreach( $this->_customFields as $fieldId => $fieldValue ) {
                    // 3 of those parameters are not really need when creating a new object... it's enough that
                    // we know the field definition id.
                    $customField = new CustomFieldValue( $fieldId, $fieldValue, "", -1, "", -1, $this->_blogInfo->getId(), -1);
                    array_push( $fields, $customField );
                }
                $article->setFields( $fields );
            }

            // in case the post is already in the db
            // TODO: this allows people with only add permissions to update any article
            if( $this->_postId != "" ) {
                $article->setId( $this->_postId );
                $postSavedOk = $articles->updateArticle( $article );

                if( $postSavedOk )
                    $artId = $this->_postId;
                else
                    $artId = false;
            }
            else {
                $artId = $articles->addArticle( $article );
            }

            // once we have built the object, we can add it to the database
            $this->_view = new AdminXmlView( $this->_blogInfo, "response" );
            $this->_view->setValue( "method", "saveDraftArticleAjax" );
            if( $artId )
            {
                $this->_view->setValue( "success", "1" );
                $this->_view->setValue( "message", $this->_locale->pr( "draft_saved_ok", $this->_postTopic ) );

                $result = '<id>'.$artId.'</id>';
                $this->_view->setValue( "result", $result );
            }
            else
            {
                $this->_view->setValue( "success", "0" );
                $this->_view->setValue( "message", $this->_locale->tr( "error_saving_draft" ) );
            }

            return true;
		}
    }
?>
