<?php

/////////////////                                          //////////////////
///////////////// STRINGS FOR THE ADMINISTRATION INTERFACE //////////////////
/////////////////                                          //////////////////

// login page
$messages['login'] = '登入';
$messages['welcome_message'] = '歡迎使用 LifeType！';
$messages['error_incorrect_username_or_password'] = '很抱歉，您輸入的帳號或密碼錯誤。';
$messages['error_dont_belong_to_any_blog'] = '很抱歉，您沒有使用系統中任何一個網誌站台的權限。';
$messages['logout_message'] = '您已經順利登出系統。';
$messages['logout_message_2'] = '請按 <a href="%1$s">這裡</a> 連結到 %2$s。';
$messages['error_access_forbidden'] = '您目前沒有權限進入管理介面，請到這裡登入。';
$messages['username'] = '使用者名稱';
$messages['password'] = '使用者密碼';

// dashboard
$messages['dashboard'] = '管理面版';
$messages['recent_articles'] = '最近發表文章';
$messages['recent_comments'] = '最近發表迴響';
$messages['recent_trackbacks'] = '最近引用列表';
$messages['blog_statistics'] = '網誌統計';
$messages['total_posts'] = '文章總數';
$messages['total_comments'] = '迴響總數';
$messages['total_trackbacks'] = '引用總數';
$messages['total_viewed'] = '文章閱讀總數';
$messages['in'] = '於';

// menu options
$messages['newPost'] = '發表新文章';
$messages['Manage'] = '內容管理';
$messages['managePosts'] = '文章管理';
$messages['editPosts'] = '文章列表';
$messages['editArticleCategories'] = '文章分類列表';
$messages['newArticleCategory'] = '新增文章分類';
$messages['manageLinks'] = '網站連結管理';
$messages['editLinks'] = '網站連結列表';
$messages['newLink'] = '新增網站連結';
$messages['editLink'] = '編輯網站連結';
$messages['editLinkCategories'] = '網站連結分類列表';
$messages['newLinkCategory'] = '新增連結分類';
$messages['editLinkCategory'] = '編輯網站連結分類';
$messages['manageCustomFields'] = '管理自訂欄位';
$messages['blogCustomFields'] = '自訂欄位列表';
$messages['newCustomField'] = '新增自訂欄位';
$messages['resourceCenter'] = '檔案中心';
$messages['resources'] = '檔案列表';
$messages['newResourceAlbum'] = '新增資料夾';
$messages['newResource'] = '新增檔案';
$messages['controlCenter'] = '個人網誌設定';
$messages['manageSettings'] = '基本設定';
$messages['blogSettings'] = '網誌設定';
$messages['userSettings'] = '使用者設定';
$messages['pluginCenter'] = '外掛中心';
$messages['Stats'] = '統計資料';
$messages['manageBlogUsers'] = '管理網誌使用者';
$messages['newBlogUser'] = '新增網誌使用者';
$messages['showBlogUsers'] = '網誌使用者列表';
$messages['manageBlogTemplates'] = '管理網誌模版';
$messages['newBlogTemplate'] = '新增網誌模版';
$messages['blogTemplates'] = '網誌模版列表';
$messages['adminSettings'] = '全部站台管控';
$messages['Users'] = '使用者';
$messages['createUser'] = '新增使用者';
$messages['editSiteUsers'] = '管理使用者';
$messages['Blogs'] = '管理網誌';
$messages['createBlog'] = '建立網誌';
$messages['editSiteBlogs'] = '網誌站台管理';
$messages['Locales'] = '語系管理';
$messages['newLocale'] = '新增語系';
$messages['siteLocales'] = '語系檔案列表';
$messages['Templates'] = '模版管理';
$messages['newTemplate'] = '新增模版';
$messages['siteTemplates'] = '模版管理';
$messages['GlobalSettings'] = '全域設定';
$messages['editSiteSettings'] = '一般設定';
$messages['summarySettings'] = ' 彙整頁面設定';
$messages['templateSettings'] = '模版設定';
$messages['urlSettings'] = '網址設定';
$messages['emailSettings'] = '電子郵件設定';
$messages['uploadSettings'] = '上傳設定';
$messages['helpersSettings'] = '工具設定';
$messages['interfacesSettings'] = '網路服務介面設定';
$messages['securitySettings'] = '系統安全設定';
$messages['bayesianSettings'] = '貝氏過濾設定';
$messages['resourcesSettings'] = '檔案中心設定';
$messages['searchSettings'] = '搜尋設定';
$messages['cleanUpSection'] = '清理垃圾專區';
$messages['cleanUp'] = '清理垃圾';
$messages['editResourceAlbum'] = '編輯檔案資料夾';
$messages['resourceInfo'] = '檔案資訊';
$messages['editBlog'] = '網誌管理';
$messages['Logout'] = '登出';

// new post
$messages['topic'] = '標題';
$messages['topic_help'] = '文章標題';
$messages['text'] = '內文';
$messages['text_help'] = '這部份的內容會在網誌首頁出現。';
$messages['extended_text'] = '延伸內文';
$messages['extended_text_help'] = '您在此輸入的文字只會在單篇彙整狀態下顯示，除非您在「設定」頁面中修改了設定。';
$messages['trackback_urls'] = '真實引用網址';
$messages['trackback_urls_help'] = '如果您要引用的文章沒有支援『引用網址自動搜尋』機制，請在這裡輸入他們的真實引用網址，並用斷行來隔開。';
$messages['post_slug'] = '短標題 ';
$messages['post_slug_help'] = '短標題將會用來建立簡潔的靜態連結網址';
$messages['date'] = '日期';
$messages['post_date_help'] = '文章發表日期';
$messages['status'] = '狀態';
$messages['post_status_help'] = '選取一個狀態';
$messages['post_status_published'] = '定稿';
$messages['post_status_draft'] = '草稿';
$messages['post_status_deleted'] = '已刪除';
$messages['post_categories_help'] = '選取一個或一個以上的分類';
$messages['post_comments_enabled_help'] = '啟用迴響留言功能';
$messages['send_notification_help'] = '如果有人對本文發表迴響，便向我發送電子郵件通知';
$messages['send_trackback_pings_help'] = '發送引用通告';
$messages['send_xmlrpc_pings_help'] = '送出 XMLRPC 通告';
$messages['save_draft_and_continue'] = '儲存草稿';
$messages['preview'] = '預覽';
$messages['add_post'] = '發表!';
$messages['error_saving_draft'] = '儲存草稿發生錯誤！';
$messages['draft_saved_ok'] = '草稿 「%s」 已順利儲存';
$messages['error_sending_request'] = '傳送要求時發生錯誤';
$messages['error_no_category_selected'] = '你沒有選擇任何分類';
$messages['error_missing_post_topic'] = '請輸入文章標題！';
$messages['error_missing_post_text'] = '請輸入文章內文！';
$messages['error_adding_post'] = '發表文章發生錯誤！';
$messages['post_added_not_published'] = '文章已順利新增，但尚未正式發表。';
$messages['post_added_ok'] = '文章已順利新增';
$messages['send_notifications_ok'] = '當有新的迴響或是引用時，便向我發送電子郵件通知';
$messages['bookmarklet'] = "書籤小程式";
$messages['bookmarklet_help'] = "把下面的連結拉到你工具列，或是按下滑鼠右鍵把連結加到我的最愛。";
$messages['blogit_to_lifetype'] = "把文章加到 LifeType！";
$messages['original_post'] = "（原文）";

// send trackbacks
$messages['error_sending_trackbacks'] = '發送下列引用通知時產生錯誤。';
$messages['send_trackbacks_help'] = '請勾選您所要發送引用通告的網址。(請確定該網站支援引用通告的功能)';
$messages['send_trackbacks'] = '發送引用通知';
$messages['ping_selected'] = '向勾選的網址發送引用通知';
$messages['trackbacks_sent_ok'] = '引用通知已經成功發送到勾選的網址。';

// posts page
$messages['show_by'] = '更新列表';
$messages['author'] = '作者';
$messages['post_status_all'] = '全部';
$messages['author_all'] = '全部作者';
$messages['search_terms'] = '搜尋關鍵字';
$messages['show'] = '更新';
$messages['delete'] = '刪除';
$messages['actions'] = '動作';
$messages['all'] = '全部';
$messages['category_all'] = '全部分類';
$messages['error_incorrect_article_id'] = '文章 ID 不正確。';
$messages['error_deleting_article'] = '在刪除文章"%s"時，發生錯誤。';
$messages['article_deleted_ok'] = '文章「%s」 已順利刪除。';
$messages['articles_deleted_ok'] = '文章「%s」 已順利刪除。';
$messages['error_deleting_article2'] = '刪除文章時發生錯誤 (id = %s) ';

// edit post page
$messages['update'] = '更新';
$messages['editPost'] = '編輯文章';
$messages['post_updated_ok'] = '文章[%s]已成功更新。';
$messages['error_updating_post'] = '更新文章時發生錯誤';
$messages['notification_added'] = '當有新的迴響或是引用時，便向我發送電子郵件通知';
$messages['notification_removed'] = '當有新的迴響或是引用時，不要向我發送電子郵件通知';

// post comments
$messages['url'] = '網址';
$messages['comment_status_all'] = '全部迴響';
$messages['comment_status_spam'] = '垃圾迴響';
$messages['comment_status_nonspam'] = '正常迴響';
$messages['error_fetching_comments'] = '讀取文章迴響資料時，發生錯誤。';
$messages['error_deleting_comments'] = '在刪除迴響時發生錯誤或您沒有勾選任何要刪除的迴響。';
$messages['comment_deleted_ok'] = '「%s」這篇文章的迴響已順利刪除。';
$messages['comments_deleted_ok'] = '「%s」這篇文章的迴響已順利刪除。';
$messages['error_deleting_comment'] = '在刪除迴響「%s」時發生錯誤。';
$messages['error_deleting_comment2'] = '刪除迴響時發生錯誤 (id = %s)';
$messages['editComments'] = '迴響列表';
$messages['mark_as_spam'] = '標示為垃圾迴響';
$messages['mark_as_no_spam'] = '標示為正常迴響';
$messages['error_incorrect_comment_id'] = '留言迴響 ID 不正確。';
$messages['error_marking_comment_as_spam'] = '在將本篇迴響留言標示為垃圾留言時發生錯誤。';
$messages['comment_marked_as_spam_ok'] = '您已經順利將本篇迴響留言標示為垃圾留言。';
$messages['error_marking_comment_as_nonspam'] = '在將本篇迴響留言標示為正常留言時發生錯誤。';
$messages['comment_marked_as_nonspam_ok'] = '您已經順利將本篇迴響留言標示為正常留言。';
$messages['comment_no_topic'] = '沒有迴響主旨。';

