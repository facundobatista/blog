<?php
// set this to the encoding that should be used to display the pages correctly
$messages['encoding'] = 'iso-8859-1';
$messages['locale_description'] = 'Traducci�n al espa�ol de LifeType';
// locale format, see Locale::formatDate for more information
$messages['date_format'] = '%d/%m/%Y %H:%M';

// days of the week
$messages['days'] = Array( 'Domingo', 'Lunes', 'Martes', 'Mi�rcoles', 'Jueves', 'Viernes', 'S�bado' );
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
$messages['search_this_blog'] = 'Buscar en esta bit�cora:';
$messages['about_myself'] = 'Qui�n soy?';
$messages['permalink_title'] = 'Enlace permanente a los archivos';
$messages['permalink'] = 'Enlace';
$messages['posted_by'] = 'Escrito por';
$messages['reply_string'] = 'Re: ';
$messages['reply'] = 'Responder';
$messages['category'] = 'Categor�a';

$messages['add_comment'] = 'A�adir comentario';
$messages['comment_topic'] = 'T�tulo';
$messages['comment_text'] = 'Texto';
$messages['comment_username'] = 'Tu nombre';
$messages['comment_email'] = 'Direcci�n de correo (opcional)';
$messages['comment_url'] = 'P�gina personal (opcional)';
$messages['comment_send'] = 'Enviar';
$messages['comment_added'] = 'Comentario a�adido!';
$messages['comment_add_error'] = 'Hubo un error a�adiendo el comentario';
$messages['article_does_not_exist'] = 'El art�culo no existe';
$messages['no_posts_found'] = 'No se encontraron art�culos';
$messages['user_has_no_posts_yet'] = 'El usuario todav�a no ha escrito ningun art�culo';
$messages['back'] = 'Atr�s';
$messages['post'] = 'art�culo';
$messages['trackbacks_for_article'] = 'Retroenlaces del art�culo: ';
$messages['trackback_excerpt'] = 'Fragmento';
$messages['trackback_weblog'] = 'Bit�cora';
$messages['search_results'] = 'Resultados de la b�squeda';
$messages['search_matching_results'] = 'Los siguientes art�culos han sido encontrados: ';
$messages['search_no_matching_posts'] = 'No se encontraron art�culos';
$messages['read_more'] = '(M�s)';
$messages['syndicate'] = 'Agregar';
$messages['main'] = 'Principal';
$messages['about'] = 'Acerca de';
$messages['download'] = 'Descargar';
$messages['error_incorrect_email_address'] = 'La direcci�n de correo no es v�lida';
$messages['invalid_url'] = 'You entered an invalid URL. Please correct and try again';

$messages['error_fetching_article'] = 'El art�culo especificado no existe.';
$messages['error_fetching_articles'] = 'No se encontraron art�culos';
$messages['error_fetching_category'] = 'There was an error fetching the category'; // translate
$messages['error_trackback_no_trackback'] = 'El art�culo no ha recibido ningun retroenlace';
$messages['error_incorrect_article_id'] = 'El identificador del art�culo no es correcto';
$messages['error_incorrect_blog_id'] = 'El identificador de la bit�cora no es correcto';
$messages['error_comment_without_text'] = 'El texto del comentario est� vac�o.';
$messages['error_comment_without_name'] = 'Es necesario que d� su nombre o apodo.';
$messages['error_adding_comment'] = 'Hubo un error a�adiendo el comentario.';
$messages['error_incorrect_parameter'] = 'Par�metro incorrecto.';
$messages['error_parameter_missing'] = 'Falta un par�metro.';
$messages['error_comments_not_enabled'] = 'Los comentarios han sido desactivados en esta bit�cora.';
$messages['error_incorrect_search_terms'] = 'Los t�rminos de la b�squeda son incorrectos.';
$messages['error_no_search_results'] = 'No se encontraros art�culos que se correspondan con los t�rminos de la b�squeda.';
$messages['error_no_albums_defined'] = 'No hay ningun �lbum disponible en esta bit�cora.';
$messages['error_incorrect_category_id'] = 'El identificador de la categor�a no es correcto o no se seleccionaron categor�as a borrar';
$messages['error_incorrect_user'] = 'El usuario no es v�lido';

$messages['posted_in'] = 'Publicado en';
$messages['form_authenticated'] = 'Autentificado';
$messages['previous_post'] = 'Anterior';
$messages['next_post'] = 'Siguiente';
$messages['comment_default_title'] = '(Sin t�tulo)';
$messages['trackbacks'] = 'Retroenlaces';
$messages['menu'] = 'Men�';
$messages['albums'] = 'Albums';
$messages['admin'] = 'Administraci�n';
$messages['guestbook'] = 'Libro de visitas';
$messages['num_reads'] = 'Lecturas';
$messages['categories'] = 'Categor�as';

$messages['error_fetching_resource'] = 'El fichero no se pudo encontrar.';
$messages['contact_me'] = 'Contactar';
$messages['required'] = 'Obligatorio';

$messages['size'] = 'Tama�o';
$messages['format'] = 'Formato';
$messages['dimensions'] = 'Dimensiones';
$messages['bits_per_sample'] = 'Bits por muestra';
$messages['sample_rate'] = 'Frecuencia de muestreo';
$messages['number_of_channels'] = 'N�mero de canales';
$messages['length'] = 'Duraci�n';

/// Strings added in LT 1.2.4 ///
$messages['audio_codec'] = 'C�dec de audio';
$messages['video_codec'] = 'C�dec de v�deo';

/// Strings added in LT 1.2.5 ///
$messages['error_rdf_syndication_not_allowed'] = 'Error: Feeds are disabled for this blog.';

?>
