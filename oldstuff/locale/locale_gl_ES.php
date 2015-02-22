<?php
// Traducción o galego de lifetype 121, realizada por Víctor Julio Quesada Varela, 2007. egalego@gmail.com, www.egalego.com

// set this to the encoding that should be used to display the pages correctly
$messages['encoding'] = 'iso-8859-1';
$messages['locale_description'] = 'Traducción o galego de LifeType';
// locale format, see Locale::formatDate for more information
$messages['date_format'] = '%d/%m/%Y %H:%M';

// days of the week
$messages['days'] = Array( 'Domingo', 'Luns', 'Martes', 'Mércores', 'Xoves', 'Venres', 'Sábado' );
// -- compatibility, do not touch -- //
$messages['Monday'] = $messages['days'][1];
$messages['Tuesday'] = $messages['days'][2];
$messages['Wednesday'] = $messages['days'][3];
$messages['Thursday'] = $messages['days'][4];
$messages['Friday'] = $messages['days'][5];
$messages['Saturday'] = $messages['days'][6];
$messages['Sunday'] = $messages['days'][0];

// abbreviations
$messages['daysshort'] = Array( 'Do', 'Lu', 'Ma', 'Me', 'Xo', 'Ve', 'Sa' );
// -- compatibility, do not touch -- //
$messages['Mo'] = $messages['daysshort'][1];
$messages['Tu'] = $messages['daysshort'][2];
$messages['We'] = $messages['daysshort'][3];
$messages['Th'] = $messages['daysshort'][4];
$messages['Fr'] = $messages['daysshort'][5];
$messages['Sa'] = $messages['daysshort'][6];
$messages['Su'] = $messages['daysshort'][0];

// months of the year
$messages['months'] = Array( 'Xaneiro', 'Febreiro', 'Marzo', 'Abril', 'Maio', 'Xuño', 'Xullo', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Decembro' );
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
$messages['message'] = 'Mensaxe';
$messages['error'] = 'Erro';
$messages['date'] = 'Data';

// miscellaneous texts
$messages['of'] = 'de';
$messages['recently'] = 'recentemente...';
$messages['comments'] = 'comentarios';
$messages['comment on this'] = 'Comentario';
$messages['my_links'] = 'meus enlaces';
$messages['archives'] = 'arquivos';
$messages['search'] = 'Procurar';
$messages['calendar'] = 'calendario';
$messages['search_s'] = 'Procurar';
$messages['search_this_blog'] = 'Procurar nesta bitácora:';
$messages['about_myself'] = 'Quen son?';
$messages['permalink_title'] = 'Enlace permanente os arquivos';
$messages['permalink'] = 'Enlace';
$messages['posted_by'] = 'Escrito por';
$messages['reply_string'] = 'Re: ';
$messages['reply'] = 'Responder';
$messages['category'] = 'Categoría';

$messages['add_comment'] = 'Engadir comentario';

$messages['comment_topic'] = 'Título';
$messages['comment_text'] = 'Texto';
$messages['comment_username'] = 'O Teu nome';
$messages['comment_email'] = 'enderezo de correo (opcional)';
$messages['comment_url'] = 'Páxina persoal (opcional)';
$messages['comment_send'] = 'Enviar';
$messages['comment_added'] = 'Comentario engadido!';
$messages['comment_add_error'] = 'Houbo un erro engadindo o comentario';
$messages['article_does_not_exist'] = 'O artigo non existe';
$messages['no_posts_found'] = 'Non se atoparon artigos';
$messages['user_has_no_posts_yet'] = 'O usuario todavía non escribiu ningun artigo';
$messages['back'] = 'Atrás';
$messages['post'] = 'artigo';
$messages['trackbacks_for_article'] = 'Retroenlaces do artigo: ';
$messages['trackback_excerpt'] = 'Fragmento';
$messages['trackback_weblog'] = 'Bitácora';
$messages['search_results'] = 'Resultados da búsqueda';
$messages['search_matching_results'] = 'Os seguintes artigos foron atopados: ';
$messages['search_no_matching_posts'] = 'Non se atoparon artigos';
$messages['read_more'] = '(Máis)';
$messages['syndicate'] = 'Agregar';
$messages['main'] = 'Principal';
$messages['about'] = 'Acerca de';
$messages['download'] = 'Descargar';
$messages['error_incorrect_email_address'] = 'O enderezo de correo non é válido';
$messages['invalid_url'] = 'You entered an invalid URL. Please correct and try again';


$messages['error_fetching_article'] = 'O artigo especificado non existe.';
$messages['error_fetching_articles'] = 'Non se atoparon artigos';
$messages['error_trackback_no_trackback'] = 'O artigo non recibiu ningun retroenlace';
$messages['error_incorrect_article_id'] = 'O identificador do artigo non e correcto';
$messages['error_incorrect_blog_id'] = 'O identificador da bitácora non e correcto';
$messages['error_comment_without_text'] = 'O texto do comentario está baleiro.';
$messages['error_comment_without_name'] = 'E necesario que dé o seu nome ou apodo.';
$messages['error_adding_comment'] = 'Houbo un erro engadindo o comentario.';
$messages['error_incorrect_parameter'] = 'Parámetro incorrecto.';
$messages['error_parameter_missing'] = 'Falta un parámetro.';
$messages['error_comments_not_enabled'] = 'Os comentarios foron desactivados nesta bitácora.';
$messages['error_incorrect_search_terms'] = 'Os térmos da búsqueda son incorrectos.';
$messages['error_no_search_results'] = 'Non se atoparon artigos que correspondan cos térmos da búsqueda.';
$messages['error_no_albums_defined'] = 'Non hai ningun álbum dispoñible nesta bitácora.';
$messages['error_incorrect_category_id'] = 'O identificador da categoría non é correcto ou non se seleccionaron categorías a borrar';

$messages['posted_in'] = 'Publicado en';
$messages['form_authenticated'] = 'Autentificado';
$messages['previous_post'] = 'Anterior';
$messages['next_post'] = 'Seguinte';
$messages['comment_default_title'] = '(Sen título)';
$messages['trackbacks'] = 'Retroenlaces';
$messages['menu'] = 'Menú';
$messages['albums'] = 'Albums';
$messages['admin'] = 'Administración';
$messages['guestbook'] = 'Libro de visitas';
$messages['num_reads'] = 'Lecturas';

$messages['error_fetching_resource'] = 'O ficheiro non se puido encontrar.';
$messages['contact_me'] = 'Contactar';
$messages['required'] = 'Obrigatorio';

$messages['size'] = 'Tamaño';
$messages['format'] = 'Formato';
$messages['dimensions'] = 'Dimensions';
$messages['bits_per_sample'] = 'Bits por mostra';
$messages['sample_rate'] = 'Frecuencia de mostreo';
$messages['number_of_channels'] = 'Número de canles';
$messages['length'] = 'Duración';

/// Strings added in LT 1.2.4 ///
$messages['audio_codec'] = 'Codec de audio';
$messages['video_codec'] = 'Codec de vídeo';

/// Strings added in LT 1.2.5 ///
$messages['error_rdf_syndication_not_allowed'] = 'Error: Feeds are disabled for this blog.';

?>
