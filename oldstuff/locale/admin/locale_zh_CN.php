<?php

/////////////////                                          //////////////////
///////////////// STRINGS FOR THE ADMINISTRATION INTERFACE //////////////////
/////////////////                                          //////////////////

// login page
$messages['login'] = '登入';
$messages['welcome_message'] = '欢迎使用 LifeType！';
$messages['error_incorrect_username_or_password'] = '很抱歉，您输入的帐号或密码错误。';
$messages['error_dont_belong_to_any_blog'] = '很抱歉，您没有使用系统中任何一个博客站台的权限。';
$messages['logout_message'] = '您已经顺利登出系统。';
$messages['logout_message_2'] = '请按 <a href="%1$s">这里</a> 链接到 %2$s。';
$messages['error_access_forbidden'] = '您目前没有权限进入管理介面，请到这里登入。';
$messages['username'] = '用户名称';
$messages['password'] = '用户密码';

// dashboard
$messages['dashboard'] = '管理面版';
$messages['recent_articles'] = '最近发表文章';
$messages['recent_comments'] = '最近发表评论';
$messages['recent_trackbacks'] = '最近引用列表';
$messages['blog_statistics'] = '博客统计';
$messages['total_posts'] = '文章总数';
$messages['total_comments'] = '评论总数';
$messages['total_trackbacks'] = '引用总数';
$messages['total_viewed'] = '文章阅读总数';
$messages['in'] = '于';

// menu options
$messages['newPost'] = '发表新文章';
$messages['Manage'] = '内容管理';
$messages['managePosts'] = '文章管理';
$messages['editPosts'] = '文章列表';
$messages['editArticleCategories'] = '文章分类列表';
$messages['newArticleCategory'] = '新增文章分类';
$messages['manageLinks'] = '网站链接管理';
$messages['editLinks'] = '网站链接列表';
$messages['newLink'] = '新增网站链接';
$messages['editLink'] = '编辑网站链接';
$messages['editLinkCategories'] = '网站链接分类列表';
$messages['newLinkCategory'] = '新增链接分类';
$messages['editLinkCategory'] = '编辑网站链接分类';
$messages['manageCustomFields'] = '管理自订栏位';
$messages['blogCustomFields'] = '自订栏位列表';
$messages['newCustomField'] = '新增自订栏位';
$messages['resourceCenter'] = '文件中心';
$messages['resources'] = '文件列表';
$messages['newResourceAlbum'] = '新增文件夹';
$messages['newResource'] = '新增文件';
$messages['controlCenter'] = '个人博客设置';
$messages['manageSettings'] = '基本设置';
$messages['blogSettings'] = '博客设置';
$messages['userSettings'] = '用户设置';
$messages['pluginCenter'] = '外挂中心';
$messages['Stats'] = '统计数据';
$messages['manageBlogUsers'] = '管理博客用户';
$messages['newBlogUser'] = '新增博客用户';
$messages['showBlogUsers'] = '博客用户列表';
$messages['manageBlogTemplates'] = '管理博客模版';
$messages['newBlogTemplate'] = '新增博客模版';
$messages['blogTemplates'] = '博客模版列表';
$messages['adminSettings'] = '全部站台管控';
$messages['Users'] = '用户';
$messages['createUser'] = '新增用户';
$messages['editSiteUsers'] = '管理用户';
$messages['Blogs'] = '管理博客';
$messages['createBlog'] = '建立博客';
$messages['editSiteBlogs'] = '博客站台管理';
$messages['Locales'] = '语系管理';
$messages['newLocale'] = '新增语系';
$messages['siteLocales'] = '语系文件列表';
$messages['Templates'] = '模版管理';
$messages['newTemplate'] = '新增模版';
$messages['siteTemplates'] = '模版管理';
$messages['GlobalSettings'] = '全域设置';
$messages['editSiteSettings'] = '一般设置';
$messages['summarySettings'] = ' 汇整页面设置';
$messages['templateSettings'] = '模版设置';
$messages['urlSettings'] = '网址设置';
$messages['emailSettings'] = '电子邮件设置';
$messages['uploadSettings'] = '上传设置';
$messages['helpersSettings'] = '工具设置';
$messages['interfacesSettings'] = '网路服务介面设置';
$messages['securitySettings'] = '系统安全设置';
$messages['bayesianSettings'] = '贝氏过滤设置';
$messages['resourcesSettings'] = '文件中心设置';
$messages['searchSettings'] = '搜寻设置';
$messages['cleanUpSection'] = '清理垃圾专区';
$messages['cleanUp'] = '清理垃圾';
$messages['editResourceAlbum'] = '编辑文件文件夹';
$messages['resourceInfo'] = '文件信息';
$messages['editBlog'] = '博客管理';
$messages['Logout'] = '登出';

// new post
$messages['topic'] = '标题';
$messages['topic_help'] = '文章标题';
$messages['text'] = '内文';
$messages['text_help'] = '这部份的内容会在博客首页出现。';
$messages['extended_text'] = '扩展内文';
$messages['extended_text_help'] = '您在此输入的文字只会在单篇汇整状态下显示，除非您在“设置”页面中修改了设置。';
$messages['trackback_urls'] = '真实引用网址';
$messages['trackback_urls_help'] = '如果您要引用的文章没有支援‘引用网址自动搜寻’机制，请在这里输入他们的真实引用网址，并用断行来隔开。';
$messages['post_slug'] = '短标题 ';
$messages['post_slug_help'] = '短标题将会用来建立简洁的静态链接网址';
$messages['date'] = '日期';
$messages['post_date_help'] = '文章发表日期';
$messages['status'] = '状态';
$messages['post_status_help'] = '选取一个状态';
$messages['post_status_published'] = '定稿';
$messages['post_status_draft'] = '草稿';
$messages['post_status_deleted'] = '已删除';
$messages['post_categories_help'] = '选取一个或一个以上的分类';
$messages['post_comments_enabled_help'] = '启用评论留言功能';
$messages['send_notification_help'] = '如果有人对本文发表评论，便向我发送电子邮件通知';
$messages['send_trackback_pings_help'] = '发送引用通告';
$messages['send_xmlrpc_pings_help'] = '送出 XMLRPC 通告';
$messages['save_draft_and_continue'] = '储存草稿';
$messages['preview'] = '预览';
$messages['add_post'] = '发表!';
$messages['error_saving_draft'] = '储存草稿发生错误！';
$messages['draft_saved_ok'] = '草稿 “%s” 已顺利储存';
$messages['error_sending_request'] = '传送要求时发生错误';
$messages['error_no_category_selected'] = '你没有选择任何分类';
$messages['error_missing_post_topic'] = '请输入文章标题！';
$messages['error_missing_post_text'] = '请输入文章内文！';
$messages['error_adding_post'] = '发表文章发生错误！';
$messages['post_added_not_published'] = '文章已顺利新增，但尚未正式发表。';
$messages['post_added_ok'] = '文章已顺利新增';
$messages['send_notifications_ok'] = '当有新的评论或是引用时，便向我发送电子邮件通知';
$messages['bookmarklet'] = "书签小程式";
$messages['bookmarklet_help'] = "把下面的链接拉到你工具列，或是按下滑鼠右键把链接加到我的最爱。";
$messages['blogit_to_lifetype'] = "把文章加到 LifeType！";
$messages['original_post'] = "（原文）";

// send trackbacks
$messages['error_sending_trackbacks'] = '发送下列引用通知时产生错误。';
$messages['send_trackbacks_help'] = '请勾选您所要发送引用通告的网址。(请确定该网站支援引用通告的功能)';
$messages['send_trackbacks'] = '发送引用通知';
$messages['ping_selected'] = '向勾选的网址发送引用通知';
$messages['trackbacks_sent_ok'] = '引用通知已经成功发送到勾选的网址。';

// posts page
$messages['show_by'] = '更新列表';
$messages['author'] = '作者';
$messages['post_status_all'] = '全部';
$messages['author_all'] = '全部作者';
$messages['search_terms'] = '搜寻关键字';
$messages['show'] = '更新';
$messages['delete'] = '删除';
$messages['actions'] = '动作';
$messages['all'] = '全部';
$messages['category_all'] = '全部分类';
$messages['error_incorrect_article_id'] = '文章 ID 不正确。';
$messages['error_deleting_article'] = '在删除文章"%s"时，发生错误。';
$messages['article_deleted_ok'] = '文章“%s” 已顺利删除。';
$messages['articles_deleted_ok'] = '文章“%s” 已顺利删除。';
$messages['error_deleting_article2'] = '删除文章时发生错误 (id = %s) ';

// edit post page
$messages['update'] = '更新';
$messages['editPost'] = '编辑文章';
$messages['post_updated_ok'] = '文章[%s]已成功更新。';
$messages['error_updating_post'] = '更新文章时发生错误';
$messages['notification_added'] = '当有新的评论或是引用时，便向我发送电子邮件通知';
$messages['notification_removed'] = '当有新的评论或是引用时，不要向我发送电子邮件通知';

// post comments
$messages['url'] = '网址';
$messages['comment_status_all'] = '全部评论';
$messages['comment_status_spam'] = '垃圾评论';
$messages['comment_status_nonspam'] = '正常评论';
$messages['error_fetching_comments'] = '读取文章评论数据时，发生错误。';
$messages['error_deleting_comments'] = '在删除评论时发生错误或您没有勾选任何要删除的评论。';
$messages['comment_deleted_ok'] = '“%s”这篇文章的评论已顺利删除。';
$messages['comments_deleted_ok'] = '“%s”这篇文章的评论已顺利删除。';
$messages['error_deleting_comment'] = '在删除评论“%s”时发生错误。';
$messages['error_deleting_comment2'] = '删除评论时发生错误 (id = %s)';
$messages['editComments'] = '评论列表';
$messages['mark_as_spam'] = '标示为垃圾评论';
$messages['mark_as_no_spam'] = '标示为正常评论';
$messages['error_incorrect_comment_id'] = '留言评论 ID 不正确。';
$messages['error_marking_comment_as_spam'] = '在将本篇评论留言标示为垃圾留言时发生错误。';
$messages['comment_marked_as_spam_ok'] = '您已经顺利将本篇评论留言标示为垃圾留言。';
$messages['error_marking_comment_as_nonspam'] = '在将本篇评论留言标示为正常留言时发生错误。';
$messages['comment_marked_as_nonspam_ok'] = '您已经顺利将本篇评论留言标示为正常留言。';
$messages['comment_no_topic'] = '没有评论主旨。';