// post trackbacks
$messages['blog'] = '網誌';
$messages['excerpt'] = '摘要';
$messages['error_fetching_trackbacks'] = '讀取引用資料時，發生錯誤。';
$messages['error_deleting_trackbacks'] = '在刪除引用時發生錯誤或是你沒有勾選任何要刪除的引用。';
$messages['error_deleting_trackback'] = '在刪除引用「%s」時發生錯誤';
$messages['error_deleting_trackback2'] = '刪除引用時發生錯誤 (id = %s)';
$messages['trackback_deleted_ok'] = '「%s」這篇引用已順利刪除。';
$messages['trackbacks_deleted_ok'] = '「%s」這篇引用已順利刪除。';
$messages['editTrackbacks'] = '引用列表';

// post statistics
$messages['referrer'] = '逆向連結';
$messages['hits'] = '點擊數';
$messages['error_no_items_selected'] = '你沒有勾選任何要刪除的項目';
$messages['error_deleting_referrer'] = '在刪除逆向連結「%s」時發生錯誤';
$messages['error_deleting_referrer2'] = '刪除逆向連結時發生錯誤 (id = %s)';
$messages['referrer_deleted_ok'] = '「%s」這篇逆向連結已順利刪除。';
$messages['referrers_deleted_ok'] = '「%s」這篇逆向連結已順利刪除。';

// categories
$messages['posts'] = '文章列表';
$messages['show_in_main_page'] = '在首頁顯示';
$messages['error_category_has_articles'] = '無法刪除「%s」這個分類，因為該分類下還有文章。請先修改文章分類後，再重試一次。';
$messages['category_deleted_ok'] = '「%s」這個分類已順利刪除。';
$messages['categories_deleted_ok'] = '「%s」這個分類已順利刪除。';
$messages['error_deleting_category'] = '在刪除分類「%s」時發生錯誤';
$messages['error_deleting_category2'] = '刪除分類時發生錯誤 (id = %s)';
$messages['yes'] = '是';
$messages['no'] = '否';

// new category
$messages['name'] = '名稱';
$messages['category_name_help'] = '請輸入分類名稱';
$messages['description'] = '描述';
$messages['category_description_help'] = '請輸入詳細的分類描述';
$messages['show_in_main_page_help'] = '選取這個選項，則在這個分類下的文章會在首頁顯示。否則只有當瀏覽這個分類時才會看到文章。';
$messages['error_empty_name'] = '你必須輸入分類名稱';
$messages['error_empty_description'] = '你必須輸入分類描述';
$messages['error_adding_article_category'] = '在新增分類時發生錯誤。請檢查輸入的資料，再重試一次。';
$messages['category_added_ok'] = '分類名稱 「%s」已經順利新增';
$messages['add'] = '新增';
$messages['reset'] = '重新設置';

// update category
$messages['error_updating_article_category'] = '更新文章分類時發生錯誤。';
$messages['article_category_updated_ok'] = '分類 「%s」 已順利更新。';

// links
$messages['feed'] = 'Feed';
$messages['error_no_links_selected'] = '網站連結 ID 錯誤或您沒有選擇任何網站連結，無法刪除。';
$messages['error_incorrect_link_id'] = '網站連結 ID 不正確';
$messages['error_removing_link'] = '在刪除網站連結「%s」時發生錯誤。';
$messages['error_removing_link2'] = '在刪除網站連結時發生錯誤，id = %d';
$messages['link_deleted_ok'] = '網站連結「%s」已順利刪除。';
$messages['links_deleted_ok'] = '網站連結「%s」已順利刪除。';

// new link
$messages['link_name_help'] = '請輸入連結名稱。';
$messages['link_url_help'] = '連結網址';
$messages['link_description_help'] = '簡短描述';
$messages['link_feed_help'] = '你也可以提供任何的 RSS 或 Atom feeds 的連結。';
$messages['link_category_help'] = '選取一個網站連結分類';
$messages['error_adding_link'] = '新增網站連結時發生錯誤。請檢查輸入的資料，再重試一次。';
$messages['error_invalid_url'] = '網址不正確';
$messages['link_added_ok'] = '網站連結「%s」已順利新增';
$messages['bookmarkit_to_lifetype'] = "把書籤加到 LifeType！";

// update link
$messages['error_updating_link'] = '更新網站連結時發生錯誤。請檢查輸入的資料，再重試一次。';
$messages['error_fetching_link'] = '讀取網站連結資料時發生錯誤。';
$messages['link_updated_ok'] = '網站連結「%s」已順利更新';

// link categories
$messages['error_invalid_link_category_id'] = '網站連結分類ID不正確或沒有選擇連結分類，無法刪除。';
$messages['error_links_in_link_category'] = '無法刪除「%s」這個網站連結分類，因為該分類下還有連結。請先修改網站連結後，再重試一次。';
$messages['error_removing_link_category'] = '在刪除網站連結分類「%s」時發生錯誤。';
$messages['link_category_deleted_ok'] = '網站連結分類「%s」已順利刪除。';
$messages['link_categories_deleted_ok'] = '網站連結分類「%s」已順利刪除。';
$messages['error_removing_link_category2'] = '刪除網站連結分類時發生錯誤 (id = %s)';

// new link category
$messages['link_category_name_help'] = '網站連結分類名稱';
$messages['error_adding_link_category'] = '新增網站連結分類時發生錯誤。';
$messages['link_category_added_ok'] = '網站連結分類「%s」已順利新增';

// edit link category
$messages['error_updating_link_category'] = '更新網站連結分類時發生錯誤。請檢查輸入資料後，再試一次。';
$messages['link_category_updated_ok'] = '網站連結分類「%s」已順利更新';
$messages['error_fetching_link_category'] = '讀取網站連結分類資料時發生錯誤。';

// custom fields
$messages['type'] = '類型';
$messages['hidden'] = '隱藏';
$messages['fields_deleted_ok'] = '「%s」 自訂欄位已順利刪除';
$messages['field_deleted_ok'] = '「%s」 自訂欄位已順利刪除';
$messages['error_deleting_field'] = '在刪除自訂欄位「%s」時發生錯誤。';
$messages['error_deleting_field2'] = '刪除自訂欄位時發生錯誤 (id = %s)';
$messages['error_incorrect_field_id'] = '自訂欄位ID不正確';

// new custom field
$messages['field_name_help'] = '在發表文章時，用來顯示自訂欄位的名稱';
$messages['field_description_help'] = '自訂欄位的簡短描述';
$messages['field_type_help'] = '選擇一個合適的欄位類型';
$messages['field_hidden_help'] = '如果勾選隱藏，那麼在新增或修改文章時便不會出現該自訂欄位。這個功能主要提供給外掛程式專用。';
$messages['error_adding_custom_field'] = '新增自訂欄位時發生錯誤。請檢查輸入資料後，再試一次。';
$messages['custom_field_added_ok'] = '自訂欄位「%s」已順利更新';
$messages['text_field'] = '文字欄位 (Text Field)';
$messages['text_area'] = '文字區塊 (Text Box)';
$messages['checkbox'] = '核取方塊 (Check Box)';
$messages['date_field'] = '日期選擇 (Date Chooser)';

// edit custom field
$messages['error_fetching_custom_field'] = '讀取自訂欄位資料時發生錯誤。';
$messages['error_updating_custom_field'] = '更新自訂欄位時發生錯誤。請檢查輸入資料後，再試一次。';
$messages['custom_field_updated_ok'] = '自訂欄位「%s」已順利更新';

// resources
$messages['root_album'] = '主資料夾';
$messages['num_resources'] = '檔案數';
$messages['total_size'] = '檔案大小';
$messages['album'] = '資料夾';
$messages['error_incorrect_album_id'] = '資料夾 ID 不正確';
$messages['error_base_storage_folder_missing_or_unreadable'] = 'LifeType 無法建立檔案存檔所必需的資料夾。 原因可能是因為PHP以安全模式在執行或是你沒有足夠的權限上傳檔案。 你可以試著手動建立下列資料夾: <br/><br/>%s<br/><br/>如果這些資料夾已經存在，請確定你可以使用瀏覽器來進行讀寫。';
$messages['items_deleted_ok'] = '「%s」已順利刪除';
$messages['error_album_has_children'] = '「%s」資料夾裡面還有檔案或子資料夾。請將檔案或資料夾移除後在重試一次。';
$messages['item_deleted_ok'] = '「%s」已順利刪除';
$messages['error_deleting_album'] = '在刪除資料夾「%s」時發生錯誤。';
$messages['error_deleting_album2'] = '刪除資料夾時發生錯誤 (id = %s)';
$messages['error_deleting_resource'] = '在刪除檔案「%s」時發生錯誤。';
$messages['error_deleting_resource2'] = '刪除檔案時發生錯誤 (id = %s)';
$messages['error_no_resources_selected'] = '沒有選擇要刪除的項目。';
$messages['resource_deleted_ok'] = '檔案：「%s」 已順利刪除';
$messages['album_deleted_ok'] = '資料夾：「%s」 已順利刪除';
$messages['add_resource'] = '新增檔案 (原圖)';
$messages['add_resource_preview'] = '新增檔案預覽 (小圖)';
$messages['add_resource_medium'] = '新增檔案預覽 (中圖)';
$messages['add_album'] = '新增資料夾';

// new album
$messages['album_name_help'] = '資料夾簡短名稱';
$messages['parent'] = '上層目錄';
$messages['no_parent'] = '頂端目錄';
$messages['parent_album_help'] = '使用這個選項來安排子資料夾，同時讓你的檔案放置更有組織。';
$messages['album_description_help'] = '對資料夾內容做詳細的描述說明。';
$messages['error_adding_album'] = '新增資料夾時發生錯誤。請檢查輸入資料後，再試一次。';
$messages['album_added_ok'] = '資料夾：「%s」 已順利新增。';

// edit album
$messages['error_incorrect_album_id'] = '資料夾ID不正確。';
$messages['error_fetching_album'] = '讀取資料夾資料時發生錯誤。';
$messages['error_updating_album'] = '更新資料夾時發生錯誤。請檢查輸入資料後，再試一次。';
$messages['album_updated_ok'] = '資料夾「%s」已順利更新';
$messages['show_album_help'] = '取消勾選，這個資料夾將不會出現在網誌資料夾列表中。';

// new resource
$messages['file'] = '檔案';
$messages['resource_file_help'] = '下面的檔案將會新增到網誌的檔案中心。如果你要同時上傳多個檔案，請使用下方「新增上傳欄位」的連結來新增欄位。';
$messages['add_field'] = '新增上傳欄位';
$messages['resource_description_help'] = '關於這個檔案內容的詳細描述。';
$messages['resource_album_help'] = '選擇你想將檔案上傳到那個資料夾。';
$messages['error_no_resource_uploaded'] = '你並未選擇任何要上傳的檔案。';
$messages['resource_added_ok'] = '檔案：「%s」已順利新增。';
$messages['error_resource_forbidden_extension'] = '無法新增檔案，因為用了系統不允許的副檔名。';
$messages['error_resource_too_big'] = '無法新增檔案，因為檔案太大了。';
$messages['error_uploads_disabled'] = '無法新增檔案，因為伺服器管理員關閉了這項功能。';
$messages['error_quota_exceeded'] = '無法新增檔案，因為已經超過容許的檔案容量限度。';
$messages['error_adding_resource'] = '在新增檔案時發生錯誤。';

