<?php

	/*
     * This is the array definition of the different mappings
     * action parameter->action class used by the controller of the administrative
     * interface.
     */

    // default action that is used if no other
    $actions["Default"]    = "AdminDefaultAction";
    // after logging in, this is the action called
    $actions["Login"]      = "AdminLoginAction";
    // this action is called after we have verified that the user is valid
    // and want to make him or her choose a blog to work on (or skip
    // directly to the admin menu of the blog if he or she belongs to
    // only one)
    $actions["blogSelect"] = "AdminMainAction";
	$actions["Dashboard"] = "AdminMainAction";
	$actions["Manage"] = "AdminManageAction";
    // form to add new posts
    $actions["newPost"]    = "AdminNewPostAction";
    $actions["previewPost"] = "AdminPreviewPostAction";
    // adds a post to the database
    $actions["addPost"]    = "AdminAddPostAction";
    // form to add new article categories
    $actions["newArticleCategory"] = "AdminNewArticleCategoryAction";
    // adds the category to the db
    $actions["addArticleCategory"] = "AdminAddArticleCategoryAction";
    // adds the category to the db through Ajax
    $actions["addArticleCategoryAjax"] = "AdminAddArticleCategoryAjaxAction";    
    // shows the settings of the blog
    $actions["blogSettings"] = "AdminBlogSettingsAction";
    // updates the settings of the blog
    $actions["updateBlogSettings"] = "AdminUpdateBlogSettingsAction";
    // shows a list of the posts
    $actions["editPosts"] = "AdminEditPostsAction";
    // removes a post from the database
    $actions["deletePost"] = "AdminDeletePostAction";
	$actions["deletePosts"] = "AdminDeletePostAction";
	// massive change post status & category
	$actions["changePostsStatus"] = "AdminChangePostsStatusAction";
	$actions["changePostsCategory"] = "AdminChangePostsCategoryAction";
    // log out
    $actions["Logout"] = "AdminLogoutAction";
    // shows form to add a new link category
    $actions["newLinkCategory"] = "AdminNewLinkCategoryAction";
    // adds a new link category
    $actions["addLinkCategory"] = "AdminAddLinkCategoryAction";
    // shows the form to add a new link
    $actions["newLink"] = "AdminNewLinkAction";
    // adds the action to the database
    $actions["addLink"] = "AdminAddLinkAction";
    // shows a post for edition
    $actions["editPost"] = "AdminEditPostAction";
    // updates the post in the database
    $actions["updatePost"] = "AdminUpdatePostAction";
    // shows the list with the categories
    $actions["editArticleCategories"] = "AdminEditArticleCategoriesAction";
    // deletes an article category from the database
    $actions["deleteArticleCategory"] = "AdminDeleteArticleCategoryAction";
    $actions["deleteArticleCategories"] = "AdminDeleteArticleCategoryAction";
    // edits an article category
    $actions["editArticleCategory"] = "AdminEditArticleCategoryAction";
    // updates the category
    $actions["updateArticleCategory"] = "AdminUpdateArticleCategoryAction";
    // shows the list of links in order to edit them
    $actions["editLinks"] = "AdminEditLinksAction";
    // deletes a link from the database
    $actions["deleteLink"] = "AdminDeleteLinkAction";
    $actions["deleteLinks"] = "AdminDeleteLinkAction";
	// massive change links category
	$actions["changeLinksCategory"] = "AdminChangeLinksCategoryAction";	
    // shows the list of link categories
    $actions["editLinkCategories"] = "AdminEditLinkCategoriesAction";
    // deletes a link category
    $actions["deleteLinkCategory"] = "AdminDeleteLinkCategoryAction";
    $actions["deleteLinkCategories"] = "AdminDeleteLinkCategoryAction";
    // edits a link
    $actions["editLink"] = "AdminEditLinkAction";
    // updates a link
    $actions["updateLink"] = "AdminUpdateLinkAction";
    // edits a link category
    $actions["editLinkCategory"] = "AdminEditLinkCategoryAction";
    // updates a link category
    $actions["updateLinkCategory"] = "AdminUpdateLinkCategoryAction";
    // statistics
    $actions["Stats"] = "AdminStatisticsAction";
    // edit comments
    $actions["editComments"] = "AdminEditCommentsAction";
	// edit trackbacks
	$actions["editTrackbacks"] = "AdminEditTrackbacksAction";
    // deletes a comment
    $actions["deleteComment"] = "AdminDeleteCommentAction";
	$actions["deleteComments"] = "AdminDeleteCommentAction";
	// massive change comments status
	$actions["changeCommentsStatus"] = "AdminChangeCommentsStatusAction";	
    // show the user settings
    $actions["userSettings"] = "AdminUserSettingsAction";
    // update the user settings
    $actions["updateUserSettings"] = "AdminUpdateUserSettingsAction";
    // show statistics about a post
    $actions["postStats"] = "AdminPostStatsAction";
    // sends trackbacks
    $actions["sendTrackbacks"] = "AdminSendTrackbacksAction";
    // plugin center
    $actions["pluginCenter"] = "AdminPluginCenterAction";
    // pop-up help window
    $actions["Help"] = "AdminHelpAction";
    // super admin interface, main part
    $actions["adminSettings"] = "AdminSiteSettingsAction";
    // list of users in the site
    $actions["editSiteUsers"] = "AdminSiteUsersAction";
	// edits a user
	$actions["editSiteUser"] = "AdminUserProfileAction";
    // delete users
    $actions["deleteUsers"] = "AdminDeleteUsersAction";
    $actions["deleteUser"] = "AdminDeleteUsersAction";
    // list of blogs in the site
    $actions["editSiteBlogs"] = "AdminSiteBlogsAction";
    // global settings that can be changed
    $actions["editSiteSettings"] = "AdminGlobalSettingsAction";
    // updates the global settings
    $actions["updateGlobalSettings"] = "AdminUpdateGlobalSettingsAction";
    // edit any user profile
    $actions["editUserProfile"] = "AdminUserProfileAction";
    // updates a user profie
    $actions["updateUserProfile"] = "AdminUpdateUserProfileAction";
    // shows the form to add a user
    $actions["createUser"] = "AdminCreateUserAction";
    // adds a user to the database
    $actions["addUser"] = "AdminAddUserAction";
    // shows the form to add a blog
    $actions["createBlog"] = "AdminCreateBlogAction";
    // adds the blog to the database
    $actions["addBlog"] = "AdminAddBlogAction";
    // edit the users in a blog
    $actions["editBlogUsers"] = "AdminEditSiteBlogUsersAction";
    // edit a blog
    $actions["editBlog"] = "AdminEditBlogAction";
    // saves the settings of the blog we edited
    $actions["updateEditBlog"] = "AdminUpdateEditBlogAction";
    // updates the users of a blog
    $actions["updateBlogUsers"] = "AdminUpdateSiteBlogUsersAction";
    // shows the form to add a user to the blog
    $actions["newBlogUser"] = "AdminNewBlogUserAction";
    // adds a user to the blog
    $actions["addBlogUser"] = "AdminAddBlogUserAction";
    // shows a list of the users that belong to this blog
    $actions["showBlogUsers"] = "AdminShowBlogUsersAction";
    // revokes the permissions of users in a blog
    $actions["deleteBlogUserPermissions"] = "AdminDeleteBlogUserPermissionsAction";
    $actions["deleteBlogUsersPermissions"] = "AdminDeleteBlogUserPermissionsAction";
	// lists the locales installed in the site
    $actions["siteLocales"] = "AdminSiteLocalesAction";
    // removes a locale from disk
    $actions["deleteLocales"] = "AdminDeleteLocalesAction";
    $actions["deleteLocale"] = "AdminDeleteLocalesAction";
    // shows the form to upload a new locale file
    $actions["newLocale"] = "AdminNewLocaleAction";
    // adds a new locale to the server
    $actions["uploadLocale"] = "AdminAddLocaleAction";
	$actions["scanLocales"] = "AdminAddLocaleAction";
    // shows a list of the locales available
    $actions["siteTemplates"] = "AdminEditTemplatesAction";
    // deletes templates
    $actions["deleteTemplates"] = "AdminDeleteTemplatesAction";
    $actions["deleteTemplate"] = "AdminDeleteTemplatesAction";
    // shows the form to add a new template
    $actions["newTemplate"] = "AdminNewTemplateAction";
    // adds a new template to the system
    $actions["addTemplateUpload"] = "AdminAddTemplateAction";
    $actions["scanTemplates"] = "AdminAddTemplateAction";
    // list of templates available in the blog
    $actions["blogTemplates"] = "AdminEditBlogTemplatesAction";
    // shows the form to add a template
    $actions["newBlogTemplate"] = "AdminNewBlogTemplateAction";
    // adds a new template to the blog
    $actions["addBlogTemplate"] = "AdminAddBlogTemplateAction";
    $actions["scanBlogTemplates"] = "AdminAddBlogTemplateAction";
    // removes a template from the blog
    $actions["deleteBlogTemplate"] = "AdminDeleteBlogTemplateAction";
    $actions["deleteBlogTemplates"] = "AdminDeleteBlogTemplateAction";
    // deletes blogs
    $actions["deleteBlogs"] = "AdminDeleteBlogAction";
    $actions["deleteBlog"] = "AdminDeleteBlogAction";
	// removes all posts marked as spam and marked as deleted
    $actions["purgePosts"] = "AdminPurgePostsAction";
    // shows the form to add a new resource album
    $actions["newResourceAlbum"] = "AdminNewResourceAlbumAction";
    // adds a new album to the database
    $actions["addResourceAlbum"] = "AdminAddResourceAlbumAction";
    // lists the resource albums of a blog
    $actions["resourceAlbums"] = "AdminResourceAlbumsAction";
    // shows the form to add a new resource to the blog
    $actions["newResource"] = "AdminNewResourceAction";
    // adds the new resource to the blog
    $actions["addResource"] = "AdminAddResourceAction";
    // list of resources
    $actions["resources"] = "AdminResourcesAction";
    // information about a resource
    $actions["resourceInfo"] = "AdminResourceInfoAction";
    // updates the information of a resource
    $actions["updateResource"] = "AdminUpdateResourceAction";
    // deletes a resource
    $actions["deleteResource"] = "AdminDeleteResourceAction";
    // shows a window with all the resources
    $actions["resourceList"] = "AdminResourceListAction";
    $actions["resourcesGroup"] = "AdminResourcesGroupAction";
    // edits a resource album
    $actions["editResourceAlbum"] = "AdminEditResourceAlbumAction";
    // updates a resource album
    $actions["updateResourceAlbum"] = "AdminUpdateResourceAlbumAction";
    // removes albums
    $actions["deleteResourceAlbum"] = "AdminDeleteResourceAlbumAction";
	$actions["deleteResourceItems"] = "AdminDeleteGalleryItemsAction";
	// massive change gallery items album
	$actions["changeGalleryItemsAlbum"] = "AdminChangeGalleryItemsAlbumAction";
    // mark as spam
    $actions["markComment"] = "AdminMarkCommentAction";
    $actions["markTrackback"] = "AdminMarkTrackbackAction";	
    // purge spam comments
    $actions["purgeSpamComments"] = "AdminPurgeSpamCommentsAction";
	// regenerate a preview
	$actions["regeneratePreview"] = "AdminRegeneratePreviewAction";
	// null action
	$actions["emptyAction"] = "AdminEmptyAction";
	// show the form to add a new custom field
	$actions["newCustomField"] = "AdminNewCustomFieldAction";
	// add the custom field
	$actions["addCustomField"] = "AdminAddCustomFieldAction";
	// list the custom fields
	$actions["blogCustomFields"] = "AdminBlogCustomFieldsAction";
	// delete custom fields from a blog
	$actions["deleteCustomFields"] = "AdminDeleteCustomFieldsAction";
	$actions["deleteCustomField"] = "AdminDeleteCustomFieldsAction";
	// edit a custom field
	$actions["editCustomField"] = "AdminEditCustomFieldAction";
	// update a custom field
	$actions["updateCustomField"] = "AdminUpdateCustomFieldAction";
	// in case it is needed, this keeps the connection alive in the background
	$actions["xmlPing"] = "AdminXmlPingAction";
	// the action below is used in cooperation with the XmlHttpRequest object to
	// automatically save drafts of posts in the background
	$actions["saveDraftArticleAjax"] = "AdminSaveDraftArticleAjaxAction";
	// remove a trackback
	$actions["deleteTrackback"] = "AdminDeleteTrackbackAction";
	$actions["deleteTrackbacks"] = "AdminDeleteTrackbackAction";
	// massive change trackbacks status
	$actions["changeTrackbacksStatus"] = "AdminChangeTrackbacksStatusAction";		
	// delete referrers
	$actions["deleteReferrer"] = "AdminDeleteReferrerAction";
	$actions["deleteReferrers"] = "AdminDeleteReferrerAction";
	$actions["deleteArticleReferrer"] = "AdminDeleteReferrerAction";
	$actions["deleteArticleReferrers"] = "AdminDeleteReferrerAction";
	// control center
	$actions["controlCenter"] = "AdminControlCenterAction";
	// user picture selector
	$actions["userPictureSelect"] = "AdminUserPictureSelectAction";
	// blog template selector
	$actions["blogTemplateChooser"] = "AdminBlogTemplateChooserAction";
	// clean up
	$actions["cleanUp"] = "AdminCleanupAction";
	$actions["doCleanUp"] = "AdminCleanupAction";
	// removes all users marked as deleted
    $actions["purgeUsers"] = "AdminPurgeUsersAction";	
	// removes all blogs marked as deleted
	$actions["purgeBlogs"] = "AdminPurgeBlogsAction";
    // register a new blog
    $actions["registerBlog"] = "AdminRegisterBlogAction";
    $actions["finishRegisterBlog"] = "AdminDoRegisterBlogAction";    
	// global blog categories
	$actions["newBlogCategory"] = "AdminNewBlogCategoryAction";
	$actions["addBlogCategory"] = "AdminAddBlogCategoryAction";	
	$actions["editBlogCategories"] = "AdminBlogCategoriesAction";	
	$actions["deleteBlogCategory"] = "AdminDeleteBlogCategoryAction";
	$actions["deleteBlogCategories"] = "AdminDeleteBlogCategoryAction";
	//nick add this to add global article categories.
	$actions["newGlobalArticleCategory"] = "AdminNewGlobalArticleCategoryAction";
  	// adds the category to the db
  	$actions["addGlobalArticleCategory"] = "AdminAddGlobalArticleCategoryAction";	
	//edit globalarticle categories.
	$actions["editGlobalArticleCategories"] = "AdminEditGlobalArticleCategoriesAction";
  	// deletes an article category from the database
  	$actions["deleteGlobalArticleCategory"] = "AdminDeleteGlobalArticleCategoryAction";
  	$actions["deleteGlobalArticleCategories"] = "AdminDeleteGlobalArticleCategoryAction";
  	// edits an article category
  	$actions["editGlobalArticleCategory"] = "AdminEditGlobalArticleCategoryAction";
  	// updates the category
  	$actions["updateGlobalArticleCategory"] = "AdminUpdateGlobalArticleCategoryAction";	
	// resend the confirmation email
	$actions["resendConfirmation"] = "AdminResendConfirmationAction";
	// allow admins to control any blog
	$actions["adminBlogSelect"] = "AdminAdminBlogSelectAction";
	// generic user chooser
	$actions["siteUsersChooser"] = "AdminUserChooserAction";
	// generic blog chooser
	$actions["siteBlogsChooser"] = "AdminBlogChooserAction";
	// edit and update blog categories
	$actions["editBlogCategory"] = "AdminEditBlogCategoryAction";
	$actions["updateBlogCategory"] = "AdminUpdateBlogCategoryAction";
	// permissions
	$actions["permissionsList"] = "AdminPermissionsListAction";
	$actions["deletePermission"] = "AdminDeletePermissionsAction";
	$actions["deletePermissions"] = "AdminDeletePermissionsAction";	
	$actions["editPermission"] = "AdminEditPermissionAction";
	$actions["updatePermission"] = "AdminUpdatePermissionAction";
	$actions["updatePermission"] = "AdminUpdatePermissionAction";		
	$actions["newPermission"] = "AdminNewPermissionAction";	
	$actions["addPermission"] = "AdminAddPermissionAction";
	// edit blog user
	$actions["editBlogUser"] = "AdminEditBlogUserAction";
	// update blog user
	$actions["updateBlogUser"] = "AdminUpdateBlogUserAction";	
	// permission required
	$actions["permissionRequired"] = "AdminPermissionRequiredAction";
	// global plugin settings
	$actions["pluginSettings"] = "AdminPluginSettingsAction";
	$actions["updatePluginSettings"] = "AdminUpdatePluginSettingsAction";	
	// bulk update of blogs
	$actions["changeBlogStatus"] = "AdminChangeBlogStatusAction";
	// bulk update of users
	$actions["changeUserStatus"] = "AdminChangeUserStatusAction";
	// perform an md5 check of some core files or LT Core Version Check
	$actions["Versions"] = "AdminVersionCheckAction";
?>