// post trackbacks
$messages['blog'] = '博客';
$messages['excerpt'] = '摘要';
$messages['error_fetching_trackbacks'] = '读取引用数据时，发生错误。';
$messages['error_deleting_trackbacks'] = '在删除引用时发生错误或是你没有勾选任何要删除的引用。';
$messages['error_deleting_trackback'] = '在删除引用“%s”时发生错误';
$messages['error_deleting_trackback2'] = '删除引用时发生错误 (id = %s)';
$messages['trackback_deleted_ok'] = '“%s”这篇引用已顺利删除。';
$messages['trackbacks_deleted_ok'] = '“%s”这篇引用已顺利删除。';
$messages['editTrackbacks'] = '引用列表';

// post statistics
$messages['referrer'] = '逆向链接';
$messages['hits'] = '点击数';
$messages['error_no_items_selected'] = '你没有勾选任何要删除的项目';
$messages['error_deleting_referrer'] = '在删除逆向链接“%s”时发生错误';
$messages['error_deleting_referrer2'] = '删除逆向链接时发生错误 (id = %s)';
$messages['referrer_deleted_ok'] = '“%s”这篇逆向链接已顺利删除。';
$messages['referrers_deleted_ok'] = '“%s”这篇逆向链接已顺利删除。';

// categories
$messages['posts'] = '文章列表';
$messages['show_in_main_page'] = '在首页显示';
$messages['error_category_has_articles'] = '无法删除“%s”这个分类，因为该分类下还有文章。请先修改文章分类后，再重试一次。';
$messages['category_deleted_ok'] = '“%s”这个分类已顺利删除。';
$messages['categories_deleted_ok'] = '“%s”这个分类已顺利删除。';
$messages['error_deleting_category'] = '在删除分类“%s”时发生错误';
$messages['error_deleting_category2'] = '删除分类时发生错误 (id = %s)';
$messages['yes'] = '是';
$messages['no'] = '否';

// new category
$messages['name'] = '名称';
$messages['category_name_help'] = '请输入分类名称';
$messages['description'] = '描述';
$messages['category_description_help'] = '请输入详细的分类描述';
$messages['show_in_main_page_help'] = '选取这个选项，则在这个分类下的文章会在首页显示。否则只有当浏览这个分类时才会看到文章。';
$messages['error_empty_name'] = '你必须输入分类名称';
$messages['error_empty_description'] = '你必须输入分类描述';
$messages['error_adding_article_category'] = '在新增分类时发生错误。请检查输入的数据，再重试一次。';
$messages['category_added_ok'] = '分类名称 “%s”已经顺利新增';
$messages['add'] = '新增';
$messages['reset'] = '重新设置';

// update category
$messages['error_updating_article_category'] = '更新文章分类时发生错误。';
$messages['article_category_updated_ok'] = '分类 “%s” 已顺利更新。';

// links
$messages['feed'] = 'Feed';
$messages['error_no_links_selected'] = '网站链接 ID 错误或您没有选择任何网站链接，无法删除。';
$messages['error_incorrect_link_id'] = '网站链接 ID 不正确';
$messages['error_removing_link'] = '在删除网站链接“%s”时发生错误。';
$messages['error_removing_link2'] = '在删除网站链接时发生错误，id = %d';
$messages['link_deleted_ok'] = '网站链接“%s”已顺利删除。';
$messages['links_deleted_ok'] = '网站链接“%s”已顺利删除。';

// new link
$messages['link_name_help'] = '请输入链接名称。';
$messages['link_url_help'] = '链接网址';
$messages['link_description_help'] = '简短描述';
$messages['link_feed_help'] = '你也可以提供任何的 RSS 或 Atom feeds 的链接。';
$messages['link_category_help'] = '选取一个网站链接分类';
$messages['error_adding_link'] = '新增网站链接时发生错误。请检查输入的数据，再重试一次。';
$messages['error_invalid_url'] = '网址不正确';
$messages['link_added_ok'] = '网站链接“%s”已顺利新增';
$messages['bookmarkit_to_lifetype'] = "把书签加到 LifeType！";

// update link
$messages['error_updating_link'] = '更新网站链接时发生错误。请检查输入的数据，再重试一次。';
$messages['error_fetching_link'] = '读取网站链接数据时发生错误。';
$messages['link_updated_ok'] = '网站链接“%s”已顺利更新';

// link categories
$messages['error_invalid_link_category_id'] = '网站链接分类ID不正确或没有选择链接分类，无法删除。';
$messages['error_links_in_link_category'] = '无法删除“%s”这个网站链接分类，因为该分类下还有链接。请先修改网站链接后，再重试一次。';
$messages['error_removing_link_category'] = '在删除网站链接分类“%s”时发生错误。';
$messages['link_category_deleted_ok'] = '网站链接分类“%s”已顺利删除。';
$messages['link_categories_deleted_ok'] = '网站链接分类“%s”已顺利删除。';
$messages['error_removing_link_category2'] = '删除网站链接分类时发生错误 (id = %s)';

// new link category
$messages['link_category_name_help'] = '网站链接分类名称';
$messages['error_adding_link_category'] = '新增网站链接分类时发生错误。';
$messages['link_category_added_ok'] = '网站链接分类“%s”已顺利新增';

// edit link category
$messages['error_updating_link_category'] = '更新网站链接分类时发生错误。请检查输入数据后，再试一次。';
$messages['link_category_updated_ok'] = '网站链接分类“%s”已顺利更新';
$messages['error_fetching_link_category'] = '读取网站链接分类数据时发生错误。';

// custom fields
$messages['type'] = '类型';
$messages['hidden'] = '隐藏';
$messages['fields_deleted_ok'] = '“%s” 自订栏位已顺利删除';
$messages['field_deleted_ok'] = '“%s” 自订栏位已顺利删除';
$messages['error_deleting_field'] = '在删除自订栏位“%s”时发生错误。';
$messages['error_deleting_field2'] = '删除自订栏位时发生错误 (id = %s)';
$messages['error_incorrect_field_id'] = '自订栏位ID不正确';

// new custom field
$messages['field_name_help'] = '在发表文章时，用来显示自订栏位的名称';
$messages['field_description_help'] = '自订栏位的简短描述';
$messages['field_type_help'] = '选择一个合适的栏位类型';
$messages['field_hidden_help'] = '如果勾选隐藏，那么在新增或修改文章时便不会出现该自订栏位。这个功能主要提供给外挂程式专用。';
$messages['error_adding_custom_field'] = '新增自订栏位时发生错误。请检查输入数据后，再试一次。';
$messages['custom_field_added_ok'] = '自订栏位“%s”已顺利更新';
$messages['text_field'] = '文字栏位 (Text Field)';
$messages['text_area'] = '文字区块 (Text Box)';
$messages['checkbox'] = '核取方块 (Check Box)';
$messages['date_field'] = '日期选择 (Date Chooser)';

// edit custom field
$messages['error_fetching_custom_field'] = '读取自订栏位数据时发生错误。';
$messages['error_updating_custom_field'] = '更新自订栏位时发生错误。请检查输入数据后，再试一次。';
$messages['custom_field_updated_ok'] = '自订栏位“%s”已顺利更新';

// resources
$messages['root_album'] = '主文件夹';
$messages['num_resources'] = '文件数';
$messages['total_size'] = '文件大小';
$messages['album'] = '文件夹';
$messages['error_incorrect_album_id'] = '文件夹 ID 不正确';
$messages['error_base_storage_folder_missing_or_unreadable'] = 'LifeType 无法建立文件存档所必需的文件夹。 原因可能是因为PHP以安全模式在执行或是你没有足够的权限上传文件。 你可以试着手动建立下列文件夹: <br/><br/>%s<br/><br/>如果这些文件夹已经存在，请确定你可以使用浏览器来进行读写。';
$messages['items_deleted_ok'] = '“%s”已顺利删除';
$messages['error_album_has_children'] = '“%s”文件夹里面还有文件或子文件夹。请将文件或文件夹移除后在重试一次。';
$messages['item_deleted_ok'] = '“%s”已顺利删除';
$messages['error_deleting_album'] = '在删除文件夹“%s”时发生错误。';
$messages['error_deleting_album2'] = '删除文件夹时发生错误 (id = %s)';
$messages['error_deleting_resource'] = '在删除文件“%s”时发生错误。';
$messages['error_deleting_resource2'] = '删除文件时发生错误 (id = %s)';
$messages['error_no_resources_selected'] = '没有选择要删除的项目。';
$messages['resource_deleted_ok'] = '文件：“%s” 已顺利删除';
$messages['album_deleted_ok'] = '文件夹：“%s” 已顺利删除';
$messages['add_resource'] = '新增文件 (原图)';
$messages['add_resource_preview'] = '新增文件预览 (小图)';
$messages['add_resource_medium'] = '新增文件预览 (中图)';
$messages['add_album'] = '新增文件夹';

// new album
$messages['album_name_help'] = '文件夹简短名称';
$messages['parent'] = '上层目录';
$messages['no_parent'] = '顶端目录';
$messages['parent_album_help'] = '使用这个选项来安排子文件夹，同时让你的文件放置更有组织。';
$messages['album_description_help'] = '对文件夹内容做详细的描述说明。';
$messages['error_adding_album'] = '新增文件夹时发生错误。请检查输入数据后，再试一次。';
$messages['album_added_ok'] = '文件夹：“%s” 已顺利新增。';

// edit album
$messages['error_incorrect_album_id'] = '文件夹ID不正确。';
$messages['error_fetching_album'] = '读取文件夹数据时发生错误。';
$messages['error_updating_album'] = '更新文件夹时发生错误。请检查输入数据后，再试一次。';
$messages['album_updated_ok'] = '文件夹“%s”已顺利更新';
$messages['show_album_help'] = '取消勾选，这个文件夹将不会出现在博客文件夹列表中。';

// new resource
$messages['file'] = '文件';
$messages['resource_file_help'] = '下面的文件将会新增到博客的文件中心。如果你要同时上传多个文件，请使用下方“新增上传栏位”的链接来新增栏位。';
$messages['add_field'] = '新增上传栏位';
$messages['resource_description_help'] = '关于这个文件内容的详细描述。';
$messages['resource_album_help'] = '选择你想将文件上传到那个文件夹。';
$messages['error_no_resource_uploaded'] = '你并未选择任何要上传的文件。';
$messages['resource_added_ok'] = '文件：“%s”已顺利新增。';
$messages['error_resource_forbidden_extension'] = '无法新增文件，因为用了系统不允许的副档名。';
$messages['error_resource_too_big'] = '无法新增文件，因为文件太大了。';
$messages['error_uploads_disabled'] = '无法新增文件，因为伺服器管理员关闭了这项功能。';
$messages['error_quota_exceeded'] = '无法新增文件，因为已经超过容许的文件容量限度。';
$messages['error_adding_resource'] = '在新增文件时发生错误。';

