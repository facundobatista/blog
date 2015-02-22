<?php
// set this to the encoding that should be used to display the pages correctly
$messages['encoding'] = 'iso-8859-15';
$messages['locale_description'] = 'File di localizzazione italiano per LifeType';
// locale format, see Locale::formatDate for more information
$messages['date_format'] = '%d/%m/%Y %H:%M';

// days of the week
$messages['days'] = Array( 'Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato' );
// -- compatibility, do not touch -- //
$messages['Monday'] = $messages['days'][1];
$messages['Tuesday'] = $messages['days'][2];
$messages['Wednesday'] = $messages['days'][3];
$messages['Thursday'] = $messages['days'][4];
$messages['Friday'] = $messages['days'][5];
$messages['Saturday'] = $messages['days'][6];
$messages['Sunday'] = $messages['days'][0];

// abbreviations
$messages['daysshort'] = Array( 'Do', 'Lu', 'Ma', 'Me', 'Gi', 'Ve', 'Sa' );
// -- compatibility, do not touch -- //
$messages['Mo'] = $messages['daysshort'][1];
$messages['Tu'] = $messages['daysshort'][2];
$messages['We'] = $messages['daysshort'][3];
$messages['Th'] = $messages['daysshort'][4];
$messages['Fr'] = $messages['daysshort'][5];
$messages['Sa'] = $messages['daysshort'][6];
$messages['Su'] = $messages['daysshort'][0];

// months of the year
$messages['months'] = Array( 'Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre' );
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
$messages['message'] = 'Messaggio';
$messages['error'] = 'Errore';
$messages['date'] = 'Data';

// miscellaneous texts
$messages['of'] = 'di';
$messages['recently'] = 'Ultimi inserimenti'; 
$messages['comments'] = 'Commenti'; 
$messages['comment on this'] = 'Commenta';
$messages['my_links'] = 'I miei Link'; 
$messages['archives'] = 'Archivi'; 
$messages['search'] = 'cerca';
$messages['calendar'] = 'calendario';
$messages['search_s'] = 'Cerca';
$messages['search_this_blog'] = 'Cerca in questo blog:'; 
$messages['about_myself'] = 'Chi sono?';
$messages['permalink_title'] = 'Link permanente agli archivi';
$messages['permalink'] = 'Permalink';
$messages['posted_by'] = 'Inviato da';
$messages['reply_string'] = 'Re: ';
$messages['reply'] = 'Replica';
$messages['category'] = 'Categoria';

// add comment form
$messages['add_comment'] = 'Aggiungi un commento'; 
$messages['comment_topic'] = 'Argomento';
$messages['comment_text'] = 'Testo';
$messages['comment_username'] = 'Il tuo nome';
$messages['comment_email'] = 'La tua e-mail (se ne hai una)';
$messages['comment_url'] = 'Il tuo sito (se ne hai uno)';
$messages['comment_send'] = 'Invia';
$messages['comment_added'] = 'Commento registrato!';
$messages['comment_add_error'] = 'Errore nell\'inserimento del commento';
$messages['article_does_not_exist'] = 'L\'articolo non esiste';
$messages['no_posts_found'] = 'Nessun articolo trovato';
$messages['user_has_no_posts_yet'] = 'L\'utente non ha ancora nessun articolo';
$messages['back'] = 'Indietro';
$messages['post'] = 'articolo';
$messages['trackbacks_for_article'] = 'Trackback per l\'articolo: ';
$messages['trackback_excerpt'] = 'Brano';
$messages['trackback_weblog'] = 'Weblog';
$messages['search_results'] = 'Risultati della Ricerca';
$messages['search_matching_results'] = 'I seguenti articoli soddisfano i criteri di ricerca: ';
$messages['search_no_matching_posts'] = 'Nessun articolo trovato';
$messages['read_more'] = '(Continua)';
$messages['syndicate'] = 'Syndicate';
$messages['main'] = 'Principale';
$messages['about'] = 'Informazioni';
$messages['download'] = 'Scarica';
$messages['error_incorrect_email_address'] = 'L\'indirizzo email non è corretto';
$messages['invalid_url'] = 'You entered an invalid URL. Please correct and try again';

////// error messages /////
$messages['error_fetching_article'] = 'L\'articolo richiesto non può essere trovato.';
$messages['error_fetching_articles'] = 'Impossibile selezionare gli articoli';
$messages['error_fetching_category'] = 'There was an error fetching the category'; // translate
$messages['error_trackback_no_trackback'] = 'Non sono presenti trackback per l\'articolo.';
$messages['error_incorrect_article_id'] = 'Identificativo dell\'articolo non corretto.';
$messages['error_incorrect_blog_id'] = 'Identificativo del blog non corretto.';
$messages['error_comment_without_text'] = 'Devi almeno inserire del testo.';
$messages['error_comment_without_name'] = 'Devi almeno inserire il tuo nome o nickname.';
$messages['error_adding_comment'] = 'Errore durante l\'inserimento del commento.';
$messages['error_incorrect_parameter'] = 'Parametri errati.';
$messages['error_parameter_missing'] = 'Manca un parametro nella richiesta.';
$messages['error_comments_not_enabled'] = 'La funzione di commento è disabilitata su questo sito.';
$messages['error_incorrect_search_terms'] = 'Se non sai quello che cerchi, non riuscirai a trovarlo ;-)'; 
$messages['error_no_search_results'] = 'Non ci sono elementi che soddisfano i criteri di ricerca';
$messages['error_no_albums_defined'] = 'Non ci sono album in questo blog.';
$messages['error_incorrect_user'] = 'Utente non valido';

$messages['comment_default_title'] = '(Senza titolo)';
$messages['error_incorrect_category_id'] = 'L\'id della categoria non è corretto o nessun elemento selezionato';
$messages['form_authenticated'] = 'Autenticato';
$messages['posted_in'] = 'Inviato in';
$messages['previous_post'] = 'Precedente';
$messages['next_post'] = 'Successivo';
$messages['guestbook'] = 'Libro degli ospiti';
$messages['trackbacks'] = 'Trackback';
$messages['menu'] = 'Menù';
$messages['albums'] = 'Album';
$messages['admin'] = 'Amministratore';
$messages['categories'] = 'Categorie';
$messages['links'] = 'Link';

// missing strings //
$messages['num_reads'] = 'Letture';

$messages['error_fetching_resource'] = 'Il file specificato non è stato trovato.';
$messages['contact_me'] = 'Contattami';
$messages['required'] = 'Richiesto';

$messages['size'] = 'Dimensione';
$messages['format'] = 'Formato';
$messages['dimensions'] = 'Dimensioni';
$messages['bits_per_sample'] = 'Bit per campione (bps)';
$messages['sample_rate'] = 'Frequenza di campionamento';
$messages['number_of_channels'] = 'Numero di canali';
$messages['length'] = 'Lunghezza';

/// Strings added in LT 1.2.4 ///
$messages['audio_codec'] = 'Codec Audio';
$messages['video_codec'] = 'Codec Video';

/// Strings added in LT 1.2.5 ///
$messages['error_rdf_syndication_not_allowed'] = 'Error: Feeds are disabled for this blog.';

?>
