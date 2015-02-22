<?php

	lt_include( PLOG_CLASS_PATH."class/view/admin/adminnewpostview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/articlenotifications.class.php" );
	
    /**
     * \ingroup View
     * @private
     *	
	 * shows the view that will allow to edit an article
	 */
	class AdminEditPostView extends AdminNewPostView
	{
		
		var $_article;
	
		function AdminEditPostView( $blogInfo )
		{
			$this->AdminTemplatedView( $blogInfo, "editpost" );
			
			// by default, we have no article
			$this->_article = null;
		}
		
		/**
		 * passes an article to the view
		 * 
		 * @param article
		 * @return nothing
		 */
		function setArticle( $article )
		{
			$this->_article = $article;
			$this->setValue( "post", $article );
		}
		
		function render()
		{
            // check if there is a notification for the article set up by this user
            if( $this->_article ) {
	            $notifications = new ArticleNotifications();
	            $userNotification = $notifications->getUserArticleNotification( $this->_article->getId(), $this->_blogInfo->getId(), $this->_userInfo->getId());
	            
	            // decide wether or not we should notify the user based on what we've just
	            // fetched from the database
	            if( $userNotification )
	            	$this->setValue( "sendNotification", true );
	            else
	            	$this->setValue( "sendNotification", false );            
	            
	            // set information about the post itself into the view
	            $this->setValue( "postTopic", $this->_article->getTopic());
                $this->setValue( "postText", str_replace('&', '&amp;', $this->_article->getText( false )));
	            $this->setValue( "postSlug", $this->_article->getPostSlug());
	            $this->setValue( "postId", $this->_article->getId());
				$this->setValue( "postUser", $this->_article->getUserId());
	            if( $this->_article->getCommentsEnabled())
					$commentsEnabled = true;
	            else
	            	$commentsEnabled = false;
	            $this->setValue( "postCommentsEnabled", $commentsEnabled );
	            $this->setValue( "postCategories", $this->_article->getCategoryIds());
	            $this->setValue( "postStatus", $this->_article->getStatus());
	            
	            // we need to play a bit with the values of the fields, as the editpost.template page is
	            // expecting them in a bit different way than if we just do an $article->getFields()
	            $customFieldValues = $this->_article->getCustomFields();
	            $customField = Array();
	            foreach( $customFieldValues as $fieldValue )
	            	$customField[$fieldValue->getFieldId()] = $fieldValue->getValue();
	            $this->setValue( "customField", $customField );
	            
	            // related to the date
	            $postDate = $this->_article->getDateObject();
	            $this->setValue( "postYear", $postDate->getYear());
	            $this->setValue( "postMonth", $postDate->getMonth());
	            $this->setValue( "postDay", $postDate->getDay());
	            $this->setValue( "postHour", $postDate->getHour());
	            $this->setValue( "postMinutes", $postDate->getMinutes());
	            $this->setValue( "globalArticleCategoryId", $this->_article->getGlobalCategoryId());
        	}
                        
            // let our parent class do the rest...
            parent::render();
		}
	}
?>