// edit resource
$messages['editResource'] = '編輯檔案';
$messages['resource_information_help'] = '下面是一些與這個檔案有關的資訊';
$messages['information'] = '檔案資訊';
$messages['thumbnail_format'] = '縮圖格式';
$messages['regenerate_preview'] = '重新產生預覽縮圖';
$messages['error_fetching_resource'] = '讀取檔案資訊時發生錯誤。';
$messages['error_updating_resource'] = '更新檔案時發生錯誤。';
$messages['resource_updated_ok'] = '檔案：「%s」已順利更新。';

// blog settings
$messages['blog_link'] = '網誌站台網址';
$messages['blog_link_help'] = '不能修改';
$messages['blog_name_help'] = '站台名稱';
$messages['blog_description_help'] = '站台相關說明';
$messages['language'] = '語系';
$messages['blog_language_help'] = '系統文字以及日期所使用的語言';
$messages['max_main_page_items'] = '首頁文章數目';
$messages['max_main_page_items_help'] = '您要在首頁顯示幾篇文章？';
$messages['max_recent_items'] = '近期文章數目';
$messages['max_recent_items_help'] = '您要在「近期文章列表」顯示幾篇文章？';
$messages['template'] = '模版';
$messages['choose'] = '預覽選取...';
$messages['blog_template_help'] = '請選擇您的網誌站台所要使用的外觀樣式模版';
$messages['use_read_more'] = '在文章使用「閱讀全文...」連結';
$messages['use_read_more_help'] = '如果設定為「是」，那麼您在首頁的文章就會自動產生「閱讀全文」連結，這個連結會連到單篇文章的靜態固定網址，再顯示全文的「延伸內文部分」。';
$messages['enable_wysiwyg'] = '啟用所見即所得（WYSIWYG）文章編輯。';
$messages['enable_wysiwyg_help'] = '如果您想要立刻看到您的編輯結果，請設定為「是」。這個功能只有在使用者使用Internet Explorer 5.5或Mozilla 1.3b以上的版本才有效果。';
$messages['enable_comments'] = '開放所有文章的迴響留言權限';
$messages['enable_comments_help'] = '如果設定為「是」，那麼您便可以讓其他使用者針對您的文章發表迴響留言。這個設定會套用到您的全部文章上。';
$messages['show_future_posts'] = '在日曆顯示未來文章。';
$messages['show_future_posts_help'] = '如果設定為「是」，那麼發表日期設定在未來的文章將會出現在日曆上。';
$messages['articles_order'] = 'Articles order';
$messages['articles_order_help'] = 'Order in which articles should be displayed.';
$messages['comments_order'] = '迴響留言排序方式';
$messages['comments_order_help'] = '如果您設定成「舊的在前」，那麼留言就會從舊到新排序，如果設定成「新的在前」，則反之，留言從新到舊排序出現。';
$messages['oldest_first'] = '舊的在前';
$messages['newest_first'] = '新的在前';
$messages['categories_order'] = '分類排列順序';
$messages['categories_order_help'] = '首頁分類排列方式。';
$messages['most_recent_updated_first'] = '最近更新在前';
$messages['alphabetical_order'] = '依英文字母順序排列';
$messages['reverse_alphabetical_order'] = '依英文字母順序反向排列';
$messages['most_articles_first'] = '最多文章在前';
$messages['link_categories_order'] = '網站連結分類排列順序';
$messages['link_categories_order_help'] = '首頁網站連結分類排列方式。';
$messages['most_links_first'] = '最多連結在前';
$messages['most_links_last'] = '最多連結在後';
$messages['time_offset'] = '網誌伺服器與您所在地的時間差';
$messages['time_offset_help'] = '您可以用這個設定，調整您所發表的文章的時間。這個功能在伺服器主機與您分別在不同時區時相當有用。如果您將時間差設定為「+3 小時」，那麼系統就會將文章的發表時間調整成您所設定的時間。';
$messages['close'] = '關閉';
$messages['select'] = '選擇';
$messages['error_updating_settings'] = '更新網誌設定時發生錯誤，請檢查輸入資料後在重試一次。';
$messages['error_invalid_number'] = '數目格式不正確。';
$messages['error_incorrect_time_offset'] = '網誌伺服器與您所在地的時間差不正確';
$messages['blog_settings_updated_ok'] = '網誌設定更新已順利完成。';
$messages['hours'] = '小時';

// user settings
$messages['username_help'] = '公開的使用者名稱，無法更改。';
$messages['full_name'] = '全名';
$messages['full_name_help'] = '完整的使用者名稱';
$messages['password_help'] = '如果你想更改密碼請輸入新密碼及確認密碼；如果您不想修改密碼，留白便可。';
$messages['confirm_password'] = '確認密碼';
$messages['email'] = '電子郵件';
$messages['email_help'] = '如果您想要使用電子郵件通知信功能，請填寫正確的信箱。';
$messages['bio'] = '自我介紹';
$messages['bio_help'] = '您可以在此填寫一些您的自我介紹，或是不填也可以。';
$messages['picture'] = '個人圖像';
$messages['user_picture_help'] = '請從上傳到網誌中的圖片選取一張做為你的個人大頭貼。';
$messages['error_invalid_password'] = '密碼太短或密碼錯誤。';
$messages['error_passwords_dont_match'] = '很抱歉，您輸入的兩次密碼不相符。';
$messages['error_updating_user_settings'] = '更新個人資料時發生錯誤。請檢查輸入的資料後在重試一次。';
$messages['user_settings_updated_ok'] = '使用者設定已順利更新。';
$messages['resource'] = '檔案';

// plugin centre
$messages['identifier'] = '代號';
$messages['error_plugins_disabled'] = '很抱歉，外掛程式目前停用中。';

// blog users
$messages['revoke_permissions'] = '取消使用權限。';
$messages['error_no_users_selected'] = '你沒有選取任何使用者。';
$messages['user_removed_from_blog_ok'] = '使用者「%s」已經順利從本站作者行列中刪除。';
$messages['users_removed_from_blog_ok'] = '使用者「%s」已經順利從本站作者行列中刪除。';
$messages['error_removing_user_from_blog'] = '在將使用者「%s」從本網誌站台作者行列中移除時發生錯誤。';
$messages['error_removing_user_from_blog2'] = '在將使用者從本網誌站台作者行列中移除時發生錯誤。(id:%s)';

// new blog user
$messages['new_blog_username_help'] = '您可以用以下表單，將其他使用者加入您的網誌作者行列中。新增加的使用者只能存取管理中心及檔案中心。';
$messages['send_notification'] = '發送通知';
$messages['send_user_notification_help'] = '用電子郵件通知這名使用者。';
$messages['notification_text'] = '通知內容';
$messages['notification_text_help'] = '請輸入您要通知這位使用者的信件內容';
$messages['error_adding_user'] = '在加入使用者時發生問題，請檢查輸入的資料在重試一次。';
$messages['error_empty_text'] = '通知內容不可以是空白。';
$messages['error_adding_user'] = '在加入使用者時發生問題，請檢查輸入的資料在重試一次。';
$messages['error_invalid_user'] = '使用者「%s」帳號不正確或該使用者不存在。';
$messages['user_added_to_blog_ok'] = '使用者「%s」已經順利加入作者行列。';

// blog templates
$messages['error_no_templates_selected'] = '您沒有選擇任何模版。';
$messages['error_template_is_current'] = '「%s」模版無法刪除，該模版正在使用中。';
$messages['error_removing_template'] = '刪除模版 「%s」時發生錯誤。';
$messages['template_removed_ok'] = ' 模版 「%s」已順利刪除。';
$messages['templates_removed_ok'] = '模版 「%s」已順利刪除。';

// new blog template
$messages['template_installed_ok'] = '新的模版設置「 %s」已經順利安裝完成。';
$messages['error_installing_template'] = '在安裝模版設置「 %s」時發生錯誤。';
$messages['error_missing_base_files'] = '在這個模版設置中有些基本檔案不見了。';
$messages['error_add_template_disabled'] = '本站不允許使用者新增模版檔案。';
$messages['error_must_upload_file'] = '您必須上傳檔案。';
$messages['error_uploads_disabled'] = '本站已關閉檔案上傳功能。';
$messages['error_no_new_templates_found'] = '找不到新的模版設置。';
$messages['error_template_not_inside_folder'] = '模版檔案必須放在與模版同名的目錄當中。';
$messages['error_missing_base_files'] = '在這個模版設置中有些基本檔案不見了。';
$messages['error_unpacking'] = '在解壓縮時發生錯誤。';
$messages['error_forbidden_extensions'] = '在這個模版設置中有些檔案禁止存取。';
$messages['error_creating_working_folder'] = '在檢查模版設置時發生錯誤。';
$messages['error_checking_template'] = '模版設置發生錯誤 (code = %s)';
$messages['template_package'] = '模版安裝包';
$messages['blog_template_package_help']  = '您可以用這個表單，上傳一個新的模版安裝包，該模版將只有你的網誌能夠使用。如果您沒有辦法用瀏覽器上傳，請手動上傳該模版並將它放置於你的網誌模板資料夾<b>%s</b>下,然後按下 "<b>掃描模版</b>" 按紐。 LifeType 會掃描該資料夾並自動新增所找到的新模版。';
$messages['scan_templates'] = '掃描模版';

// site users
$messages['user_status_active'] = '啟用';
$messages['user_status_disabled'] = '停用';
$messages['user_status_all'] = '所有狀態';
$messages['user_status_unconfirmed'] = '尚未確認';
$messages['error_invalid_user2'] = '使用者代號「%s」不存在。';
$messages['error_deleting_user'] = '在停用使用者帳號「%s」時發生錯誤。';
$messages['user_deleted_ok'] = '使用者帳號「%s」已順利停用。';
$messages['users_deleted_ok'] = '使用者帳號「%s」已順利停用。';

// create user
$messages['user_added_ok'] = '新使用者帳號「%s」已順利新增。';
$messages['user_status_help'] = '使用者帳號目前狀態';
$messages['user_blog_help'] = '使用者預設的網誌';
$messages['none'] = '無';

// edit user
$messages['error_invalid_user'] = '使用者ID不正確或使用者不存在。';
$messages['error_updating_user'] = '更新使用者設定時發生錯誤。請檢查輸入資料後再重試一次。';
$messages['blogs'] = '網誌';
$messages['user_blogs_help'] = '使用者擁有或可以存取的網誌。';
$messages['site_admin'] = '全站系統管理';
$messages['site_admin_help'] = '如果使用者擁有全站系統管理權限，他就可以看見[站台設定]區域，可以進行全站的管理工作。';
$messages['user_updated_ok'] = '使用者帳號「%s」已順利更新。';

