<?php

// set this to the encoding that should be used to display the pages correctly
$messages['encoding'] = 'utf-8';
$messages['locale_description'] = 'Simplified Chinese translation (UTF-8)';
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
$messages['message'] = '信息';
$messages['error'] = '错误';
$messages['date'] = '日期';

// miscellaneous texts
$messages['of'] = 'of';
$messages['recently'] = '近期文章';
$messages['comments'] = '评论';
$messages['comment on this'] = '评论';
$messages['my_links'] = '我的链接';
$messages['archives'] = '文章汇整';
$messages['search'] = '站内搜寻';
$messages['calendar'] = '日历';
$messages['search_s'] = '搜寻';
$messages['search_this_blog'] = '搜寻博客内容:';
$messages['about_myself'] = '自我介绍';
$messages['permalink_title'] = '文章汇整静态链接网址';
$messages['permalink'] = '静态链接网址';
$messages['posted_by'] = '作者';
$messages['reply_string'] = 'Re: ';
$messages['reply'] = '回覆';
$messages['category'] = '分类';

// add comment form
$messages['add_comment'] = '发表评论';
$messages['comment_topic'] = '标题';
$messages['comment_text'] = '内容';
$messages['comment_username'] = '昵称';
$messages['comment_email'] = '电子邮件';
$messages['comment_url'] = '个人网页';
$messages['comment_send'] = '发表';
$messages['comment_added'] = '您的评论已经顺利发表。';
$messages['comment_add_error'] = '发表评论时发生错误。';
$messages['article_does_not_exist'] = '本文章不存在。';
$messages['no_posts_found'] = '找不到文章。';
$messages['user_has_no_posts_yet'] = '该用户还没有发表过任何文章。';
$messages['back'] = '回到上一页';
$messages['post'] = '文章';
$messages['trackbacks_for_article'] = '引用本文的文章标题：';
$messages['trackback_excerpt'] = '摘要';
$messages['trackback_weblog'] = '博客';
$messages['search_results'] = '搜寻结果';
$messages['search_matching_results'] = '以下文章符合您的搜寻关键字：';
$messages['search_no_matching_posts'] = '找不到符合的文章。';
$messages['read_more'] = '(查看全文)';
$messages['syndicate'] = '新闻聚合';
$messages['main'] = '主页面';
$messages['about'] = '关于';
$messages['download'] = '下载';
$messages['error_incorrect_email_address'] = '电子邮件信箱格式错误。';
$messages['invalid_url'] = '网址格式错误，请输入正确格式';

////// error messages /////
$messages['error_fetching_article'] = '找不到您所指定的文章。';
$messages['error_fetching_articles'] = '找不到您所指定的文章。';
$messages['error_fetching_category'] = 'There was an error fetching the category'; // translate
$messages['error_trackback_no_trackback'] = '尚未有人向本文发送引用通告。';
$messages['error_incorrect_article_id'] = '文章 ID 错误。';
$messages['error_incorrect_blog_id'] = '博客站台 ID 错误。';
$messages['error_comment_without_text'] = '无评论留言内容。';
$messages['error_comment_without_name'] = '您必须要填写姓名或昵称。';
$messages['error_adding_comment'] = '在将留言新增至数据库时发生问题。';
$messages['error_incorrect_parameter'] = '参数不正确。';
$messages['error_parameter_missing'] = '您少传递了一项参数。';
$messages['error_comments_not_enabled'] = '这个博客站台关闭了评论功能。';
$messages['error_incorrect_search_terms'] = '搜寻关键字不正确。';
$messages['error_no_search_results'] = '找不到与关键字相符的项目。';
$messages['error_no_albums_defined'] = '这个博客站台没有任何文件夹。';
$messages['error_incorrect_category_id'] = '文章分类 ID 错误。';
$messages['error_fetching_resource'] = '读取文件信息时发生错误。';
$messages['error_incorrect_user'] = '不合法的用户';

$messages['form_authenticated'] = '已登入';
$messages['posted_in'] = '发表于';

$messages['previous_post'] = '上一篇';
$messages['next_post'] = '下一篇';
$messages['comment_default_title'] = '(无标题)';
$messages['guestbook'] = '留言版';
$messages['trackbacks'] = '引用';
$messages['menu'] = '选单';
$messages['albums'] = '文件夹';
$messages['admin'] = '管理介面';
$messages['links'] = '网站链接';
$messages['categories'] = '文章分类';
$messages['articles'] = '文章数';

$messages['num_reads'] = '阅读';
$messages['contact_me'] = '联络我';
$messages['required'] = '必填';

$messages['size'] = '文件大小';
$messages['format'] = '文件格式';
$messages['dimensions'] = '维度';
$messages['bits_per_sample'] = '样本位元率';
$messages['sample_rate'] = '取样比例';
$messages['number_of_channels'] = '频道数目';
$messages['length'] = '长度';

/// Strings added in LT 1.2.4 ///
$messages['audio_codec'] = '音乐编码';
$messages['video_codec'] = '影片编码';

/// Strings added in LT 1.2.5 ///
$messages['error_rdf_syndication_not_allowed'] = 'Error: Feeds are disabled for this blog.';

?>
