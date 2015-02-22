<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admincreateblogview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminsiteblogslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/blognamevalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/usernamevalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/domainvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Adds a new user to the database.
     */
    class AdminAddBlogAction extends AdminAction 
	{

    	var $_blogName;
        var $_ownerId;

    	function AdminAddBlogAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
        	
        	// data validation
        	$this->registerFieldValidator( "blogName", new BlogNameValidator());
        	$this->registerFieldValidator( "userId", new IntegerValidator());
			if( Subdomains::getSubdomainsEnabled()) {
				$this->registerFieldValidator( "blogSubDomain", new DomainValidator());
				$this->registerFieldValidator( "blogMainDomain", new DomainValidator());
			}

        	$this->registerFieldValidator( "userName", new UsernameValidator());
			$view = new AdminCreateBlogView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr( "error_adding_blog" ));
        	$this->setValidationErrorView( $view );

			$this->requireAdminPermission( "add_site_blog" );
        }

        function perform()
        {
	        // fetch the validated data
        	$this->_blogName = Textfilter::filterAllHTML($this->_request->getValue( "blogName" ));
            $this->_ownerId  = $this->_request->getValue( "userId" );
			
            // check that the user really exists
            $users = new Users();
            $userInfo = $users->getUserInfoFromId( $this->_ownerId );
            if( !$userInfo ) {
            	$this->_view = new AdminCreateBlogView( $this->_blogInfo );
                $this->_form->setFieldValidationStatus( "blogOwner", false );
                $this->setCommonData( true );
                return false;
            }			
	        
        	// now that we have validated the data, we can proceed to create the user, making
            // sure that it doesn't already exists
            $blogs = new Blogs();
			$blog = new BlogInfo( $this->_blogName, $this->_ownerId, "", "" );
			
            // check to see whether we are going to save subdomain information			
            if( Subdomains::getSubdomainsEnabled()) {
	
                // Translate a few characters to valid names, and remove the rest
                $mainDomain = Textfilter::domainize($this->_request->getValue( "blogMainDomain" ));
                if(!$mainDomain)
					$mainDomain = "?";
                $subDomain = Textfilter::domainize($this->_request->getValue( "blogSubDomain" ));

                if( !Subdomains::isDomainAvailable( $mainDomain )) {
                    $this->_view = new AdminCreateBlogView( $this->_blogInfo );
                    $this->_view->setErrorMessage( $this->_locale->tr("error_updating_blog_subdomain"));
					$this->_form->setFieldValidationStatus( "blogMainDomain", false );
                    $this->setCommonData();
                    return false;
                }

				if( !Subdomains::isValidDomainName( $subDomain )) {
	                $this->_view = new AdminCreateBlogView( $this->_blogInfo );
	                $this->_view->setErrorMessage( $this->_locale->tr("error_updating_blog_subdomain"));
					$this->_form->setFieldValidationStatus( "blogSubDomain", false );	
	                $this->setCommonData();
	                return false;
				}

                if($mainDomain == "?"){
					$blog_domain = $subDomain;
                }
                else{
					$blog_domain = $subDomain . "." . $mainDomain;
                }

				$blog->setCustomDomain( $blog_domain );
            }			
			
			// save the blog
			$this->notifyEvent( EVENT_PRE_BLOG_ADD, Array( "blog" => &$blog ));
            $newBlogId = $blogs->addBlog( $blog );
            if( !$newBlogId) {
            	$this->_view = new AdminCreateBlogView( $this->_blogInfo );
                $this->_form->setFieldValidationStatus( "blogName", false );
                $this->setCommonData();

                return false;
            }

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
			
            // Get the defaul locale object
            $config =& Config::getConfig();
            $locale =& Locales::getLocale( $config->getValue( "default_locale" ));

            // add a default category and a default post
            $articleCategories = new ArticleCategories();
            $articleCategory = new ArticleCategory( $locale->tr( "register_default_category" ), "", $newBlogId, true );
            $catId = $articleCategories->addArticleCategory( $articleCategory );
            $articleTopic = $locale->tr( "register_default_article_topic" );
            $articleText  = $locale->tr( "register_default_article_text" );
            $article = new Article( $articleTopic, 
                                    $articleText, 
                                    Array( $catId ), 
                                    $this->_ownerId, 
                                    $newBlogId, 
                                    POST_STATUS_PUBLISHED, 
                                    0, 
                                    Array(), 
                                    "welcome" );
            $article->setGlobalCategoryId( $globalArticleCategoryId );  // set the default ArticleGlobalCategory id to article
            $t = new Timestamp();
            $article->setDateObject( $t );
			$article->setInSummary( false );
            $articles = new Articles();
            $articles->addArticle( $article );
            
            // add a new first album so that users can start uploading stuff right away
            lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );
            lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbum.class.php" );            
            $album = new GalleryAlbum( $newBlogId,   // blog id
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
            $linksCategory = new MyLinksCategory( $locale->tr("register_default_category" ), $newBlogId );
            $linksCategories = new MyLinksCategories();
            $linksCategories->addMyLinksCategory( $linksCategory );            

            // and inform everyone that everything went ok
			$this->notifyEvent( EVENT_POST_BLOG_ADD, Array( "blog" => &$blog ));
			if( $this->userHasPermission( "view_site_blogs", ADMIN_PERMISSION ))
            	$this->_view = new AdminSiteBlogsListView( $this->_blogInfo );
			else
				$this->_view = new AdminCreateBlogView( $this->_blogInfo );
				
            $this->_view->setSuccessMessage($this->_locale->pr("blog_added_ok", $blog->getBlog()));
            $this->setCommonData();

            return true;
        }
    }
?>