// edit resource
$messages['editResource'] = '编辑文件';
$messages['resource_information_help'] = '下面是一些与这个文件有关的信息';
$messages['information'] = '文件信息';
$messages['thumbnail_format'] = '缩图格式';
$messages['regenerate_preview'] = '重新产生预览缩图';
$messages['error_fetching_resource'] = '读取文件信息时发生错误。';
$messages['error_updating_resource'] = '更新文件时发生错误。';
$messages['resource_updated_ok'] = '文件：“%s”已顺利更新。';

// blog settings
$messages['blog_link'] = '博客站台网址';
$messages['blog_link_help'] = '不能修改';
$messages['blog_name_help'] = '站台名称';
$messages['blog_description_help'] = '站台相关说明';
$messages['language'] = '语系';
$messages['blog_language_help'] = '系统文字以及日期所使用的语言';
$messages['max_main_page_items'] = '首页文章数目';
$messages['max_main_page_items_help'] = '您要在首页显示几篇文章？';
$messages['max_recent_items'] = '近期文章数目';
$messages['max_recent_items_help'] = '您要在“近期文章列表”显示几篇文章？';
$messages['template'] = '模版';
$messages['choose'] = '预览选取...';
$messages['blog_template_help'] = '请选择您的博客站台所要使用的外观样式模版';
$messages['use_read_more'] = '在文章使用“查看全文...”链接';
$messages['use_read_more_help'] = '如果设置为“是”，那么您在首页的文章就会自动产生“查看全文”链接，这个链接会连到单篇文章的静态固定网址，再显示全文的“扩展内文部分”。';
$messages['enable_wysiwyg'] = '启用所见即所得（WYSIWYG）文章编辑。';
$messages['enable_wysiwyg_help'] = '如果您想要立刻看到您的编辑结果，请设置为“是”。这个功能只有在用户使用Internet Explorer 5.5或Mozilla 1.3b以上的版本才有效果。';
$messages['enable_comments'] = '开放所有文章的评论留言权限';
$messages['enable_comments_help'] = '如果设置为“是”，那么您便可以让其他用户针对您的文章发表评论留言。这个设置会套用到您的全部文章上。';
$messages['show_future_posts'] = '在日历显示未来文章。';
$messages['show_future_posts_help'] = '如果设置为“是”，那么发表日期设置在未来的文章将会出现在日历上。';
$messages['articles_order'] = 'Articles order';
$messages['articles_order_help'] = 'Order in which articles should be displayed.';
$messages['comments_order'] = '评论留言排序方式';
$messages['comments_order_help'] = '如果您设置成“旧的在前”，那么留言就会从旧到新排序，如果设置成“新的在前”，则反之，留言从新到旧排序出现。';
$messages['oldest_first'] = '旧的在前';
$messages['newest_first'] = '新的在前';
$messages['categories_order'] = '分类排列顺序';
$messages['categories_order_help'] = '首页分类排列方式。';
$messages['most_recent_updated_first'] = '最近更新在前';
$messages['alphabetical_order'] = '依英文字母顺序排列';
$messages['reverse_alphabetical_order'] = '依英文字母顺序反向排列';
$messages['most_articles_first'] = '最多文章在前';
$messages['link_categories_order'] = '网站链接分类排列顺序';
$messages['link_categories_order_help'] = '首页网站链接分类排列方式。';
$messages['most_links_first'] = '最多链接在前';
$messages['most_links_last'] = '最多链接在后';
$messages['time_offset'] = '博客伺服器与您所在地的时间差';
$messages['time_offset_help'] = '您可以用这个设置，调整您所发表的文章的时间。这个功能在伺服器主机与您分别在不同时区时相当有用。如果您将时间差设置为“+3 小时”，那么系统就会将文章的发表时间调整成您所设置的时间。';
$messages['close'] = '关闭';
$messages['select'] = '选择';
$messages['error_updating_settings'] = '更新博客设置时发生错误，请检查输入数据后在重试一次。';
$messages['error_invalid_number'] = '数目格式不正确。';
$messages['error_incorrect_time_offset'] = '博客伺服器与您所在地的时间差不正确';
$messages['blog_settings_updated_ok'] = '博客设置更新已顺利完成。';
$messages['hours'] = '小时';

// user settings
$messages['username_help'] = '公开的用户名称，无法更改。';
$messages['full_name'] = '全名';
$messages['full_name_help'] = '完整的用户名称';
$messages['password_help'] = '如果你想更改密码请输入新密码及确认密码；如果您不想修改密码，留白便可。';
$messages['confirm_password'] = '确认密码';
$messages['email'] = '电子邮件';
$messages['email_help'] = '如果您想要使用电子邮件通知信功能，请填写正确的信箱。';
$messages['bio'] = '自我介绍';
$messages['bio_help'] = '您可以在此填写一些您的自我介绍，或是不填也可以。';
$messages['picture'] = '个人图像';
$messages['user_picture_help'] = '请从上传到博客中的图片选取一张做为你的个人大头贴。';
$messages['error_invalid_password'] = '密码太短或密码错误。';
$messages['error_passwords_dont_match'] = '很抱歉，您输入的两次密码不相符。';
$messages['error_updating_user_settings'] = '更新个人数据时发生错误。请检查输入的数据后在重试一次。';
$messages['user_settings_updated_ok'] = '用户设置已顺利更新。';
$messages['resource'] = '文件';

// plugin centre
$messages['identifier'] = '代号';
$messages['error_plugins_disabled'] = '很抱歉，外挂程式目前停用中。';

// blog users
$messages['revoke_permissions'] = '取消使用权限。';
$messages['error_no_users_selected'] = '你没有选取任何用户。';
$messages['user_removed_from_blog_ok'] = '用户“%s”已经顺利从本站作者行列中删除。';
$messages['users_removed_from_blog_ok'] = '用户“%s”已经顺利从本站作者行列中删除。';
$messages['error_removing_user_from_blog'] = '在将用户“%s”从本博客站台作者行列中移除时发生错误。';
$messages['error_removing_user_from_blog2'] = '在将用户从本博客站台作者行列中移除时发生错误。(id:%s)';

// new blog user
$messages['new_blog_username_help'] = '您可以用以下表单，将其他用户加入您的博客作者行列中。新增加的用户只能存取管理中心及文件中心。';
$messages['send_notification'] = '发送通知';
$messages['send_user_notification_help'] = '用电子邮件通知这名用户。';
$messages['notification_text'] = '通知内容';
$messages['notification_text_help'] = '请输入您要通知这位用户的信件内容';
$messages['error_adding_user'] = '在加入用户时发生问题，请检查输入的数据在重试一次。';
$messages['error_empty_text'] = '通知内容不可以是空白。';
$messages['error_adding_user'] = '在加入用户时发生问题，请检查输入的数据在重试一次。';
$messages['error_invalid_user'] = '用户“%s”帐号不正确或该用户不存在。';
$messages['user_added_to_blog_ok'] = '用户“%s”已经顺利加入作者行列。';

// blog templates
$messages['error_no_templates_selected'] = '您没有选择任何模版。';
$messages['error_template_is_current'] = '“%s”模版无法删除，该模版正在使用中。';
$messages['error_removing_template'] = '删除模版 “%s”时发生错误。';
$messages['template_removed_ok'] = ' 模版 “%s”已顺利删除。';
$messages['templates_removed_ok'] = '模版 “%s”已顺利删除。';

// new blog template
$messages['template_installed_ok'] = '新的模版设置“ %s”已经顺利安装完成。';
$messages['error_installing_template'] = '在安装模版设置“ %s”时发生错误。';
$messages['error_missing_base_files'] = '在这个模版设置中有些基本文件不见了。';
$messages['error_add_template_disabled'] = '本站不允许用户新增模版文件。';
$messages['error_must_upload_file'] = '您必须上传文件。';
$messages['error_uploads_disabled'] = '本站已关闭文件上传功能。';
$messages['error_no_new_templates_found'] = '找不到新的模版设置。';
$messages['error_template_not_inside_folder'] = '模版文件必须放在与模版同名的目录当中。';
$messages['error_missing_base_files'] = '在这个模版设置中有些基本文件不见了。';
$messages['error_unpacking'] = '在解压缩时发生错误。';
$messages['error_forbidden_extensions'] = '在这个模版设置中有些文件禁止存取。';
$messages['error_creating_working_folder'] = '在检查模版设置时发生错误。';
$messages['error_checking_template'] = '模版设置发生错误 (code = %s)';
$messages['template_package'] = '模版安装包';
$messages['blog_template_package_help']  = '您可以用这个表单，上传一个新的模版安装包，该模版将只有你的博客能够使用。如果您没有办法用浏览器上传，请手动上传该模版并将它放置于你的博客模板文件夹<b>%s</b>下,然后按下 "<b>扫描模版</b>" 按纽。 LifeType 会扫描该文件夹并自动新增所找到的新模版。';
$messages['scan_templates'] = '扫描模版';

// site users
$messages['user_status_active'] = '启用';
$messages['user_status_disabled'] = '停用';
$messages['user_status_all'] = '所有状态';
$messages['user_status_unconfirmed'] = '尚未确认';
$messages['error_invalid_user2'] = '用户代号“%s”不存在。';
$messages['error_deleting_user'] = '在停用用户帐号“%s”时发生错误。';
$messages['user_deleted_ok'] = '用户帐号“%s”已顺利停用。';
$messages['users_deleted_ok'] = '用户帐号“%s”已顺利停用。';

// create user
$messages['user_added_ok'] = '新用户帐号“%s”已顺利新增。';
$messages['user_status_help'] = '用户帐号目前状态';
$messages['user_blog_help'] = '用户默认的博客';
$messages['none'] = '无';

// edit user
$messages['error_invalid_user'] = '用户ID不正确或用户不存在。';
$messages['error_updating_user'] = '更新用户设置时发生错误。请检查输入数据后再重试一次。';
$messages['blogs'] = '博客';
$messages['user_blogs_help'] = '用户拥有或可以存取的博客。';
$messages['site_admin'] = '全站系统管理';
$messages['site_admin_help'] = '如果用户拥有全站系统管理权限，他就可以看见[站台设置]区域，可以进行全站的管理工作。';
$messages['user_updated_ok'] = '用户帐号“%s”已顺利更新。';

