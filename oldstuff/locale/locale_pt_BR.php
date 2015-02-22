<?php

// set this to the encoding that should be used to display the pages correctly
$messages['encoding'] = 'iso-8859-1';
$messages['locale_description'] = 'Portuguese-Brazilian locale file for LifeType';
// locale format, see Locale::formatDate for more information
$messages['date_format'] = '%d/%m/%Y %H:%M';

// days of the week
$messages['days'] = Array( 'Domingo', 'Segunda-feira', 'Ter�a-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-fera', 'S�bado' );
// -- compatibility, do not touch -- //
$messages['Monday'] = $messages['days'][1];
$messages['Tuesday'] = $messages['days'][2];
$messages['Wednesday'] = $messages['days'][3];
$messages['Thursday'] = $messages['days'][4];
$messages['Friday'] = $messages['days'][5];
$messages['Saturday'] = $messages['days'][6];
$messages['Sunday'] = $messages['days'][0];

// abbreviations
$messages['daysshort'] = Array( 'Do', 'Se', 'Te', 'Qu', 'Qu', 'Se', 'Sa' );
// -- compatibility, do not touch -- //
$messages['Mo'] = $messages['daysshort'][1];
$messages['Tu'] = $messages['daysshort'][2];
$messages['We'] = $messages['daysshort'][3];
$messages['Th'] = $messages['daysshort'][4];
$messages['Fr'] = $messages['daysshort'][5];
$messages['Sa'] = $messages['daysshort'][6];
$messages['Su'] = $messages['daysshort'][0];

// months of the year
$messages['months'] = Array( 'Janeiro', 'Fevereiro', 'Mar�o', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro' );
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
$messages['message'] = 'Messagem';
$messages['error'] = 'Erro';
$messages['date'] = 'Data';

// miscellaneous texts
$messages['of'] = 'of';
$messages['recently'] = 'recentes...';
$messages['comments'] = 'coment�rios';
$messages['comment on this'] = 'Coment�rio';
$messages['my_links'] = 'Meus endere�os';
$messages['archives'] = 'arquivos';
$messages['search'] = 'buscar';
$messages['calendar'] = 'calend�rio';
$messages['search_s'] = 'Buscar';
$messages['search_this_blog'] = 'Procurar neste blog:';
$messages['about_myself'] = 'Who am I?';
$messages['permalink_title'] = 'Permanent link to the archives';
$messages['permalink'] = 'Permalink';
$messages['posted_by'] = 'Postado por';
$messages['reply_string'] = 'Re: ';
$messages['reply'] = 'Reply';
$messages['category'] = 'Categoria';

// add comment form
$messages['add_comment'] = 'Adicionar coment�rio';
$messages['comment_topic'] = 'T�pico';
$messages['comment_text'] = 'Texto';
$messages['comment_username'] = 'Seu nome';
$messages['comment_email'] = 'Seu endere�o-eletr�nico (se tiver)';
$messages['comment_url'] = 'Sua p�gina web pessoal (se tiver)';
$messages['comment_send'] = 'Enviar';
$messages['comment_added'] = 'Coment�rio adicionado!';
$messages['comment_add_error'] = 'Erro ao adicionar coment�rio';
$messages['article_does_not_exist'] = 'O artigo n�o existe';
$messages['no_posts_found'] = 'Nenhuma postagem foi encontrada';
$messages['user_has_no_posts_yet'] = 'O usu�rio n�o tem nenhua postagem ainda';
$messages['back'] = 'Voltar';
$messages['post'] = 'Postar';
$messages['trackbacks_for_article'] = 'Trackbacks para artigo: ';
$messages['trackback_excerpt'] = 'Excerpt';
$messages['trackback_weblog'] = 'Weblog';
$messages['search_results'] = 'Resultado da Busca';
$messages['search_matching_results'] = 'As seguintes postagens combinam seus termos da busca: ';
$messages['search_no_matching_posts'] = 'Nenhuma postagem combinando foi encontrado';
$messages['read_more'] = '(Mais)';
$messages['syndicate'] = 'Sindic�ncia';
$messages['main'] = 'Principal';
$messages['about'] = 'Sobre';
$messages['download'] = 'Baixar';
$messages['error_incorrect_email_address'] = 'O endere�o email n�o est� correto';
$messages['invalid_url'] = 'Voc� incorporou um URL inv�lido. Corrija e tente outra vez';

////// error messages /////
$messages['error_fetching_article'] = 'O artigo que voc� especificou n�o pode ser encontrado.';
$messages['error_fetching_articles'] = 'Os artigos n�o puderam ser buscados.';
$messages['error_fetching_category'] = 'Houve um erro na busca da categoria';
$messages['error_trackback_no_trackback'] = 'No trackbacks were found for the article.';
$messages['error_incorrect_article_id'] = 'The article identifier is not correct.';
$messages['error_incorrect_blog_id'] = 'The blog identifier is not correct.';
$messages['error_comment_without_text'] = 'You should at least provide some text.';
$messages['error_comment_without_name'] = 'You should at least give your name or nickname.';
$messages['error_adding_comment'] = 'There was an error adding the comment.';
$messages['error_incorrect_parameter'] = 'Par�metro incorreto.';
$messages['error_parameter_missing'] = 'H� um par�metro que falta do pedido.';
$messages['error_comments_not_enabled'] = 'The commenting feature has been disabled in this site.';
$messages['error_incorrect_search_terms'] = 'Os termos da busca s�o inv�lidos';
$messages['error_no_search_results'] = 'Nenhum artigo que combina os termos da busca foi encontrado';
$messages['error_no_albums_defined'] = 'N�o h� nenhum album dispon�vel neste blog.';
$messages['error_incorrect_category_id'] = 'O identificador da categoria n�o est� correto ou nenhum artigo foi selecionado';
$messages['error_fetching_resource'] = 'O arquivo que voc� especificou n�o pode ser encontrado.';
$messages['error_incorrect_user'] = 'Usu�rio n�o � v�lido';

$messages['form_authenticated'] = 'Autenticado';
$messages['posted_in'] = 'Postado em';

$messages['previous_post'] = 'Anterior';
$messages['next_post'] = 'Pr�ximo';
$messages['comment_default_title'] = '(Sem t�tulo)';
$messages['guestbook'] = 'Livro de Visita';
$messages['trackbacks'] = 'Trackbacks';
$messages['menu'] = 'Menu';
$messages['albums'] = 'Albuns';
$messages['admin'] = 'Admin';
$messages['links'] = 'Endere�os';
$messages['categories'] = 'Categorias';
$messages['articles'] = 'Artigos';

$messages['num_reads'] = 'Visualia��o';
$messages['contact_me'] = 'Contato';
$messages['required'] = 'Requerido';

$messages['size'] = 'Tamanho';
$messages['format'] = 'Formato';
$messages['dimensions'] = 'Dimens�o';
$messages['bits_per_sample'] = 'Bits per sample';
$messages['sample_rate'] = 'Taxa da amostragem';
$messages['number_of_channels'] = 'N�mero de canais';
$messages['length'] = 'Largura';

/// Strings added in LT 1.2.4 ///
$messages['audio_codec'] = 'Audio codec';
$messages['video_codec'] = 'Video codec';

/// Strings added in LT 1.2.5 ///
$messages['error_rdf_syndication_not_allowed'] = 'Error: Feeds are disabled for this blog.';

?>