// site blogs
$messages['blog_status_all'] = '所有狀態';
$messages['blog_status_active'] = '啟用';
$messages['blog_status_disabled'] = '停用';
$messages['blog_status_unconfirmed'] = '尚未確認';
$messages['owner'] = '管理員';
$messages['quota'] = '檔案限度';
$messages['bytes'] = 'bytes';
$messages['error_no_blogs_selected'] = '您必須要選擇您所想要刪除的網誌站台。';
$messages['error_blog_is_default_blog'] = '「%s」是系統預設網誌站台，無法刪除。';
$messages['blog_deleted_ok'] = '「%s」網誌站台已順利刪除。';
$messages['blogs_deleted_ok'] = '「%s」網誌站台已順利刪除。';
$messages['error_deleting_blog'] = '在刪除「%s」這個網誌站台時發生錯誤。';
$messages['error_deleting_blog2'] = '在刪除網誌站台時發生錯誤。(id:%s)';

// create blog
$messages['error_adding_blog'] = '在新增網誌時發生錯誤。請檢查輸入的資料在重試一次。';
$messages['blog_added_ok'] = '新的網誌站台「%s」已成功加入資料庫中。';

// edit blog
$messages['blog_status_help'] = '網誌狀態';
$messages['blog_owner_help'] = '網誌站台管理者，將擁有完整的權限來修改網誌設定。';
$messages['users'] = '使用者';
$messages['blog_quota_help'] = '檔案容量限度(單位：bytes)。設為0或空白將使用系統的全域檔案限度做為預設值。';
$messages['edit_blog_settings_updated_ok'] = '網誌 「%s」已順利更新。';
$messages['error_updating_blog_settings'] = '更新網誌站台 「%s」時發生錯誤。';
$messages['error_incorrect_blog_owner'] = '要設定為網誌站台管理員的使用者帳號不存在。';
$messages['error_fetching_blog'] = '讀取網誌資料時發生錯誤。';
$messages['error_updating_blog_settings2'] = '更新網誌時發生錯誤。請檢查輸入資料在重試一次。';
$messages['add_or_remove'] = '新增或移除使用者';

// site locales
$messages['locale'] = '語系';
$messages['locale_encoding'] = '編碼方式';
$messages['locale_deleted_ok'] = '「%s」語系已順利刪除。';
$messages['error_no_locales_selected'] = '您沒有選擇要刪除的語系。';
$messages['error_deleting_only_locale'] = '您不可以刪除這個語系檔案，因為這是系統中目前唯一的語系檔案。';
$messages['locales_deleted_ok']= '「%s」語系已順利刪除。';
$messages['error_deleting_locale'] = '在刪除「%s」語系時發生錯誤。';
$messages['error_locale_is_default'] = '您不可以刪除「%s」語系，因為這是系統目前的預設語系。';

// add locale
$messages['error_invalid_locale_file'] = '這個檔案並不是正確的語系檔案。';
$messages['error_no_new_locales_found'] = '找不到新的語系檔案。';
$messages['locale_added_ok'] = '語系「%s」已經順利新增';
$messages['error_saving_locale'] = '在將新的語系檔案儲存至語系檔案目錄時發生錯誤。請檢查檔案目錄的寫入權限是否正確。';
$messages['scan_locales'] = '掃描語系檔';
$messages['add_locale_help'] = '您可以用這個表單，上傳一個新的語系檔。如果您沒有辦法用瀏覽器上傳，請手動上傳該檔案並將它放置於 <b>./locales/</b>下,然後按下 "<b>掃描語系檔</b>" 按紐。 LifeType 會掃描該資料夾並自動新增所找到的語系檔。 ';

// site templates
$messages['error_template_is_default'] = '您不可以刪除「%s」模版，因為這是新網誌目前的預設模版。';

// add template
$messages['global_template_package_help'] = '您可以用這個表單，上傳一個新的模版安裝包，該模版將提供給網站上所有網誌使用。如果您沒有辦法用瀏覽器上傳，請手動上傳該模版並將它放置於你的網誌模板資料夾<b>%s</b>下,然後按下 "<b>掃描模版</b>" 按紐。 LifeType 會掃描該資料夾並自動新增所找到的新模版。';

