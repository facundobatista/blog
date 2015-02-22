<?php

	lt_include( PLOG_CLASS_PATH."class/net/xmlrpc/IXR_Library.lib.php" );
	lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
	lt_include( PLOG_CLASS_PATH."class/database/db.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/users.class.php");
	lt_include( PLOG_CLASS_PATH."class/dao/article.class.php");
	lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php");
	lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php");
	lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/cachecontrol.class.php" );
	lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );
	lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryresources.class.php" );
	lt_include( PLOG_CLASS_PATH."class/plugin/pluginmanager.class.php" );
		
	if( !defined( "ADMIN_PERMISSION" )) 
		define( "ADMIN_PERMISSION", 1 );
	if( !defined( "BLOG_PERMISSION" )) 
		define( "BLOG_PERMISSION", 2 );	

	class XmlRpcServer extends IXR_Server
	{
		function XmlRpcServer()
		{
			$config =& Config::getConfig();
		    if ($config->getValue("xmlrpc_api_enabled"))
		    {
				$this->IXR_Server(
                    array (
			        	"blogger.newPost"           => "this:newPost",
			        	"blogger.getPost"           => "this:getPost",
			        	"blogger.editPost"          => "this:editPost",
			        	"blogger.deletePost"        => "this:deletePost",
			        	"blogger.getRecentPosts"    => "this:getRecentPosts",
			        	"blogger.getUserInfo"       => "this:getUserInfo",
			        	"blogger.getUsersBlogs"     => "this:getUsersBlogs",
			            "metaWeblog.newPost"        => "this:metaWeblogNewPost",
			            "metaWeblog.editPost"       => "this:metaWeblogEditPost",
			            "metaWeblog.getPost"        => "this:metaWeblogGetPost",
			            "metaWeblog.getRecentPosts" => "this:metaWeblogGetRecentPosts",
			            "metaWeblog.getCategories"  => "this:metaWeblogGetCategories",
			            "metaWeblog.newMediaObject" => "this:metaWeblogNewMediaObject",	
			            "metaWeblog.getUsersBlogs"  => "this:getUsersBlogs",
			            "mt.getCategoryList"        => "this:mtGetCategoryList",
			            "mt.supportedTextFilters"   => "this:mtSupportedTextFilters", 
			            "mt.getPostCategories"      => "this:mtGetPostCategories",
			            "mt.setPostCategories"      => "this:mtSetPostCategories"
			    	    ));
			} 
			else {
                    // xmlrpc_api disabled, no methods configured
				$this->IXR_Server( Array ());
		    }			
		}
	
		function newPost( $args )
		{
			$users = new Users();
			$articles = new Articles();
			$category = new ArticleCategories();
			$blogsG = new Blogs();
		
	        $appkey     = $args[0];
	        $blogid     = $args[1];
	        $username   = $args[2];
	        $password   = $args[3];
	        $content    = $args[4];
	        $publish    = $args[5]; // true post&publish | false post only
                /*
	         int postid
                */

                // -mhe todo security

	        $userInfo = $users->getUserInfo( $username, $password );

	        if (!$userInfo) {
	            return new IXR_Error(-1, 'You did not provide the correct username and/or password');
            }

            if(!$blogid){
                $blogs = $userInfo->getBlogs();
                if(!$blogs){
                    return new IXR_Error(-1, "This user doesn't have access to any blogs.");
                }
                $blogid = $blogs[0]->getId();
            }

            $blogInfo = $blogsG->getBlogInfo( $blogid );
            if( !$blogInfo ) {
                return new IXR_Error(-1, 'Error loading blog' );
            }
            
				// check this user's permissions before proceeding
            if( !$this->userHasPermission( $userInfo, $blogInfo, "add_post" )) {
                return new IXR_Error(-1, 'This user does not have enough permissions' );
            }		
            
            if ($publish) {
                $status = POST_STATUS_PUBLISHED;
            } 
            else {
                $status = POST_STATUS_DRAFT;
            }
            
	            // Get the default category
            $cats = $category->getBlogCategories($blogid);
            
				// some protection again blogs without categories
            if( !$cats ) {				
                return new IXR_Error(-1, 'This blog does not have any categories!');
            }
            
            foreach($cats as $cat) {
                $idCategory = $cat->getId();
	                // Stop here, we have a category
                break;
            }

				// ecto sends the topic as <title>blah blah</title>rest of the body
            if( preg_match( "/<title>(.*)<\/title>(.*)/i", $content, $matches )) {
                $title = $matches[1];
                $content = $matches[2];
            }
            else {
                $dummy = explode("\n", $content);
                if( count( $dummy ) == 1 ) {
                    $title = substr( $content, 0, 60 );
                }
                else {
                    $title = $dummy[0];
                    unset($dummy[0]);
                    $content = implode("\n", $dummy);
                    unset($dummy);
                }
            }
            
            $article = new Article(
                $title,
                $content, // text
                Array( $idCategory ), // catid
                $userInfo->getId(), // userid
                $blogid, // blogid
                $status,
                0, // numread
                Array( "comments_enabled" => true ) // enable comments
	            );
            
            $article->setDate(date("YmdHis"));
            
	            // Get the plugin manager
            $plugMgr =& PluginManager::getPluginManager();
            $plugMgr->setBlogInfo( $blogInfo);
            $plugMgr->setUserInfo( $userInfo );
            $plugMgr->loadPlugins();
	            // Send the PRE_POST_POST_ADD message
            $plugMgr->notifyEvent( EVENT_PRE_POST_ADD, Array( "article" => &$article ));
            
            $postid = $articles->addArticle($article);
            
            if ($postid == 0) {
                return new IXR_Error(-1, 'Internal error occurred while creating your post!');
            }
                // The post was successful
                // Send the EVENT_POST_POST_ADD messages to the plugins
            $plugMgr->notifyEvent( EVENT_POST_POST_ADD, Array( "article" => &$article ));
            CacheControl::resetBlogCache( $blogid );
            
            $blogSettings = $blogInfo->getSettings();
            
                // Add article notifcations if this is specified by the default setting.
            if ($blogSettings->getValue( "default_send_notification" ))
            {
                lt_include( PLOG_CLASS_PATH."class/dao/articlenotifications.class.php" );
                
                $artNotifications = new ArticleNotifications();
                $artNotifications->addNotification( $postid, $blogid, $userInfo->getId());
            }
            
            $this->setResponseCharset( $blogInfo );
            
            return sprintf( "%d", $postid );
		}
		
        function metaWeblogNewPost($args)
        {
			$users = new Users();
			$articles = new Articles();
			$category = new ArticleCategories();
			$blogsG = new Blogs();

            $blogid     = $args[0];
            $username   = $args[1];
            $password   = $args[2];
            $content    = $args[3];
            $publish    = $args[4]; // true post&publish | false post only
                /*
             int postid
                */
    
            $userInfo = $users->getUserInfo( $username, $password);
    
            if( !$userInfo ) {
                return new IXR_Error(-1, 'You did not provide the correct username and/or password');
            }	
            if ($publish) {
                $status = POST_STATUS_PUBLISHED;
            } 
            else {
                $status = POST_STATUS_DRAFT;
            }

            if(!$blogid){
                $blogs = $userInfo->getBlogs();
                if(!$blogs){
                    return new IXR_Error(-1, "This user doesn't have access to any blogs.");
                }
                $blogid = $blogs[0]->getId();
            }

            $blogInfo = $blogsG->getBlogInfo( $blogid );
            if( !$this->userHasPermission( $userInfo, $blogInfo, "add_post" )) {
                return new IXR_Error(-1, 'This user does not have enough permissions' );
            }
                
            $title = $content["title"];
                
                // Check to see if the MovableType extnensions have been added
            $mt_excerpt = "";
            $mt_text_more = "";
            $mt_allow_comments = "";
            if( isset( $content["mt_excerpt"] ))
                $mt_excerpt = $content["mt_excerpt"]; 
            if( isset( $content["mt_text_more"] ))
                $mt_text_more = $content["mt_text_more"]; 
            if( isset( $content["mt_allow_comments"] ))
                $mt_allow_comments = $content["mt_allow_comments"]; 
                
            if ( $mt_text_more != NULL && trim($mt_text_more != ""))
            {
                $body = $content["description"] . POST_EXTENDED_TEXT_MODIFIER . $mt_text_more;
            }
            else
            {
                $body = $content["description"];
            }
            $catList = NULL;
            if( isset( $content["categories"] ))
                $catList = $content["categories"];

            $categoryName = NULL;
    
                //
                // :KLUDGE:
                // not exactly the smartest and fastest bit of code ever but it seems to work :-)
                //
            $categories = Array();
            $cats = $category->getBlogCategories($blogid);            
    
                // some protection again blogs without categories
            if( !$cats ) {
                return new IXR_Error(-1, 'This blog does not have any categories!');
            }
    
            if ( $catList != NULL )
            {
                foreach( $catList as $categoryName ) {
                    foreach( $cats as $blogCategory ) {
                        $categoryName = trim($categoryName);
                        if ( strcmp( $categoryName, $blogCategory->getName()) == 0 )
                        {
                            $categories[] = $blogCategory->getId();
                        }
                    }
                }
            }
            else {
                    // if no category, let's pick a random one
                $blogCategory = array_pop( $cats );
                $categories[] = $blogCategory->getId();
            }
    
            $userInfo = $users->getUserInfoFromUsername( $username );
                
                // Initially assume that comments are enabled
            $enableComments = true;
                
                // Was a setting specified in the MovableType fields?
            if ($mt_allow_comments != NULL)
            {
                $enableComments = $mt_allow_comments;
            }
                
                
            $article = new Article(
                $title,
                $body, // text
                $categories, // catid
                $userInfo->getId(), // userid
                $blogid, // blogid
                $status,
                0, // numread
                Array( "comments_enabled" => $enableComments ) 
                );
    
            $dateCreated = NULL;
            if( isset( $content['dateCreated'] ))
                $dateCreated = $content['dateCreated'];
               
                // there must be a bug in the xmlrpc library, we're getting an object in $dateCreated
                // that does not have a type or anyhting, but it still is an object... kinda weird. Anyway,
                // clients like ecto do not allow to change the time an article is posted so this is not 
                // too annoying, *yet*
            if (!empty($dateCreated))
            {
                    // Convert the UTC time to local time, since articleDate is in local time
                $ar = localtime ( $dateCreated->getTimestamp() );
                $ar[5] += 1900; $ar[4]++;
                $localTimeStamp = gmmktime ( $ar[2], $ar[1], $ar[0], $ar[4], $ar[3], $ar[5], $ar[8] );
                $articleDate = date("YmdHis", $localTimeStamp);
            } else
            {
                $articleDate = date("YmdHis");
            }
                
            $article->setDate($articleDate);
                
                // Get the plugin manager
            $plugMgr =& PluginManager::getPluginManager();
            $plugMgr->setBlogInfo( $blogInfo );
            $plugMgr->setUserInfo( $userInfo );
            $plugMgr->loadPlugins();
                // Send the PRE_POST_POST_ADD message
            $plugMgr->notifyEvent( EVENT_PRE_POST_ADD, Array( "article" => &$article ));            
                
            $postid = $articles->addArticle($article);
            if ($postid == 0){
                return new IXR_Error(-1, 'Internal error occurred while creating your post!');
            }
                // The post was successful
                    
                // Send the EVENT_POST_POST_ADD messages to the plugins
            $plugMgr->notifyEvent( EVENT_POST_POST_ADD, Array( "article" => &$article ));
                    
            CacheControl::resetBlogCache( $blogid );
                    
            $blogSettings = $blogInfo->getSettings();
                    
                // Add article notifcations if this is specified by the default setting.
            if ($blogSettings->getValue( "default_send_notification" ))
            {
                lt_include( PLOG_CLASS_PATH."class/dao/articlenotifications.class.php" );
    
                $artNotifications = new ArticleNotifications();
                $artNotifications->addNotification( $postid, $blogid, $userInfo->getId());
            }

            $this->setResponseCharset( $blogInfo );
                   
            return sprintf( "%d", $postid );
        }

	
            /**
             * @private
             * sets the character set for responses
             */
		function setResponseCharset( $blog )
		{
			$locale = $blog->getLocale();
			$this->defencoding = $locale->getCharset();
		}
			

            /** 
             * NOTE: this method does not perform permission checking since if it did,
             * it would be impossible to post: no categories would be available if the
             * view_categories is not available. This is in line with the browser-based UI,
             * there it is not necessary to have this permission in order to post new articles,
             * only add_post is needed
             */
	    function metaWeblogGetCategories($args)
	    {
			$users = new Users();
			$category = new ArticleCategories();
			$blogsG = new Blogs();
	
	        $blogid     = $args[0];
	        $username   = $args[1];
	        $password   = $args[2];

	        $auth = $users->authenticateUser( $username, $password );

	        if (!$auth){
	            return new IXR_Error(-1, 'You did not provide the correct username and/or password');
            }
            $blogInfo = $blogsG->getBlogInfo( $blogid );
            if( !$blogInfo ) {
                return new IXR_Error(-1, 'Incorrect blog id');
            }
            
            $cats = $category->getBlogCategories($blogid);
            $url = $blogInfo->getBlogRequestGenerator();
            $ret = array();	
            foreach($cats as $cat)
            {
                $dummy                   = array();
                $dummy["description"]    = $cat->getDescription();
                
	        // disable the generation of xhtml content or else the IXR_XMLRPC library will
	        // escape things twice!
                $url->setXHTML( false );
                
                $dummy["htmlUrl"]        = $url->categoryLink( $cat );
                $dummy["rssUrl"]         = $url->categoryRssLink( $cat, "", $blogInfo );
                $ret[$cat->getName()]    = $dummy;
            }
            
            $this->setResponseCharset( $blogInfo );
            
            return $ret;
	    }

	    function mtGetCategoryList($args)
	    {
			$users = new Users();
			$category = new ArticleCategories();
			$blogsG = new Blogs();
	
	        $blogid     = $args[0];
	        $username   = $args[1];
	        $password   = $args[2];

	        $auth = $users->authenticateUser( $username, $password );

	        if (!$auth){
	            return new IXR_Error(-1, 'You did not provide the correct username and/or password');
            }
            $blogInfo = $blogsG->getBlogInfo( $blogid );
            if( !$blogInfo ) {
                return new IXR_Error(-1, 'Incorrect blog id');
            }
            
            $cats = $category->getBlogCategories($blogid);
            $url = $blogInfo->getBlogRequestGenerator();
            $ret = array();	
            foreach($cats as $cat)
            {
                $dummy                   = array();
                $dummy["categoryId"]     = $cat->getId();
                $dummy["categoryName"]   = $cat->getName();
                
                $ret[]                   = $dummy;
            }
            
            $this->setResponseCharset( $blogInfo );
            
            return $ret;
	    }

	    function getPost($args)
	    {
	        lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );		
		
			$users = new Users();
			$articles = new Articles();
	
	        $appkey     = $args[0];
	        $postid     = $args[1];
	        $username   = $args[2];
	        $password   = $args[3];

	        /*
	            "userid" =>
	            "dateCreated" =>
	            "content" =>
	            "postid" =>
	        */

	        $userInfo = $users->getUserInfo($username,$password);
	        if( !$userInfo ) {
	            return new IXR_Error(-1, 'You did not provide the correct username and/or password');
            }                
            $item = $articles->getBlogArticle($postid,
                                              -1, // blogId
                                              true, // includeHiddenFields
                                              -1, // date
                                              -1, // categoryId
                                              $userInfo->getId());
            $dateObject = $item->getDateObject();
	            // Get the unix time stamp 
            $time = $dateObject->getTimestamp(DATE_FORMAT_UNIXTIME);
            
            $dummy                  = array();
            $userInfo               = $item->getUserInfo();
            $dummy["userid"]        = $userInfo->getId();
            $dummy["dateCreated"]   = new IXR_Date($time);
            $dummy["content"]       = $item->getTopic() . "\r\n" . $item->getText(false) . " ";
            $dummy["postid"]        = $item->getId();
            
            $blogInfo = $item->getBlogInfo();
            
				// check the permissions
            if( !$this->userHasPermission( $userInfo, $blogInfo, "view_posts" )) {
                return new IXR_Error(-1, 'This user does not have enough permissions' );
            }				
            
            $this->setResponseCharset( $blogInfo );
            
            return $dummy;
	    }

	    function metaWeblogGetPost($args)
	    {
	        $users = new Users();
			$articles = new Articles();
	
	        $postid     = $args[0];
	        $username   = $args[1];
	        $password   = $args[2];

	        $userInfo = $users->getUserInfo( $username, $password );

	        if( !$userInfo ){
	            return new IXR_Error(-1, 'You did not provide the correct username and/or password');
            }

            lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );
            
            $item = $articles->getBlogArticle($postid,
                                              -1, // blogId
                                              true, // includeHiddenFields
                                              -1, // date
                                              -1, // categoryId
                                              $userInfo->getId());
            
				// check if the article is valid
            if( !$item ) {
                return( new IXR_Error(-1, 'The article is not valid' ));
            }
            
				// check permissions
            $blogInfo = $item->getBlogInfo();				
            if( !$this->userHasPermission( $userInfo, $blogInfo, "view_posts" )) {
                return( new IXR_Error(-1, 'This user does not have enough permissions' ));
            }	
            
            $dateObject = $item->getDateObject();
	            // Get the unix time stamp 
            $time = $dateObject->getTimestamp(DATE_FORMAT_UNIXTIME);
            
