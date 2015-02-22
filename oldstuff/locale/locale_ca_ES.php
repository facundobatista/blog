<?php
// set this to the encoding that should be used to display the pages correctly
$messages['encoding'] = 'iso-8859-1';
$messages['locale_description'] = 'Traducci� de LifeType al catal�';
// locale format, see Locale::formatDate for more information
$messages['date_format'] = '%d/%m/%Y %H:%M';

// days of the week
$messages["days"] = Array( "Diumenge", "Dilluns", "Dimarts", "Dimecres", "Dijous", "Divendres", "Dissabte" );
// -- compatibility, do not touch -- //
$messages['Monday'] = $messages['days'][1];
$messages['Tuesday'] = $messages['days'][2];
$messages['Wednesday'] = $messages['days'][3];
$messages['Thursday'] = $messages['days'][4];
$messages['Friday'] = $messages['days'][5];
$messages['Saturday'] = $messages['days'][6];
$messages['Sunday'] = $messages['days'][0];

// abbreviations
$messages['daysshort'] = Array( 'Dg', 'Dl', 'Dm', 'Dc', 'Dj', 'Dv', 'Ds' );
// -- compatibility, do not touch -- //
$messages['Mo'] = $messages['daysshort'][1];
$messages['Tu'] = $messages['daysshort'][2];
$messages['We'] = $messages['daysshort'][3];
$messages['Th'] = $messages['daysshort'][4];
$messages['Fr'] = $messages['daysshort'][5];
$messages['Sa'] = $messages['daysshort'][6];
$messages['Su'] = $messages['daysshort'][0];

// months of the year
$messages["months"] = Array( "Gener", "Febrer", "Mar�", "Abril", "Maig", "Juny", "Juliol", "Agost", "Setembre", "Octubre", "Novembre", "Desembre" );
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
$messages["message"] = "Missatge";
$messages["error"] = "Error";
$messages["date"] = "Data";

// miscellaneous texts
$messages["of"] = "de";
$messages["recently"] = "Recentment";
$messages["comments"] = "Comentaris";
$messages["comment on this"] = "Comentaris";
$messages["my_links"] = "Els meus enlla�os";
$messages["archives"] = "Arxius";
$messages["search"] = "Cerca";
$messages["calendar"] = "Calendari";
$messages["search_s"] = "Cercar";
$messages["search_this_blog"] = "Cerca en aquest bloc:";
$messages["about_myself"] = "Presentaci�";
$messages["permalink_title"] = "Enlla� fix a un article concret dels arxius del bloc";
$messages["permalink"] = "Enlla� permanent";
$messages["posted_by"] = "Publicat per ";
$messages["on_the"] = "el ";
$messages["page"] = "p�gina";
$messages["posted"] = "publicat";
$messages['reply_string'] = 'Re: ';
$messages["reply"] = "Respon";
$messages['category'] = 'Categoria';

// add comment form
$messages["add_comment"] = "Afegeix un comentari";
$messages["comment_topic"] = "Tema";
$messages["comment_text"] = "Text";
$messages["comment_username"] = "El teu nom";
$messages["comment_email"] = "La teva adre�a de correu (si en tens)";
$messages["comment_url"] = "La teva p�gina personal (si en tens)";
$messages["comment_send"] = "Envia";
$messages["comment_added"] = "Comentari enviat!";
$messages["comment_add_error"] = "S'ha produ�t un error en enviar el comentari.";
$messages["article_does_not_exist"] = "L'article sol�licitat no existeix.";
$messages["no_posts_found"] = "No s'ha trobat cap article.";
$messages["user_has_no_posts_yet"] = "Aquest usuari no t� cap article encara.";
$messages["info_about_myself"] = "Informaci� sobre mi...";
$messages["back"] = "Torna";
$messages['post'] = 'article';
$messages["trackbacks_for_article"] = "Retronella�os per a l'article";
$messages["trackback_excerpt"] = "Fragment";
$messages["trackback_weblog"] = "Bloc";
$messages["search_results"] = "Resultats de la cerca";
$messages["search_matching_results"] = "Els seg�ents articles concorden amb els termes de la cerca";
$messages["search_no_matching_posts"] = "No s'ha trobat cap article que concordi amb els termes de la cerca";
$messages["read_more"] = "(Segueix)";
$messages['syndicate'] = 'Subscripci�';
$messages['main'] = 'Inici';
$messages['about'] = 'Sobre';
$messages['download'] = 'Baixa';
$messages['error_incorrect_email_address'] = 'L\'adre�a de correu electr�nic no �s correcta';
$messages['invalid_url'] = 'Has posat una URL inv�lida. Si us plau corregeix-la  torna-ho a provar';


