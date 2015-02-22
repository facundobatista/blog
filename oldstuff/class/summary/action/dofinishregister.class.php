<?php

    lt_include( PLOG_CLASS_PATH."class/summary/action/registeraction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/template/cachecontrol.class.php" );    
    lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/bloginfo.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/userinfo.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/article.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
	lt_include( PLOG_CLASS_PATH."class/summary/mail/summarymailer.class.php" );
    lt_include( PLOG_CLASS_PATH."class/summary/view/blogtemplatechooserview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/templatenamevalidator.class.php" );    
    lt_include( PLOG_CLASS_PATH."class/net/http/subdomains.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/permissions.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );	

    /**
     * Finish the user and blog registration process.
 	 *
 	 * In case you need to blog creation process, take a look at the methods
	 * preBlogCreatHook, postBlogCreateHook, preUserCreateHook and postUserCreateHook. Unfortunately
	 * plugins are not supported in summary.php/register.php so implementing your custom
	 * code in this methods will be considered the cleanest way to customize this process for the
	 * time being.
	 *
     * @package summary
     * @subpackage action
     */
    class doFinishRegister extends RegisterAction 
    {
        var $need_confirm;

        /**
         * constructor
         */
        function doFinishRegister( $actionInfo, $request )
        {
            $this->RegisterAction( $actionInfo, $request );
            
        	$this->registerFieldValidator( "templateId", new TemplateNameValidator());
        	$this->setValidationErrorView( new BlogTemplateChooserView());        	            
        }   

        /**
         * perform
 	     * 
		 * @private
         */
        function perform()
        {
            $this->_view = new SummaryView( "registererror" );
            $this->need_confirm = $this->_config->getValue("need_email_confirm_registration");
	        
            $userId = $this->createUser();
            if( !$userId )
                return false;
                
            $blogId = $this->createBlog($userId);
            if( !$blogId )
                return false;

            // let's assume that everything went fine at this point...
            $this->doneRegister();
            
            // reset the summary cache, since there's new information to show
            CacheControl::resetSummaryCache(); 

           return( true );
        }

        /**
         * create the user
		 *
		 * @private
         */
        function createUser()
		{
            // all data is already correct
            $this->userName = SessionManager::getSessionValue("userName");
            $this->userFullName = SessionManager::getSessionValue("userFullName");
            $this->userPassword = SessionManager::getSessionValue("userPassword");
            $this->userEmail = SessionManager::getSessionValue("userEmail");

            $users = new Users();
            $user = new UserInfo( $this->userName, 
                        $this->userPassword, 
                        $this->userEmail, 
                        "", // about myself
                        $this->userFullName );

            // if user registration need email confirm, that is
            // user must active his account 
            if($this->need_confirm == true){
                $user->setStatus(USER_STATUS_UNCONFIRMED);
            } else {
                $user->setStatus(USER_STATUS_ACTIVE);
            }

			// pre-user create hook
			$this->preUserCreateHook( $user );

            $userId = $users->addUser( $user );
            if( !$userId ) {
                $this->_view = new SummaryView( "registererror" );
                $this->_view->setErrorMessage( $this->_locale->tr("error_adding_user" ));
                $this->setCommonData( true );
                return false;
            }

			// assign the login_perm permission so that the user can log in
			$perms = new Permissions();
			$loginPerm = $perms->getPermissionByName( "login_perm" );
			$userPerms = new UserPermissions();
			$userPerm = new UserPermission( $userId, 0, $loginPerm->getId());
			$userPerms->grantPermission( $userPerm );

			// post-user create hook
			$this->postUserCreateHook( $user );

            return $userId;
        }

        /**
         * create the blog
         *
		 * @private
         */
        function createBlog($userId)
		{
            $this->blogName = stripslashes(SessionManager::getSessionValue("blogName"));
            $this->blogDomain = stripslashes(SessionManager::getSessionValue("blogDomain"));
            $this->blogCategoryId = SessionManager::getSessionValue("blogCategoryId");
            $this->blogLocale = SessionManager::getSessionValue("blogLocale");
            $this->templateId = $this->_request->getValue("templateId");
        
            // get the default locale configured for the site
            $blogs = new Blogs();
            $blogInfo = new BlogInfo( $this->blogName, $userId, "", "" );

            if($this->need_confirm == 1){
                $blogInfo->setStatus( BLOG_STATUS_UNCONFIRMED );
            } else {
                $blogInfo->setStatus( BLOG_STATUS_ACTIVE );
            }

            $locale = Locales::getLocale( $this->blogLocale );
            $blogInfo->setLocale( $locale );
            $blogInfo->setTemplate( $this->templateId );
            $blogInfo->setBlogCategoryId( $this->blogCategoryId );

            if( Subdomains::getSubdomainsEnabled()) {
				$blogDomain = SessionManager::getSessionValue( "blogDomain" );
				$blogInfo->setCustomDomain( $blogDomain );
            }			

			// pre-blog create hook
			$this->preBlogCreateHook( $blogInfo );

            $newblogId = $blogs->addBlog( $blogInfo );

            if( !$newblogId ) {
                $this->_view = new SummaryView( "registererror" );
                $this->_view->setErrorMessage( $this->_locale->tr("error_creating_blog"));
                return false;
            }

            // get info about the blog
            $blogInfo = $blogs->getBlogInfo( $newblogId );
            
            $this->_blogInfo = $blogInfo;

            // get the default global article category id
	        lt_include( PLOG_CLASS_PATH."class/dao/globalarticlecategories.class.php" );
	        $globalArticleCategories = new GlobalArticleCategories();
	        $globalArticleCategoryId = $this->_config->getValue("default_global_article_category_id");
            if( !empty( $globalArticleCategoryId ) ) {
            	$globalArticleCategory = $globalArticleCategories->getGlobalArticleCategory( $globalArticleCategoryId );
            	if( empty( $globalArticleCategory ) )
            		$globalArticleCategoryId = 0;
            }	
            else {
				$globalArticleCategoryId = 0;
			}
                        
            // if the blog was created, we can add some basic information
            // add a category
            $articleCategories = new ArticleCategories();
            $articleCategory = new ArticleCategory( $locale->tr("register_default_category" ), "", $newblogId, true );
            $catId = $articleCategories->addArticleCategory( $articleCategory );

            // add an article based on that category
            $articleTopic = $locale->tr( "register_default_article_topic" );
            $articleText  = $locale->tr("register_default_article_text" );
            $article = new Article( $articleTopic, 
                                    $articleText, 
                                    Array( $catId ), 
                                    $userId, 
                                    $newblogId, 
                                    POST_STATUS_PUBLISHED, 
                                    0, 
                                    Array(), 
                                    "welcome" );
            $article->setGlobalCategoryId( $globalArticleCategoryId );  // set the default ArticleGlobalCategory id to article
            $article->setDateObject( new Timestamp());  // set it to the current date
			$article->setInSummary( false );	// no need to see these in the summary!
            $articles = new Articles();
            $articles->addArticle( $article );
            // add a new first album so that users can start uploading stuff right away
            lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );
            lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbum.class.php" );            
            $t = new Timestamp();
            $album = new GalleryAlbum( $this->_blogInfo->getId(),   // blog id
                                       $locale->tr( "register_default_album_name" ), // album name
                                       $locale->tr( "register_default_album_description" ), // album description
                                       GALLERY_RESOURCE_PREVIEW_AVAILABLE,   // flags
                                       0,   // no parent id
                                       $t->getTimestamp(),   // current date
                                       Array(),   // no properties
                                       true );  // show the album in the interface
            $albums = new GalleryAlbums();
            $albums->addAlbum( $album );
            
            // add a new default mylinkscategory
    		lt_include( PLOG_CLASS_PATH."class/dao/mylinkscategories.class.php" );
            $linksCategory = new MyLinksCategory( $locale->tr("register_default_category" ), $this->_blogInfo->getId() );
            $linksCategories = new MyLinksCategories();
            $linksCategories->addMyLinksCategory( $linksCategory ); 

			// post-blog create hook
			$this->postBlogCreateHook( $blogInfo );
			
            return true;
        }

		/**
		 * Sends a notification message to all site admins to inform them
		 * that a new blog has just been created
		 *
		 * @private
		 */
		function notifyNewBlog( $blog )
		{
			// find all users who have the update_site_blog permission, as they are the only
			// ones who are able to do anything about this registration, i.e. delete the blog
			$userPerms = new UserPermissions();
			$users = $userPerms->getUsersWithPermissionByName( "update_site_blog" );
			
			// laod the default locale
			$locale =& Locales::getLocale();

			// build up the message, as much as we can			
			$config =& Config::getConfig();
            $message = new EmailMessage();
            $message->setFrom( $config->getValue( "post_notification_source_address" ));
            $message->setSubject( $locale->tr("notification_subject" ));
            $message->setCharset( $locale->getCharset());
			$url = $this->_blogInfo->getBlogRequestGenerator();
			$body = $locale->pr( "new_blog_admin_notification_text", $this->_blogInfo->getBlog(), $url->blogLink());
            $message->setBody( $body );
			
	        $service = new EmailService();
			foreach( $users as $user ) {
            	$message->setTo( $user->getEmail());
	            $service->sendMessage( $message );
			}
		}

        /**
         * finished registaration
		 *
		 * @private
         */
        function doneRegister()
		{
            $this->_view = new SummaryView("registerstep5");
            
            if($this->need_confirm == 1){
                $this->_view->setValue("need_email_confirm",1);
                SummaryMailer::sendConfirmationEmail( $this->userName );
            }
            else {
                // add the blog object to the template
                $this->_view->setValue( "blog", $this->_blogInfo );
            }

			// notify admins if needed to inform that a new blog has been created
			$config =& Config::getConfig();
			if( $config->getValue( "notify_new_blogs", false )) {
				$this->notifyNewBlog( $this->_blogInfo );
			}

            $this->setCommonData();
            return true;
        }

		/**
		 * This method will be called prior to saving the new user to the database. Please
		 * place here your custom code if needed.
		 *
		 * @param user By reference, the UserInfo object with information about the user who we are
		 * going to create
		 * @return Always true
		 */
		function preUserCreateHook( &$user )
		{
			// please implement here your custom code if needed
		}

		/**
		 * This method will be called after to saving the new user to the database. Please
		 * place here your custom code if needed.
		 *
		 * @param user By reference, the UserInfo object with information about the user who was
		 * just saved to the database
		 * @return Always true
		 */		
		function postUserCreateHook( &$user )
		{
			// please implement here your custom code if needed
		}

		/**
		 * This method will be called prior to saving the new blog to the database. Please
		 * place here your custom code if needed.
		 *
		 * @param blog By reference, the BlogInfo object with information about the blog that we are
		 * going to create
		 * @return Always true
		 */		
		function preBlogCreateHook( &$blog )
		{
			// please implement here your custom code if needed
		}

		/**
		 * At this point the blog has already been created, as well as the default albums, link categories,
		 * and so on. This method could be used to create some new custom fields, activate some plugins
		 * by default, etc.
		 *
		 * @param blog By reference, the BlogInfo object with information about the blog that was just
		 * created to the database
		 * @return Always true
		 */		
		function postBlogCreateHook( &$blog )
		{
			// please implement here your custom code if needed
		}		
    }
?>