// site blogs
$messages['blog_status_all'] = '所有状态';
$messages['blog_status_active'] = '启用';
$messages['blog_status_disabled'] = '停用';
$messages['blog_status_unconfirmed'] = '尚未确认';
$messages['owner'] = '管理员';
$messages['quota'] = '文件限度';
$messages['bytes'] = 'bytes';
$messages['error_no_blogs_selected'] = '您必须要选择您所想要删除的博客站台。';
$messages['error_blog_is_default_blog'] = '“%s”是系统默认博客站台，无法删除。';
$messages['blog_deleted_ok'] = '“%s”博客站台已顺利删除。';
$messages['blogs_deleted_ok'] = '“%s”博客站台已顺利删除。';
$messages['error_deleting_blog'] = '在删除“%s”这个博客站台时发生错误。';
$messages['error_deleting_blog2'] = '在删除博客站台时发生错误。(id:%s)';

// create blog
$messages['error_adding_blog'] = '在新增博客时发生错误。请检查输入的数据在重试一次。';
$messages['blog_added_ok'] = '新的博客站台“%s”已成功加入数据库中。';

// edit blog
$messages['blog_status_help'] = '博客状态';
$messages['blog_owner_help'] = '博客站台管理者，将拥有完整的权限来修改博客设置。';
$messages['users'] = '用户';
$messages['blog_quota_help'] = '文件容量限度(单位：bytes)。设为0或空白将使用系统的全域文件限度做为默认值。';
$messages['edit_blog_settings_updated_ok'] = '博客 “%s”已顺利更新。';
$messages['error_updating_blog_settings'] = '更新博客站台 “%s”时发生错误。';
$messages['error_incorrect_blog_owner'] = '要设置为博客站台管理员的用户帐号不存在。';
$messages['error_fetching_blog'] = '读取博客数据时发生错误。';
$messages['error_updating_blog_settings2'] = '更新博客时发生错误。请检查输入数据在重试一次。';
$messages['add_or_remove'] = '新增或移除用户';

// site locales
$messages['locale'] = '语系';
$messages['locale_encoding'] = '编码方式';
$messages['locale_deleted_ok'] = '“%s”语系已顺利删除。';
$messages['error_no_locales_selected'] = '您没有选择要删除的语系。';
$messages['error_deleting_only_locale'] = '您不可以删除这个语系文件，因为这是系统中目前唯一的语系文件。';
$messages['locales_deleted_ok']= '“%s”语系已顺利删除。';
$messages['error_deleting_locale'] = '在删除“%s”语系时发生错误。';
$messages['error_locale_is_default'] = '您不可以删除“%s”语系，因为这是系统目前的默认语系。';

// add locale
$messages['error_invalid_locale_file'] = '这个文件并不是正确的语系文件。';
$messages['error_no_new_locales_found'] = '找不到新的语系文件。';
$messages['locale_added_ok'] = '语系“%s”已经顺利新增';
$messages['error_saving_locale'] = '在将新的语系文件储存至语系文件目录时发生错误。请检查文件目录的写入权限是否正确。';
$messages['scan_locales'] = '扫描语系档';
$messages['add_locale_help'] = '您可以用这个表单，上传一个新的语系档。如果您没有办法用浏览器上传，请手动上传该文件并将它放置于 <b>./locales/</b>下,然后按下 "<b>扫描语系档</b>" 按纽。 LifeType 会扫描该文件夹并自动新增所找到的语系档。 ';

// site templates
$messages['error_template_is_default'] = '您不可以删除“%s”模版，因为这是新博客目前的默认模版。';

// add template
$messages['global_template_package_help'] = '您可以用这个表单，上传一个新的模版安装包，该模版将提供给网站上所有博客使用。如果您没有办法用浏览器上传，请手动上传该模版并将它放置于你的博客模板文件夹<b>%s</b>下,然后按下 "<b>扫描模版</b>" 按纽。 LifeType 会扫描该文件夹并自动新增所找到的新模版。';

