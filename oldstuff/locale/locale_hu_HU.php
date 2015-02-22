<?php
// Initial translation by Tamás Gulácsi
// gthomas@gthomas.homelinux.org http://gthomas.homelinux.org/
// Göd - HUNGARY (Europe)

$messages["encoding"] = 'UTF-8';
$messages['locale_description'] = 'Magyar ford&iacute;t&aacute;s a LifeType-hoz';	// must use HTML entities to be ok with any charset

// locale format, see Locale::formatDate for more information
$messages['date_format'] = '%Y.%m.%d %H:%M';

$messages['days'] = Array( "vasárnap", "hétfő", "kedd", "szerda", "csütörtök", "péntek", "szombat" );
// -- compatibility, do not touch -- //
$messages["Monday"] = $messages["days"][1];
$messages["Tuesday"] = $messages["days"][2];
$messages["Wednesday"] = $messages["days"][3];
$messages["Thursday"] = $messages["days"][4];
$messages["Friday"] = $messages["days"][5];
$messages["Saturday"] = $messages["days"][6];
$messages["Sunday"] = $messages["days"][0];

// abbreviations
$messages["daysshort"] = Array( "vas", "hé", "ke", "sze", "csüt", "pén", "szo" );
// -- compatibility, do not touch -- //
$messages["Mo"] = $messages["daysshort"][1];
$messages["Tu"] = $messages["daysshort"][2];
$messages["We"] = $messages["daysshort"][3];
$messages["Th"] = $messages["daysshort"][4];
$messages["Fr"] = $messages["daysshort"][5];
$messages["Sa"] = $messages["daysshort"][6];
$messages["Su"] = $messages["daysshort"][0];

// months of the year
$messages["months"] = Array( "január", "február", "március", "április", "május", "június", "július", "augusztus", "szeptember", "október", "november", "december" );
// -- compatibility, do not touch -- //
$messages["January"] = $messages["months"][0];
$messages["February"] = $messages["months"][1];
$messages["March"] = $messages["months"][2];
$messages["April"] = $messages["months"][3];
$messages["May"] = $messages["months"][4];
$messages["June"] = $messages["months"][5];
$messages["July"] = $messages["months"][6];
$messages["August"] = $messages["months"][7];
$messages["September"] = $messages["months"][8];
$messages["October"] = $messages["months"][9];
$messages["November"] = $messages["months"][10];
$messages["December"] = $messages["months"][11];

// miscellaneous texts
$messages['message'] = 'üzenet';
$messages['error'] = 'Hiba';
$messages['date'] = 'dátum';
$messages['of'] = '';
$messages['recently'] = 'nemrég…';
$messages['comments'] = 'megjegyzések';
$messages['comment on this'] = 'Megjegyzés';
$messages['my_links'] = 'Linkjeim';
$messages['archives'] = 'archívumok';
$messages['search'] = 'keresés';
$messages['calendar'] = 'kalendárium';
$messages['search_s'] = 'Keress';
$messages['search_this_blog'] = 'Keress ebben a blogban :';
$messages['about_myself'] = 'Ki vagyok én?';
$messages['permalink_title'] = 'Állandó link az archívumokra';
$messages['permalink'] = 'Permalink';
$messages['posted_by'] = 'Beküldte';
$messages['reply_string'] = 'Re: ';
$messages['reply'] = 'Válasz';
$messages['articles'] = 'Cikkek';
$messages['contact_me'] = 'Lépj kapcsolatba velem';
$messages['required'] = 'kötelező';
$messages['form_authenticated'] = 'Hitelesítve';
$messages['posted_in'] = 'Beküldve';
$messages['previous_post'] = 'Előző';
$messages['next_post'] = 'Következő';
$messages['comment_default_title'] = '(nincs címe)';
$messages['guestbook'] = 'Vendégkönyv';
$messages['trackbacks'] = 'Visszautalás';
$messages['menu'] = 'Menü';
$messages['albums'] = 'Albumok';
$messages['admin'] = 'Adminisztrátor';
$messages['links'] = 'Linkek';
$messages['categories'] = 'Kategóriák';
$messages['num_reads'] = 'Nézetek';
$messages['category'] = 'Kategória';

