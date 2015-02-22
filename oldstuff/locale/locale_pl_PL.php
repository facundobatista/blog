<?php

// set this to the encoding that should be used to display the pages correctly
$messages['encoding'] = 'utf-8';
$messages['locale_description'] = 'Polskie tłumaczenie dla LifeType';
// locale format, see Locale::formatDate for more information
$messages['date_format'] = '%d/%m/%Y %H:%M';

// days of the week
$messages['days'] = Array( 'Niedziela', 'Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek', 'Sobota' );
// -- compatibility, do not touch -- //
$messages['Monday'] = $messages['days'][1];
$messages['Tuesday'] = $messages['days'][2];
$messages['Wednesday'] = $messages['days'][3];
$messages['Thursday'] = $messages['days'][4];
$messages['Friday'] = $messages['days'][5];
$messages['Saturday'] = $messages['days'][6];
$messages['Sunday'] = $messages['days'][0];

// abbreviations
$messages['daysshort'] = Array( 'Pon', 'Wto', 'Śr', 'Czw', 'Pt', 'Sob', 'Nd' );
// -- compatibility, do not touch -- //
$messages['Mo'] = $messages['daysshort'][1];
$messages['Tu'] = $messages['daysshort'][2];
$messages['We'] = $messages['daysshort'][3];
$messages['Th'] = $messages['daysshort'][4];
$messages['Fr'] = $messages['daysshort'][5];
$messages['Sa'] = $messages['daysshort'][6];
$messages['Su'] = $messages['daysshort'][0];

// months of the year
$messages['months'] = Array( 'Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 'Czerwiec', 'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 'Listopad', 'Grudzień' );
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
$messages['message'] = 'Wiadomość';
$messages['error'] = 'Błąd';
$messages['date'] = 'Data';

// miscellaneous texts
$messages['of'] = 'of';
$messages['recently'] = 'ostatnio...';
$messages['comments'] = 'komentarze';
$messages['comment on this'] = 'Komentarz';
$messages['my_links'] = 'moje Linki';
$messages['archives'] = 'archiwum';
$messages['search'] = 'szukaj';
$messages['calendar'] = 'kalendarz';
$messages['search_s'] = 'Szukaj';
$messages['search_this_blog'] = 'Przeszukaj ten blog:';
$messages['about_myself'] = 'kim jestem?';
$messages['permalink_title'] = 'Permalink do archiwum';
$messages['permalink'] = 'Permalink';
$messages['posted_by'] = 'Wysłany przez';
$messages['reply'] = 'Odpowiedz';
$messages['category'] = 'Kategoria';

// add comment form
$messages['add_comment'] = 'Dogaj komentarz';
$messages['comment_topic'] = 'Temat';
$messages['comment_text'] = 'Tekst';
$messages['comment_username'] = 'Twoje imię';
$messages['comment_email'] = 'Twój adres email (jeśli posiadasz)';
$messages['comment_url'] = 'Twoja strona (jeśli posiadasz)';
$messages['comment_send'] = 'Wyślij';
$messages['comment_added'] = 'Komentarz dodany!';
$messages['comment_add_error'] = 'Błąd dodawania komentarza';
$messages['article_does_not_exist'] = 'Artykuł nie istnieje';
$messages['no_posts_found'] = 'Nowy post został znaleziony';
$messages['user_has_no_posts_yet'] = 'Użytkownik nie ma jeszcze postów';
$messages['back'] = 'Powrót';
$messages['post'] = 'Post';
$messages['trackbacks_for_article'] = 'Trackback-i dla artykułu: ';
$messages['trackback_excerpt'] = 'Wyjątek';
$messages['trackback_weblog'] = 'Weblog';
$messages['search_results'] = 'Rezultaty wyszukiwania';
$messages['search_matching_results'] = 'Poniższe posty spełniają kryteria wyszukiwania: ';
$messages['search_no_matching_posts'] = 'Nie znaleziono postów';
$messages['read_more'] = '(Więcej)';
$messages['syndicate'] = 'Syndykat';
$messages['main'] = 'Główna';
$messages['about'] = 'O';
$messages['download'] = 'Pobierz';
$messages['error_incorrect_email_address'] = 'Adres email nie jest poprawny';
$messages['invalid_url'] = 'Wprowadziłeś niepoprawny adres URL. Popraw go i spróbuj ponownie';

////// error messages /////
$messages['error_fetching_article'] = 'Artykuł który wybrałeś nie mógł zostać znaleziony.';
$messages['error_fetching_articles'] = 'Artykuły nie mogły zostać powiązane.';
$messages['error_fetching_category'] = 'Wystąpił błąd w czasie powiązywania kategorii';
$messages['error_trackback_no_trackback'] = 'Nie znaleziono trackbak-ów do artykułu.';
$messages['error_incorrect_article_id'] = 'Identyfikator artykułu jest niepoprawny.';
$messages['error_incorrect_blog_id'] = 'Identyfikator blog-u jest niepoprawny.';
$messages['error_comment_without_text'] = 'Powinieneś wprowadzić tekst.';
$messages['error_comment_without_name'] = 'Powinieneś podać swoje imię lub nick.';
$messages['error_adding_comment'] = 'Wystąpił błąd w czasie dodawania komentarza.';
$messages['error_incorrect_parameter'] = 'Niepoprawny parametr.';
$messages['error_parameter_missing'] = 'Brak jednego z parametrów w zapytaniu.';
$messages['error_comments_not_enabled'] = 'Mośliwość komentowania została wyłączona na tej stronie.';
$messages['error_incorrect_search_terms'] = 'Kryteria wyszukiwania nie były poprawne.';
$messages['error_no_search_results'] = 'Nie znaleziono wyników pasujących do warunków wyszukiwania.';
$messages['error_no_albums_defined'] = 'Ten blog nie posiada albumów.';
$messages['error_incorrect_category_id'] = 'Identyfikator kategorii jest niepoprawny lub nie znaleziono wpisów.';
$messages['error_fetching_resource'] = 'Plik który wybrałeś nie został znaleziony.';
$messages['error_incorrect_user'] = 'Użytkownik jest niepoprawny';

$messages['form_authenticated'] = 'Zalogowany';
$messages['posted_in'] = 'Wysłany w';

$messages['previous_post'] = 'Poprzedni';
$messages['next_post'] = 'Następny';
$messages['comment_default_title'] = '(Brak tytułu)';
$messages['guestbook'] = 'Księga gości';
$messages['trackbacks'] = 'Trackbacki-i';
$messages['menu'] = 'Menu';
$messages['albums'] = 'Albumy';
$messages['admin'] = 'Admin';
$messages['links'] = 'Linki';
$messages['categories'] = 'Kategorie';
$messages['articles'] = 'Artykuły';

$messages['num_reads'] = 'Ilość wyświetleń';
$messages['contact_me'] = 'Skontaktuj się ze mną';
$messages['required'] = 'Wymagane';

$messages['size'] = 'Wielkość';
$messages['format'] = 'Format';
$messages['dimensions'] = 'Wymiary';
$messages['bits_per_sample'] = 'Ilość bitów';
$messages['sample_rate'] = 'Częstotliwość';
$messages['number_of_channels'] = 'Ilość kanłów';
$messages['length'] = 'Długość';

/// Strings added in LT 1.2.4 ///
$messages['audio_codec'] = 'Kodek audio';
$messages['video_codec'] = 'Kodek video';

/// Strings added in LT 1.2.5 ///
$messages['error_rdf_syndication_not_allowed'] = 'Błąd: Kanały informacyjne zostały wyłączone na tej stronie.';

?>
