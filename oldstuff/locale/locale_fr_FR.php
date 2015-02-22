<?php
// Initial translation by Nino NJOPKOU
// nino@akopo.com - http://nino.akopo.com
// Yaoundé - CAMEROUN (Africa)

// 6/06/2007 - Modifications by Gabriel ROUSSEAU
// grvg@free.fr - http://grvg.free.fr
// FRANCE
// – L'encodage a été remis correctement sur UTF-8
// – Les jours et les mois doivent être en minuscules.
// – Corrections de fautes de frappes et de quelques angliscismes.
// – Changement des apostrophes informatiques ' par les apostrophes typographiques ’
// – Respect des règles de la typographie de ponctuation francophone (espaces, etc…)
// – Harmonisation globale (ajouts de points, utilisation de la même logique partout)
// – Remise à niveau avec le dernier locale_en_UK qui est la référence pour les clef

// 06/07/2007 - added LT 1.2.4 strings by Gabriel ROUSSEAU


$messages["encoding"] = 'UTF-8';
$messages['locale_description'] = 'Traduction fran&ccedil;aise de LifeType';	// must use HTML entities to be ok with any charset

// locale format, see Locale::formatDate for more information
$messages['date_format'] = '%d/%m/%Y %H:%M';

$messages['days'] = Array( "dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi" );
// -- compatibility, do not touch -- //
$messages["Monday"] = $messages["days"][1];
$messages["Tuesday"] = $messages["days"][2];
$messages["Wednesday"] = $messages["days"][3];
$messages["Thursday"] = $messages["days"][4];
$messages["Friday"] = $messages["days"][5];
$messages["Saturday"] = $messages["days"][6];
$messages["Sunday"] = $messages["days"][0];

// abbreviations
$messages["daysshort"] = Array( "di", "lu", "ma", "me", "je", "ve", "sa" );
// -- compatibility, do not touch -- //
$messages["Mo"] = $messages["daysshort"][1];
$messages["Tu"] = $messages["daysshort"][2];
$messages["We"] = $messages["daysshort"][3];
$messages["Th"] = $messages["daysshort"][4];
$messages["Fr"] = $messages["daysshort"][5];
$messages["Sa"] = $messages["daysshort"][6];
$messages["Su"] = $messages["daysshort"][0];

// months of the year
$messages["months"] = Array( "janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre" );
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
$messages['message'] = 'Message';
$messages['error'] = 'Erreur';
$messages['date'] = 'Date';
$messages['of'] = 'de';
$messages['recently'] = 'Récemment…';
$messages['comments'] = 'Commentaires';
$messages['comment on this'] = 'Commenter';
$messages['my_links'] = 'Mes liens';
$messages['archives'] = 'Archives';
$messages['search'] = 'Chercher';
$messages['calendar'] = 'Calendrier';
$messages['search_s'] = 'Recherche';
$messages['search_this_blog'] = 'Chercher dans ce blog :';
$messages['about_myself'] = 'Qui suis-je ?';
$messages['permalink_title'] = 'Liens permanents vers les archives';
$messages['permalink'] = 'Permalien';
$messages['posted_by'] = 'Par';
$messages['reply_string'] = 'À';
$messages['reply'] = 'Répondre';
$messages['articles'] = 'Articles';
$messages['contact_me'] = 'Me contacter';
$messages['required'] = 'requis';
$messages['form_authenticated'] = 'Authentifié(e)';
$messages['posted_in'] = 'Posté dans';
$messages['previous_post'] = 'Article précédent';
$messages['next_post'] = 'Article suivant';
$messages['comment_default_title'] = '(sans titre)';
$messages['guestbook'] = 'Livre d’or';
$messages['trackbacks'] = 'Rétroliens';
$messages['menu'] = 'Menu';
$messages['albums'] = 'Albums';
$messages['admin'] = 'Administrateur';
$messages['links'] = 'Liens';
$messages['categories'] = 'Catégories';
$messages['num_reads'] = 'Clics';
$messages['category'] = 'Catégorie';