//            $articleCat = $item->getCategory();
            
//            $blogId = $item->getBlog();
//            $blogs = new Blogs();
            $url = $blogInfo->getBlogRequestGenerator();
            
            $dummy                  = array();
            $userInfo               = $item->getUserInfo();
            $dummy["userid"]        = $userInfo->getId();
            $dummy["dateCreated"]   = new IXR_Date($time);
            $dummy["title"]         = $item->getTopic();
            
            $blogSettings = $blogInfo->getSettings();
            
            $dummy["description"]   = $item->getIntroText(); 
            
            $dummy["postid"]        = $item->getId();
            
            $dummy["link"]          = $url->postLink( $item );
            $dummy["permaLink"]     = $url->postPermalink( $item );

            $catArray               = array();
            foreach( $item->getCategories() as $category ) {
                $catArray[]             = $category->getName();
            }
            $dummy["categories"]      = $catArray;

	            // The MovableType Extensions
            $dummy["mt_text_more"]       = $item->getExtendedText(); 
            $dummy["mt_allow_comments"]  = $item->getCommentsEnabled(); 
	            
	
            $this->setResponseCharset( $blogInfo );
	
            return $dummy;
	    }

	    function editPost($args)
	    {
			$users = new Users();
			$articles = new Articles();
			$blogsG = new Blogs();

	        $appkey     = $args[0];
	        $postid     = $args[1];
	        $username   = $args[2];
	        $password   = $args[3];
	        $content    = $args[4];
	        $publish    = $args[5];

	        /*
	            boolean, true or false
	        */

	        $userInfo = $users->getUserInfo( $username, $password );
	        if( !$userInfo ) {
	            return new IXR_Error(-1, 'You did not provide the correct username and/or password');
            }

	            // fake topic
            $dummy = explode("\n", $content);
            if( count($dummy) == 1 ) {
                $title = substr( $content, 0, 60 );
            }
            else {
                $title = $dummy[0];
                unset($dummy[0]);
                $content = implode("\n", $dummy);
                unset($dummy);
            }

            $article = $articles->getBlogArticle($postid,
                                                 -1, // blogId
                                                 true, // includeHiddenFields
                                                 -1, // date
                                                 -1, // categoryId
                                                 $userInfo->getId());
	
            if( !$article ) {
                return( new IXR_Error(-1, 'The article id is not correct' ));
            }
				
            $blogInfo = $article->getBlogInfo();				
				
				// check the permissions
            if( !$this->userHasPermission( $userInfo, $blogInfo, "update_post" )) {
                return new IXR_Error(-1, 'This user does not have enough permissions' );
            }				
	
            if ($publish) {
                $status = POST_STATUS_PUBLISHED;
            } 
            else {
                $status = POST_STATUS_DRAFT;
            }
	
            $article->setText($content);
            $article->setTopic($title);
            $article->setStatus($status);

				// Get the plugin manager
            $plugMgr =& PluginManager::getPluginManager();
            $plugMgr->setBlogInfo( $blogInfo );
            $plugMgr->setUserInfo( $userInfo );
            $plugMgr->loadPlugins();
				// Send the EVENT_PRE_POST_UPDATE message
            $plugMgr->notifyEvent( EVENT_PRE_POST_UPDATE, Array( "article" => &$article ));            

            $articles->updateArticle($article);

	            // Send the EVENT_POST_POST_UPDATE messages to the plugins
            $plugMgr->notifyEvent( EVENT_POST_POST_UPDATE, Array( "article" => &$article ));				

            CacheControl::resetBlogCache( $blogInfo->getId());

            $this->setResponseCharset( $blogInfo );

            return true;
	    }

	    function metaWeblogEditPost($args)
	    {
			$users = new Users();
			$articles = new Articles();
			$category = new ArticleCategories();

	        $postid     = $args[0];
	        $username   = $args[1];
	        $password   = $args[2];
	        $content    = $args[3];
	        $publish    = $args[4];

	        /*
	            boolean, true or false
	        */

	        $userInfo = $users->getUserInfo( $username, $password );
	        if( !$userInfo ) {
	            return new IXR_Error(-1, 'You did not provide the correct username and/or password');
            }
            if ($publish) {
                $status = POST_STATUS_PUBLISHED;
            } 
            else {
                $status = POST_STATUS_DRAFT;
            }            

            $title = $content["title"];
                
                // Check to see if the MovableType extnensions have been added
            $mt_excerpt = $content["mt_excerpt"]; 
            $mt_text_more = $content["mt_text_more"]; 
            $mt_allow_comments = $content["mt_allow_comments"]; 
                
            if ( $mt_text_more != NULL && trim($mt_text_more) != "") {
                $body = $content["description"] . POST_EXTENDED_TEXT_MODIFIER . $mt_text_more;
            }
            else {
                $body = $content["description"];
            }

            $article = $articles->getBlogArticle($postid,
                                                 -1, // blogId
                                                 true, // includeHiddenFields
                                                 -1, // date
                                                 -1, // categoryId
                                                 $userInfo->getId());
	
				// check that the article is valid
            if( !$article ) {
                return( new IXR_Error(-1, 'Incorrect article id' ));					
            }
				
				// see that the user can update articles
            $blogid = $article->getBlog();
            $blogInfo = $article->getBlogInfo();
            if( !$this->userHasPermission( $userInfo, $blogInfo, "update_post" )) {
                return( new IXR_Error(-1, 'This user does not have enough permissions' ));
            }

            $catList = NULL;
            if ( array_key_exists( "categories",  $content ) ) {
                $catList = $content["categories"];
            }
	            //
	            // :KLUDGE:
	            // not exactly the smartest and fastest bit of code ever but it seems to work :-)
	            //
            $categories = Array();
            $cats = $category->getBlogCategories($blogid);
            if ( $catList != NULL )
            {
                foreach( $catList as $categoryName ) {
                    foreach( $cats as $blogCategory ) {
                        $categoryName = trim($categoryName);
                        if ( strcmp( $categoryName, $blogCategory->getName()) == 0 )
                        {
                            $categories[] = $blogCategory->getId();
                        }
                    }
                }
                $article->setCategoryIds( $categories );
            }
            else if ( count($article->getCategories()) == 0) {
                    // Only assign a new category if there isn't one   
                    
	                // if no category, let's pick a random one
                $blogCategory = array_pop( $cats );
                $categories[] = $blogCategory->getId();
	                
                $article->setCategoryIds( $categories );
            }

            $article->setText($body);
            $article->setTopic($title);
            $article->setStatus($status);

				// Get the plugin manager
            $plugMgr =& PluginManager::getPluginManager();
            $plugMgr->setBlogInfo( $blogInfo );
            $plugMgr->setUserInfo( $userInfo );
            $plugMgr->loadPlugins();
				// Send the EVENT_PRE_POST_UPDATE message
            $plugMgr->notifyEvent( EVENT_PRE_POST_UPDATE, Array( "article" => &$article ));            

            $articles->updateArticle($article);

	            // Send the EVENT_POST_POST_UPDATE messages to the plugins
            $plugMgr->notifyEvent( EVENT_POST_POST_UPDATE, Array( "article" => &$article ));				

            CacheControl::resetBlogCache( $blogid );            
	
            $this->setResponseCharset( $blogInfo );

            return true;
	    }

	    function deletePost($args)
	    {
			$users = new Users();
			$articles = new Articles();
			$blogsG = new Blogs();

	        $appkey     = $args[0];
	        $postid     = $args[1];
	        $username   = $args[2];
	        $password   = $args[3];
	        $publish    = $args[4];

	        $userInfo = $users->getUserInfo( $username, $password );
	        if( !$userInfo ) {
	            return new IXR_Error(-1, 'You did not provide the correct username and/or password');
            }
            $article = $articles->getBlogArticle($postid,
                                                 -1, // blogId
                                                 true, // includeHiddenFields
                                                 -1, // date
                                                 -1, // categoryId
                                                 $userInfo->getId());
	
				// check if the article that was pulled is valid at all
            if( !$article ) {
                return( new IXR_Error(-1, 'The article id is not correct' ));
            }
				
				// check the permissions
            $blogInfo = $article->getBlogInfo();
            if( !$this->userHasPermission( $userInfo, $blogInfo, "update_post" )) {
                return( new IXR_Error(-1, 'This user does not have enough permissions' ));
            }				

				// Get the plugin manager
            $plugMgr =& PluginManager::getPluginManager();
            $plugMgr->setBlogInfo( $blogInfo );
            $plugMgr->setUserInfo( $userInfo );
            $plugMgr->loadPlugins();
				// Send the EVENT_PRE_POST_DELETE message
            $plugMgr->notifyEvent( EVENT_PRE_POST_DELETE, Array( "article" => &$article ));            

            $articles->deleteArticle(
                $postid,
                $userInfo->getId(), // userid
                $article->getBlog()
	            );

	            // Send the EVENT_POST_POST_DELETE messages to the plugins
            $plugMgr->notifyEvent( EVENT_POST_POST_DELETE, Array( "article" => &$article ));				

            CacheControl::resetBlogCache( $blogInfo->getId());

            $this->setResponseCharset( $blogInfo );

            return true;
	    }

	    function getRecentPosts($args)
	    {
			$users = new Users();
			$articles = new Articles();
			$blogs = new Blogs();
	
	        /*
	            "userid" =>
	            "dateCreated" =>
	            "content" =>
	            "postid" =>
	        */
	        $appkey     = $args[0];
	        $blogid     = $args[1];
	        $username   = $args[2];
	        $password   = $args[3];
	        $number     = $args[4];

	        $userInfo = $users->getUserInfo($username,$password);
	        if( !$userInfo ){
	            return new IXR_Error(-1, 'You did not provide the correct username and/or password');
            }
            $blogInfo = $blogs->getBlogInfo( $blogid );
            if( !$blogInfo ) {
                return new IXR_Error(-1, 'Incorrect blog id');					
            }
				
				// check this user's permissions
            if( !$this->userHasPermission( $userInfo, $blogInfo, "view_posts" )) {
                return new IXR_Error(-1, 'This user does not have enough permissions' );
            }
			
            $ret = array();
            $list = $articles->getBlogArticles(
                $blogid,
                -1, // date
                $number,  // amount
                -1  // any category id
	            );
	
            foreach( $list as $item ) {
                $dateObject = $item->getDateObject();
                lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );
	                // Get the unix time stamp 
                $time = $dateObject->getTimestamp(DATE_FORMAT_UNIXTIME);

                $dummy                  = array();
                $userInfo               = $item->getUserInfo();
                $dummy["userid"]        = $userInfo->getId();
                $dummy["dateCreated"]   = new IXR_Date($time);
                $dummy["content"]       = $item->getTopic() . "\r\n" . $item->getText(false) . " ";
                $dummy["postid"]        = $item->getId();

                $ret[]                  = $dummy;
            }
	
            $this->setResponseCharset( $blogInfo );
	
            return $ret;
	    }

	    function metaWeblogGetRecentPosts($args)
	    {
			$users = new Users();
			$articles = new Articles();

	        $blogid     = $args[0];
	        $username   = $args[1];
	        $password   = $args[2];
	        $number     = $args[3];

	        $userInfo = $users->getUserInfo( $username, $password );

	        if( !$userInfo ) {
	            return new IXR_Error(-1, 'You did not provide the correct username and/or password');
            }
            $ret = array();
            $list = $articles->getBlogArticles(
                $blogid,  
                -1,  // date
                $number, // number of articles
                -1  // category id
	            );

            $blogs = new Blogs();
            $blogInfo = $blogs->getBlogInfo( $blogid );
	
				// check if the blog is valid
            if( !$blogInfo ) {
                return new IXR_Error(-1, 'The blog identifier is not valid' );
            }
				
				// check this user's permissions
            if( !$this->userHasPermission( $userInfo, $blogInfo, "view_posts" )) {
                return new IXR_Error(-1, 'This user does not have enough permissions' );
            }				
	
            $url = $blogInfo->getBlogRequestGenerator();

            $blogSettings = $blogInfo->getSettings();

            foreach($list as $item)
            {
                $dateObject = $item->getDateObject();
                lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );
	                // Get the unix time stamp 
                $time = $dateObject->getTimestamp( DATE_FORMAT_UNIXTIME );

                $articleCat = $item->getCategory();

                $dummy                  = array();
                $userInfo               = $item->getUserInfo();
                $dummy["userid"]        = $userInfo->getId();
                $dummy["dateCreated"]   = new IXR_Date($time);
                $dummy["title"]         = $item->getTopic(); 

                $dummy["description"]   = $item->getIntroText(); 
                $dummy["postid"]        = $item->getId();

                $dummy["link"]          = $url->postLink( $item );
                $dummy["permaLink"]     = $url->postPermalink( $item );

                $catArray               = array();
                foreach( $item->getCategories() as $category ) {
                    $catArray[]             = $category->getName();
                }
                $dummy["categories"]      = $catArray;

                    // The MovableType Extensions
                $dummy["mt_text_more"]       = $item->getExtendedText(); 
                $dummy["mt_allow_comments"]  = $item->getCommentsEnabled(); 
	            
	
                $this->setResponseCharset( $blogInfo );

                $ret[]                  = $dummy;
            }
            return $ret;
	    }

	    function metaWeblogNewMediaObject($args)
	    {
			$users = new Users();
			$articles = new Articles();
			$blogsG = new Blogs();

	        $blogid     = $args[0];
	        $username   = $args[1];
	        $password   = $args[2];
	        $file       = $args[3];

	        $userInfo = $users->getUserInfo( $username, $password );
	        if( !$userInfo ) {
	            return new IXR_Error(-1, 'You did not provide the correct username and/or password');
            }		
				// check if the blog id is valid
            $blogInfo = $blogsG->getBlogInfo( $blogid );
            if( !$blogInfo ) {
                return new IXR_Error(-1, 'The blog id is not valid' );
            }
				
				// and now check if the user has enough access to upload resources
            if( !$this->userHasPermission( $userInfo, $blogInfo, "add_resource" )) {
                return new IXR_Error(-1, 'This user does not have enough permissions' );
            }
		
	            // Save this file to the tmp directory

	            // Create a temp file
	            // Get the temp directory
	            /**if (!$tmp_dir = get_cfg_var('upload_tmp_dir')) {
	                $tmp_dir = dirname(tempnam('/tmp', ''));
	            }*/
            $config =& Config::getConfig();
            $tmp_dir = $config->getTempFolder();

	            // Remove all characters that would need to be urlencoded
	            // This may not be necessary, but this was causing problems when given file
	            // names with spaces in it.
            $tempFile = ereg_replace("[^a-zA-Z0-9._-]", "_", basename($file['name']));
	            // Make the tmp name
            $tempFile = $tmp_dir . '/' . $tempFile;

	            // Open the file
            if (!$handle = fopen( $tempFile, "wb" ) ) {
                return new IXR_Error(-1, 'Could not open temp file');
            }    

	            // It appears that the data has already been decoded, no need to call base64_decode
            $decodedBits = $file['bits'];
	            // Write the data to the file
            if ( fwrite( $handle, $decodedBits ) === false ) {
                return new IXR_Error(-1, 'Could not write to temp file');
            }

	            // Close the file
            fclose($handle);

	            // let the gallery library do its work...
            $resources = new GalleryResources();

	            // Get the first album for this blog
            $albums = new GalleryAlbums();
	            // get the list of albums for this blog
            $albumArray = $albums->getUserAlbums( $blogid );
            if ( $albumArray == NULL || count( $albumArray ) == 0 ) {
                return new IXR_Error(-1, 'Could not find album');
            }

	            // Add the resource to the first album
            $resId = $resources->addResourceFromDisk( $blogid, $albumArray[0]->getId(),
                                                      basename($file['name']), $tempFile );
	            // Get the resource from the id
            $resource = $resources->getResource( $resId, $blogid, $albumArray[0]->getId() );
	            // Now we need to get the url for the resource
            $url = $blogInfo->getBlogRequestGenerator();

            $responseStruct               = array();

            $responseStruct['url'] = $url->resourceDownloadLink( $resource );
	
            $this->setResponseCharset( $blogInfo );

            return $responseStruct;
	    }	

	    function getUserInfo($args)
	    {
	        $appkeyp    = $args[0];
	        $username   = $args[1];
	        $password   = $args[2];

			$users = new Users();

	        $userInfo = $users->getUserInfo( $username, $password );

	        if (!$userInfo){
	            return new IXR_Error(-1, 'You did not provide the correct username and/or password');
            }

            $ret                = array();
            $ret["nickname"]    = $userInfo->getUsername();
            $ret["firstname"]   = $userInfo->getUsername();
            $ret["lastname"]    = "";
            $ret["email"]       = $userInfo->getEmail();
            $ret["userid"]      = $userInfo->getId();
            $ret["url"]         = "";

				// set the response encoding according to one of the blogs owned by this user
            $userBlogs = $users->getUsersBlogs( $userInfo->getId(), BLOG_STATUS_ACTIVE );
            if( count($userBlogs) > 0 ) {
                $blogInfo = array_pop( $userBlogs );
                $this->setResponseCharset( $blogInfo );					
            }

            return $ret;
	    }

	    function getUsersBlogs($args)
        {
            $users = new Users();
            $category = new ArticleCategories();

            $appkey     = $args[0];
            $username   = $args[1];
            $password   = $args[2];
                /*
	            "blogName" =>
	            "url" =>
	            "blogid" =>
                */

            $userInfo = $users->getUserInfo( $username, $password );

            if (!$userInfo){
                return new IXR_Error(-1, 'You did not provide the correct username and/or password');
            }
            $blogs = $users->getUsersBlogs($userInfo->getId(), BLOG_STATUS_ACTIVE );
            $ret = array();
            foreach($blogs as $blog)
            {
                $dummy              = array();
                $dummy["blogid"]    = $blog->_id;
                $dummy["blogName"]  = $blog->_blog;
                $url = $blog->getBlogRequestGenerator();
                $dummy["url"]       = $url->blogLink();
                $ret[]              = $dummy;
            }

                // set the encoding as long as we've got at least one blog
            if( count( $blogs ) > 0 ) {
                $blogInfo = $blogs[0];
                $this->setResponseCharset( $blogInfo );
            }

            return $ret;
	    }	
	    
	    function mtSupportedTextFilters($args)
	    {
            $ret = array();
            return $ret;
	    }
	    
	    function mtGetPostCategories($args)
	    {
	        $users = new Users();
			$articles = new Articles();
	
	        $postid     = $args[0];
	        $username   = $args[1];
	        $password   = $args[2];

	        $userInfo = $users->getUserInfo( $username, $password );

	        if( !$userInfo ){
	            return new IXR_Error(-1, 'You did not provide the correct username and/or password');
            }

            lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );

            $item = $articles->getBlogArticle($postid,
                                              -1, // blogId
                                              true, // includeHiddenFields
                                              -1, // date
                                              -1, // categoryId
                                              $userInfo->getId());
	
				// check if the article is valid
            if( !$item ) {
                return( new IXR_Error(-1, 'The article id is not valid' ));
            }
				
				// and permissions
            $blogInfo = $item->getBlogInfo();
            if( !$this->userHasPermission( $userInfo, $blogInfo, "view_posts" )) {
                return new IXR_Error(-1, 'This user does not have enough permissions' );
            }

            $catArray               = array();
            foreach( $item->getCategories() as $category ) {
                $dummy                   = array();
                $dummy["categoryId"]     = $category->getId();
                $dummy["categoryName"]   = $category->getName();

                $catArray[]              = $dummy;

            }
	
            $this->setResponseCharset( $blogInfo );
	
            return $catArray;
	    }
	
        function mtSetPostCategories($args)
	    {
	        $users = new Users();
			$articles = new Articles();
	
	        $postid     = $args[0];
	        $username   = $args[1];
	        $password   = $args[2];
	        $categories = $args[3];

	        $userInfo = $users->getUserInfo( $username, $password );

	        if( !$userInfo ) {
	            return new IXR_Error(-1, 'You did not provide the correct username and/or password');
            }
            lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );

            $article = $articles->getBlogArticle($postid,
                                                 -1, // blogId
                                                 true, // includeHiddenFields
                                                 -1, // date
                                                 -1, // categoryId
                                                 $userInfo->getId());
				
				// check that the article is valid
            if( !$article ) {
                return( new IXR_Error(-1, 'The article id is not correct' ));
            }

	
				// check the permissions
            $blogId = $article->getBlog();
            $blogInfo = $article->getBlogInfo();	
            if( !$this->userHasPermission( $userInfo, $blogInfo, "update_post" )) {
                return new IXR_Error(-1, 'This user does not have enough permissions' );
            }	

            $articleCategories = new ArticleCategories();


            $catArray      = Array();

            if ( $categories != NULL )
            {
                foreach( $categories as $category ) {
                        // Get the category object for the category
                    $catArray[] = $category["categoryId"];
                }
            }
	            
            $article->setCategoryIds($catArray);
	
				// Get the plugin manager
            $plugMgr =& PluginManager::getPluginManager();
            $plugMgr->setBlogInfo( $blogInfo );
            $plugMgr->setUserInfo( $userInfo );
            $plugMgr->loadPlugins();
				// Send the EVENT_PRE_POST_UPDATE message
            $plugMgr->notifyEvent( EVENT_PRE_POST_UPDATE, Array( "article" => &$article ));            

            $articles->updateArticle($article);

	            // Send the EVENT_POST_POST_UPDATE messages to the plugins
            $plugMgr->notifyEvent( EVENT_POST_POST_UPDATE, Array( "article" => &$article ));				

            CacheControl::resetBlogCache( $blogId );            
	
            $this->setResponseCharset( $blogInfo );
	
            return true;
	    }
	
		/**
		 * Extra helper method to check permissions
		 *
		 * @param user A UserInfo object
		 * @param blog A BlogInfo object
		 * @param permName Name of the permission
		 * @param mode Either BLOG_PERMISSION or ADMIN_PERMISSION, depending on whether
		 * we're checking the user's permissions in this blog or an admin permission
		 */
		function userHasPermission( $userInfo, $blogInfo, $permName, $mode = BLOG_PERMISSION )
		{			
			// check for the permission, whether the user is the blog owner or
			// whether the user is a site administrator
			$hasPermission = false;
			if( $mode == BLOG_PERMISSION ) {
		    	$hasPermission = ( 
		    		$userInfo->hasPermissionByName( $permName, $blogInfo->getId()) ||
		    		$blogInfo->getOwnerId() == $userInfo->getId() ||
					$userInfo->hasPermissionByName( "edit_blog_admin_mode", 0 )
		    	);
			}
			else {				
		    	$hasPermission = ( $userInfo->hasPermissionByName( $permName, 0 ));
			}
			
			return( $hasPermission );
		}	

	}
?>
