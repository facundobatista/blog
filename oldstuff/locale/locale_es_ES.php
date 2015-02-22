<?php
// set this to the encoding that should be used to display the pages correctly
$messages['encoding'] = 'iso-8859-1';
$messages['locale_description'] = 'Traducción al español de LifeType';
// locale format, see Locale::formatDate for more information
$messages['date_format'] = '%d/%m/%Y %H:%M';

// days of the week
$messages['days'] = Array( 'Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado' );
// -- compatibility, do not touch -- //
$messages['Monday'] = $messages['days'][1];
$messages['Tuesday'] = $messages['days'][2];
$messages['Wednesday'] = $messages['days'][3];
$messages['Thursday'] = $messages['days'][4];
$messages['Friday'] = $messages['days'][5];
$messages['Saturday'] = $messages['days'][6];
$messages['Sunday'] = $messages['days'][0];

// abbreviations
$messages['daysshort'] = Array( 'Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa' );
// -- compatibility, do not touch -- //
$messages['Mo'] = $messages['daysshort'][1];
$messages['Tu'] = $messages['daysshort'][2];
$messages['We'] = $messages['daysshort'][3];
$messages['Th'] = $messages['daysshort'][4];
$messages['Fr'] = $messages['daysshort'][5];
$messages['Sa'] = $messages['daysshort'][6];
$messages['Su'] = $messages['daysshort'][0];

// months of the year
$messages['months'] = Array( 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
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
$messages['message'] = 'Mensaje';
$messages['error'] = 'Error';
$messages['date'] = 'Fecha';

// miscellaneous texts
$messages['of'] = 'de';
$messages['recently'] = 'recientemente...';
$messages['comments'] = 'comentarios';
$messages['comment on this'] = 'Comentario';
$messages['my_links'] = 'mis enlaces';
$messages['archives'] = 'archivos';
$messages['search'] = 'buscar';
$messages['calendar'] = 'calendario';
$messages['search_s'] = 'Buscar';
$messages['search_this_blog'] = 'Buscar en esta bitácora:';
$messages['about_myself'] = 'Quién soy?';
$messages['permalink_title'] = 'Enlace permanente a los archivos';
$messages['permalink'] = 'Enlace';
$messages['posted_by'] = 'Escrito por';
$messages['reply_string'] = 'Re: ';
$messages['reply'] = 'Responder';
$messages['category'] = 'Categoría';

$messages['add_comment'] = 'Añadir comentario';
$messages['comment_topic'] = 'Título';
$messages['comment_text'] = 'Texto';
$messages['comment_username'] = 'Tu nombre';
$messages['comment_email'] = 'Dirección de correo (opcional)';
$messages['comment_url'] = 'Página personal (opcional)';
$messages['comment_send'] = 'Enviar';
$messages['comment_added'] = 'Comentario añadido!';
$messages['comment_add_error'] = 'Hubo un error añadiendo el comentario';
$messages['article_does_not_exist'] = 'El artículo no existe';
$messages['no_posts_found'] = 'No se encontraron artículos';
$messages['user_has_no_posts_yet'] = 'El usuario todavía no ha escrito ningun artículo';
$messages['back'] = 'Atrás';
$messages['post'] = 'artículo';
$messages['trackbacks_for_article'] = 'Retroenlaces del artículo: ';
$messages['trackback_excerpt'] = 'Fragmento';
$messages['trackback_weblog'] = 'Bitácora';
$messages['search_results'] = 'Resultados de la búsqueda';
$messages['search_matching_results'] = 'Los siguientes artículos han sido encontrados: ';
$messages['search_no_matching_posts'] = 'No se encontraron artículos';
$messages['read_more'] = '(Más)';
$messages['syndicate'] = 'Agregar';
$messages['main'] = 'Principal';
$messages['about'] = 'Acerca de';
$messages['download'] = 'Descargar';
$messages['error_incorrect_email_address'] = 'La dirección de correo no es válida';
$messages['invalid_url'] = 'You entered an invalid URL. Please correct and try again';

$messages['error_fetching_article'] = 'El artículo especificado no existe.';
$messages['error_fetching_articles'] = 'No se encontraron artículos';
$messages['error_fetching_category'] = 'There was an error fetching the category'; // translate
$messages['error_trackback_no_trackback'] = 'El artículo no ha recibido ningun retroenlace';
$messages['error_incorrect_article_id'] = 'El identificador del artículo no es correcto';
$messages['error_incorrect_blog_id'] = 'El identificador de la bitácora no es correcto';
$messages['error_comment_without_text'] = 'El texto del comentario está vacío.';
$messages['error_comment_without_name'] = 'Es necesario que dé su nombre o apodo.';
$messages['error_adding_comment'] = 'Hubo un error añadiendo el comentario.';
$messages['error_incorrect_parameter'] = 'Parámetro incorrecto.';
$messages['error_parameter_missing'] = 'Falta un parámetro.';
$messages['error_comments_not_enabled'] = 'Los comentarios han sido desactivados en esta bitácora.';
$messages['error_incorrect_search_terms'] = 'Los términos de la búsqueda son incorrectos.';
$messages['error_no_search_results'] = 'No se encontraros artículos que se correspondan con los términos de la búsqueda.';
$messages['error_no_albums_defined'] = 'No hay ningun álbum disponible en esta bitácora.';
$messages['error_incorrect_category_id'] = 'El identificador de la categoría no es correcto o no se seleccionaron categorías a borrar';
$messages['error_incorrect_user'] = 'El usuario no es válido';

$messages['posted_in'] = 'Publicado en';
$messages['form_authenticated'] = 'Autentificado';
$messages['previous_post'] = 'Anterior';
$messages['next_post'] = 'Siguiente';
$messages['comment_default_title'] = '(Sin título)';
$messages['trackbacks'] = 'Retroenlaces';
$messages['menu'] = 'Menú';
$messages['albums'] = 'Albums';
$messages['admin'] = 'Administración';
$messages['guestbook'] = 'Libro de visitas';
$messages['num_reads'] = 'Lecturas';
$messages['categories'] = 'Categorías';

$messages['error_fetching_resource'] = 'El fichero no se pudo encontrar.';
$messages['contact_me'] = 'Contactar';
$messages['required'] = 'Obligatorio';

$messages['size'] = 'Tamaño';
$messages['format'] = 'Formato';
$messages['dimensions'] = 'Dimensiones';
$messages['bits_per_sample'] = 'Bits por muestra';
$messages['sample_rate'] = 'Frecuencia de muestreo';
$messages['number_of_channels'] = 'Número de canales';
$messages['length'] = 'Duración';

/// Strings added in LT 1.2.4 ///
$messages['audio_codec'] = 'Códec de audio';
$messages['video_codec'] = 'Códec de vídeo';

/// Strings added in LT 1.2.5 ///
$messages['error_rdf_syndication_not_allowed'] = 'Error: Feeds are disabled for this blog.';

?>
