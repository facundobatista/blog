<?php

	//function _dump($x) { echo "<pre>"; var_dump($x); echo "</pre>"; }

    lt_include( PLOG_CLASS_PATH."class/dao/userdata/lifetypeuserdataprovider.class.php" );

/*

Oles Hnatkevych <don_oles@able.com.ua>

This provider just extends lifetype provider. When user logs in, its authenticated against LDAP, not
against database. But record for user in database must exist. If the user logs in for the first time,
and he is in LDAP, but no record in database, the use is automatically created in DB, and granted
some basic permissions.

Put in userdata.properties.php:

$config = Array(
  "provider" 		=> "LDAPUserDataProvider",
  "ad_domain"		=> "DOMAIN", // can be empty, will be added to username when binding to LDAP, like DOMAIN\username
  "ldap_host"		=> "dc.comain.com",
  "ldap_port"		=> "389",
  "ldap_binddn"		=> "cn=ldapreader,ou=systemaccounts,ou=allusers,dc=domain,dc=com",
  "ldap_bindpass"	=> "sEcReT",
  "ldap_base"		=> "ou=allusers,dc=domain,dc=com",
  "ldap_attr_user"	=> "sAMAccountName",
  "ldap_attr_email"	=> "mail",
  "ldap_attr_fullname"	=> "cn",
  "ldap_filter_user"=> "objectClass=person",
  "createBlogIfNotExisting" => true,
);

*/


    /**
     * Model representing the users in our application. Provides the methods such as
     * authentication and querying for users.
	 *
	 * \ingroup User_Data_Providers
     */
    class LDAPUserDataProvider extends LifeTypeUserDataProvider
    {
		var $ldap_conn;
		var $ad_domain;
		var $ldap_binddn;
		var $ldap_bindpass;
		var $ldap_base;
		var $ldap_filter_user;
		var $ldap_attr_user;
		var $ldap_attr_email;
		var $ldap_attr_fullname;

        /**
         * Initializes the model
         */
        function LDAPUserDataProvider( $providerConfig )
        {
            $this->LifeTypeUserDataProvider( $providerConfig );
            $config = $this->getProviderConfiguration();

            $this->ad_domain = $config->getValue("ad_domain");
            $this->ldap_base = $config->getValue("ldap_base");
            $this->ldap_filter_user = $config->getValue("ldap_filter_user");
            $this->ldap_attr_user = $config->getValue("ldap_attr_user");
            $this->ldap_attr_email = $config->getValue("ldap_attr_email");
            $this->ldap_attr_fullname = $config->getValue("ldap_attr_fullname");

            $ldap_host = $config->getValue("ldap_host");
            $ldap_port = $config->getValue("ldap_port");
            $this->ldap_conn = ldap_connect( $ldap_host, $ldap_port);
            if (! $this->ldap_conn)
            	die ("Can not connect to LDAP server $ldap_host:$ldap_port");
        }


		// found in another class ;)
        function _phpBB2AddBlog( $username, $id)
        {
		    // create a new blog
		    lt_include( PLOG_CLASS_PATH."class/dao/bloginfo.class.php" );
		    lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
            lt_include( PLOG_CLASS_PATH."class/dao/articles.class.php" );
            lt_include( PLOG_CLASS_PATH."class/dao/articlecategories.class.php" );

		    $blogs = new Blogs();
		    $blog = new BlogInfo( $username,
		       	                  $id,
		           	              "",  // no about
		            	          ""); // no properties either
		    $newBlogId = $blogs->addBlog( $blog );

            // add a default category and a default post
            $articleCategories = new ArticleCategories();
            $articleCategory = new ArticleCategory( "General", "", $newBlogId, true );
            $catId = $articleCategories->addArticleCategory( $articleCategory );
            $config =& Config::getConfig();
            $locale =& Locales::getLocale( $config->getValue( "default_locale" ));
            $articleTopic = $locale->tr( "register_default_article_topic" );
            $articleText  = $locale->tr( "register_default_article_text" );
            $article = new Article( $articleTopic,
                                    $articleText,
                                    Array( $catId ),
                                    $id,
                                    $newBlogId,
                                    POST_STATUS_PUBLISHED,
                                    0,
                                    Array(),
                                    "welcome" );
            $t = new Timestamp();
            $article->setDateObject( $t );
            $article->setInSummary( false );
            $articles = new Articles();
            $articles->addArticle( $article );
        }

		// just checks if it is possible to login
		function _authenticateUserLdap($user,$pass) {
			if ($this->ad_domain != "")
				$ldap_user = $this->ad_domain.'\\'.$user;
			else
				$ldap_user = $user;
			return @ldap_bind($this->ldap_conn, $ldap_user, $pass);
		}

		// we look in LDAP/AD email and full name
		function _getLDAPUserInfo($username) {
			$filter = "(&(".$this->ldap_filter_user.")(".$this->ldap_attr_user."=$username))";
			$sr = ldap_search($this->ldap_conn, $this->ldap_base, $filter, array($this->ldap_attr_email,$this->ldap_attr_fullname));
			$user =  ldap_first_entry ($this->ldap_conn, $sr);
			if (!$user)
				return false;
			$email_attr = ldap_get_values ($this->ldap_conn, $user, $this->ldap_attr_email);
			$email = strtolower($email_attr[0]);
			$fullname_attr = ldap_get_values ($this->ldap_conn, $user, $this->ldap_attr_fullname);
			$full_name = $fullname_attr[0];
			return array($email,$full_name);;
		}


		// automatically created users
		function _grantMiscPermission( $userInfo )
		{
			lt_include( PLOG_CLASS_PATH."class/dao/permissions.class.php" );
			lt_include( PLOG_CLASS_PATH."class/dao/userpermission.class.php" );
			lt_include( PLOG_CLASS_PATH."class/dao/userpermissions.class.php" );
			$perms = new Permissions();
			foreach(preg_split("/,/","view_users,view_site_blogs,view_blog_categories") as $tmpperm) {
				$thePerm = $perms->getPermissionByName( $tmpperm );
				$perm = new UserPermission( $userInfo->getId(),    // user id
											0,   // it's a global permission, no blog id needed
											$thePerm->getId()  // id of the permission
				);
				$userPerms = new UserPermissions();
				$userPerms->grantPermission( $perm, true );
			}
		}

        /**
         * Returns true if the user is in the database and the username
         * and password match
         *
         * @param user Username of the user who we'd like to authenticate
         * @param pass Password of the user
         * @return true if user and password correct or false otherwise.
         */
        function authenticateUser( $user, $pass )
        {
			$binded = $this->_authenticateUserLdap( $user, $pass );
			if ($binded) {
				// it exists in LDAP
        		$userInfo = parent::getUserInfoFromUsername( $user );
        		list($email,$full_name) = $this->_getLDAPUserInfo($user);
        		if ( ! $userInfo )  {
					// create user in database
					$userInfo = new UserInfo( $user, $pass, $email, "", $full_name);
					$this->addUser( $userInfo );
					$this->grantLoginPermission( $userInfo );
					$this->_grantMiscPermission( $userInfo );
				}

				// create blog so he can do something.
				$providerConfig = $this->getProviderConfiguration();
				if( $providerConfig->getValue( "createBlogIfNotExisting" )) {
					$userBlogs = $userInfo->getBlogs();
					if( empty($userBlogs )) {
						$this->grantLoginPermission( $userInfo );
						$this->_phpBB2AddBlog( $user, $userInfo->getId());
						$userInfo->setBlogs( $this->getUsersBlogs( $userInfo->getId()));
					}
				}
				return true;
			}
			return parent::authenticateUser( $user, $pass );
        }


		// serves to patch default UserInfo with email and fullname from LDAP
        function getUserInfo( $user, $pass )
        {
			$binded = $this->_authenticateUserLdap( $user, $pass );
            $userInfo = parent::getUserInfo( $user, $pass );
            if ($binded) {
            	list ($email, $fullname) = $this->_getLDAPUserInfo( $user );
            	$userInfo->setEmail($email);
            	$userInfo->setFullName($fullname);
			}
			return $userInfo;
        }
    }
?>