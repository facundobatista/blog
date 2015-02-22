<?php

// set this to the encoding that should be used to display the pages correctly
$messages['encoding'] = 'iso-8859-1';
$messages['locale_description'] = 'English locale file for LifeType';
// locale format, see Locale::formatDate for more information
$messages['date_format'] = '%d/%m/%Y %H:%M';

// days of the week
$messages['days'] = Array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );
// -- compatibility, do not touch -- //
$messages['Monday'] = $messages['days'][1];
$messages['Tuesday'] = $messages['days'][2];
$messages['Wednesday'] = $messages['days'][3];
$messages['Thursday'] = $messages['days'][4];
$messages['Friday'] = $messages['days'][5];
$messages['Saturday'] = $messages['days'][6];
$messages['Sunday'] = $messages['days'][0];

// abbreviations
$messages['daysshort'] = Array( 'Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa' );
// -- compatibility, do not touch -- //
$messages['Mo'] = $messages['daysshort'][1];
$messages['Tu'] = $messages['daysshort'][2];
$messages['We'] = $messages['daysshort'][3];
$messages['Th'] = $messages['daysshort'][4];
$messages['Fr'] = $messages['daysshort'][5];
$messages['Sa'] = $messages['daysshort'][6];
$messages['Su'] = $messages['daysshort'][0];

// months of the year
$messages['months'] = Array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
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
$messages['message'] = 'Message';
$messages['error'] = 'Error';
$messages['date'] = 'Date';

// miscellaneous texts
$messages['of'] = 'of';
$messages['recently'] = 'Recently...';
$messages['comments'] = 'Comments';
$messages['comment on this'] = 'Comment';
$messages['my_links'] = 'My Links';
$messages['archives'] = 'Archives';
$messages['search'] = 'Search';
$messages['calendar'] = 'Calendar';
$messages['search_s'] = 'Search';
$messages['search_this_blog'] = 'Search this blog:';
$messages['about_myself'] = 'Who am I?';
$messages['permalink_title'] = 'Permanent link to the archives';
$messages['permalink'] = 'Permalink';
$messages['posted_by'] = 'Posted by';
$messages['reply_string'] = 'Re: ';
$messages['reply'] = 'Reply';
$messages['category'] = 'Category';

// add comment form
$messages['add_comment'] = 'Add comment';
$messages['comment_topic'] = 'Topic';
$messages['comment_text'] = 'Text';
$messages['comment_username'] = 'Your name';
$messages['comment_email'] = 'Your email address (if any)';
$messages['comment_url'] = 'Your personal page (if any)';
$messages['comment_send'] = 'Send';
$messages['comment_added'] = 'Comment added!';
$messages['comment_add_error'] = 'Error adding comment';
$messages['article_does_not_exist'] = 'The article does not exist';
$messages['no_posts_found'] = 'No posts were found';
$messages['user_has_no_posts_yet'] = 'The user does not have any posts yet';
$messages['back'] = 'Back';
$messages['post'] = 'Post';
$messages['trackbacks_for_article'] = 'Trackbacks for article: ';
$messages['trackback_excerpt'] = 'Excerpt';
$messages['trackback_weblog'] = 'Weblog';
$messages['search_results'] = 'Search Results';
$messages['search_matching_results'] = 'The following posts match your search terms: ';
$messages['search_no_matching_posts'] = 'No matching posts were found';
$messages['read_more'] = '(More)';
$messages['syndicate'] = 'Syndicate';
$messages['main'] = 'Main';
$messages['about'] = 'About';
$messages['download'] = 'Download';
$messages['error_incorrect_email_address'] = 'The email address is not correct';
$messages['invalid_url'] = 'You entered an invalid URL. Please correct and try again';

////// error messages /////
$messages['error_fetching_article'] = 'The article you specified could not be found.';
$messages['error_fetching_articles'] = 'No articles could be found in this category.';
$messages['error_fetching_category'] = 'The category you specified could not be found.';
$messages['error_trackback_no_trackback'] = 'No trackbacks were found for the article.';
$messages['error_incorrect_article_id'] = 'The article identifier is not correct.';
$messages['error_incorrect_blog_id'] = 'The blog identifier is not correct.';
$messages['error_comment_without_text'] = 'You should at least provide some text.';
$messages['error_comment_without_name'] = 'You should at least give your name or nickname.';
$messages['error_adding_comment'] = 'There was an error adding the comment.';
$messages['error_incorrect_parameter'] = 'Incorrect parameter.';
$messages['error_parameter_missing'] = 'There is one parameter missing from the request.';
$messages['error_comments_not_enabled'] = 'The commenting feature has been disabled in this site.';
$messages['error_incorrect_search_terms'] = 'The search terms were not valid';
$messages['error_no_search_results'] = 'No items matching the search terms were found';
$messages['error_no_albums_defined'] = 'There are no albums available in this blog.';
$messages['error_incorrect_category_id'] = 'The category identifier is not correct or no items were selected';
$messages['error_fetching_resource'] = 'The file you specified could not be found.';
$messages['error_incorrect_user'] = 'User is not valid';

$messages['form_authenticated'] = 'Authenticated';
$messages['posted_in'] = 'Posted in';

$messages['previous_post'] = 'Previous';
$messages['next_post'] = 'Next';
$messages['comment_default_title'] = '(Untitled)';
$messages['guestbook'] = 'Guestbook';
$messages['trackbacks'] = 'Trackbacks';
$messages['menu'] = 'Menu';
$messages['albums'] = 'Albums';
$messages['admin'] = 'Admin';
$messages['links'] = 'Links';
$messages['categories'] = 'Categories';
$messages['articles'] = 'Articles';

$messages['num_reads'] = 'Views';
$messages['contact_me'] = 'Contact Me';
$messages['required'] = 'Required';

$messages['size'] = 'Size';
$messages['format'] = 'Format';
$messages['dimensions'] = 'Dimensions';
$messages['bits_per_sample'] = 'Bits per sample';
$messages['sample_rate'] = 'Sample rate';
$messages['number_of_channels'] = 'Number of channels';
$messages['length'] = 'Length';

/// Strings added in LT 1.2.4 ///
$messages['audio_codec'] = 'Audio codec';
$messages['video_codec'] = 'Video codec';

/// Strings added in LT 1.2.5 ///
$messages['error_rdf_syndication_not_allowed'] = 'Error: Feeds are disabled for this blog.';

?>
