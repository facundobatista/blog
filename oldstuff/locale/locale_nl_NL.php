<?php

// set this to the encoding that should be used to display the pages correctly
$messages['encoding'] = 'iso-8859-1';
$messages['locale_description'] = 'Nederlands locale bestand voor LifeType';
// locale format, see Locale::formatDate for more information
$messages['date_format'] = '%d/%m/%Y %H:%M';

// days of the week
$messages['days'] = Array( 'Zondag', 'Maandag', 'Dindsag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag' );
// -- compatibility, do not touch -- //
$messages['Monday'] = $messages['days'][1];
$messages['Tuesday'] = $messages['days'][2];
$messages['Wednesday'] = $messages['days'][3];
$messages['Thursday'] = $messages['days'][4];
$messages['Friday'] = $messages['days'][5];
$messages['Saturday'] = $messages['days'][6];
$messages['Sunday'] = $messages['days'][0];

// abbreviations
$messages['daysshort'] = Array( 'Zo', 'Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za' );
// -- compatibility, do not touch -- //
$messages['Mo'] = $messages['daysshort'][1];
$messages['Tu'] = $messages['daysshort'][2];
$messages['We'] = $messages['daysshort'][3];
$messages['Th'] = $messages['daysshort'][4];
$messages['Fr'] = $messages['daysshort'][5];
$messages['Sa'] = $messages['daysshort'][6];
$messages['Su'] = $messages['daysshort'][0];

// months of the year
$messages['months'] = Array( 'Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December' );
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
$messages['message'] = 'Bericht';
$messages['error'] = 'Fout';
$messages['date'] = 'Datum';

// miscellaneous texts
$messages['of'] = 'of';
$messages['recently'] = 'Recent...';
$messages['comments'] = 'Reacties';
$messages['comment on this'] = 'Reacties';
$messages['my_links'] = 'Mijn links';
$messages['archives'] = 'Archief';
$messages['search'] = 'Zoeken';
$messages['calendar'] = 'Kalender';
$messages['search_s'] = 'Zoeken';
$messages['search_this_blog'] = 'Doorzoek deze blog:';
$messages['about_myself'] = 'Wie ben ik?';
$messages['permalink_title'] = 'PermaLink naar het archief';
$messages['permalink'] = 'Permalink';
$messages['posted_by'] = 'Geplaatst door';
$messages['reply_string'] = 'Re: ';
$messages['reply'] = 'Reacties';
$messages['category'] = 'Categorie';

// add comment form
$messages['add_comment'] = 'Voeg reactie toe';
$messages['comment_topic'] = 'Onderwerp';
$messages['comment_text'] = 'Tekst';
$messages['comment_username'] = 'Je naam';
$messages['comment_email'] = 'Je e-mail adres (indien aanwezig)';
$messages['comment_url'] = 'Je persoonlijke website (indien aanwezig)';
$messages['comment_send'] = 'Verstuur';
$messages['comment_added'] = 'Reactie toegevoegd!';
$messages['comment_add_error'] = 'Fout tijdens toevoegen reactie';
$messages['article_does_not_exist'] = 'Het artikel bestaat niet';
$messages['no_posts_found'] = 'Geen documenten gevonden';
$messages['user_has_no_posts_yet'] = 'De gebruiker heeft nog geen documenten geplaatst';
$messages['back'] = 'Terug';
$messages['post'] = 'Document';
$messages['trackbacks_for_article'] = 'Trackbacks voor artikel: ';
$messages['trackback_excerpt'] = 'Samenvatting';
$messages['trackback_weblog'] = 'Weblog';
$messages['search_results'] = 'Zoekresultaten';
$messages['search_matching_results'] = 'De volgende documenten voldoen aan de zoekcriteria: ';
$messages['search_no_matching_posts'] = 'Er zijn geen documenten gevonden';
$messages['read_more'] = '(Meer)';
$messages['syndicate'] = 'Verkort lezen';
$messages['main'] = 'Hoofdmenu';
$messages['about'] = 'Over...';
$messages['download'] = 'Download';
$messages['error_incorrect_email_address'] = 'Het e-mail adres is niet correct';
$messages['invalid_url'] = 'You entered an invalid URL. Please correct and try again';

////// error messages /////
$messages['error_fetching_article'] = 'Het opgevraagde artikel is niet gevonden.';
$messages['error_fetching_articles'] = 'Het artikel kan niet worden opgevraagd.';
$messages['error_fetching_category'] = 'There was an error fetching the category'; // translate
$messages['error_trackback_no_trackback'] = 'Er zijn geen trackbacks gevonden voor dit artikel.';
$messages['error_incorrect_article_id'] = 'Het artikel ID bestaat.';
$messages['error_incorrect_blog_id'] = 'Het blog ID gestaat niet.';
$messages['error_comment_without_text'] = 'Je moet tenminste enige tekst invoeren.';
$messages['error_comment_without_name'] = 'Je moet tenminste je naam of bijnaam invoeren.';
$messages['error_adding_comment'] = 'Fout tijdens het toevoegen van commentaar.';
$messages['error_incorrect_parameter'] = 'Foute parameter.';
$messages['error_parameter_missing'] = 'Er ontbreek een parameter uit het verzoek.';
$messages['error_comments_not_enabled'] = 'De commentaar functionaliteit is op deze site uitgeschakeld.';
$messages['error_incorrect_search_terms'] = 'Foutieve zoekopdracht opgegeven';
$messages['error_no_search_results'] = 'Er zijn geen resultaten gevonden die voldoen aan de zoekopdracht';
$messages['error_no_albums_defined'] = 'Er zijn geen albums beschikbaar in deze blog.';
$messages['error_incorrect_category_id'] = 'De categorie id is niet juist of er werden geen items geselecteerd';
$messages['error_incorrect_user'] = 'Gebruiker is niet correct';

$messages['form_authenticated'] = 'Geauthenticeerd';
$messages['posted_in'] = 'Gepubliceerd in';

$messages['previous_post'] = 'Vorige';
$messages['next_post'] = 'Volgende';
$messages['comment_default_title'] = '(Zonder titel)';
$messages['guestbook'] = 'Gastenboek';
$messages['trackbacks'] = 'Trackbacks';
$messages['menu'] = 'Menu';
$messages['albums'] = 'Albums';
$messages['admin'] = 'Admin';

$messages['num_reads'] = 'Bekeken';

$messages['links'] = 'Links';
$messages['categories'] = 'Categorieen';

$messages['error_fetching_resource'] = 'Het door u aangewezen document kan niet worden gevonden.';
$messages['contact_me'] = 'Contacteer Mij';
$messages['required'] = 'Verplicht';

$messages['size'] = 'Grootte';
$messages['format'] = 'Formaat';
$messages['dimensions'] = 'Afmetingen';
$messages['bits_per_sample'] = 'Bits per sample';
$messages['sample_rate'] = 'Sample rate';
$messages['number_of_channels'] = 'Aantal kanalen';
$messages['length'] = 'Lengte';

/// Strings added in LT 1.2.4 ///
$messages['audio_codec'] = 'Audio codec';
$messages['video_codec'] = 'Video codec';

/// Strings added in LT 1.2.5 ///
$messages['error_rdf_syndication_not_allowed'] = 'Error: Feeds are disabled for this blog.';

?>
