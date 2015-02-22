<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminregisterblogview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/admindashboardview.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/blognamevalidator.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );	
    lt_include( PLOG_CLASS_PATH."class/data/validator/domainvalidator.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );	
	lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );
	lt_include( PLOG_CLASS_PATH."class/net/http/subdomains.class.php" );
    lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/mylinkscategories.class.php" );
	
	class AdminDoRegisterBlogAction extends AdminAction
	{
	
		function AdminDoRegisterBlogAction( $actionInfo, $request )
		{
			$this->AdminAction( $actionInfo, $request );
			
			$this->registerFieldValidator( "blogName", new BlogNameValidator());
            $this->registerFieldValidator( "blogLocale", new StringValidator());
			$this->registerFieldValidator( "templateId", new StringValidator());
			$this->registerFieldValidator( "blogCategory", new IntegerValidator());

			if( Subdomains::getSubdomainsEnabled()) {
                $this->registerFieldValidator( "blogSubDomain", new DomainValidator());
                $this->registerFieldValidator( "blogMainDomain", new DomainValidator());
            } else {
            	$this->registerFieldValidator( "blogSubDomain", new DomainValidator(), true );
            	$this->registerFieldValidator( "blogMainDomain", new DomainValidator(), true );
            }
            
			$this->setValidationErrorView( new AdminRegisterBlogView( $this->_userInfo ));
		}

		function validate()
		{
			if( !parent::validate())
				return false;

			$maxBlogsPerUser = $this->_config->getValue( "num_blogs_per_user" );
			if( !is_numeric( $maxBlogsPerUser ))
				$maxBlogsPerUser = DEFAULT_MAX_BLOGS_PER_USER;
			$numOfUserBlogs = count( $this->_userInfo->getOwnBlogs() );
			
			if( $numOfUserBlogs >= $maxBlogsPerUser ) {
		        $this->_view = new AdminRegisterBlogView( $this->_blogInfo, $this->_userInfo );
		        $this->_view->setErrorMessage( $this->_locale->tr("error_already_over_blog_creation_limition") );
		        $this->setCommonData();

		        return false;
			}
			
			return true;
		}
		
		function perform()
		{
			// if the validation of data went fine, then we can proceed and add the blog
			$localeCode = $this->_request->getValue( "blogLocale" );
			$template = $this->_request->getValue( "templateId" );
			$name = $this->_request->getValue( "blogName" );
			$blogCategory = $this->_request->getValue( "blogCategory" );
			
			// create the blog...
			$blog = new BlogInfo( $name, $this->_userInfo->getId(),  // owner id
			                      '',  // about
			                      '');  // settings

            // check to see whether we are going to save subdomain information			
            if( Subdomains::getSubdomainsEnabled()) {
                // Translate a few characters to valid names, and remove the rest
                $mainDomain = Textfilter::domainize($this->_request->getValue( "blogMainDomain" ));
                if(!$mainDomain)
					$mainDomain = "?";
                $subDomain = Textfilter::domainize($this->_request->getValue( "blogSubDomain" ));

                if(!Subdomains::isDomainAvailable($mainDomain)){
                    $this->_view = new AdminRegisterBlogView( $this->_userInfo );
                    $this->_view->setErrorMessage( $this->_locale->tr("error_updating_blog_subdomain"));
                    $this->setCommonData();
                    return false;
                }

				if(!Subdomains::isValidDomainName($subDomain)){
	                $this->_view = new AdminRegisterBlogView( $this->_userInfo );
	                $this->_view->setErrorMessage( $this->_locale->tr("error_updating_blog_subdomain"));
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

                // set the template
			$blog->setTemplate( $template );
			// set the locale
			$locale =& Locales::getLocale( $localeCode );
			$blog->setLocale( $locale );
			// and set the status to ready
			$blog->setStatus( BLOG_STATUS_ACTIVE );
			// set the blog category
			$blog->setBlogCategoryId( $blogCategory );
			
			// and finally add everything to the db
			$blogs = new Blogs();
			if( !$blogs->addBlog( $blog )) {
				$this->_view = new AdminRegisterBlogView( $this->_userInfo );
				$this->setCommonData( true );
			}
			
			$newBlogId = $blog->getId();
            $articleCategories = new ArticleCategories();
            $articleCategory = new ArticleCategory( $locale->tr("register_default_category"), "", $newBlogId, true );
            $catId = $articleCategories->addArticleCategory( $articleCategory );
            $articleTopic = $locale->tr( "register_default_article_topic" );
            $articleText  = $locale->tr( "register_default_article_text" );
            $article = new Article( $articleTopic, 
                                    $articleText, 
                                    Array( $catId ), 
                                    $this->_userInfo->getId(), 
                                    $newBlogId, 
                                    POST_STATUS_PUBLISHED, 
                                    0, 
                                    Array(), 
                                    "welcome" );
            $t = new Timestamp();
            $article->setDateObject( $t );
            $articles = new Articles();
            $articles->addArticle( $article );

			// create a new link category and album
            $t = new Timestamp();
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

			// after we update everything, we need to get the userInfo from db and set to session again.
			lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
			$users = new Users();
			$this->_userInfo = $users->getUserInfoFromId( $this->_userInfo->getId() );
            $this->_session->setValue( "userInfo", $this->_userInfo );
            $this->saveSession();
						
			// redirect process to the dashboard view
			$usersBlogs = $users->getUsersBlogs( $this->_userInfo->getId(), BLOG_STATUS_ACTIVE );
			$this->_view = new AdminDashboardView( $this->_userInfo, $usersBlogs ); 
		}
	}
?>