// global settings
$messages['site_config_saved_ok'] = '站台设置已顺利储存。';
$messages['error_saving_site_config'] = '在储存站台设置时发生问题。';
/// general settings
$messages['help_comments_enabled'] = '启用或停用全站的评论留言功能。';
$messages['help_beautify_comments_text'] = '在用户发表评论留言时，使用他所输入的文字格式。';
$messages['help_temp_folder'] = 'LifeType系统用来储存暂存文件用的目录。';
$messages['help_base_url'] = '这个博客安装的网址，这个项目务必要正确，请小心输入。';
$messages['help_subdomains_enabled'] = '启用或停用次网域设置。';
$messages['help_include_blog_id_in_url'] = '当[次网域]功能启用及[一般网址]功能启用时才有意义。强迫产生的网址不要包含 blogId 这个参数。请不要变更设置值，除非你知道你在做什么。';
$messages['help_script_name'] = '如果你将index.php更改为其它名称的话，请在下方输入更改后的文件名称。';
$messages['help_show_posts_max'] = '在首页显示文章数的默认值。';
$messages['help_recent_posts_max'] = '在首页“近期文章”列表中显示文章数的默认值。';
$messages['help_save_drafts_via_xmlhttprequest_enabled'] = '当 XmlHttpRequest 功能被启用时，将可以使用 Javascript 来储存文章草稿。';
$messages['help_locale_folder'] = '语系文件所在目录。';
$messages['help_default_locale'] = '在建立新博客站台时默认使用的语系。';
$messages['help_default_blog_id'] = '默认博客ID';
$messages['help_default_time_offset'] = '默认的网站伺服器时间差。';
$messages['help_html_allowed_tags_in_comments'] = '在发表评论评论时可以使用的HTML语法标签。';
$messages['help_referer_tracker_enabled'] = '是否使用文章逆向链接功能。(停用此功能可以提高系统效能。)';
$messages['help_show_more_enabled'] = '启用或停用“查看全文”链接功能。';
$messages['help_update_article_reads'] = '是否使用内建的点阅率统计工具计算每篇文章的点阅次数。(停用此功能可以提高系统效能。)';
$messages['help_update_cached_article_reads'] = '在快取功能开启的情形下，是否使用内建的点阅率统计工具计算每篇文章的点阅次数。';
$messages['help_xmlrpc_ping_enabled'] = '在系统中有人发表新文章时，是否送出 XMLRPC 通告。';
$messages['help_send_xmlrpc_pings_enabled_by_default'] = '默认启用该功能。当有新文章发表或更新时，是否送出 XMLRPC 通告。。';
$messages['help_xmlrpc_ping_hosts'] = 'XMLRPC 通告列表，如果您要向多处发送通告，请在文字框下面加入通告发送网址，每个网址一行。';
$messages['help_trackback_server_enabled'] = '是否接受从站外传来的引用通告（TrackBack）。';
$messages['help_htmlarea_enabled'] = '启用或停用即视即所得（WYSIWYG）文章编辑。';
$messages['help_plugin_manager_enabled'] = '启用或停用外挂程式。';
$messages['help_minimum_password_length'] = '密码最短需要多少字元。';
$messages['help_xhtml_converter_enabled'] = '如果启用此功能，LifeType会试着将所有的HTML转换为适当的XHTML。';
$messages['help_xhtml_converter_aggressive_mode_enabled'] = '如果启用此功能，LifeType会试着将HTML进一步转换为XHTML，但这样可能会导致更多的错误。';
$messages['help_session_save_path'] = '此设置将使用PHP的session_save_path()函数，来更改LifeType存放session的文件夹。请确定该文件夹可以透过网站伺服器进行写入动作。如果你要使用PHP默认的session存放路径，请将此设置空白。';
// summary settings
$messages['help_summary_page_show_max'] = '在汇整页面中要显示多少项目。此选项控制在汇整页面中列出的所有项目。(包括最新文章数目、最活跃博客等)';
$messages['help_summary_items_per_page'] = '在[博客列表]中每一页要显示多少博客。';
$messages['help_forbidden_usernames'] = '列出所有不允许注册的用户名称。';
$messages['help_force_one_blog_per_email_account'] = '一个电子邮件是否只能注册一个博客';
$messages['help_summary_show_agreement'] = '在用户进行注册动作之前，是否显示并确认用户同意服务条款。';
$messages['help_need_email_confirm_registration'] = '是否启用电子邮件的确认链接来启用帐号。';
$messages['help_summary_disable_registration'] = '是否关闭用户注册新博客的功能。';
// templates
$messages['help_template_folder'] = '模版文件的所在目录路径。';
$messages['help_default_template'] = '在新建博客站台时，默认使用的模版。';
$messages['help_users_can_add_templates'] = '用户是否可以在模版设置当中，加入属于自己专属需求的文件。';
$messages['help_template_compile_check'] = '停用此功能时，Smarty只有在模版有更改时才会重新产生页面。停用此功能可以提高系统效能。';
$messages['help_template_cache_enabled'] = '启用模版快取功能。启用此功能，快取的版本将会持续被使用，而不需要对数据库进行数据存取的动作。';
$messages['help_template_cache_lifetime'] = '快取存活时间(单位：秒).设为-1快取将永不过期，或设为0来关闭快取功能。';
$messages['help_template_http_cache_enabled'] = '是否启用对HTTP链接要求的快取支援。启用此功能LifeType只会传送必要的内容，可以节省网路频宽。';
$messages['help_allow_php_code_in_templates'] = '允许在Smarty 模版中的{php}...{/php}区块置入原生PHP程式码(native PHP code)';
// urls
$messages['help_request_format_mode'] = '如果您设置为“一般网址”，那么系统所呈现的网址，就会使用将参数以get方式传入的一般方式。如果您选用“让搜寻引擎易于搜寻的简洁网址”，那么就会让网址变得简洁，搜寻引擎也容易取得您网站上的内容，不过您的Apache伺服器必须要能够接受.htaccess文件中的覆写设置。如果使用自订网址，请调整下方的设置。';
$messages['plain'] = '一般网址';
$messages['search_engine_friendly'] = '让搜寻引擎易于搜寻的简洁网址';
$messages['custom_url_format'] = '自订网址';
$messages['help_permalink_format'] = '当使用自订网址时，静态链接网址格式。';
$messages['help_category_link_format'] = '当使用自订网址时，网站链接分类网址格式。';
$messages['help_blog_link_format'] = '当使用自订网址时，博客链接网址格式。';
$messages['help_archive_link_format'] = '当使用自订网址时，文章汇整链接网址格式。';
$messages['help_user_posts_link_format'] = '当使用自订网址时，特定用户发表的文章链接网址格式。';
$messages['help_post_trackbacks_link_format'] = '当使用自订网址时，引用链接网址格式。';
$messages['help_template_link_format'] = '当使用自订网址时，自订静态模版链接网址格式。';
$messages['help_album_link_format'] = '当使用自订网址时，文件夹链接网址格式。';
$messages['help_resource_link_format'] = '当使用自订网址时，文件链接网址格式。';
$messages['help_resource_preview_link_format'] = '当使用自订网址时，文件预览链接网址格式。';
$messages['help_resource_medium_size_preview_link_format'] = '当使用自订网址时，中型文件预览链接网址格式。';
$messages['help_resource_download_link_format'] = '当使用自订网址时，文件下载链接网址格式。';
// email
$messages['help_check_email_address_validity'] = '在用户注册申请新的博客站台时，是否要认证他所填写的电子邮件信箱是否正确。';
$messages['help_email_service_enabled'] = '使用或停用用来寄送通知信函的电子邮件服务。';
$messages['help_post_notification_source_address'] = '系统通知信函的寄件人电子邮件信箱。';
$messages['help_email_service_type'] = '用来寄送电子邮件的方式，请在各种方法选择其中之一。';
$messages['help_smtp_host'] = '如果您选用SMTP寄送电子邮件，请输入您要用来发送邮件的主机。';
$messages['help_smtp_port'] = '前项设置的SMTP主机端口（port）';
$messages['help_smtp_use_authentication'] = 'SMTP主机是否需要授权认证。如果需要的话，请继续填写下面两项设置。';
$messages['help_smtp_username'] = '如果SMTP主机需要授权认证，请填写用户帐号。';
$messages['help_smtp_password'] = '如果SMTP主机需要授权认证，请填写用户密码。';
// helpers
$messages['help_path_to_tar'] = '“tar”指令所在目录。(用来解压缩使用 .tar.gz 或 .tar.gz2格式压缩的模版包)';
$messages['help_path_to_gzip'] = '“gzip”指令所在目录。(用来解压缩使用 .tar.gz 格式压缩的模版包)';
$messages['help_path_to_bz2'] = '“bzip2”指令所在目录。(用来解压缩使用 .tar.gz2格式压缩的模版包)';
$messages['help_path_to_unzip'] = '“unzip”指令所在目录。(用来解压缩使用 .zip格式压缩的模版包)';
$messages['help_unzip_use_native_version'] = '使用PHP内建的版本来解压缩 .zip 的文件';
// uploads
$messages['help_uploads_enabled'] = '启用或停用上传文件功能。这个功能会影响到用户能否上传新的模版安装包，以及在模版中添加新的文件。';
$messages['help_maximum_file_upload_size'] = '用户上传文件大小的上限。';
$messages['help_upload_forbidden_files'] = '禁止用户上传的文件类型。如果有多个不同的文件类型，请在不同的类型间用空白区隔。也可使用\'*\' and \'?\'的方式。';
// interfaces
$messages['help_xmlrpc_api_enabled'] = '启用或停用XMLRPC介面。XMLRPC介面的用途是可以让您使用桌面博客写作工具出版博客文章。';
$messages['help_rdf_enabled'] = '启用或停用产生RSS新闻交换文件功能。';
$messages['help_default_rss_profile'] = '默认的RSS/RDF新闻交换格式';
// security
$messages['help_security_pipeline_enabled'] = '启用系统安全功能。如果您关闭了这个选项，那么所有的系统安全功能都会停用，如果您想要关闭一些系统安全功能，建议您将这个设置设为开启，然后在以下的选项中，逐一停用我们不需要的系统安全功能项目。';
$messages['help_maximum_comment_size'] = '评论留言的内文字元数上限。';
// bayesian filter
$messages['help_bayesian_filter_enabled'] = '启用或停用贝氏过滤机制。';
$messages['help_bayesian_filter_spam_probability_treshold'] = '被认定为是垃圾评论留言的数值下限。设置范围在0.01到0.99之间。';
$messages['help_bayesian_filter_nonspam_probability_treshold'] = '设置评论留言是正常留言的数值上限。任何符合在前一设置与本设置之间数值的留言评论，都会被认定是正常而非垃圾留言。';
$messages['help_bayesian_filter_min_length_token'] = '在多少字元数以上才会启动贝氏过滤机制。';
$messages['help_bayesian_filter_max_length_token'] = '贝氏过滤机制可以处理的最多字元数上限。';
$messages['help_bayesian_filter_number_significant_tokens'] = '在信息中必须要有多少显著有意义的文字。';
$messages['help_bayesian_filter_spam_comments_action'] = '处理垃圾留言的方法。您可以直接清理这些垃圾留言（不会存进数据库中），或是保存这些垃圾留言，但是加上垃圾留言标示标示。建议当您的过滤机制在还没有妥善建立阻挡规则时，先用后者。';
$messages['keep_spam_comments'] = '保存垃圾评论';
$messages['throw_away_spam_comments'] = '清理垃圾评论';
// resources
$messages['help_resources_enabled'] = '启用或关闭文件中心功能。';
$messages['help_resources_folder'] = '用来存放文件中心的目录。这个目录不一定要在网页目录下。如果您不希望别人直接浏览您的文件目录，您可以把这个目录设置到其他地方。';
$messages['help_thumbnail_method'] = '您用来产生缩图的后端系统。如果使用PHP，GD的支援是必须的。';
$messages['help_path_to_convert'] = '用来产生缩图的系统工具路径。如果您要使用ImageMagick，那么您必须接着填写ImageMagick的工具程式路径。';
$messages['help_thumbnail_format'] = '在产生预览缩图时所使用的默认格式。如果您选择“与原始影像相同”，那么预览缩图就会储存成与原始影像相同的格式。';
$messages['help_thumbnail_height'] = '缩图默认高度。';
$messages['help_thumbnail_width'] = '缩图默认宽度。';
$messages['help_medium_size_thumbnail_height'] = '中型缩图默认高度';
$messages['help_medium_size_thumbnail_width'] = '中型缩图默认宽度';
$messages['help_thumbnails_keep_aspect_ratio'] = '缩图是否保持原始比例。';
$messages['help_thumbnail_generator_force_use_gd1'] = '是否强迫LifeType使用GD1函数来产生缩图';
$messages['help_thumbnail_generator_user_smoothing_algorithm'] = '是否使用演算法来使缩图画面更平顺。只有当缩图产生工具是GD时才适用。';
$messages['help_resources_quota'] = '全域文件容量限额';
$messages['help_resource_server_http_cache_enabled'] = '当 HTTP 请求档头为"If-Modified-Since"启用快取支援。启用此功能来节省网路频宽。';
$messages['help_resource_server_http_cache_lifetime'] = '客户端可以使用快取文件的时间(单位：千分之一秒)';
$messages['same_as_image'] = '与原始影像相同';
// search
$messages['help_search_engine_enabled'] = '启用或停用搜寻引擎';
$messages['help_search_in_custom_fields'] = '搜寻包含自订栏位';
$messages['help_search_in_comments'] = '搜寻包含评论';

// cleanup
$messages['purge'] = '清除';
$messages['cleanup_spam'] = '清除垃圾评论';
$messages['cleanup_spam_help'] = '这会清除所有被用户标示为垃圾的评论。被清除的垃圾评论将无法回复。';
$messages['spam_comments_purged_ok'] = '垃圾评论已顺利清除。';
$messages['cleanup_posts'] = '清除文章';
$messages['cleanup_posts_help'] = '这会清除所有被用户标示为删除的文章。 被清除的文章将无法回复。';
$messages['posts_purged_ok'] = '文章已顺利清除。';
$messages['purging_error'] = '清理时发生错误。';

/// summary ///
// front page
$messages['summary'] = '汇整';
$messages['register'] = '注册';
$messages['summary_welcome'] = '欢迎!';
$messages['summary_most_active_blogs'] = '最活跃博客';
$messages['summary_most_commented_articles'] = '最多评论文章';
$messages['summary_most_read_articles'] = '最多人阅读文章';
$messages['password_forgotten'] = '忘记密码?';
$messages['summary_newest_blogs'] = '最新建立的博客';
$messages['summary_latest_posts'] = '最新发表的文章';
$messages['summary_search_blogs'] = '搜寻博客';

// blog list
$messages['updated'] = '更新';
$messages['total_reads'] = '浏览总次数';

// blog profile
$messages['blog'] = '博客';
$messages['latest_posts'] = '最新发表的文章';

// registration
$messages['register_step0_title'] = '服务条款';
$messages['agreement'] = '同意条款';
$messages['decline'] = '不接受';
$messages['accept'] = '接受';
$messages['read_service_agreement'] = '请详细阅读服务条款，如果你同意以上条款请按下接受键。';
$messages['register_step1_title'] = '建立用户 [1/4]';
$messages['register_step1_help'] = '首先你必须先建立一个用户帐号来取得一个博客，这个用户拥有该博客，同时可以进行所有博客设置功能。';
$messages['register_next'] = '下一步';
$messages['register_back'] = '上一步';
$messages['register_step2_title'] = '建立博客 [2/4]';
$messages['register_blog_name_help'] = '帮你的博客取个名称';
$messages['register_step3_title'] = '选择一个模版[3/4]';
$messages['step1'] = '步骤 1';
$messages['step2'] = '步骤 2';
$messages['step3'] = '步骤 3';
$messages['register_step3_help'] = '请选择一个模版做为博客的默认模版。只要你不喜欢，你可以随时把它换掉。';
$messages['error_must_choose_template'] = '请选择一个模版';
$messages['select_template'] = '选取模版';
$messages['register_step5_title'] = '恭喜你! [4/4]';
$messages['finish'] = '注册完成';
$messages['register_need_confirmation'] = '一封包含注册[确认信息链接]的电子邮件已经寄到你的电子信箱中。请尽快点选该链接来开始你的blogging生活！';
$messages['register_step5_help'] = '恭喜你，新的用户帐号及博客已经顺利建立！';
$messages['register_blog_link'] = '如果你要看一看你的新博客，你现在可以到<a href="%2$s">%1$s</a>这里看一看。';
$messages['register_blog_admin_link'] = '如果你想要立刻开始发表文章，请点选链接到 <a href="admin.php">管理介面</a>';
$messages['register_error'] = '过程中有错误发生！';
$messages['error_registration_disabled'] = '很抱歉，网站管理者停用注册新博客的功能。';
// registration article topic and text
$messages['register_default_article_topic'] = '恭喜！';
$messages['register_default_article_text'] = '如果你可以看到这篇文章，表示注册过程已经顺利完成。现在你可以开始blogging了！';
$messages['register_default_category'] = '一般';
// confirmation email
$messages['register_confirmation_email_text'] = '请点选下面的链接来启用你的博客：:

%s

祝你有个美好的一天！';
$messages['error_invalid_activation_code'] = '很抱歉，确认码不正确！';
$messages['blog_activated_ok'] = '恭喜，你的用户帐号和博客已经顺利启用了！';
// forgot your password?
$messages['reset_password'] = '重设密码';
$messages['reset_password_username_help'] = '你要重设那个用户的密码？';
$messages['reset_password_email_help'] = '用户用来注册的电子邮件位址';
$messages['reset_password_help'] = '使用下方的表单来重设密码。请输入用户名称及注册时使用的电子邮件位址。';
$messages['error_resetting_password'] = '重设密码时发生错误。请检查输入的数据再重试一次。';
$messages['reset_password_error_incorrect_email_address'] = '电子邮件位址错误或着这不是你注册时使用的电子邮件。';
$messages['password_reset_message_sent_ok'] = '一封有着重设密码链接的电子邮件已经送到你的电子邮件信箱，请点选该链接来重设密码。';
$messages['error_incorrect_request'] = '网址中的参数不正确。';
$messages['change_password'] = '重设密码';
$messages['change_password_help'] = '请输入新密码及确认密码';
$messages['new_password'] = '新密码';
$messages['new_password_help'] = '在这里输入新密码';
$messages['password_updated_ok'] = '你的密码已经顺利更新';

// Suggested by BCSE, some useful messages that not available in official locale
$messages['upgrade_information'] = '您所使用的浏览器未符合网页设计标准，因此本网页将以纯文字模式显示。如欲以最佳的排版方式浏览本站，请考虑<a href="http://www.webstandards.org/upgrade/" title="The Web Standards Project\'s Browser Upgrade initiative">升级</a>您的浏览器。';
$messages['jump_to_navigation'] = '移动到导览列。';
$messages['comment_email_never_display'] = '系统会自动为你设置分行，且不会显示你留下的邮件地址。';
$messages['comment_html_allowed'] = '可使用之 <acronym title="Hypertext Markup Language">HTML</acronym> 标签如下：&lt;<acronym title="用途：超链接">a</acronym> href=&quot;&quot; title=&quot;&quot; rel=&quot;&quot;&gt; &lt;<acronym title="用途：头字语标注">acronym</acronym> title=&quot;&quot;&gt; &lt;<acronym title="用途：引用文字">blockquote</acronym> cite=&quot;&quot;&gt; &lt;<acronym title="用途：删除线">del</acronym>&gt; &lt;<acronym title="用途：斜体">em</acronym>&gt; &lt;<acronym title="用途：底线">ins</acronym>&gt; &lt;<acronym title="用途：粗体">strong</acronym>&gt;';
$messages['trackback_uri'] = '这篇文章的引用链接网址：';

$messages['xmlrpc_ping_ok'] = 'XMLRPC Ping sent successfully: ';
$messages['error_sending_xmlrpc_ping'] = 'There was an error sending the XMLRPC ping to: ';
$messages['error_sending_xmlrpc_ping_message'] = 'There was an error sending the XMLRPC ping: ';

//
// new strings for 1.1
//
$messages['error_incorrect_trackback_id'] = '引用的识别码不正确';
$messages['error_marking_trackback_as_spam'] = '标记垃圾引用时发生错误';
$messages['trackback_marked_as_spam_ok'] = '标记垃圾引用成功';
$messages['error_marking_trackback_as_nonspam'] = '取消标记垃圾引用时发生错误';
$messages['trackback_marked_as_nonspam_ok'] = '取消标记垃圾引用成功';
$messages['upload_here'] = '上传到这里';
$messages['cleanup_users'] = '删除用户';
$messages['cleanup_users_help'] = '这个操作会把所有被管理员标示为(已删除)的用户完全删除，同时也会把这些用户的所有博客也删除，包括所有包含在博客里的任何东西。如果这些用户有在其他博客写文章的权限，那他们在其他博客里所写的文章也会一起被删除。当用户被删除时，这些动作是不可能恢复的。';
$messages['users_purged_ok'] = '成功删除用户';
$messages['cleanup_blogs'] = '删除博客';
$messages['cleanup_blogs_help'] = '这个操作会把所有被管理员标示为(已删除)的博客完全删除，包括所有包含在博客里的任何东西。当博客被删除时，这些动作是不可能恢复的。';
$messages['blogs_purged_ok'] = '成功删除博客';
$messages['help_use_http_accept_language_detection'] = '大部分的浏览器像 Mozilla Firefox 、 Safari 或 Internet Explorer 至少会传送一个用户<i>应该</i>了解的语言码。如果启用这个功能，而且该语言是可用的， LifeType 会试着以这个请求的语言来服务用户。[默认值 = 否]';

$messages['error_invalid_blog_category'] = '不合法的博客分类';
$messages['error_adding_blog_category'] = '新增博客分类时发生错误';
$messages['newBlogCategory'] = '新增博客分类';
$messages['editBlogCategories'] = '编辑博客分类';
$messages['blog_category_added_ok'] = '成功新增博客分类';
$messages['error_blog_category_has_blogs'] = '已经有一些博客指定到博客分类 "%s" 。请先编辑这些博客之后再试一次';
$messages['error_deleting_blog_category'] = '删除博客分类 "%s" 时发生错误';
$messages['blog_category_deleted_ok'] = '成功删除博客分类 "%s"';
$messages['blog_categories_deleted_ok'] = '成功删除博客分类 "%s"';
$messages['error_deleting_blog_category2'] = '删除 id 为 %s 的博客分类时发生错误';
$messages['blog_category'] = '博客分类';
$messages['blog_category_help'] = '替博客指定一个全域博客分类';

$messages['help_use_captcha_auth'] = '在注册程序使用 CAPTCHA 机制，以防止自动注册机器人程式';
$messages['help_skip_dashboard'] = '让用户跳过管理面板，直接进入他目前所拥有的第一个博客';

$messages['manageGlobalArticleCategory'] = '全域文章分类';
$messages['newGlobalArticleCategory'] = '新增全域文章分类';
$messages['editGlobalArticleCategories'] = '编辑全域文章分类';
$messages['global_category_name_help'] = '新的全域文章分类的名称';
$messages['global_category_description_help'] = '新的全域文章分类的详细描述';
$messages['error_incorrect_global_category_id'] = '不合法的全域文章分类';
$messages['global_category_deleted_ok'] = '成功删除全域文章分类 "%s"';
$messages['global_category_added_ok'] = '成功新增全域文章分类 "%s"';
$messages['error_deleting_global_category2'] = '删除 id 为 %S 的全域文章分类时发生错误';

$messages['help_page_suffix_format'] = '支援分页时，加在网址尾端的字尾';

$messages['help_final_size_thumbnail_width'] = '上传图档的最后宽度。使用空白或 0 则使用图档的原始大小';
$messages['help_final_size_thumbnail_height'] = '上传图档的最后高度。使用空白或 0 则使用图档的原始大小';
$messages['error_comment_too_big'] = '评论内容太长';
$messages['error_you_have_been_blocked'] = '阻挡：这个请求并没有完成';
$messages['created'] = '已建立';
$messages['view'] = '阅读';
$messages['editUser'] = '编辑用户';
$messages['help_urlize_word_separator'] = '建立 LifeType 相关的链接时，用于链接单字的字元。如果启用次网域网址时，这也会使用于产生网址里的网站名称。[默认值 = -]';
$messages['help_summary_template_cache_lifetime'] = '汇整页面快取的有效时间。若设为 \'0\' ，只要有数据更新就会更新汇整页面的快取。如果设为其他值，则会等到这段时间过了之后再更新汇整页面的快取。[默认值 = 0]';
$messages['register_default_album_name'] = '一般';
$messages['register_default_album_description'] = '使用这个文件夹上传新图档';
$messages['show_in_summary'] = '在汇整页面中显示';
$messages['show_in_summary_help'] = '在汇整页面中显示这个博客';

$messages['saving_message'] = '储存中 ...';
$messages['show_option_panel'] = '显示文章选项';
$messages['hide_option_panel'] = '隐藏文章选项';

$messages['quick_launches'] = '快捷列';

$messages['confirmation_message_resent_ok'] = '注册确认信件已成功地重新寄出';

$messages['goto_blog_page'] = '打开 %s 首页';

$messages['help_num_blogs_per_user'] = '拥有者可以从管理介面建立博客的数目';

$messages['massive_change_option'] = '大量修改选项';
$messages['show_massive_change_option'] = '显示大量修改选项';
$messages['hide_massive_change_option'] = '隐藏大量修改选项';
$messages['change_status'] = '修改状态';
$messages['change_category'] = '修改分类';
$messages['error_updating_comment_no_comment'] = '更新评论时发生错误。评论 #%s 不存在。';
$messages['error_updating_comment_wrong_blog'] = '更新评论时发生错误。评论 (%s)不在这篇文章上。';
$messages['error_updating_comment'] = '更新评论 (%s) 发生错误。';
$messages['error_updating_comment_already_updated'] = '(%s) 没有进行任何更新。';
$messages['comment_updated_ok'] = '评论已顺利更新。';
$messages['comments_updated_ok'] = '%s 评论已顺利更新。';

$messages['error_post_status'] = '请选择文章状态。';
$messages['error_comment_status'] = '请选择评论状态。';
$messages['admin_mode'] = '管理员模式';
$messages['administrate_user_blog'] = '管理这个博客';
$messages['trackbacks_updated_ok'] = '%s 个引用已成功的更新';
$messages['trackback_updated_ok'] = '引用已成功的更新';
$messages['error_trackback_status'] = '请选择一个合法的状态';
$messages['select'] = '选择';
$messages['remove_selected'] = '取消选取';

$messages['notification_subject'] = 'LifeType 通知系统';
$messages['error_no_trackback_links_sent'] = '警告：没有送出任何引用';

$messages['help_http_cache_lifetime'] = '客户端快取的有效时间，以秒为单位。 (浏览器在这段期间不会再连线到网页主机，而直接使用本地端的快取)。这个做法将加快浏览网页的速度，但是将延后文章和评论的出现时间。[默认值 = 1800]';

$messages['trackbacks_no_trackback'] = '送出引用到下面的网址失败：';

$messages['error_comment_spam_throw_away'] = '你不能发表这个信息。反垃圾过滤系统已经将这个信息阻挡下来。';
$messages['error_comment_spam_keep'] = '反垃圾过滤系统已经将你的评论放到伫列里等待博客拥有者的审核。';

