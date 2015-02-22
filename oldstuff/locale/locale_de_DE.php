<?php

// set this to the encoding that should be used to display the pages correctly
$messages['encoding'] = 'iso-8859-1';
$messages['locale_description'] = 'Deutsche Sprachdatei f&uuml;r LifeType';
// locale format, see Locale::formatDate for more information
$messages['date_format'] = '%d/%m/%Y %H:%M';

// days of the week
$messages['days'] = Array( 'Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag' );
// -- compatibility, do not touch -- //
$messages['Monday'] = $messages['days'][1];
$messages['Tuesday'] = $messages['days'][2];
$messages['Wednesday'] = $messages['days'][3];
$messages['Thursday'] = $messages['days'][4];
$messages['Friday'] = $messages['days'][5];
$messages['Saturday'] = $messages['days'][6];
$messages['Sunday'] = $messages['days'][0];

// abbreviations
$messages['daysshort'] = Array( 'So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa' );
// -- compatibility, do not touch -- //
$messages['Mo'] = $messages['daysshort'][1];
$messages['Tu'] = $messages['daysshort'][2];
$messages['We'] = $messages['daysshort'][3];
$messages['Th'] = $messages['daysshort'][4];
$messages['Fr'] = $messages['daysshort'][5];
$messages['Sa'] = $messages['daysshort'][6];
$messages['Su'] = $messages['daysshort'][0];