// global settings
$messages['site_config_saved_ok'] = '站台設定已順利儲存。';
$messages['error_saving_site_config'] = '在儲存站台設置時發生問題。';
/// general settings
$messages['help_comments_enabled'] = '啟用或停用全站的迴響留言功能。';
$messages['help_beautify_comments_text'] = '在使用者發表迴響留言時，使用他所輸入的文字格式。';
$messages['help_temp_folder'] = 'LifeType系統用來儲存暫存檔案用的目錄。';
$messages['help_base_url'] = '這個網誌安裝的網址，這個項目務必要正確，請小心輸入。';
$messages['help_subdomains_enabled'] = '啟用或停用次網域設定。';
$messages['help_include_blog_id_in_url'] = '當[次網域]功能啟用及[一般網址]功能啟用時才有意義。強迫產生的網址不要包含 blogId 這個參數。請不要變更設定值，除非你知道你在做什麼。';
$messages['help_script_name'] = '如果你將index.php更改為其它名稱的話，請在下方輸入更改後的檔案名稱。';
$messages['help_show_posts_max'] = '在首頁顯示文章數的預設值。';
$messages['help_recent_posts_max'] = '在首頁「近期文章」列表中顯示文章數的預設值。';
$messages['help_save_drafts_via_xmlhttprequest_enabled'] = '當 XmlHttpRequest 功能被啟用時，將可以使用 Javascript 來儲存文章草稿。';
$messages['help_locale_folder'] = '語系檔案所在目錄。';
$messages['help_default_locale'] = '在建立新網誌站台時預設使用的語系。';
$messages['help_default_blog_id'] = '預設網誌ID';
$messages['help_default_time_offset'] = '預設的網站伺服器時間差。';
$messages['help_html_allowed_tags_in_comments'] = '在發表迴響評論時可以使用的HTML語法標籤。';
$messages['help_referer_tracker_enabled'] = '是否使用文章逆向連結功能。(停用此功能可以提高系統效能。)';
$messages['help_show_more_enabled'] = '啟用或停用「閱讀全文」連結功能。';
$messages['help_update_article_reads'] = '是否使用內建的點閱率統計工具計算每篇文章的點閱次數。(停用此功能可以提高系統效能。)';
$messages['help_update_cached_article_reads'] = '在快取功能開啟的情形下，是否使用內建的點閱率統計工具計算每篇文章的點閱次數。';
$messages['help_xmlrpc_ping_enabled'] = '在系統中有人發表新文章時，是否送出 XMLRPC 通告。';
$messages['help_send_xmlrpc_pings_enabled_by_default'] = '預設啟用該功能。當有新文章發表或更新時，是否送出 XMLRPC 通告。。';
$messages['help_xmlrpc_ping_hosts'] = 'XMLRPC 通告列表，如果您要向多處發送通告，請在文字框下面加入通告發送網址，每個網址一行。';
$messages['help_trackback_server_enabled'] = '是否接受從站外傳來的引用通告（TrackBack）。';
$messages['help_htmlarea_enabled'] = '啟用或停用即視即所得（WYSIWYG）文章編輯。';
$messages['help_plugin_manager_enabled'] = '啟用或停用外掛程式。';
$messages['help_minimum_password_length'] = '密碼最短需要多少字元。';
$messages['help_xhtml_converter_enabled'] = '如果啟用此功能，LifeType會試著將所有的HTML轉換為適當的XHTML。';
$messages['help_xhtml_converter_aggressive_mode_enabled'] = '如果啟用此功能，LifeType會試著將HTML進一步轉換為XHTML，但這樣可能會導致更多的錯誤。';
$messages['help_session_save_path'] = '此設定將使用PHP的session_save_path()函數，來更改LifeType存放session的資料夾。請確定該資料夾可以透過網站伺服器進行寫入動作。如果你要使用PHP預設的session存放路徑，請將此設定空白。';
// summary settings
$messages['help_summary_page_show_max'] = '在彙整頁面中要顯示多少項目。此選項控制在彙整頁面中列出的所有項目。(包括最新文章數目、最活躍網誌等)';
$messages['help_summary_items_per_page'] = '在[網誌列表]中每一頁要顯示多少網誌。';
$messages['help_forbidden_usernames'] = '列出所有不允許註冊的使用者名稱。';
$messages['help_force_one_blog_per_email_account'] = '一個電子郵件是否只能註冊一個網誌';
$messages['help_summary_show_agreement'] = '在使用者進行註冊動作之前，是否顯示並確認使用者同意服務條款。';
$messages['help_need_email_confirm_registration'] = '是否啟用電子郵件的確認連結來啟用帳號。';
$messages['help_summary_disable_registration'] = '是否關閉使用者註冊新網誌的功能。';
// templates
$messages['help_template_folder'] = '模版檔案的所在目錄路徑。';
$messages['help_default_template'] = '在新建網誌站台時，預設使用的模版。';
$messages['help_users_can_add_templates'] = '使用者是否可以在模版設置當中，加入屬於自己專屬需求的檔案。';
$messages['help_template_compile_check'] = '停用此功能時，Smarty只有在模版有更改時才會重新產生頁面。停用此功能可以提高系統效能。';
$messages['help_template_cache_enabled'] = '啟用模版快取功能。啟用此功能，快取的版本將會持續被使用，而不需要對資料庫進行資料存取的動作。';
$messages['help_template_cache_lifetime'] = '快取存活時間(單位：秒).設為-1快取將永不過期，或設為0來關閉快取功能。';
$messages['help_template_http_cache_enabled'] = '是否啟用對HTTP連結要求的快取支援。啟用此功能LifeType只會傳送必要的內容，可以節省網路頻寬。';
$messages['help_allow_php_code_in_templates'] = '允許在Smarty 模版中的{php}...{/php}區塊置入原生PHP程式碼(native PHP code)';
// urls
$messages['help_request_format_mode'] = '如果您設定為「一般網址」，那麼系統所呈現的網址，就會使用將參數以get方式傳入的一般方式。如果您選用「讓搜尋引擎易於搜尋的簡潔網址」，那麼就會讓網址變得簡潔，搜尋引擎也容易取得您網站上的內容，不過您的Apache伺服器必須要能夠接受.htaccess檔案中的覆寫設定。如果使用自訂網址，請調整下方的設定。';
$messages['plain'] = '一般網址';
$messages['search_engine_friendly'] = '讓搜尋引擎易於搜尋的簡潔網址';
$messages['custom_url_format'] = '自訂網址';
$messages['help_permalink_format'] = '當使用自訂網址時，靜態連結網址格式。';
$messages['help_category_link_format'] = '當使用自訂網址時，網站連結分類網址格式。';
$messages['help_blog_link_format'] = '當使用自訂網址時，網誌連結網址格式。';
$messages['help_archive_link_format'] = '當使用自訂網址時，文章彙整連結網址格式。';
$messages['help_user_posts_link_format'] = '當使用自訂網址時，特定使用者發表的文章連結網址格式。';
$messages['help_post_trackbacks_link_format'] = '當使用自訂網址時，引用連結網址格式。';
$messages['help_template_link_format'] = '當使用自訂網址時，自訂靜態模版連結網址格式。';
$messages['help_album_link_format'] = '當使用自訂網址時，資料夾連結網址格式。';
$messages['help_resource_link_format'] = '當使用自訂網址時，檔案連結網址格式。';
$messages['help_resource_preview_link_format'] = '當使用自訂網址時，檔案預覽連結網址格式。';
$messages['help_resource_medium_size_preview_link_format'] = '當使用自訂網址時，中型檔案預覽連結網址格式。';
$messages['help_resource_download_link_format'] = '當使用自訂網址時，檔案下載連結網址格式。';
// email
$messages['help_check_email_address_validity'] = '在使用者註冊申請新的網誌站台時，是否要認證他所填寫的電子郵件信箱是否正確。';
$messages['help_email_service_enabled'] = '使用或停用用來寄送通知信函的電子郵件服務。';
$messages['help_post_notification_source_address'] = '系統通知信函的寄件人電子郵件信箱。';
$messages['help_email_service_type'] = '用來寄送電子郵件的方式，請在各種方法選擇其中之一。';
$messages['help_smtp_host'] = '如果您選用SMTP寄送電子郵件，請輸入您要用來發送郵件的主機。';
$messages['help_smtp_port'] = '前項設定的SMTP主機連接埠（port）';
$messages['help_smtp_use_authentication'] = 'SMTP主機是否需要授權認證。如果需要的話，請繼續填寫下面兩項設定。';
$messages['help_smtp_username'] = '如果SMTP主機需要授權認證，請填寫使用者帳號。';
$messages['help_smtp_password'] = '如果SMTP主機需要授權認證，請填寫使用者密碼。';
// helpers
$messages['help_path_to_tar'] = '「tar」指令所在目錄。(用來解壓縮使用 .tar.gz 或 .tar.gz2格式壓縮的模版包)';
$messages['help_path_to_gzip'] = '「gzip」指令所在目錄。(用來解壓縮使用 .tar.gz 格式壓縮的模版包)';
$messages['help_path_to_bz2'] = '「bzip2」指令所在目錄。(用來解壓縮使用 .tar.gz2格式壓縮的模版包)';
$messages['help_path_to_unzip'] = '「unzip」指令所在目錄。(用來解壓縮使用 .zip格式壓縮的模版包)';
$messages['help_unzip_use_native_version'] = '使用PHP內建的版本來解壓縮 .zip 的檔案';
// uploads
$messages['help_uploads_enabled'] = '啟用或停用上傳檔案功能。這個功能會影響到使用者能否上傳新的模版安裝包，以及在模版中添加新的檔案。';
$messages['help_maximum_file_upload_size'] = '使用者上傳檔案大小的上限。';
$messages['help_upload_forbidden_files'] = '禁止使用者上傳的檔案類型。如果有多個不同的檔案類型，請在不同的類型間用空白區隔。也可使用\'*\' and \'?\'的方式。';
// interfaces
$messages['help_xmlrpc_api_enabled'] = '啟用或停用XMLRPC介面。XMLRPC介面的用途是可以讓您使用桌面網誌寫作工具出版網誌文章。';
$messages['help_rdf_enabled'] = '啟用或停用產生RSS新聞交換檔案功能。';
$messages['help_default_rss_profile'] = '預設的RSS/RDF新聞交換格式';
// security
$messages['help_security_pipeline_enabled'] = '啟用系統安全功能。如果您關閉了這個選項，那麼所有的系統安全功能都會停用，如果您想要關閉一些系統安全功能，建議您將這個設定設為開啟，然後在以下的選項中，逐一停用我們不需要的系統安全功能項目。';
$messages['help_maximum_comment_size'] = '迴響留言的內文字元數上限。';
// bayesian filter
$messages['help_bayesian_filter_enabled'] = '啟用或停用貝氏過濾機制。';
$messages['help_bayesian_filter_spam_probability_treshold'] = '被認定為是垃圾迴響留言的數值下限。設定範圍在0.01到0.99之間。';
$messages['help_bayesian_filter_nonspam_probability_treshold'] = '設定迴響留言是正常留言的數值上限。任何符合在前一設定與本設定之間數值的留言迴響，都會被認定是正常而非垃圾留言。';
$messages['help_bayesian_filter_min_length_token'] = '在多少字元數以上才會啟動貝氏過濾機制。';
$messages['help_bayesian_filter_max_length_token'] = '貝氏過濾機制可以處理的最多字元數上限。';
$messages['help_bayesian_filter_number_significant_tokens'] = '在訊息中必須要有多少顯著有意義的文字。';
$messages['help_bayesian_filter_spam_comments_action'] = '處理垃圾留言的方法。您可以直接清理這些垃圾留言（不會存進資料庫中），或是保存這些垃圾留言，但是加上垃圾留言標示標示。建議當您的過濾機制在還沒有妥善建立阻擋規則時，先用後者。';
$messages['keep_spam_comments'] = '保存垃圾迴響';
$messages['throw_away_spam_comments'] = '清理垃圾迴響';
// resources
$messages['help_resources_enabled'] = '啟用或關閉檔案中心功能。';
$messages['help_resources_folder'] = '用來存放檔案中心的目錄。這個目錄不一定要在網頁目錄下。如果您不希望別人直接瀏覽您的檔案目錄，您可以把這個目錄設定到其他地方。';
$messages['help_thumbnail_method'] = '您用來產生縮圖的後端系統。如果使用PHP，GD的支援是必須的。';
$messages['help_path_to_convert'] = '用來產生縮圖的系統工具路徑。如果您要使用ImageMagick，那麼您必須接著填寫ImageMagick的工具程式路徑。';
$messages['help_thumbnail_format'] = '在產生預覽縮圖時所使用的預設格式。如果您選擇「與原始影像相同」，那麼預覽縮圖就會儲存成與原始影像相同的格式。';
$messages['help_thumbnail_height'] = '縮圖預設高度。';
$messages['help_thumbnail_width'] = '縮圖預設寬度。';
$messages['help_medium_size_thumbnail_height'] = '中型縮圖預設高度';
$messages['help_medium_size_thumbnail_width'] = '中型縮圖預設寬度';
$messages['help_thumbnails_keep_aspect_ratio'] = '縮圖是否保持原始比例。';
$messages['help_thumbnail_generator_force_use_gd1'] = '是否強迫LifeType使用GD1函數來產生縮圖';
$messages['help_thumbnail_generator_user_smoothing_algorithm'] = '是否使用演算法來使縮圖畫面更平順。只有當縮圖產生工具是GD時才適用。';
$messages['help_resources_quota'] = '全域檔案容量限額';
$messages['help_resource_server_http_cache_enabled'] = '當 HTTP 請求檔頭為"If-Modified-Since"啟用快取支援。啟用此功能來節省網路頻寬。';
$messages['help_resource_server_http_cache_lifetime'] = '客戶端可以使用快取檔案的時間(單位：千分之一秒)';
$messages['same_as_image'] = '與原始影像相同';
// search
$messages['help_search_engine_enabled'] = '啟用或停用搜尋引擎';
$messages['help_search_in_custom_fields'] = '搜尋包含自訂欄位';
$messages['help_search_in_comments'] = '搜尋包含迴響';

// cleanup
$messages['purge'] = '清除';
$messages['cleanup_spam'] = '清除垃圾迴響';
$messages['cleanup_spam_help'] = '這會清除所有被使用者標示為垃圾的迴響。被清除的垃圾迴響將無法回復。';
$messages['spam_comments_purged_ok'] = '垃圾迴響已順利清除。';
$messages['cleanup_posts'] = '清除文章';
$messages['cleanup_posts_help'] = '這會清除所有被使用者標示為刪除的文章。 被清除的文章將無法回復。';
$messages['posts_purged_ok'] = '文章已順利清除。';
$messages['purging_error'] = '清理時發生錯誤。';

/// summary ///
// front page
$messages['summary'] = '彙整';
$messages['register'] = '註冊';
$messages['summary_welcome'] = '歡迎!';
$messages['summary_most_active_blogs'] = '最活躍網誌';
$messages['summary_most_commented_articles'] = '最多迴響文章';
$messages['summary_most_read_articles'] = '最多人閱讀文章';
$messages['password_forgotten'] = '忘記密碼?';
$messages['summary_newest_blogs'] = '最新建立的網誌';
$messages['summary_latest_posts'] = '最新發表的文章';
$messages['summary_search_blogs'] = '搜尋網誌';

// blog list
$messages['updated'] = '更新';
$messages['total_reads'] = '瀏覽總次數';

// blog profile
$messages['blog'] = '網誌';
$messages['latest_posts'] = '最新發表的文章';

// registration
$messages['register_step0_title'] = '服務條款';
$messages['agreement'] = '同意條款';
$messages['decline'] = '不接受';
$messages['accept'] = '接受';
$messages['read_service_agreement'] = '請詳細閱讀服務條款，如果你同意以上條款請按下接受鍵。';
$messages['register_step1_title'] = '建立使用者 [1/4]';
$messages['register_step1_help'] = '首先你必須先建立一個使用者帳號來取得一個網誌，這個使用者擁有該網誌，同時可以進行所有網誌設定功能。';
$messages['register_next'] = '下一步';
$messages['register_back'] = '上一步';
$messages['register_step2_title'] = '建立網誌 [2/4]';
$messages['register_blog_name_help'] = '幫你的網誌取個名稱';
$messages['register_step3_title'] = '選擇一個模版[3/4]';
$messages['step1'] = '步驟 1';
$messages['step2'] = '步驟 2';
$messages['step3'] = '步驟 3';
$messages['register_step3_help'] = '請選擇一個模版做為網誌的預設模版。只要你不喜歡，你可以隨時把它換掉。';
$messages['error_must_choose_template'] = '請選擇一個模版';
$messages['select_template'] = '選取模版';
$messages['register_step5_title'] = '恭喜你! [4/4]';
$messages['finish'] = '註冊完成';
$messages['register_need_confirmation'] = '一封包含註冊[確認訊息連結]的電子郵件已經寄到你的電子信箱中。請盡快點選該連結來開始你的blogging生活！';
$messages['register_step5_help'] = '恭喜你，新的使用者帳號及網誌已經順利建立！';
$messages['register_blog_link'] = '如果你要看一看你的新網誌，你現在可以到<a href="%2$s">%1$s</a>這裡看一看。';
$messages['register_blog_admin_link'] = '如果你想要立刻開始發表文章，請點選連結到 <a href="admin.php">管理介面</a>';
$messages['register_error'] = '過程中有錯誤發生！';
$messages['error_registration_disabled'] = '很抱歉，網站管理者停用註冊新網誌的功能。';
// registration article topic and text
$messages['register_default_article_topic'] = '恭喜！';
$messages['register_default_article_text'] = '如果你可以看到這篇文章，表示註冊過程已經順利完成。現在你可以開始blogging了！';
$messages['register_default_category'] = '一般';
// confirmation email
$messages['register_confirmation_email_text'] = '請點選下面的連結來啟用你的網誌：:

%s

祝你有個美好的一天！';
$messages['error_invalid_activation_code'] = '很抱歉，確認碼不正確！';
$messages['blog_activated_ok'] = '恭喜，你的使用者帳號和網誌已經順利啟用了！';
// forgot your password?
$messages['reset_password'] = '重設密碼';
$messages['reset_password_username_help'] = '你要重設那個使用者的密碼？';
$messages['reset_password_email_help'] = '使用者用來註冊的電子郵件位址';
$messages['reset_password_help'] = '使用下方的表單來重設密碼。請輸入使用者名稱及註冊時使用的電子郵件位址。';
$messages['error_resetting_password'] = '重設密碼時發生錯誤。請檢查輸入的資料再重試一次。';
$messages['reset_password_error_incorrect_email_address'] = '電子郵件位址錯誤或著這不是你註冊時使用的電子郵件。';
$messages['password_reset_message_sent_ok'] = '一封有著重設密碼連結的電子郵件已經送到你的電子郵件信箱，請點選該連結來重設密碼。';
$messages['error_incorrect_request'] = '網址中的參數不正確。';
$messages['change_password'] = '重設密碼';
$messages['change_password_help'] = '請輸入新密碼及確認密碼';
$messages['new_password'] = '新密碼';
$messages['new_password_help'] = '在這裡輸入新密碼';
$messages['password_updated_ok'] = '你的密碼已經順利更新';

// Suggested by BCSE, some useful messages that not available in official locale
$messages['upgrade_information'] = '您所使用的瀏覽器未符合網頁設計標準，因此本網頁將以純文字模式顯示。如欲以最佳的排版方式瀏覽本站，請考慮<a href="http://www.webstandards.org/upgrade/" title="The Web Standards Project\'s Browser Upgrade initiative">升級</a>您的瀏覽器。';
$messages['jump_to_navigation'] = '移動到導覽列。';
$messages['comment_email_never_display'] = '系統會自動為你設定分行，且不會顯示你留下的郵件地址。';
$messages['comment_html_allowed'] = '可使用之 <acronym title="Hypertext Markup Language">HTML</acronym> 標籤如下：&lt;<acronym title="用途：超連結">a</acronym> href=&quot;&quot; title=&quot;&quot; rel=&quot;&quot;&gt; &lt;<acronym title="用途：頭字語標註">acronym</acronym> title=&quot;&quot;&gt; &lt;<acronym title="用途：引用文字">blockquote</acronym> cite=&quot;&quot;&gt; &lt;<acronym title="用途：刪除線">del</acronym>&gt; &lt;<acronym title="用途：斜體">em</acronym>&gt; &lt;<acronym title="用途：底線">ins</acronym>&gt; &lt;<acronym title="用途：粗體">strong</acronym>&gt;';
$messages['trackback_uri'] = '這篇文章的引用連結網址：';

$messages['xmlrpc_ping_ok'] = 'XMLRPC Ping sent successfully: ';
$messages['error_sending_xmlrpc_ping'] = 'There was an error sending the XMLRPC ping to: ';
$messages['error_sending_xmlrpc_ping_message'] = 'There was an error sending the XMLRPC ping: ';

//
// new strings for 1.1
//
$messages['error_incorrect_trackback_id'] = '引用的識別碼不正確';
$messages['error_marking_trackback_as_spam'] = '標記垃圾引用時發生錯誤';
$messages['trackback_marked_as_spam_ok'] = '標記垃圾引用成功';
$messages['error_marking_trackback_as_nonspam'] = '取消標記垃圾引用時發生錯誤';
$messages['trackback_marked_as_nonspam_ok'] = '取消標記垃圾引用成功';
$messages['upload_here'] = '上傳到這裡';
$messages['cleanup_users'] = '刪除使用者';
$messages['cleanup_users_help'] = '這個操作會把所有被管理員標示為(已刪除)的使用者完全刪除，同時也會把這些使用者的所有網誌也刪除，包括所有包含在網誌裡的任何東西。如果這些使用者有在其他網誌寫文章的權限，那他們在其他網誌裡所寫的文章也會一起被刪除。當使用者被刪除時，這些動作是不可能恢復的。';
$messages['users_purged_ok'] = '成功刪除使用者';
$messages['cleanup_blogs'] = '刪除網誌';
$messages['cleanup_blogs_help'] = '這個操作會把所有被管理員標示為(已刪除)的網誌完全刪除，包括所有包含在網誌裡的任何東西。當網誌被刪除時，這些動作是不可能恢復的。';
$messages['blogs_purged_ok'] = '成功刪除網誌';
$messages['help_use_http_accept_language_detection'] = '大部分的瀏覽器像 Mozilla Firefox 、 Safari 或 Internet Explorer 至少會傳送一個使用者<i>應該</i>瞭解的語言碼。如果啟用這個功能，而且該語言是可用的， LifeType 會試著以這個請求的語言來服務使用者。[預設值 = 否]';

$messages['error_invalid_blog_category'] = '不合法的網誌分類';
$messages['error_adding_blog_category'] = '新增網誌分類時發生錯誤';
$messages['newBlogCategory'] = '新增網誌分類';
$messages['editBlogCategories'] = '編輯網誌分類';
$messages['blog_category_added_ok'] = '成功新增網誌分類';
$messages['error_blog_category_has_blogs'] = '已經有一些網誌指定到網誌分類 "%s" 。請先編輯這些網誌之後再試一次';
$messages['error_deleting_blog_category'] = '刪除網誌分類 "%s" 時發生錯誤';
$messages['blog_category_deleted_ok'] = '成功刪除網誌分類 "%s"';
$messages['blog_categories_deleted_ok'] = '成功刪除網誌分類 "%s"';
$messages['error_deleting_blog_category2'] = '刪除 id 為 %s 的網誌分類時發生錯誤';
$messages['blog_category'] = '網誌分類';
$messages['blog_category_help'] = '替網誌指定一個全域網誌分類';

$messages['help_use_captcha_auth'] = '在註冊程序使用 CAPTCHA 機制，以防止自動註冊機器人程式';
$messages['help_skip_dashboard'] = '讓使用者跳過管理面板，直接進入他目前所擁有的第一個網誌';

$messages['manageGlobalArticleCategory'] = '全域文章分類';
$messages['newGlobalArticleCategory'] = '新增全域文章分類';
$messages['editGlobalArticleCategories'] = '編輯全域文章分類';
$messages['global_category_name_help'] = '新的全域文章分類的名稱';
$messages['global_category_description_help'] = '新的全域文章分類的詳細描述';
$messages['error_incorrect_global_category_id'] = '不合法的全域文章分類';
$messages['global_category_deleted_ok'] = '成功刪除全域文章分類 "%s"';
$messages['global_category_added_ok'] = '成功新增全域文章分類 "%s"';
$messages['error_deleting_global_category2'] = '刪除 id 為 %S 的全域文章分類時發生錯誤';

$messages['help_page_suffix_format'] = '支援分頁時，加在網址尾端的字尾';

$messages['help_final_size_thumbnail_width'] = '上傳圖檔的最後寬度。使用空白或 0 則使用圖檔的原始大小';
$messages['help_final_size_thumbnail_height'] = '上傳圖檔的最後高度。使用空白或 0 則使用圖檔的原始大小';
$messages['error_comment_too_big'] = '回響內容太長';
$messages['error_you_have_been_blocked'] = '阻擋：這個請求並沒有完成';
$messages['created'] = '已建立';
$messages['view'] = '閱讀';
$messages['editUser'] = '編輯使用者';
$messages['help_urlize_word_separator'] = '建立 LifeType 相關的連結時，用於連結單字的字元。如果啟用次網域網址時，這也會使用於產生網址裡的網站名稱。[預設值 = -]';
$messages['help_summary_template_cache_lifetime'] = '彙整頁面快取的有效時間。若設為 \'0\' ，只要有資料更新就會更新彙整頁面的快取。如果設為其他值，則會等到這段時間過了之後再更新彙整頁面的快取。[預設值 = 0]';
$messages['register_default_album_name'] = '一般';
$messages['register_default_album_description'] = '使用這個資料夾上傳新圖檔';
$messages['show_in_summary'] = '在彙整頁面中顯示';
$messages['show_in_summary_help'] = '在彙整頁面中顯示這個網誌';

$messages['saving_message'] = '儲存中 ...';
$messages['show_option_panel'] = '顯示文章選項';
$messages['hide_option_panel'] = '隱藏文章選項';

$messages['quick_launches'] = '快捷列';

$messages['confirmation_message_resent_ok'] = '註冊確認信件已成功地重新寄出';

$messages['goto_blog_page'] = '打開 %s 首頁';

$messages['help_num_blogs_per_user'] = '擁有者可以從管理介面建立網誌的數目';

$messages['massive_change_option'] = '大量修改選項';
$messages['show_massive_change_option'] = '顯示大量修改選項';
$messages['hide_massive_change_option'] = '隱藏大量修改選項';
$messages['change_status'] = '修改狀態';
$messages['change_category'] = '修改分類';
$messages['error_updating_comment_no_comment'] = '更新迴響時發生錯誤。迴響 #%s 不存在。';
$messages['error_updating_comment_wrong_blog'] = '更新迴響時發生錯誤。迴響 (%s)不在這篇文章上。';
$messages['error_updating_comment'] = '更新迴響 (%s) 發生錯誤。';
$messages['error_updating_comment_already_updated'] = '(%s) 沒有進行任何更新。';
$messages['comment_updated_ok'] = '迴響已順利更新。';
$messages['comments_updated_ok'] = '%s 迴響已順利更新。';

$messages['error_post_status'] = '請選擇文章狀態。';
$messages['error_comment_status'] = '請選擇迴響狀態。';
$messages['admin_mode'] = '管理員模式';
$messages['administrate_user_blog'] = '管理這個網誌';
$messages['trackbacks_updated_ok'] = '%s 個引用已成功的更新';
$messages['trackback_updated_ok'] = '引用已成功的更新';
$messages['error_trackback_status'] = '請選擇一個合法的狀態';
$messages['select'] = '選擇';
$messages['remove_selected'] = '取消選取';

$messages['notification_subject'] = 'LifeType 通知系統';
$messages['error_no_trackback_links_sent'] = '警告：沒有送出任何引用';

$messages['help_http_cache_lifetime'] = '客戶端快取的有效時間，以秒為單位。 (瀏覽器在這段期間不會再連線到網頁主機，而直接使用本地端的快取)。這個做法將加快瀏覽網頁的速度，但是將延後文章和迴響的出現時間。[預設值 = 1800]';

$messages['trackbacks_no_trackback'] = '送出引用到下面的網址失敗：';

$messages['error_comment_spam_throw_away'] = '你不能發表這個訊息。反垃圾過濾系統已經將這個訊息阻擋下來。';
$messages['error_comment_spam_keep'] = '反垃圾過濾系統已經將你的迴響放到佇列裡等待網誌擁有者的審核。';