$messages['blog_categories'] = '博客分类';
$messages['global_article_categories'] = '全站文章分类'; 

$messages['help_force_posturl_unique'] = '强迫博客里所有文章的网址都是唯一的。这只有当你更改网址并且将日期部份从网址中移除时才需要。[默认值 = no]';

$messages['default_send_notification'] = '默认发送通知';

$messages['enable_pull_down_menu'] = '下拉式选单';
$messages['enable_pull_down_menu_help'] = '启用或关闭下拉式选单。';

$messages['change_album'] = '修改文件夹'; 

$messages['warning_autosave_message'] = '<img src="imgs/admin/icon_warning-16.png" alt="Error" class="InfoIcon"/><p class="ErrorText">你好像有之前尚未存档的文章。如果你还想继续编辑，你可以 <a href="#" onclick="restoreAutoSave();">取回未存档文章继续编辑</a> 或是 <a href="#" onclick="eraseAutoSave();">把他删除</a> 。</p>';

$messages['check_username'] = '检查用户名称';
$messages['check_username_ok'] = '恭喜！这个用户名称还没有任何人使用。';
$messages['error_username_exist'] = '抱歉！这个用户名称已经被别人用了，试试其他的吧！'; 

$messages['error_rule_email_dns_server_temp_fail'] = '发生暂时性的错误，请稍后再试！';
$messages['error_rule_email_dns_server_unreachable'] = '电子邮件主机无法连线';
$messages['error_rule_email_dns_not_permitted'] = '不被允许的电子邮件地址'; 

$messages['blog_users_help'] = '可以存取这个博客的用户。请从左边选取用户将他移到右边提供该用户存取博客的权限。'; 

$messages['summary_welcome_paragraph'] = '请将此处修改为你希望你的用户看到的欢迎信息，或将这部份删除并重新安排整个页面。这个页面的模版在 templates/summary 里面，你可以自由地依你的喜好修改他。'; 

$messages['first_day_of_week'] = 1;
$messages['first_day_of_week_label'] = '每一周的开始';
$messages['first_day_of_week_help'] = '在首页月历中的显示方式。'; 

$messages['help_subdomains_base_url'] = '当次网域设置启用时，这个网址将用来替代系统网址。使用 {blogname}来取得博客名称及{username}取得博客用户名称以及{blogdomain}，用来产生链接到博客的网址。'; 

$messages['registration_default_subject'] = 'LifeType 注册确认';

$messages['error_invalid_subdomain'] = '不合法的子网域名称，或是名称不是唯一的';
$messages['register_blog_domain_help'] = '你的新博客要使用的名称和子网域';
$messages['domain'] = '网域(Domain)';
$messages['help_subdomains_available_domains'] = '允许的主网域名称清单。主网域名称请以一个空格分隔。用户会看到一个包含这些值的下拉式选单，并加入他所要使用的主网域。只有当你启用子网域并且在上方的 subdomain_base_url 使用了 (blogdomain) 。如果你允许任何的网域，则使用 \'?\'';
$messages['subdomains_any_domain'] = '<- 启用多重网域。输入完整的网域名称';
$messages['error_updating_blog_subdomain'] = '更新子网域时发生错误，请检查数据并再试一次。';
$messages['error_updating_blog_main_domain'] = '更新主网域设置时发生错误。这可是管理者的一些系统参数调整错误造成的。';

$messages['monthsshort'] = Array( '元', '二', '三', '四', '五', '六', '七', '八', '九', '十', '十一', '十二' );
$messages['weekdaysshort'] = Array( '日', '一', '二', '三', '四', '五', '六' );

$messages['search_type'] = '搜寻方式';
$messages['posts'] = '文章';
$messages['blogs'] = '博客';
$messages['resources'] = '文件';
$messages['upload_in_progress'] = '文件正在上传中，请稍后 ...';
$messages['error_incorrect_username'] = '用户名称不正确。可能这个用户名称已经有人使用了，或是它的长度超过 15 个字元。';

$messages['Miscellaneous'] = '其他设置';
$messages['Plugins'] = '外挂程式';

$messages['auth_img'] = '认证码';
$messages['auth_img_help'] = '请输入你在图片中所看到的文字。';

$messages['global_category'] = '全域文章分类';
$messages['global_article_category_help'] = '替文章指定一个全域文章分类。';

$messages['password_reset_subject'] = 'LifeType 重新设置密码';

//
// new strings for LifeType 1.2
//
$messages['auth'] = '验证';
$messages['authenticated'] = '已登入';
$messages['dropdown_list_field'] = '下拉式选项';
$messages['values'] = '数值';
$messages['field_values'] = '这些值会变成这个下拉式选单中的选项。其中第一个值会是下拉式选单中的默认值。';

$messages['permission_added_ok'] = '权限已经顺利新增。';
$messages['core_perm'] = '主要权限';
$messages['admin_only'] = '管理者限定';
$messages['permissionsList'] = '权限列表';
$messages['newPermission'] = '新增权限';
$messages['permission_name_help'] = '必须是系统中唯一的权限名称';
$messages['permission_description_help'] = '权限的简短描述';
$messages['core_perm_help'] = '如果这个权限是主要权限，他将无法被删除。';
$messages['admin_only_help'] = '这个权限只能指定给管理者。';
$messages['error_adding_new_permission'] = '新增权限时发生错误，请检查你的数据。';
$messages['error_incorrect_permission_id'] = '权限 ID 不正确。';
$messages['error_permission_cannot_be_deleted'] = '权限 "%s" 无法删除。因为他已经至少被一个用户使用或者是主要权限。';
$messages['error_deleting_permission'] = '删除权限 "%s" 发生错误。';
$messages['permission_deleted_ok'] = '权限 "%s" 已经顺利删除。';
$messages['permissions_deleted_ok'] = '%s 权限已经顺利删除。';
$messages['error_deleting_permission2'] = '删除权限 ID "%s" 时发生错误。';

$messages['help_hard_show_posts_max'] = '首页显示文章数量的最大值。如果用户的设置超过这个数值，它将会被忽略，并且直接使用这个数值作为限制。[ 默认 = 50 ]';
$messages['help_hard_recent_posts_max'] = '首页显示近期文章数量的最大值。如果用户的设置超过这个数值，它将会被忽略，并且直接使用这个数值作为限制。[ 默认 = 25 ]';

$messages['error_permission_required'] = '你没有进行这个动作的权限。';
$messages['user_permissions_updated_ok'] = '用户权限顺利更新。';

// blog permissions
$messages['add_album_desc'] = '新增文件夹';
$messages['add_blog_template_desc'] = '新增博客模版';
$messages['add_blog_user_desc'] = '新增博客作者';
$messages['add_category_desc'] = '新增文章分类';
$messages['add_custom_field_desc'] = '新增自订栏位';
$messages['add_link_desc'] = '新增链接网址';
$messages['add_link_category_desc'] = '新增网站链接分类';
$messages['add_post_desc'] = '新增文章';
$messages['add_resource_desc'] = '新增文件';
$messages['blog_access_desc'] = '访问这个博客';
$messages['update_album_desc'] = '更新与删除文件夹';
$messages['update_blog_desc'] = '更新与删除博客';
$messages['update_blog_template_desc'] = '更新与删除博客模版';
$messages['update_blog_user_desc'] = '更新与删除博客作者权限';
$messages['update_category_desc'] = '更新与删除文章分类';
$messages['update_comment_desc'] = '更新与删除评论';
$messages['update_custom_field_desc'] = '更新与删除自订栏位';
$messages['update_link_desc'] = '更新与删除链接网址';
$messages['update_link_category_desc'] = '更新与删除网站链接分类';
$messages['update_post_desc'] = '更新与删除文章';
$messages['update_resource_desc'] = '更新与删除文件';
$messages['update_trackback_desc'] = '更新与删除引用';
$messages['view_blog_templates_desc'] = '浏览博客模版列表';
$messages['view_blog_users_desc'] = '浏览博客作者列表';
$messages['view_categories_desc'] = '浏览文章分类列表';
$messages['view_comments_desc'] = '浏览评论列表';
$messages['view_custom_fields_desc'] = '浏览自订栏位列表';
$messages['view_links_desc'] = '浏览链接网址列表';
$messages['view_link_categories_desc'] = '浏览网站链接分类列表';
$messages['view_posts_desc'] = '浏览文章列表';
$messages['view_resources_desc'] = '浏览文件列表';
$messages['view_trackbacks_desc'] = '浏览引用列表';
$messages['login_perm_desc'] = '允许登入管理介面';
// admin permissions
$messages['add_blog_category_desc'] = '新增博客分类';
$messages['add_global_article_category_desc'] = '新增全域文章分类';
$messages['add_locale_desc'] = '新增语系';
$messages['add_permission_desc'] = '新增权限';
$messages['add_site_blog_desc'] = '新增博客';
$messages['add_template_desc'] = '新增全域模版';
$messages['add_user_desc'] = '新增用户';
$messages['edit_blog_admin_mode_desc'] = '修改其他博客 (管理者模式)';
$messages['purge_data_desc'] = '清除数据';
$messages['update_blog_category_desc'] = '更新与删除博客分类';
$messages['update_global_article_category_desc'] = '更新与删除全域文章分类';
$messages['update_global_settings_desc'] = '更新与删除全域设置';
$messages['update_locale_desc'] = '更新与删除语系';
$messages['update_permission_desc'] = '更新与删除权限';
$messages['update_plugin_settings_desc'] = '更新与删除外挂程式设置';
$messages['update_site_blog_desc'] = '更新与删除博客';
$messages['update_template_desc'] = '更新与删除全域模版';
$messages['update_user_desc'] = '更新与删除用户';
$messages['view_blog_categories'] = '浏览博客分类列表';
$messages['view_global_article_categories_desc'] = '浏览全域文章分类列表';
$messages['view_global_settings_desc'] = '浏览全域设置';
$messages['view_locales_desc'] = '浏览语系列表';
$messages['view_permissions_desc'] = '浏览权限列表';
$messages['view_plugins_desc'] = '浏览外挂程式列表';
$messages['view_site_blogs_desc'] = '浏览博客列表';
$messages['view_templates_desc'] = '浏览全域模版列表';
$messages['view_users_desc'] = '浏览用户列表';
$messages['update_blog_stats_desc'] = '更新与删除逆向链接';
$messages['manage_admin_plugins_desc'] = '管理全域外挂程式设置';

$messages['summary_welcome_msg'] = '欢迎， %s！';
$messages['summary_go_to_admin'] = '管理者介面';

