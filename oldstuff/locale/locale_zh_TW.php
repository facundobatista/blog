<?php

// set this to the encoding that should be used to display the pages correctly
$messages['encoding'] = 'utf-8';
$messages['locale_description'] = 'Traditional Chinese translation (UTF-8)';
// locale format, see Locale::formatDate for more information
$messages['date_format'] = '%d/%m/%Y %H:%M';

// days of the week
$messages['days'] = Array( '星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六' );
// -- compatibility, do not touch -- //
$messages['Monday'] = $messages['days'][1];
$messages['Tuesday'] = $messages['days'][2];
$messages['Wednesday'] = $messages['days'][3];
$messages['Thursday'] = $messages['days'][4];
$messages['Friday'] = $messages['days'][5];
$messages['Saturday'] = $messages['days'][6];
$messages['Sunday'] = $messages['days'][0];

// abbreviations
$messages['daysshort'] = Array( '日', '一', '二', '三', '四', '五', '六' );
// -- compatibility, do not touch -- //
$messages['Mo'] = $messages['daysshort'][1];
$messages['Tu'] = $messages['daysshort'][2];
$messages['We'] = $messages['daysshort'][3];
$messages['Th'] = $messages['daysshort'][4];
$messages['Fr'] = $messages['daysshort'][5];
$messages['Sa'] = $messages['daysshort'][6];
$messages['Su'] = $messages['daysshort'][0];

// months of the year
$messages['months'] = Array( '元月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月');
$messages['monthsshort'] = Array( '元月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月');
// -- compatibility, do not touch -- //
$messages['January'] = $messages['months'][0];
$messages['February'] = $messages['months'][1];
$messages['March'] = $messages['months'][2];
$messages['April'] = $messages['months'][3];
$messages['May'] = $messages['months'][4];
$messages['June'] = $messages['months'][5];
$messages['July'] = $messages['months'][6];
$messages['August'] = $messages['months'][7];
$messages['September'] = $messages['months'][8];
$messages['October'] = $messages['months'][9];
$messages['November'] = $messages['months'][10];
$messages['December'] = $messages['months'][11];
$messages['message'] = '訊息';
$messages['error'] = '錯誤';
$messages['date'] = '日期';

// miscellaneous texts
$messages['of'] = 'of';
$messages['recently'] = '近期文章';
$messages['comments'] = '迴響';
$messages['comment on this'] = '迴響';
$messages['my_links'] = '我的連結';
$messages['archives'] = '文章彙整';
$messages['search'] = '站內搜尋';
$messages['calendar'] = '日曆';
$messages['search_s'] = '搜尋';
$messages['search_this_blog'] = '搜尋網誌內容:';
$messages['about_myself'] = '自我介紹';
$messages['permalink_title'] = '文章彙整靜態連結網址';
$messages['permalink'] = '靜態連結網址';
$messages['posted_by'] = '作者';
$messages['reply'] = '回覆';
$messages['reply_string'] = 'Re: ';
$messages['category'] = '分類';

// add comment form
$messages['add_comment'] = '發表迴響';
$messages['comment_topic'] = '標題';
$messages['comment_text'] = '內容';
$messages['comment_username'] = '暱稱';
$messages['comment_email'] = '電子郵件';
$messages['comment_url'] = '個人網頁';
$messages['comment_send'] = '發表';
$messages['comment_added'] = '您的迴響已經順利發表。';
$messages['comment_add_error'] = '發表迴響時發生錯誤。';
$messages['article_does_not_exist'] = '本文章不存在。';
$messages['no_posts_found'] = '找不到文章。';
$messages['user_has_no_posts_yet'] = '該使用者還沒有發表過任何文章。';
$messages['back'] = '回到上一頁';
$messages['post'] = '文章';
$messages['trackbacks_for_article'] = '引用本文的文章標題：';
$messages['trackback_excerpt'] = '摘要';
$messages['trackback_weblog'] = '網誌';
$messages['search_results'] = '搜尋結果';
$messages['search_matching_results'] = '以下文章符合您的搜尋關鍵字：';
$messages['search_no_matching_posts'] = '找不到符合的文章。';
$messages['read_more'] = '(閱讀全文)';
$messages['syndicate'] = '新聞交換';
$messages['main'] = '主頁面';
$messages['about'] = '關於';
$messages['download'] = '下載';
$messages['error_incorrect_email_address'] = '電子郵件信箱格式錯誤。';
$messages['invalid_url'] = '網址格式錯誤，請輸入正確格式';

////// error messages /////
$messages['error_fetching_article'] = '找不到您所指定的文章。';
$messages['error_fetching_articles'] = '找不到您所指定的文章。';
$messages['error_fetching_category'] = '找不到您所指定的分類';
$messages['error_trackback_no_trackback'] = '尚未有人向本文發送引用通告。';
$messages['error_incorrect_article_id'] = '文章 ID 錯誤。';
$messages['error_incorrect_blog_id'] = '網誌站台 ID 錯誤。';
$messages['error_comment_without_text'] = '無迴響留言內容。';
$messages['error_comment_without_name'] = '您必須要填寫姓名或暱稱。';
$messages['error_adding_comment'] = '在將留言新增至資料庫時發生問題。';
$messages['error_incorrect_parameter'] = '參數不正確。';
$messages['error_parameter_missing'] = '您少傳遞了一項參數。';
$messages['error_comments_not_enabled'] = '這個網誌站台關閉了迴響功能。';
$messages['error_incorrect_search_terms'] = '搜尋關鍵字不正確。';
$messages['error_no_search_results'] = '找不到與關鍵字相符的項目。';
$messages['error_no_albums_defined'] = '這個網誌站台沒有任何資料夾。';
$messages['error_incorrect_category_id'] = '文章分類 ID 錯誤。';
$messages['error_fetching_resource'] = '讀取檔案資訊時發生錯誤。';
$messages['error_incorrect_user'] = '不合法的使用者';

$messages['form_authenticated'] = '已登入';
$messages['posted_in'] = '發表於';

$messages['previous_post'] = '上一篇';
$messages['next_post'] = '下一篇';
$messages['comment_default_title'] = '(無標題)';
$messages['guestbook'] = '留言版';
$messages['trackbacks'] = '引用';
$messages['menu'] = '選單';
$messages['albums'] = '資料夾';
$messages['admin'] = '管理介面';
$messages['links'] = '網站連結';
$messages['categories'] = '文章分類';
$messages['articles'] = '文章數';

$messages['num_reads'] = '閱讀';
$messages['contact_me'] = '聯絡我';
$messages['required'] = '必填';

$messages['size'] = '檔案大小';
$messages['format'] = '檔案格式';
$messages['dimensions'] = '維度';
$messages['bits_per_sample'] = '樣本位元率';
$messages['sample_rate'] = '取樣比例';
$messages['number_of_channels'] = '頻道數目';
$messages['length'] = '長度';

/// Strings added in LT 1.2.4 ///
$messages['audio_codec'] = '音樂編碼';
$messages['video_codec'] = '影片編碼';

/// Strings added in LT 1.2.5 ///
$messages['error_rdf_syndication_not_allowed'] = 'Error: Feeds are disabled for this blog.';

?>
