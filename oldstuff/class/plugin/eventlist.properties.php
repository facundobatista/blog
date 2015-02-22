<?php

    /**
     * @package plugin
     */

	/**
	 * defines the list of events that can be thrown by default
	 *
	 * more events can be defined everywhere, and if you define them
	 * with a constant called "EVENT_", the PluginManager class will be able
	 * to discover them
	 */
	 
	$eventValue = 0;
	 
	// single post and bunch of posts loaded
	define( "EVENT_POST_LOADED", ++$eventValue ); 
	define( "EVENT_POSTS_LOADED", ++$eventValue ); 
	// single comment and bunch of comments loaded
	define( "EVENT_COMMENT_LOADED", ++$eventValue );
	define( "EVENT_COMMENTS_LOADED", ++$eventValue );
	// post previewed, in the admin interface
	define( "EVENT_POST_PREVIEW", ++$eventValue );
	// before and after a post is added, updated and deleted
	define( "EVENT_PRE_POST_ADD", ++$eventValue ); 
	define( "EVENT_POST_POST_ADD", ++$eventValue ); 
	define( "EVENT_PRE_POST_UPDATE", ++$eventValue ); 
	define( "EVENT_POST_POST_UPDATE", ++$eventValue ); 
	define( "EVENT_PRE_POST_DELETE", ++$eventValue ); 
	define( "EVENT_POST_POST_DELETE", ++$eventValue ); 
	// before and after a new category is added, updated and deleted
	define( "EVENT_PRE_CATEGORY_ADD", ++$eventValue ); 
	define( "EVENT_POST_CATEGORY_ADD", ++$eventValue ); 
	define( "EVENT_PRE_CATEGORY_UPDATE", ++$eventValue ); 
	define( "EVENT_POST_CATEGORY_UPDATE", ++$eventValue ); 
	define( "EVENT_PRE_CATEGORY_DELETE", ++$eventValue ); 
	define( "EVENT_POST_CATEGORY_DELETE", ++$eventValue ); 
	// before and after a comment is added, updated and deleted
	define( "EVENT_PRE_COMMENT_ADD", ++$eventValue ); 
	define( "EVENT_POST_COMMENT_ADD", ++$eventValue ); 
	define( "EVENT_PRE_COMMENT_UPDATE", ++$eventValue );
	define( "EVENT_POST_COMMENT_UPDATE", ++$eventValue );
	define( "EVENT_PRE_COMMENT_DELETE", ++$eventValue ); 
	define( "EVENT_POST_COMMENT_DELETE", ++$eventValue ); 
	// before and after a comment is marked as spam and no-spam
	define( "EVENT_PRE_MARK_SPAM_COMMENT", ++$eventValue ); 
	define( "EVENT_POST_MARK_SPAM_COMMENT", ++$eventValue ); 
	define( "EVENT_PRE_MARK_NO_SPAM_COMMENT", ++$eventValue ); 
	define( "EVENT_POST_MARK_NO_SPAM_COMMENT", ++$eventValue ); 
	// before and after a trackback is received
	define( "EVENT_PRE_TRACKBACK_ADD", ++$eventValue );
	define( "EVENT_POST_TRACKBACK_ADD", ++$eventValue );
	define( "EVENT_PRE_TRACKBACK_DELETE", ++$eventValue );
	define( "EVENT_POST_TRACKBACK_DELETE", ++$eventValue );
	define( "EVENT_PRE_TRACKBACK_UPDATE", ++$eventValue );
	define( "EVENT_POST_TRACKBACK_UPDATE", ++$eventValue );		
	// load the post trackbacks
	define( "EVENT_TRACKBACKS_LOADED", ++$eventValue ); 
	// successful and unsuccessful login
	define( "EVENT_LOGIN_SUCCESS", ++$eventValue ); 
	define( "EVENT_LOGIN_FAILURE", ++$eventValue ); 
	// logout event
	define( "EVENT_PRE_LOGOUT", ++$eventValue ); 
	define( "EVENT_POST_LOGOUT", ++$eventValue ); 
	// before and after a user is registered, deleted and updated
	define( "EVENT_PRE_USER_ADD", ++$eventValue ); 
	define( "EVENT_POST_USER_ADD", ++$eventValue ); 
	define( "EVENT_PRE_USER_DELETE", ++$eventValue ); 
	define( "EVENT_POST_USER_DELETE", ++$eventValue ); 
	define( "EVENT_PRE_USER_UPDATE", ++$eventValue ); 
	define( "EVENT_POST_USER_UPDATE", ++$eventValue ); 
	// before and after a new blog is registered
	define( "EVENT_PRE_BLOG_ADD", ++$eventValue );
	define( "EVENT_POST_BLOG_ADD", ++$eventValue );
	define( "EVENT_PRE_BLOG_DELETE", ++$eventValue ); 
	define( "EVENT_POST_BLOG_DELETE", ++$eventValue ); 
	define( "EVENT_PRE_BLOG_UPDATE", ++$eventValue ); 
	define( "EVENT_POST_BLOG_UPDATE", ++$eventValue ); 
	// before and after a custom field is added, updated and deleted
	define( "EVENT_PRE_CUSTOM_FIELD_ADD", ++$eventValue ); 
	define( "EVENT_POST_CUSTOM_FIELD_ADD", ++$eventValue ); 
	define( "EVENT_PRE_CUSTOM_FIELD_UPDATE", ++$eventValue ); 
	define( "EVENT_POST_CUSTOM_FIELD_UPDATE", ++$eventValue ); 
	define( "EVENT_PRE_CUSTOM_FIELD_DELETE", ++$eventValue ); 
	define( "EVENT_POST_CUSTOM_FIELD_DELETE", ++$eventValue ); 
	// before and after the settings of the blog are updated
	define( "EVENT_PRE_BLOG_SETTINGS_UPDATE", ++$eventValue );
	define( "EVENT_POST_BLOG_SETTINGS_UPDATE", ++$eventValue );
	// before and after a resource is added, updated and deleted
	define( "EVENT_PRE_RESOURCE_ADD", ++$eventValue );  
	define( "EVENT_POST_RESOURCE_ADD", ++$eventValue ); 
	define( "EVENT_PRE_RESOURCE_UPDATE", ++$eventValue ); 
	define( "EVENT_POST_RESOURCE_UPDATE", ++$eventValue ); 
	define( "EVENT_PRE_RESOURCE_DELETE", ++$eventValue ); 
	define( "EVENT_POST_RESOURCE_DELETE", ++$eventValue ); 
	// before and after a resource album is added, updated and deleted
	define( "EVENT_PRE_ALBUM_ADD", ++$eventValue ); 
	define( "EVENT_POST_ALBUM_ADD", ++$eventValue ); 
	define( "EVENT_PRE_ALBUM_UPDATE", ++$eventValue ); 
	define( "EVENT_POST_ALBUM_UPDATE", ++$eventValue ); 
	define( "EVENT_PRE_ALBUM_DELETE", ++$eventValue ); 
	define( "EVENT_POST_ALBUM_DELETE", ++$eventValue ); 
	// resources loaded
	define( "EVENT_RESOURCE_LOADED", ++$eventValue ); 
	define( "EVENT_RESOURCES_LOADED", ++$eventValue ); 
	// some others that I forgot..
	define( "EVENT_CUSTOM_FIELD_LOADED", ++$eventValue ); 
	define( "EVENT_CUSTOM_FIELDS_LOADED", ++$eventValue ); 
	define( "EVENT_CATEGORY_LOADED", ++$eventValue ); 
	define( "EVENT_CATEGORIES_LOADED", ++$eventValue ); 
	define( "EVENT_USER_REGISTER", ++$eventValue );
	define( "EVENT_BLOG_REGISTER", ++$eventValue );
	define( "EVENT_USERS_LOADED", ++$eventValue ); 
	define( "EVENT_USER_LOADED", ++$eventValue ); 
	define( "EVENT_BLOG_LOADED", ++$eventValue ); 
	define( "EVENT_BLOGS_LOADED", ++$eventValue ); 
	// template and locale events
	define( "EVENT_PRE_TEMPLATE_ADD", ++$eventValue );
	define( "EVENT_POST_TEMPLATE_ADD", ++$eventValue);
	define( "EVENT_PRE_TEMPLATE_DELETE", ++$eventValue );
	define( "EVENT_POST_TEMPLATE_DELETE", ++$eventValue );
	define( "EVENT_PRE_LOCALE_ADD", ++$eventValue );
	define( "EVENT_POST_LOCALE_ADD", ++$eventValue );
	define( "EVENT_PRE_LOCALE_DELETE", ++$eventValue );
	define( "EVENT_POST_LOCALE_DELETE", ++$eventValue );
	// albums
	define( "EVENT_ALBUM_LOADED", ++$eventValue ); 
	define( "EVENT_ALBUMS_LOADED", ++$eventValue ); 
	// links
	define( "EVENT_PRE_LINK_ADD", ++$eventValue ); 
	define( "EVENT_POST_LINK_ADD", ++$eventValue ); 
	define( "EVENT_PRE_LINK_UPDATE", ++$eventValue ); 
	define( "EVENT_POST_LINK_UPDATE", ++$eventValue );
	define( "EVENT_PRE_LINK_DELETE", ++$eventValue );
	define( "EVENT_POST_LINK_DELETE", ++$eventValue );
	define( "EVENT_LINK_LOADED", ++$eventValue );
	define ("EVENT_LINKS_LOADED", ++$eventValue );
	// link categories
	define( "EVENT_PRE_LINK_CATEGORY_ADD", ++$eventValue );
	define( "EVENT_POST_LINK_CATEGORY_ADD", ++$eventValue );
	define( "EVENT_PRE_LINK_CATEGORY_UPDATE", ++$eventValue );
	define( "EVENT_POST_LINK_CATEGORY_UPDATE", ++$eventValue );
	define( "EVENT_PRE_LINK_CATEGORY_DELETE", ++$eventValue );
	define( "EVENT_POST_LINK_CATEGORY_DELETE", ++$eventValue );
	define( "EVENT_LINK_CATEGORY_LOADED", ++$eventValue ); 
	define( "EVENT_LINK_CATEGORIES_LOADED", ++$eventValue ); 
	// event thrown when plog is going to render some text that could be written
	// in something else other than xhtml markup (wiki) or to implement text filters
	// such as textile and stuff like that... by popular request, again :-)
	define( "EVENT_TEXT_FILTER", ++$eventValue );
	// for referrers
	define( "EVENT_PRE_REFERRER_DELETE", ++$eventValue );
	define( "EVENT_POST_REFERRER_DELETE", ++$eventValue );
	define( "EVENT_PRE_REFERRER_ADD", ++$eventValue );
	define( "EVENT_POST_REFERRER_ADD", ++$eventValue );
	// before and after a trackback is marked as spam and no-spam
	define( "EVENT_PRE_MARK_SPAM_TRACKBACK", EVENT_PRE_MARK_SPAM_COMMENT ); 
	define( "EVENT_POST_MARK_SPAM_TRACKBACK", EVENT_POST_MARK_SPAM_COMMENT ); 
	define( "EVENT_PRE_MARK_NO_SPAM_TRACKBACK", EVENT_PRE_MARK_NO_SPAM_COMMENT ); 
	define( "EVENT_POST_MARK_NO_SPAM_TRACKBACK", EVENT_POST_MARK_NO_SPAM_COMMENT ); 	
	// events related to new blog categories
	define( "EVENT_PRE_ADD_BLOG_CATEGORY", ++$eventValue );
	define( "EVENT_POST_ADD_BLOG_CATEGORY", ++$eventValue );
	define( "EVENT_PRE_UPDATE_BLOG_CATEGORY", ++$eventValue );
	define( "EVENT_POST_UPDATE_BLOG_CATEGORY", ++$eventValue );
	define( "EVENT_PRE_DELETE_BLOG_CATEGORY", ++$eventValue );
	define( "EVENT_POST_DELETE_BLOG_CATEGORY", ++$eventValue );
	define( "EVENT_BLOG_CATEGORIES_LOADED", ++$eventValue );
	define( "EVENT_BLOG_CATEGORY_LOADED", ++$eventValue );
	// global article categories
	define( "EVENT_PRE_ADD_GLOBAL_CATEGORY", ++$eventValue );
	define( "EVENT_POST_ADD_GLOBAL_CATEGORY", ++$eventValue );
	define( "EVENT_PRE_UPDATE_GLOBAL_CATEGORY", ++$eventValue );
	define( "EVENT_POST_UPDATE_GLOBAL_CATEGORY", ++$eventValue );
	define( "EVENT_PRE_DELETE_GLOBAL_CATEGORY", ++$eventValue );
	define( "EVENT_POST_DELETE_GLOBAL_CATEGORY", ++$eventValue );
	define( "EVENT_GLOBAL_CATEGORIES_LOADED", ++$eventValue );	
	// post-processing of templates
	define( "EVENT_PROCESS_BLOG_TEMPLATE_OUTPUT", ++$eventValue );
	// handling of permissions
	define( "EVENT_PRE_ADD_PERMISSION", ++$eventValue );
	define( "EVENT_POST_ADD_PERMISSION", ++$eventValue );
	define( "EVENT_PRE_UPDATE_PERMISSION", ++$eventValue );
	define( "EVENT_POST_UPDATE_PERMISSION", ++$eventValue );
	define( "EVENT_PRE_DELETE_PERMISSION", ++$eventValue );
	define( "EVENT_POST_DELETE_PERMISSION", ++$eventValue );
    define( "EVENT_PROCESS_BLOG_ADMIN_TEMPLATE_OUTPUT", ++$eventValue );
    define( "EVENT_POST_ADMIN_PURGE_TEMP_FOLDER", ++$eventValue );
?>