$messages['error_can_only_update_own_articles'] = '你的权限只允许你修改自己的文章。';
$messages['update_all_user_articles_desc'] = '允许修改其他博客作者的文章。';
$messages['error_can_only_view_own_articles'] = '你的权限只允许你浏览自己的文章。';
$messages['view_all_user_articles_desc'] = '允许浏览其他博客作者的文章。';
$messages['error_fetching_permission'] = '读取权限数据时发生错误。';
$messages['editPermission'] = '修改权限';
$messages['error_updating_permission'] = '更新权限时发生错误。';
$messages['permission_updated_ok'] = '权限已顺利更新。';
$messages['error_adding_permission'] = '新增权限时发生错误。';
$messages['error_cannot_login'] = '抱歉，你不被允许登入！';
$messages['admin_user_permissions_help'] = '指定用户具有管理全站的权限。';

$messages['permissions'] = '权限列表';
$messages['blog_user_permissions_help'] = '指定用户具有管理博客的权限。';
$messages['pluginSettings'] = '外挂程式设置';
$messages['user_can_override'] = '用户可以覆盖外挂程式全域设置';
$messages['user_cannot_override'] = '用户不能覆盖外挂全域程式设置';
$messages['global_plugin_settings_saved_ok'] = '外挂程式全域设置已顺利更新。';
$messages['error_updating_global_plugin_settings'] = '更新外挂程式全域设置时发生错误。';
$messages['error_incorrect_value'] = '这个数值不正确。';
$messages['parameter'] = '参数';
$messages['value'] = '设置值';
$messages['override'] = '覆盖';
$messages['editCustomField'] = '编辑自订栏位';
$messages['view_blog_stats_desc'] = '浏览博客统计';
$messages['manage_plugins_desc'] = '管理博客外挂程式';

$messages['error_global_category_has_articles'] = '无法删除这个全域文章分类，因为该分类下还有文章。';
$messages['error_adding_global_article_category'] = '新增全域文章分类时发生错误。请检查输入的数据，再重试一次。';

$messages['temp_folder_reset_ok'] = '清理暂存目录已经顺利清理。';
$messages['cleanup_temp_help'] = '清理暂存目录中所有博客的网页快取与数据快取。';
$messages['cleanup_temp'] = '清理暂存目录。';

$messages['comment_only_auth_users'] = '评论用户验证';
$messages['comment_only_auth_users_help'] = '只有已经登入博客的用户才能够评论。';
$messages['show_comments_max'] = '最大每篇文章显示评论数目';
$messages['show_comments_max_help'] = '每篇文章显示评论数目的默认值 [ 默认 = 20 ]';
$messages['hard_show_comments_max_help'] = '每篇文章显示评论数目的默认值。如果用户的设置超过这个数值，它将会被忽略，并且直接使用这个数值作为限制。[ 默认 = 50 ]';

$messages['error_resource_not_whitelisted_extension'] = '文件类时不在系统允许的副档名列表中。';
$messages['help_upload_allowed_files'] = '允许用户上传的文件类型。如果有多个不同的文件类型，请在不同的类型间用空白区隔。也可使用\'*\' and \'?\'的方式。 如果 upload_forbidden_file 与这个选项同时设置。允许用户上传的文件类型 (upload_allowed_files) 将会优先于禁止用户上传的文件类型 [Default = None]';

$messages['help_template_load_order'] = '默认模版载入顺序。如果使用 \'优先载入默认模版\'，LifeType 会尝试优先搜寻 ./templates/default/ 目录下的模版，如果默认模版不存在，则载入用户自订模版。如果相同的模版同时存在这两个地方，则优先采用默认模版。如果使用 \'优先载入用户自订模版\'，则用户自订模版将被优先使用。如果用户自订模版不存在，将使用默认模版。如果相同的模版同时存在这两个地方，则优先采用用户自订模版。';
$messages['template_load_order_user_first'] = '优先载入默认模版';
$messages['template_load_order_default_first'] = '优先载入用户自订模版';

$messages['editBlogUser'] = '编辑博客作者';

$messages['help_summary_service_name'] = '你的网站或是服务的名称。这个名称会使用在你的汇整首页与 RSS 的输出中。[ 默认值 = 空白 ]';

$messages['register_step2_help'] = '请提供建立博客所需要的信息。';

$messages['create_date'] = '建立时间';

$messages['insert_media'] = '插入文件';
$messages['insert_more'] = '插入 "查看全文" 分隔';

$messages['purging_please_wait'] = '请耐心等候清理数据。本页面会持续更新直到所有数据清理完毕，请勿中断清理动作以免造成数据损坏。';

$messages['error_cannot_delete_last_blog_category'] = '您无法删除最后一个博客分类。';

$messages['help_logout_destination_url'] = '当用户登出时所要显示网页的 URL 。例如，你提供服务的首页。若是保持空白，则使用默认的 LifeType 登入页。[ 默认值 = 空白 ]';
$messages['help_default_global_article_category_id'] = '默认的全域文章分类 ID。[ 默认值 = 空白 ]';
$messages['help_blog_does_not_exist_url'] = '当博客不存在时所要显示的网页 URL。当博客不存在时，你可以透过这一个选项将 URL 转到某一个特定网址，而非系统默认的博客。[ 默认值 = 空白 ]';

$messages['error_invalid_blog_name'] = '博客名称不正确。';

/* strings for /default/ templates */


$messages['help_forbidden_blognames'] = '列出所有不允许使用的博客名称。如果有多个不同的博客名称，请在不同的名称间用空白区隔。 也可以使用正规表示是来表示。[ 默认值 = 空白 ]';

$messages['posts_updated_ok'] = '%s 篇文章已顺利更新。';
$messages['error_updating_post2'] = '更新文章 ID %s 时发生错误。';
$messages['resources_updated_ok'] = '%s 个文件已顺利更新。';
$messages['error_updating_resource2'] = '更新文件 ID %s 时发生错误。';
$messages['albums_updated_ok'] = '%s 个文件夹已顺利更新。';
$messages['error_updating_album2'] = '更新文件夹 ID %s 时发生错误。';
$messages['links_updated_ok'] = '%s 网站链接已顺利更新。';
$messages['error_updating_link2'] = '更新网站链接 ID %s 时发生错误。';

$messages['version'] = '版本';

$messages['error_resources_disabled'] = '抱歉！本网站的上传功能已经被管理者关闭。';
$messages['help_login_admin_panel'] = '点选博客名称，进入博客管理页面。';

$messages['blog_updated_ok'] = '博客 "%s" 已经顺利更新。';
$messages['blogs_updated_ok'] = '%s 个博客已经顺利更新。';
$messages['error_updating_blog2'] = '更新博客 ID = "%s" 时发生错误。';
$messages['error_updating_blog'] = '更新博客 "%s" 时发生错误';

$messages['error_updating_user'] = '更新用户 "%s" 时发生错误。';
$messages['user_updated_ok'] = '用户 "%s" 已经顺利更新。';
$messages['users_updated_ok'] = '%s 个用户已经顺利更新。';
$messages['eror_updating_user2'] = '更新用户 "%s" 时发生错误。';

$messages['error_select_status'] = '请选择合法的状态。';
$messages['error_invalid_blog_name'] = '网志博客“%s”不正确。';

$messages['help_resources_naming_rule'] = '选择档案上传后在主机的储存方式。“原始档案名称”使用原来的档案名称来储存上传的档案。“编码档案名称”使用编码过的档案名称 [BlogId]-[ResourceId].[Ext] 来储存上传的档案。在 Windows 多字元下安装 LifeType 请使用“编码档案名称”。<strong>另外，当使用者开始上传档案后，请勿修改此选项，这会造成以上传的档案无法再被读取。</strong> [预设 = 原始档案名称]';
$messages['original_file_name'] = '原始档案名称';
$messages['encoded_file_name'] = '编码档案名称';

$messages['quick_permission_selection'] = '快速权限设定选单';
$messages['basic_blog_permission'] = '博客作者可以新增、编修与删除文章、链接与文件';
$messages['full_blog_permission'] = '博客作者可以跟博客拥有者一样，操作所有功能';

$messages['error_template_exist'] = '上传模版时发生错误，“%s”模版已经存在。';

/// new strings in LT 1.2.2 ///
$messages['posted_by_help'] = '选择文章作者';
$messages['insert_player'] = '插入播放器';

/// new strings in LT 1.2.3 ///
$messages['help_allow_javascript_blocks_in_posts'] = '允许在文章中使用 &lt;script&gt; 的标签。tags. 请小心使用，允许使用 Javascript 的标签可能会让你的博客产生安全上的漏洞 [预设 = 否]';

$messages['Versions'] = '版本';
$messages['incorrect_file_version_error'] = '下列的文件内容有问题（可能上传不完整或是被修改过）：';
$messages['lifetype_version'] = 'LifeType';
$messages['lifetype_version_help'] = '目前安装的 LifeType 版本是：';
$messages['file_version_check'] = '文件版本检查';
$messages['file_version_check_help'] = '这个动作会检查 LifeType 的核心文件，主要是用来确定目前的文件内容的确符合预期安装的版本。如果你没有对文件进行任何的修改，
所有文件的内容应该都会符合检查的结果。请耐心等候，这个检查需要花一点时间。';
$messages['check'] = '检查';
$messages['all_files_ok'] = '所有档案都正确。';

/// new strings for LT 1.2.4 ///
$messages['plugin_latest_version'] = '最新版本： ';
$messages['check_versions'] = '检查版本';
$messages['lt_version_ok'] = '目前的 LifeType 是最新版的。';
$messages['lt_version_error'] = '最新版的 LifeType 是： ';
$messages['release_notes'] = '释出纪录';

$messages['kb'] = 'KB';
$messages['mb'] = 'MB';
$messages['gb'] = 'DB';
$messages['edit'] = '编辑';

/// new strings for LT 1.2.5 ///
$messages['bookmark_this_filter'] = '加到书签';
$messages['help_trim_whitespace_output'] = '输出时，移除所有 HTML 程式码中的空白字元，这会让输出的 HTML 程式码最多减少 40% 的大小。除非你非常在意他会稍稍的影响你伺服器的 CPU 效能，否则建议将他打开。 [ 预设 = Yes ]';
$messages['help_notify_new_blogs'] = '当有网志新增时，通知网站管理者';
$messages['new_blog_admin_notification_text'] = '这是 LifeType 的网志自动通知系统。

有一个新的网志 "%1$s" (%2$s) 已经新增到你的 LifeType 网站中。

祝你有美好的一天。
';
?>