$messages['blog_categories'] = '網誌分類';
$messages['global_article_categories'] = '全站文章分類'; 

$messages['help_force_posturl_unique'] = '強迫網誌裡所有文章的網址都是唯一的。這只有當你更改網址並且將日期部份從網址中移除時才需要。[預設值 = 否]';

$messages['default_send_notification'] = '預設發送通知';

$messages['enable_pull_down_menu'] = '下拉式選單';
$messages['enable_pull_down_menu_help'] = '啟用或關閉下拉式選單。';

$messages['change_album'] = '修改檔案夾'; 

$messages['warning_autosave_message'] = '<img src="imgs/admin/icon_warning-16.png" alt="Error" class="InfoIcon"/><p class="ErrorText">你好像有之前尚未存檔的文章。如果你還想繼續編輯，你可以 <a href="#" onclick="restoreAutoSave();">取回未存檔文章繼續編輯</a> 或是 <a href="#" onclick="eraseAutoSave();">把他刪除</a> 。</p>';

$messages['check_username'] = '檢查使用者名稱';
$messages['check_username_ok'] = '恭喜！這個使用者名稱還沒有任何人使用。';
$messages['error_username_exist'] = '抱歉！這個使用者名稱已經被別人用了，試試其他的吧！'; 

$messages['error_rule_email_dns_server_temp_fail'] = '發生暫時性的錯誤，請稍後再試！';
$messages['error_rule_email_dns_server_unreachable'] = '電子郵件主機無法連線';
$messages['error_rule_email_dns_not_permitted'] = '不被允許的電子郵件地址'; 

$messages['blog_users_help'] = '可以存取這個網誌的使用者。請從左邊選取使用者將他移到右邊提供該使用者存取網誌的權限。'; 

$messages['summary_welcome_paragraph'] = '請將此處修改為你希望你的使用者看到的歡迎訊息，或將這部份刪除並重新安排整個頁面。這個頁面的模版在 templates/summary 裡面，你可以自由地依你的喜好修改他。'; 

$messages['first_day_of_week'] = 1;
$messages['first_day_of_week_label'] = '每一週的開始';
$messages['first_day_of_week_help'] = '在首頁月曆中的顯示方式。'; 

$messages['help_subdomains_base_url'] = '當次網域設定啟用時，這個網址將用來替代系統網址。使用 {blogname}來取得網誌名稱及{username}取得網誌使用者名稱以及{blogdomain}，用來產生連結到網誌的網址。'; 

$messages['registration_default_subject'] = 'LifeType 註冊確認';

$messages['error_invalid_subdomain'] = '不合法的子網域名稱，或是名稱不是唯一的';
$messages['register_blog_domain_help'] = '你的新網誌要使用的名稱和子網域';
$messages['domain'] = '網域(Domain)';
$messages['help_subdomains_available_domains'] = '允許的主網域名稱清單。主網域名稱請以一個空格分隔。使用者會看到一個包含這些值的下拉式選單，並加入他所要使用的主網域。只有當你啟用子網域並且在上方的 subdomain_base_url 使用了 (blogdomain) 。如果你允許任何的網域，則使用 \'?\'';
$messages['subdomains_any_domain'] = '<- 啟用多重網域。輸入完整的網域名稱';
$messages['error_updating_blog_subdomain'] = '更新子網域時發生錯誤，請檢查資料並再試一次。';
$messages['error_updating_blog_main_domain'] = '更新主網域設定時發生錯誤。這可是管理者的一些系統參數調整錯誤造成的。';

$messages['monthsshort'] = Array( '元', '二', '三', '四', '五', '六', '七', '八', '九', '十', '十一', '十二' );
$messages['weekdaysshort'] = Array( '日', '一', '二', '三', '四', '五', '六' );

$messages['search_type'] = '搜尋方式';
$messages['posts'] = '文章';
$messages['blogs'] = '網誌';
$messages['resources'] = '檔案';
$messages['upload_in_progress'] = '檔案正在上傳中，請稍後 ...';
$messages['error_incorrect_username'] = '使用者名稱不正確。可能這個使用者名稱已經有人使用了，或是它的長度超過 15 個字元。';

$messages['Miscellaneous'] = '其他設定';
$messages['Plugins'] = '外掛程式';

$messages['auth_img'] = '認證碼';
$messages['auth_img_help'] = '請輸入你在圖片中所看到的文字。';

$messages['global_category'] = '全域文章分類';
$messages['global_article_category_help'] = '替文章指定一個全域文章分類。';

$messages['password_reset_subject'] = 'LifeType 重新設置密碼';

//
// new strings for LifeType 1.2
//
$messages['auth'] = '驗證';
$messages['authenticated'] = '已登入';
$messages['dropdown_list_field'] = '下拉式選項';
$messages['values'] = '數值';
$messages['field_values'] = '這些值會變成這個下拉式選單中的選項。其中第一個值會是下拉式選單中的預設值。';

$messages['permission_added_ok'] = '權限已經順利新增。';
$messages['core_perm'] = '主要權限';
$messages['admin_only'] = '管理者限定';
$messages['permissionsList'] = '權限列表';
$messages['newPermission'] = '新增權限';
$messages['permission_name_help'] = '必須是系統中唯一的權限名稱';
$messages['permission_description_help'] = '權限的簡短描述';
$messages['core_perm_help'] = '如果這個權限是主要權限，他將無法被刪除。';
$messages['admin_only_help'] = '這個權限只能指定給管理者。';
$messages['error_adding_new_permission'] = '新增權限時發生錯誤，請檢查你的資料。';
$messages['error_incorrect_permission_id'] = '權限 ID 不正確。';
$messages['error_permission_cannot_be_deleted'] = '權限 "%s" 無法刪除。因為他已經至少被一個使用者使用或者是主要權限。';
$messages['error_deleting_permission'] = '刪除權限 "%s" 發生錯誤。';
$messages['permission_deleted_ok'] = '權限 "%s" 已經順利刪除。';
$messages['permissions_deleted_ok'] = '%s 權限已經順利刪除。';
$messages['error_deleting_permission2'] = '刪除權限 ID "%s" 時發生錯誤。';

$messages['help_hard_show_posts_max'] = '首頁顯示文章數量的最大值。如果使用者的設定超過這個數值，它將會被忽略，並且直接使用這個數值作為限制。[ 預設 = 50 ]';
$messages['help_hard_recent_posts_max'] = '首頁顯示近期文章數量的最大值。如果使用者的設定超過這個數值，它將會被忽略，並且直接使用這個數值作為限制。[ 預設 = 25 ]';

$messages['error_permission_required'] = '你沒有進行這個動作的權限。';
$messages['user_permissions_updated_ok'] = '使用者權限順利更新。';

// blog permissions
$messages['add_album_desc'] = '新增資料夾';
$messages['add_blog_template_desc'] = '新增網誌模版';
$messages['add_blog_user_desc'] = '新增網誌作者';
$messages['add_category_desc'] = '新增文章分類';
$messages['add_custom_field_desc'] = '新增自訂欄位';
$messages['add_link_desc'] = '新增連結網址';
$messages['add_link_category_desc'] = '新增網站連結分類';
$messages['add_post_desc'] = '新增文章';
$messages['add_resource_desc'] = '新增檔案';
$messages['blog_access_desc'] = '訪問這個網誌';
$messages['update_album_desc'] = '更新與刪除資料夾';
$messages['update_blog_desc'] = '更新與刪除網誌';
$messages['update_blog_template_desc'] = '更新與刪除網誌模版';
$messages['update_blog_user_desc'] = '更新與刪除網誌作者權限';
$messages['update_category_desc'] = '更新與刪除文章分類';
$messages['update_comment_desc'] = '更新與刪除迴響';
$messages['update_custom_field_desc'] = '更新與刪除自訂欄位';
$messages['update_link_desc'] = '更新與刪除連結網址';
$messages['update_link_category_desc'] = '更新與刪除網站連結分類';
$messages['update_post_desc'] = '更新與刪除文章';
$messages['update_resource_desc'] = '更新與刪除檔案';
$messages['update_trackback_desc'] = '更新與刪除引用';
$messages['view_blog_templates_desc'] = '瀏覽網誌模版列表';
$messages['view_blog_users_desc'] = '瀏覽網誌作者列表';
$messages['view_categories_desc'] = '瀏覽文章分類列表';
$messages['view_comments_desc'] = '瀏覽迴響列表';
$messages['view_custom_fields_desc'] = '瀏覽自訂欄位列表';
$messages['view_links_desc'] = '瀏覽連結網址列表';
$messages['view_link_categories_desc'] = '瀏覽網站連結分類列表';
$messages['view_posts_desc'] = '瀏覽文章列表';
$messages['view_resources_desc'] = '瀏覽檔案列表';
$messages['view_trackbacks_desc'] = '瀏覽引用列表';
$messages['login_perm_desc'] = '允許登入管理介面';
// admin permissions
$messages['add_blog_category_desc'] = '新增網誌分類';
$messages['add_global_article_category_desc'] = '新增全域文章分類';
$messages['add_locale_desc'] = '新增語系';
$messages['add_permission_desc'] = '新增權限';
$messages['add_site_blog_desc'] = '新增網誌';
$messages['add_template_desc'] = '新增全域模版';
$messages['add_user_desc'] = '新增使用者';
$messages['edit_blog_admin_mode_desc'] = '修改其他網誌 (管理者模式)';
$messages['purge_data_desc'] = '清除資料';
$messages['update_blog_category_desc'] = '更新與刪除網誌分類';
$messages['update_global_article_category_desc'] = '更新與刪除全域文章分類';
$messages['update_global_settings_desc'] = '更新與刪除全域設定';
$messages['update_locale_desc'] = '更新與刪除語系';
$messages['update_permission_desc'] = '更新與刪除權限';
$messages['update_plugin_settings_desc'] = '更新與刪除外掛程式設定';
$messages['update_site_blog_desc'] = '更新與刪除網誌';
$messages['update_template_desc'] = '更新與刪除全域模版';
$messages['update_user_desc'] = '更新與刪除使用者';
$messages['view_blog_categories'] = '瀏覽網誌分類列表';
$messages['view_global_article_categories_desc'] = '瀏覽全域文章分類列表';
$messages['view_global_settings_desc'] = '瀏覽全域設定';
$messages['view_locales_desc'] = '瀏覽語系列表';
$messages['view_permissions_desc'] = '瀏覽權限列表';
$messages['view_plugins_desc'] = '瀏覽外掛程式列表';
$messages['view_site_blogs_desc'] = '瀏覽網誌列表';
$messages['view_templates_desc'] = '瀏覽全域模版列表';
$messages['view_users_desc'] = '瀏覽使用者列表';
$messages['update_blog_stats_desc'] = '更新與刪除逆向連結';
$messages['manage_admin_plugins_desc'] = '管理全域外掛程式設定';

$messages['summary_welcome_msg'] = '歡迎， %s！';
$messages['summary_go_to_admin'] = '管理者介面';