// months of the year
$messages['months'] = Array( 'Januar', 'Februar', 'M&auml;rz', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember' );
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
$messages['message'] = 'Nachricht';
$messages['error'] = 'Fehler';
$messages['date'] = 'Datum';

// miscellaneous texts
$messages['of'] = 'von';
$messages['recently'] = 'Aktuell';
$messages['comments'] = 'Kommentare';
$messages['comment on this'] = 'Kommentare';
$messages['my_links'] = 'meine Links';
$messages['archives'] = 'Archiv';
$messages['search'] = 'suchen';
$messages['calendar'] = 'Kalender';
$messages['search_s'] = 'Suche';
$messages['search_this_blog'] = 'Dieses Weblog durchsuchen:';
$messages['about_myself'] = '&Uuml;ber mich';
$messages['permalink_title'] = 'Permanenter Link zum Archiv';
$messages['permalink'] = 'Permalink';
$messages['posted_by'] = 'geschrieben von';
$messages['reply_string'] = 'Re: ';
$messages['reply'] = 'antworten';
$messages['category'] = 'Kategorie';

// add comment form
$messages['add_comment'] = 'Artikel kommentieren';
$messages['comment_topic'] = 'Betreff';
$messages['comment_text'] = 'Text';
$messages['comment_username'] = 'Ihr Name';
$messages['comment_email'] = 'E-Mail Addresse (wenn vorhanden)';
$messages['comment_url'] = 'Webseite (wenn vorhanden)';
$messages['comment_send'] = 'absenden';
$messages['comment_added'] = 'Kommentar hinzugef&uuml;gt.';
$messages['comment_add_error'] = 'Beim Hinzuf&uuml;gen des Kommentars ist ein Fehler aufgetreten.';
$messages['article_does_not_exist'] = 'Dieser Artikel existiert nicht.';
$messages['no_posts_found'] = 'Es wurden keine Artikel gefunden.';
$messages['user_has_no_posts_yet'] = 'Der Benutzer hat noch keine Artikel ver&ouml;ffentlicht.';
$messages['back'] = 'zur&uuml;ck';
$messages['post'] = 'Artikel';
$messages['trackbacks_for_article'] = 'Trackbacks f&uuml;r diesen Artikel';
$messages['trackback_excerpt'] = 'Auszug';
$messages['trackback_weblog'] = 'Weblog';
$messages['search_results'] = 'Suchergebnisse';
$messages['search_matching_results'] = 'Die folgenden Artikel entsprechen Ihrer Suchanfrage: ';
$messages['search_no_matching_posts'] = 'Es wurden keine Artikel gefunden.';
$messages['read_more'] = '(weiter)';
$messages['syndicate'] = 'Meta';
$messages['main'] = '&Uuml;bersicht';
$messages['about'] = '&Uuml;ber';
$messages['download'] = 'Download';
$messages['error_incorrect_email_address'] = 'Die E-Mail Adresse ist ung&uuml;ltig.';
$messages['invalid_url'] = 'You entered an invalid URL. Please correct and try again';

////// error messages /////
$messages['error_fetching_article'] = 'Der gesuchte Artikel kann nicht gefunden werden.';
$messages['error_fetching_articles'] = 'F&uuml;r diese Auswahl k&ouml;nnen keine Artikel angezeigt werden.';
$messages['error_fetching_category'] = 'There was an error fetching the category'; // translate
$messages['error_trackback_no_trackback'] = 'F&uuml;r diesen Artikel wurden keine Trackbacks gefunden.';
$messages['error_incorrect_article_id'] = 'Die Artikel-ID ist nicht korrekt.';
$messages['error_incorrect_blog_id'] = 'Die Weblog-ID ist nicht korrekt.';
$messages['error_comment_without_text'] = 'Sie haben keinen Text eingegeben.';
$messages['error_comment_without_name'] = 'Bitte geben Sie Ihren Namen oder ein Pseudonym ein.';
$messages['error_adding_comment'] = 'Beim Hinzuf&uuml;gen des Kommentars ist ein Fehler aufgetreten.';
$messages['error_incorrect_parameter'] = 'Falsche Parameter.';
$messages['error_parameter_missing'] = 'Es fehlt ein Parameter f&uuml;r diese Anfrage.';
$messages['error_comments_not_enabled'] = 'Die M&ouml;glichkeit Kommentare hinzuzuf&uuml;gen wurde f&uuml;r diese Site gesperrt.';
$messages['error_incorrect_search_terms'] = 'Dies ist keine g&uuml;ltige Suchanfrage.';
$messages['error_no_search_results'] = 'Es wurden keine den Suchbegriffen entsprechenden Ergebnisse gefunden.';
$messages['error_no_albums_defined'] = 'In diesem Blog sind keine Alben verf&uuml;gbar.';
$messages['error_incorrect_category_id'] = 'Es wurden keine Kategorien ausgew&auml;hlt oder die entsprechenden IDs sind nicht korrekt.';
$messages['error_fetching_resource'] = 'Die gew&uuml;nschte Datei wurde nicht gefunden.';
$messages['error_incorrect_user'] = 'Benutzer ist ung&uuml;ltig';

$messages['form_authenticated'] = 'Authentifiziert';
$messages['posted_in'] = 'Abgelegt unter';

$messages['previous_post'] = 'zur&uuml;ck';
$messages['next_post'] = 'vor';
$messages['comment_default_title'] = '(ohne Titel)';
$messages['guestbook'] = 'G&auml;stebuch';
$messages['trackbacks'] = 'Trackbacks';
$messages['menu'] = 'Menu';
$messages['albums'] = 'Alben';
$messages['admin'] = 'Admin';
$messages['links'] = 'Links';
$messages['categories'] = 'Kategorien';
$messages['articles'] = 'Artikel';

$messages['num_reads'] = 'gesehen';
$messages['contact_me'] = 'Kontaktieren Sie mich';
$messages['required'] = 'Notwendig';

$messages['size'] = 'Dateigr&ouml;&szlig;e';
$messages['format'] = 'Format';
$messages['dimensions'] = 'Gr&ouml;&szlig;e';
$messages['bits_per_sample'] = 'Bits pro Sample';
$messages['sample_rate'] = 'Samplerate';
$messages['number_of_channels'] = 'Anzahl der Kan&auml;le';
$messages['length'] = 'L&auml;nge';

/// Strings added in LT 1.2.4 ///
$messages['audio_codec'] = 'Audio Codec';
$messages['video_codec'] = 'Video Codec';

/// Strings added in LT 1.2.5 ///
$messages['error_rdf_syndication_not_allowed'] = 'Fehler: Feeds sind für dieses Blog deaktiviert.';

?>