////// error messages /////
$messages["error_fetching_article"] = "L'article especificat no s'ha pogut trobar.";
$messages["error_fetching_articles"] = "Els articles no s'han pogut carregar.";
$messages['error_fetching_category'] = 'S\'ha produ�t un error obtenint la categoria';
$messages["error_trackback_no_trackback"] = "No s'han trobat retroenlla�os per a l'article especificat.";
$messages["error_incorrect_article_id"] = "L'identificador de l'article �s incorrecte.";
$messages["error_incorrect_blog_id"] = "L'identificador del bloc �s incorrecte.";
$messages["error_comment_without_text"] = "Has d'escriure alguna cosa com a contingut del comentari.";
$messages["error_comment_without_name"] = "Has de donar com a m�nim el teu nom.";
$messages["error_adding_comment"] = "S'ha produ�t un error en afegir el comentari a la base de dades.";
$messages["error_incorrect_parameter"] = "Par�metre incorrecte.";
$messages["error_parameter_missing"] = "Falta un par�metre a la petici�.";
$messages["error_blog_has_no_links"] = "Aquest bloc encara no t� enlla�os.";
$messages["error_comments_not_enabled"] = "La funci� de comentar articles ha estat desactivada en aquest bloc.";
$messages['error_incorrect_search_terms'] = 'Els termes de la recerca no s�n v�lids.';
$messages['error_no_search_results'] = 'No hi ha coincid�ncies amb els termes de la cerca.';
$messages['error_no_albums_defined'] = 'Aquest bloc no t� cap �lbum definit.';
$messages['error_incorrect_category_id'] = 'L\'identificador de la categoria no �s correcte.';
$messages['error_incorrect_user'] = 'L\'usuari no �s v�lid';

$messages['posted_in'] = 'Publicat a';
$messages['form_authenticated'] = 'Autentificat';
$messages['previous_post'] = 'Anterior';
$messages['next_post'] = 'Seg�ent';
$messages['comment_default_title'] = '(Sense t�tol)';
$messages['trackbacks'] = 'Retroenlla�os';
$messages['menu'] = 'Men�';
$messages['albums'] = '�lbums';
$messages['admin'] = 'Administraci�';
$messages['guestbook'] = 'Llibre visites';
$messages['num_reads'] = 'Lectures';

$messages['error_fetching_resource'] = 'El fitxer no s\'ha pogut trobar.';
$messages['contact_me'] = 'Contacte';
$messages['required'] = 'Obligatori';

$messages['size'] = 'Mida';
$messages['format'] = 'Format';
$messages['dimensions'] = 'Dimensions';
$messages['bits_per_sample'] = 'Bits per mostra (BPS)';

/// Strings added in LT 1.2.4 ///
$messages['audio_codec'] = 'C�dex d\'�udio';
$messages['video_codec'] = 'C�dex de v�deo';
$messages['length'] = 'Duraci�';

/// Strings added in LT 1.2.5 ///
$messages['error_rdf_syndication_not_allowed'] = 'Error: La sindicaci� est� deshabilitada per aquest bloc.';

?>