// add comment form
$messages['add_comment'] = 'Commenter';
$messages['comment_topic'] = 'Sujet';
$messages['comment_text'] = 'Texte';
$messages['comment_username'] = 'Identifiant';
$messages['comment_email'] = 'Courriel (facultatif)';
$messages['comment_url'] = 'Page personnelle (facultatif)';
$messages['comment_send'] = 'Envoyer';
$messages['comment_added'] = 'Commentaire ajouté !';
$messages['comment_add_error'] = 'Erreur lors de l’ajout du commentaire';
$messages['article_does_not_exist'] = 'L’article n’existe pas';
$messages['no_posts_found'] = 'Aucun article trouvé';
$messages['user_has_no_posts_yet'] = 'Cet utilisateur n’a encore posté aucun article';
$messages['back'] = 'Retour';
$messages['post'] = 'Poster';
$messages['trackbacks_for_article'] = 'Rétroliens pour l’article ';
$messages['trackback_excerpt'] = 'Extrait';
$messages['trackback_weblog'] = 'Blog';
$messages['search_results'] = 'Résultats de la recherche';
$messages['search_matching_results'] = 'Résultats correspondants : ';
$messages['search_no_matching_posts'] = 'Aucun article trouvé';
$messages['read_more'] = '<br /> (Lire la suite de l’article)';
$messages['syndicate'] = 'Syndiquer';
$messages['main'] = 'Principal';
$messages['about'] = 'À propos';
$messages['download'] = 'Télécharger';
$messages['invalid_url'] = 'You entered an invalid URL. Please correct and try again';

////// error messages /////
$messages['error_fetching_article'] = 'Article spécifié non trouvé.';
$messages['error_fetching_articles'] = 'Erreur : Les articles ne peuvent pas être affichés.';
$messages['error_trackback_no_trackback'] = 'Aucun rétrolien trouvé pour cet article.';
$messages['error_incorrect_article_id'] = 'Identifiant d’article incorrect.';
$messages['error_incorrect_blog_id'] = 'Identifiant du blog incorrect.';
$messages['error_comment_without_text'] = 'Texte obligatoire.';
$messages['error_comment_without_name'] = 'Nom et/ou prénom obligatoires';
$messages['error_adding_comment'] = 'Erreur lors de l’ajout du commentaire.';
$messages['error_incorrect_parameter'] = 'Paramètre incorrect.';
$messages['error_parameter_missing'] = 'Paramètre manquant dans la requête.';
$messages['error_comments_not_enabled'] = 'Commentaires désactivés sur ce site.';
$messages['error_incorrect_search_terms'] = 'Termes recherchés invalides';
$messages['error_no_search_results'] = 'Pas de correspondance trouvée pour les termes recherchés';
$messages['error_no_albums_defined'] = 'Aucun album n’est disponible sur ce blog.';
$messages['error_incorrect_category_id'] = 'Catégorie de l’identifiant incorrecte, ou aucune entrée sélectionnée';
$messages['error_incorrect_email_address'] = 'Adresse électronique incorrecte';
$messages['error_fetching_category'] = 'Erreur lors de la requête de la catégorie.';
$messages['error_fetching_resource'] = 'Erreur lors de la requête de la ressource.';
$messages['error_incorrect_user'] = 'Membre non valide';

$messages['size'] = 'Taille';
$messages['format'] = 'Format';
$messages['dimensions'] = 'Dimensions';
$messages['bits_per_sample'] = 'Bits par échantillon';
$messages['sample_rate'] = 'Taux de l’échantillonnage';
$messages['number_of_channels'] = 'Nombre de canaux';
$messages['length'] = 'Longueur';

/// Strings added in LT 1.2.4 ///
$messages['audio_codec'] = 'Codec audio';
$messages['video_codec'] = 'Codec vidéo';

/// Strings added in LT 1.2.5 ///
$messages['error_rdf_syndication_not_allowed'] = 'Error: Feeds are disabled for this blog.';

?>
