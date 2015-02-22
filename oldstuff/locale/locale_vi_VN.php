<?php

// set this to the encoding that should be used to display the pages correctly
$messages['encoding'] = 'utf-8';
$messages['locale_description'] = 'Vietnamese locale file for LifeType';
// locale format, see Locale::formatDate for more information
$messages['date_format'] = '%d/%m/%Y %H:%M';

// days of the week
$messages['days'] = Array( 'Ch&#7911; nh&#7853;t', 'Th&#7913; hai', 'Th&#7913; ba', 'Th&#7913; ba', 'Th&#7913; n&#259;m', 'Th&#7913; s&aacute;u', 'Th&#7913; b&#7849;y' );
// -- compatibility, do not touch -- //
$messages['Monday'] = $messages['days'][1];
$messages['Tuesday'] = $messages['days'][2];
$messages['Wednesday'] = $messages['days'][3];
$messages['Thursday'] = $messages['days'][4];
$messages['Friday'] = $messages['days'][5];
$messages['Saturday'] = $messages['days'][6];
$messages['Sunday'] = $messages['days'][0];

// abbreviations
$messages['daysshort'] = Array( 'Cn', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7' );
// -- compatibility, do not touch -- //
$messages['Mo'] = $messages['daysshort'][1];
$messages['Tu'] = $messages['daysshort'][2];
$messages['We'] = $messages['daysshort'][3];
$messages['Th'] = $messages['daysshort'][4];
$messages['Fr'] = $messages['daysshort'][5];
$messages['Sa'] = $messages['daysshort'][6];
$messages['Su'] = $messages['daysshort'][0];

// months of the year
$messages['months'] = Array( 'Th&aacute;ng m&#7897;t', 'Th&aacute;ng hai', 'Th&aacute;ng ba', 'Th&aacute;ng t&#432;', 'Th&aacute;ng n&#259;m', 'Th&aacute;ng s&aacute;u', 'Th&aacute;ng b&#7849;y', 'Th&aacute;ng t&aacute;m', 'Th&aacute;ng ch&iacute;n', 'Th&aacute;ng m&#432;&#7901;i', 'Th&aacute;ng m&#432;&#7901;i m&#7897;t', 'Th&aacute;ng m&#432;&#7901;i hai' );
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
$messages['message'] = 'Tin nh&#7855;n';
$messages['error'] = 'L&#7895;i';
$messages['date'] = 'Ng&agrave;y ';

// miscellaneous texts
$messages['of'] = 'c&#7911;a';
$messages['recently'] = 'g&#7847;n &#273;&acirc;y...';
$messages['comments'] = 'c&aacute;c b&igrave;nh lu&#7853;n';
$messages['comment on this'] = 'B&igrave;nh lu&#7853;n';
$messages['my_links'] = 'C&aacute;c li&ecirc;n k&#7871;t c&#7911;a t&ocirc;i';
$messages['archives'] = 'c&aacute;c b&agrave;i vi&#7871;t l&#432;u tr&#7919;';
$messages['search'] = 't&igrave;m ki&#7871;m';
$messages['calendar'] = 'l&#7883;ch';
$messages['search_s'] = 'T&igrave;m ki&#7871;m';
$messages['search_this_blog'] = 'T&igrave;m ki&#7871;m trong blog n&agrave;y:';
$messages['about_myself'] = 'T&ocirc;i l&agrave; ai?';
$messages['permalink_title'] = '&#272;&#432;&#7901;ng d&#7851;n t&#7899;i kho l&#432;u tr&#7919;';
$messages['permalink'] = '&#272;&#432;&#7901;ng d&#7851;n th&#432;&#7901;ng tr&#7921;c';
$messages['posted_by'] = 'G&#7917;i b&#7903;i';
$messages['reply_string'] = 'Tr&#7843; l&#7901;i: ';
$messages['reply'] = 'Tr&#7843; l&#7901;i';
$messages['category'] = 'Danh m&#7909;c';

// add comment form
$messages['add_comment'] = 'Vi&#7871;t b&igrave;nh lu&#7853;n';
$messages['comment_topic'] = 'Ch&#7911; &#273;&#7873;';
$messages['comment_text'] = '&#272;&#7873; t&agrave;i';
$messages['comment_username'] = 'T&ecirc;n b&#7841;n';
$messages['comment_email'] = '&#272;&#7883;a ch&#7881; th&#432; &#273;i&#7879;n t&#7917; ( n&#7871;u c&oacute; )';
$messages['comment_url'] = 'Li&ecirc;n k&#7871;t t&#7899;i trang web c&aacute; nh&acirc;n ( n&#7871;u c&oacute; )';
$messages['comment_send'] = 'G&#7917;i';
$messages['comment_added'] = 'G&#7917;i nh&#7853;n x&eacute;t';
$messages['comment_add_error'] = 'L&#7895;i g&#7917;i nh&#7853;n x&eacute;t';
$messages['article_does_not_exist'] = 'B&agrave;i vi&#7871;t kh&ocirc;ng t&#7891;n t&#7841;i';
$messages['no_posts_found'] = 'Kh&ocirc;ng t&igrave;m th&#7845;y b&agrave;i vi&#7871;t ';
$messages['user_has_no_posts_yet'] = 'Ng&#432;&#7901;i d&ugrave;ng ch&#432;a g&#7917;i b&agrave;i vi&#7871;t';
$messages['back'] = 'Quay l&#7841;i';
$messages['post'] = 'G&#7917;i';
$messages['trackbacks_for_article'] = 'L&#432;u v&#7871;t cho b&agrave;i vi&#7871;t: ';
$messages['trackback_excerpt'] = 'Tr&iacute;ch d&#7851;n';
$messages['trackback_weblog'] = 'Weblog';
$messages['search_results'] = 'K&#7871;t qu&#7843; t&igrave;m ki&#7871;m';
$messages['search_matching_results'] = 'C&aacute;c b&agrave;i vi&#7871;t d&#432;&#7899;i &#273;&acirc;y ph&ugrave; h&#7907;p v&#7899;i n&#7897;i dung b&#7841;n c&#7847;n t&igrave;m: ';
$messages['search_no_matching_posts'] = 'Kh&ocirc;ng t&igrave;m th&#7845;y b&agrave;i vi&#7871;t ph&ugrave; h&#7907;p';
$messages['read_more'] = '(Th&ecirc;m )';
$messages['syndicate'] = 'Syndicate';
$messages['main'] = 'Ch&iacute;nh';
$messages['about'] = 'Gi&#7899;i thi&#7879;u v&#7873;';
$messages['download'] = 'T&#7843;i v&#7873;';
$messages['error_incorrect_email_address'] = '&#272;&#7883;a ch&#7881; th&#432; &#273;i&#7879;n t&#7917; kh&ocirc;ng h&#7907;p l&#7879;';
$messages['invalid_url'] = 'B&#7841;n &#273;&atilde; nh&#7853;p URL kh&ocirc;ng h&#7907;p l&#7879;. Xin vui l&ograve;ng ki&#7875;m tra v&agrave; th&#7917; l&#7841;i';

////// error messages /////
$messages['error_fetching_article'] = 'B&agrave;i vi&#7871;t b&#7841;n c&#7847;n kh&ocirc;ng t&igrave;m th&#7845;y.';
$messages['error_fetching_articles'] = 'Kh&ocirc;ng th&#7875; n&#7841;p c&aacute;c b&agrave;i vi&#7871;t.';
$messages['error_fetching_category'] = 'C&oacute; l&#7895;i khi t&#7841;o danh m&#7909;c';
$messages['error_trackback_no_trackback'] = 'Kh&ocirc;ng t&igrave;m th&#7845;y l&#432;u v&#7871;t c&#7911;a b&agrave;i vi&#7871;t.';
$messages['error_incorrect_article_id'] = '&#272;&#7883;nh danh c&#7911;a b&agrave;i vi&#7871;t kh&ocirc;ng &#273;&uacute;ng.';
$messages['error_incorrect_blog_id'] = '&#272;&#7883;nh danh c&#7911;a blog kh&ocirc;ng &#273;&uacute;ng.';
$messages['error_comment_without_text'] = 'B&#7841;n ph&#7843;i nh&#7853;p n&#7897;i dung b&agrave;i vi&#7871;t.';
$messages['error_comment_without_name'] = 'B&#7841;n ph&#7843;i nh&#7853;p t&ecirc;n ho&#7863;c t&ecirc;n th&acirc;n m&#7853;t.';
$messages['error_adding_comment'] = 'C&oacute; l&#7895;i trong qu&aacute; tr&igrave;nh th&ecirc;m nh&#7853;n x&eacute;t.';
$messages['error_incorrect_parameter'] = 'Tham s&#7889; kh&ocirc;ng &#273;&uacute;ng.';
$messages['error_parameter_missing'] = 'Thi&#7871;u m&#7897;t tham s&#7889; theo y&ecirc;u c&#7847;u.';
$messages['error_comments_not_enabled'] = 'Ch&#7913;c n&#259;ng th&ecirc;m nh&#7853;n x&eacute;t &#273;&atilde; b&#7883; v&ocirc; hi&#7879;u h&oacute;a tr&ecirc;n blog n&agrave;y.';
$messages['error_incorrect_search_terms'] = 'C&#7909;m t&#7915; t&igrave;m ki&#7871;m kh&ocirc;ng h&#7907;p l&#7879;';
$messages['error_no_search_results'] = 'Kh&ocirc;ng t&igrave;m th&#7845;y n&#7897;i dung ph&ugrave; h&#7907;p v&#7899;i c&#7909;m t&#7915; t&igrave;m ki&#7871;m';
$messages['error_no_albums_defined'] = 'Kh&ocirc;ng c&oacute; album n&agrave;o trong blog n&agrave;y.';
$messages['error_incorrect_category_id'] = '&#272;&#7883;nh danh danh m&#7909;c kh&ocirc;ng &#273;&uacute;ng ho&#7863;c kh&ocirc;ng c&oacute; m&#7909;c n&agrave;o &#273;&#432;&#7907;c l&#7921;a ch&#7885;n';
$messages['error_fetching_resource'] = 'T&#7879;p tin m&agrave; b&#7841;n mu&#7889;n kh&ocirc;ng t&igrave;m th&#7845;y.';
$messages['error_incorrect_user'] = 'Ng&#432;&#7901;i d&ugrave;ng kh&ocirc;ng h&#7907;p l&#7879;';

$messages['form_authenticated'] = '&#272;&atilde; x&aacute;c th&#7921;c';
$messages['posted_in'] = '&#272;&atilde; g&#7917;i v&agrave;o';

$messages['previous_post'] = 'Tr&#432;&#7899;c &#273;&oacute;';
$messages['next_post'] = 'Ti&#7871;p theo';
$messages['comment_default_title'] = '(Kh&ocirc;ng ti&ecirc;u &#273;&#7873;)';
$messages['guestbook'] = 'L&#7901;i nh&#7855;n';
$messages['trackbacks'] = 'C&aacute;c l&#432;u v&#7871;t';
$messages['menu'] = 'M&#7909;c l&#7909;c';
$messages['albums'] = 'Albums';
$messages['admin'] = 'Qu&#7843;n tr&#7883;';
$messages['links'] = 'C&aacute;c li&ecirc;n k&#7871;t';
$messages['categories'] = 'C&aacute;c danh m&#7909;c';
$messages['articles'] = 'C&aacute;c b&agrave;i vi&#7871;t';

$messages['num_reads'] = 'S&#7889; l&#7847;n xem ';
$messages['contact_me'] = 'Li&ecirc;n h&#7879;';
$messages['required'] = 'Y&ecirc;u c&#7847;u';

$messages['size'] = 'C&#7905;';
$messages['format'] = '&#272;&#7883;nh d&#7841;ng';
$messages['dimensions'] = 'Chi&#7873;u';
$messages['bits_per_sample'] = 'C&aacute;c bit m&#7895;i m&#7851;u';
$messages['sample_rate'] = 'T&#7927; l&#7879; m&#7851;u';
$messages['number_of_channels'] = 'S&#7889; l&#432;&#7907;ng k&ecirc;nh';
$messages['length'] = 'Chi&#7873;u d&agrave;i';

/// Strings added in LT 1.2.4 ///
$messages['audio_codec'] = 'Audio codec';
$messages['video_codec'] = 'Video codec';

/// Strings added in LT 1.2.5 ///
$messages['error_rdf_syndication_not_allowed'] = 'Error: Feeds are disabled for this blog.';

?>