$messages['error_can_only_update_own_articles'] = '你的權限只允許你修改自己的文章。';
$messages['update_all_user_articles_desc'] = '允許修改其他網誌作者的文章。';
$messages['error_can_only_view_own_articles'] = '你的權限只允許你瀏覽自己的文章。';
$messages['view_all_user_articles_desc'] = '允許瀏覽其他網誌作者的文章。';
$messages['error_fetching_permission'] = '讀取權限資料時發生錯誤。';
$messages['editPermission'] = '修改權限';
$messages['error_updating_permission'] = '更新權限時發生錯誤。';
$messages['permission_updated_ok'] = '權限已順利更新。';
$messages['error_adding_permission'] = '新增權限時發生錯誤。';
$messages['error_cannot_login'] = '抱歉，你不被允許登入！';
$messages['admin_user_permissions_help'] = '指定使用者具有管理全站的權限。';

$messages['permissions'] = '權限列表';
$messages['blog_user_permissions_help'] = '指定使用者具有管理網誌的權限。';
$messages['pluginSettings'] = '外掛程式設定';
$messages['user_can_override'] = '使用者可以覆蓋外掛程式全域設定';
$messages['user_cannot_override'] = '使用者不能覆蓋外掛全域程式設定';
$messages['global_plugin_settings_saved_ok'] = '外掛程式全域設定已順利更新。';
$messages['error_updating_global_plugin_settings'] = '更新外掛程式全域設定時發生錯誤。';
$messages['error_incorrect_value'] = '這個數值不正確。';
$messages['parameter'] = '參數';
$messages['value'] = '設定值';
$messages['override'] = '覆蓋';
$messages['editCustomField'] = '編輯自訂欄位';
$messages['view_blog_stats_desc'] = '瀏覽網誌統計';
$messages['manage_plugins_desc'] = '管理網誌外掛程式';

$messages['error_global_category_has_articles'] = '無法刪除這個全域文章分類，因為該分類下還有文章。';
$messages['error_adding_global_article_category'] = '新增全域文章分類時發生錯誤。請檢查輸入的資料，再重試一次。';

$messages['temp_folder_reset_ok'] = '清理暫存目錄已經順利清理。';
$messages['cleanup_temp_help'] = '清理暫存目錄中所有網誌的網頁快取與資料快取。';
$messages['cleanup_temp'] = '清理暫存目錄。';

$messages['comment_only_auth_users'] = '迴響使用者驗證';
$messages['comment_only_auth_users_help'] = '只有已經登入網誌的使用者才能夠迴響。';
$messages['show_comments_max'] = '最大每篇文章顯示迴響數目';
$messages['show_comments_max_help'] = '每篇文章顯示迴響數目的預設值 [ 預設 = 20 ]';
$messages['hard_show_comments_max_help'] = '每篇文章顯示迴響數目的預設值。如果使用者的設定超過這個數值，它將會被忽略，並且直接使用這個數值作為限制。[ 預設 = 50 ]';

$messages['error_resource_not_whitelisted_extension'] = '檔案類時不在系統允許的副檔名列表中。';
$messages['help_upload_allowed_files'] = '允許使用者上傳的檔案類型。如果有多個不同的檔案類型，請在不同的類型間用空白區隔。也可使用\'*\' and \'?\'的方式。 如果 upload_forbidden_file 與這個選項同時設定。允許使用者上傳的檔案類型 (upload_allowed_files) 將會優先於禁止使用者上傳的檔案類型 [Default = None]';

$messages['help_template_load_order'] = '預設模版載入順序。如果使用 \'優先載入預設模版\'，LifeType 會嘗試優先搜尋 ./templates/default/ 目錄下的模版，如果預設模版不存在，則載入使用者自訂模版。如果相同的模版同時存在這兩個地方，則優先採用預設模版。如果使用 \'優先載入使用者自訂模版\'，則使用者自訂模版將被優先使用。如果使用者自訂模版不存在，將使用預設模版。如果相同的模版同時存在這兩個地方，則優先採用使用者自訂模版。';
$messages['template_load_order_user_first'] = '優先載入預設模版';
$messages['template_load_order_default_first'] = '優先載入使用者自訂模版';

$messages['editBlogUser'] = '編輯網誌作者';

$messages['help_summary_service_name'] = '你的網站或是服務的名稱。這個名稱會使用在你的彙整首頁與 RSS 的輸出中。[ 預設值 = 空白 ]';

$messages['register_step2_help'] = '請提供建立網誌所需要的資訊。';

$messages['create_date'] = '建立時間';

$messages['insert_media'] = '插入檔案';
$messages['insert_more'] = '插入 "閱讀全文" 分隔';

$messages['purging_please_wait'] = '請耐心等候清理資料。本頁面會持續更新直到所有資料清理完畢，請勿中斷清理動作以免造成資料損壞。';

$messages['error_cannot_delete_last_blog_category'] = '您無法刪除最後一個網誌分類。';

$messages['help_logout_destination_url'] = '當使用者登出時所要顯示網頁的 URL 。例如，你提供服務的首頁。若是保持空白，則使用預設的 LifeType 登入頁。[ 預設值 = 空白 ]';
$messages['help_default_global_article_category_id'] = '預設的全域文章分類 ID。[ 預設值 = 空白 ]';
$messages['help_blog_does_not_exist_url'] = '當網誌不存在時所要顯示的網頁 URL。當網誌不存在時，你可以透過這一個選項將 URL 轉到某一個特定網址，而非系統預設的網誌。[ 預設值 = 空白 ]';

$messages['error_invalid_blog_name'] = '網誌名稱不正確。';

/* strings for /default/ templates */


$messages['help_forbidden_blognames'] = '列出所有不允許使用的網誌名稱。如果有多個不同的網誌名稱，請在不同的名稱間用空白區隔。 也可以使用正規表示是來表示。[ 預設值 = 空白 ]';

$messages['posts_updated_ok'] = '%s 篇文章已順利更新。';
$messages['error_updating_post2'] = '更新文章 ID %s 時發生錯誤。';
$messages['resources_updated_ok'] = '%s 個檔案已順利更新。';
$messages['error_updating_resource2'] = '更新檔案 ID %s 時發生錯誤。';
$messages['albums_updated_ok'] = '%s 個資料夾已順利更新。';
$messages['error_updating_album2'] = '更新資料夾 ID %s 時發生錯誤。';
$messages['links_updated_ok'] = '%s 網站連結已順利更新。';
$messages['error_updating_link2'] = '更新網站連結 ID %s 時發生錯誤。';

$messages['version'] = '版本';

$messages['error_resources_disabled'] = '抱歉！本網站的上傳功能已經被管理者關閉。';
$messages['help_login_admin_panel'] = '點選網誌名稱，進入網誌管理頁面。';

$messages['blog_updated_ok'] = '網誌 "%s" 已經順利更新。';
$messages['blogs_updated_ok'] = '%s 個網誌已經順利更新。';
$messages['error_updating_blog2'] = '更新網誌 ID = "%s" 時發生錯誤。';
$messages['error_updating_blog'] = '更新網誌 "%s" 時發生錯誤';

$messages['error_updating_user'] = '更新使用者 "%s" 時發生錯誤。';
$messages['user_updated_ok'] = '使用者 "%s" 已經順利更新。';
$messages['users_updated_ok'] = '%s 個使用者已經順利更新。';
$messages['eror_updating_user2'] = '更新使用者 "%s" 時發生錯誤。';

$messages['error_select_status'] = '請選擇合法的狀態。';
$messages['error_invalid_blog_name'] = '網誌名稱「%s」不正確。';

$messages['help_resources_naming_rule'] = '選擇檔案上傳後在主機的儲存方式。「原始檔案名稱」使用原來的檔案名稱來儲存上傳的檔案。「編碼檔案名稱」使用編碼過的檔案名稱 [BlogId]-[ResourceId].[Ext] 來儲存上傳的檔案。在 Windows 多字元下安裝 LifeType 請使用「編碼檔案名稱」。<strong>另外，當使用者開始上傳檔案後，請勿修改此選項，這會造成以上傳的檔案無法再被讀取。</strong> [預設 = 原始檔案名稱]';
$messages['original_file_name'] = '原始檔案名稱';
$messages['encoded_file_name'] = '編碼檔案名稱';

$messages['quick_permission_selection'] = '快速權限設定選單';
$messages['basic_blog_permission'] = '網誌作者可以新增、編修與刪除文章、連結與檔案';
$messages['full_blog_permission'] = '網誌作者可以跟網誌擁有者一樣，操作所有功能';

$messages['error_template_exist'] = '上傳模版時發生錯誤，「%s」模版已經存在。';

/// new strings in LT 1.2.2 ///
$messages['posted_by_help'] = '選擇文章作者';
$messages['insert_player'] = '插入播放器';

/// new strings in LT 1.2.3 ///
$messages['help_allow_javascript_blocks_in_posts'] = '允許在文章中使用 &lt;script&gt; 的標籤。tags. 請小心使用，允許使用 Javascript 的標籤可能會讓你的網誌產生安全上的漏洞 [預設 = 否]';

$messages['Versions'] = '版本';
$messages['incorrect_file_version_error'] = '下列的檔案內容有問題（可能上傳不完整或是被修改過）：';
$messages['lifetype_version'] = 'LifeType';
$messages['lifetype_version_help'] = '目前安裝的 LifeType 版本是：';
$messages['file_version_check'] = '檔案版本檢查';
$messages['file_version_check_help'] = '這個動作會檢查 LifeType 的核心檔案，主要是用來確定目前的檔案內容的確符合預期安裝的版本。如果你沒有對檔案進行任何的修改，
所有檔案的內容應該都會符合檢查的結果。請耐心等候，這個檢查需要花一點時間。';
$messages['check'] = '檢查';
$messages['all_files_ok'] = '所有檔案都正確。';

/// new strings for LT 1.2.4 ///
$messages['plugin_latest_version'] = '最新版本： ';
$messages['check_versions'] = '檢查版本';
$messages['lt_version_ok'] = '目前的 LifeType 是最新版的。';
$messages['lt_version_error'] = '最新版的 LifeType 是： ';
$messages['release_notes'] = '釋出紀錄';

$messages['kb'] = 'KB';
$messages['mb'] = 'MB';
$messages['gb'] = 'DB';
$messages['edit'] = '編輯';

/// new strings for LT 1.2.5 ///
$messages['bookmark_this_filter'] = '加到書籤';
$messages['help_trim_whitespace_output'] = '輸出時，移除所有 HTML 程式碼中的空白字元，這會讓輸出的 HTML 程式碼最多減少 40% 的大小。除非你非常在意他會稍稍的影響你伺服器的 CPU 效能，否則建議將他打開。 [ 預設 = Yes ]';
$messages['help_notify_new_blogs'] = '當有網誌新增時，通知網站管理者';
$messages['new_blog_admin_notification_text'] = '這是 LifeType 的網誌自動通知系統。

有一個新的網誌 "%1$s" (%2$s) 已經新增到你的 LifeType 網站中。

祝你有美好的一天。
';
?>