// add comment form
$messages['add_comment'] = 'Kommentálás';
$messages['comment_topic'] = 'Téma';
$messages['comment_text'] = 'Szöveg';
$messages['comment_username'] = 'Neved';
$messages['comment_email'] = 'email címed (ha van)';
$messages['comment_url'] = 'Honlapod (ha van)';
$messages['comment_send'] = 'Küldés';
$messages['comment_added'] = 'Megjegyzést hozzáadtam!';
$messages['comment_add_error'] = 'Hiba miatt nem sikerült a megjegyzést hozzáadni';
$messages['article_does_not_exist'] = 'A cikk nem létezik';
$messages['no_posts_found'] = 'Nem találtam hozzászólást';
$messages['user_has_no_posts_yet'] = 'A felhasználónak még nincs hozzászólása';
$messages['back'] = 'Vissza';
$messages['post'] = 'Hozzászólás';
$messages['trackbacks_for_article'] = 'Visszautalás egy cikkre: ';
$messages['trackback_excerpt'] = 'Kivonat';
$messages['trackback_weblog'] = 'Blog';
$messages['search_results'] = 'A keresés eredménye';
$messages['search_matching_results'] = 'A következő hozzászólásokat találtam: ';
$messages['search_no_matching_posts'] = 'Nem találtam a feltételeknek megfelelő hozzászólást';
$messages['read_more'] = '<br /> (Folytatás)';
$messages['syndicate'] = 'Szindikátus';
$messages['main'] = 'Főmenü';
$messages['about'] = 'Rólunk';
$messages['download'] = 'Letöltés';
$messages['invalid_url'] = 'Érvénytelen URL-t adtál meg. Kérlek javítsd és próbáld újra';

////// error messages /////
$messages['error_fetching_article'] = 'A cikket nem tudtam betölteni.';
$messages['error_fetching_articles'] = 'A cikkeket nem sikerült betölteni.';
$messages['error_trackback_no_trackback'] = 'A cikkhez nem találtam visszautalást.';
$messages['error_incorrect_article_id'] = 'A cikk azonosítója hibás.';
$messages['error_incorrect_blog_id'] = 'A blog azonosító hibás.';
$messages['error_comment_without_text'] = 'Valami szöveget meg kell adni!';
$messages['error_comment_without_name'] = 'A név kötelező!';
$messages['error_adding_comment'] = 'Hiba történt a megjegyzés hozzáadásakor.';
$messages['error_incorrect_parameter'] = 'Hibás paraméter.';
$messages['error_parameter_missing'] = 'Egy paraméter hiányzik.';
$messages['error_comments_not_enabled'] = 'Nem lehet megjegyzést hozzáfűzni.';
$messages['error_incorrect_search_terms'] = 'Hibás keresési feltételek';
$messages['error_no_search_results'] = 'Nincs találat';
$messages['error_no_albums_defined'] = 'Nincsenek albumok.';
$messages['error_incorrect_category_id'] = 'A kategória azonosító hibás, vagy nem lett kijelölve';
$messages['error_incorrect_email_address'] = 'Hibás email-cím';
$messages['error_fetching_category'] = 'Hiba történt a kategória betöltésekor.';
$messages['error_fetching_resource'] = 'A megadott fájl nem találató.';
$messages['error_incorrect_user'] = 'A felhasználónév nem érvényes';

$messages['size'] = 'Méret';
$messages['format'] = 'Formátum';
$messages['dimensions'] = 'Dimenziók';
$messages['bits_per_sample'] = 'Színmélység';
$messages['sample_rate'] = 'Mintavételi sebesség';
$messages['number_of_channels'] = 'Csatornák száma';
$messages['length'] = 'Hossz';

/// Strings added in LT 1.2.4 ///
$messages['audio_codec'] = 'Hang kodek';
$messages['video_codec'] = 'Video kodek';

/// Strings added in LT 1.2.5 ///
$messages['error_rdf_syndication_not_allowed'] = 'Error: Feeds are disabled for this blog.';

?>
