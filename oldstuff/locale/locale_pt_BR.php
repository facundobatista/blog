<?php

// set this to the encoding that should be used to display the pages correctly
$messages['encoding'] = 'iso-8859-1';
$messages['locale_description'] = 'Portuguese-Brazilian locale file for LifeType';
// locale format, see Locale::formatDate for more information
$messages['date_format'] = '%d/%m/%Y %H:%M';

// days of the week
$messages['days'] = Array( 'Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-fera', 'Sábado' );
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
$messages['months'] = Array( 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro' );
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
$messages['comments'] = 'comentários';
$messages['comment on this'] = 'Comentário';
$messages['my_links'] = 'Meus endereços';
$messages['archives'] = 'arquivos';
$messages['search'] = 'buscar';
$messages['calendar'] = 'calendário';
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
$messages['add_comment'] = 'Adicionar comentário';
$messages['comment_topic'] = 'Tópico';
$messages['comment_text'] = 'Texto';
$messages['comment_username'] = 'Seu nome';
$messages['comment_email'] = 'Seu endereço-eletrônico (se tiver)';
$messages['comment_url'] = 'Sua página web pessoal (se tiver)';
$messages['comment_send'] = 'Enviar';
$messages['comment_added'] = 'Comentário adicionado!';
$messages['comment_add_error'] = 'Erro ao adicionar comentário';
$messages['article_does_not_exist'] = 'O artigo não existe';
$messages['no_posts_found'] = 'Nenhuma postagem foi encontrada';
$messages['user_has_no_posts_yet'] = 'O usuário não tem nenhua postagem ainda';
$messages['back'] = 'Voltar';
$messages['post'] = 'Postar';
$messages['trackbacks_for_article'] = 'Trackbacks para artigo: ';
$messages['trackback_excerpt'] = 'Excerpt';
$messages['trackback_weblog'] = 'Weblog';
$messages['search_results'] = 'Resultado da Busca';
$messages['search_matching_results'] = 'As seguintes postagens combinam seus termos da busca: ';
$messages['search_no_matching_posts'] = 'Nenhuma postagem combinando foi encontrado';
$messages['read_more'] = '(Mais)';
$messages['syndicate'] = 'Sindicância';
$messages['main'] = 'Principal';
$messages['about'] = 'Sobre';
$messages['download'] = 'Baixar';
$messages['error_incorrect_email_address'] = 'O endereço email não está correto';
$messages['invalid_url'] = 'Você incorporou um URL inválido. Corrija e tente outra vez';

////// error messages /////
$messages['error_fetching_article'] = 'O artigo que você especificou não pode ser encontrado.';
$messages['error_fetching_articles'] = 'Os artigos não puderam ser buscados.';
$messages['error_fetching_category'] = 'Houve um erro na busca da categoria';
$messages['error_trackback_no_trackback'] = 'No trackbacks were found for the article.';
$messages['error_incorrect_article_id'] = 'The article identifier is not correct.';
$messages['error_incorrect_blog_id'] = 'The blog identifier is not correct.';
$messages['error_comment_without_text'] = 'You should at least provide some text.';
$messages['error_comment_without_name'] = 'You should at least give your name or nickname.';
$messages['error_adding_comment'] = 'There was an error adding the comment.';
$messages['error_incorrect_parameter'] = 'Parâmetro incorreto.';
$messages['error_parameter_missing'] = 'Há um parâmetro que falta do pedido.';
$messages['error_comments_not_enabled'] = 'The commenting feature has been disabled in this site.';
$messages['error_incorrect_search_terms'] = 'Os termos da busca são inválidos';
$messages['error_no_search_results'] = 'Nenhum artigo que combina os termos da busca foi encontrado';
$messages['error_no_albums_defined'] = 'Não há nenhum album disponível neste blog.';
$messages['error_incorrect_category_id'] = 'O identificador da categoria não está correto ou nenhum artigo foi selecionado';
$messages['error_fetching_resource'] = 'O arquivo que você especificou não pode ser encontrado.';
$messages['error_incorrect_user'] = 'Usuário não é válido';

$messages['form_authenticated'] = 'Autenticado';
$messages['posted_in'] = 'Postado em';

$messages['previous_post'] = 'Anterior';
$messages['next_post'] = 'Próximo';
$messages['comment_default_title'] = '(Sem título)';
$messages['guestbook'] = 'Livro de Visita';
$messages['trackbacks'] = 'Trackbacks';
$messages['menu'] = 'Menu';
$messages['albums'] = 'Albuns';
$messages['admin'] = 'Admin';
$messages['links'] = 'Endereços';
$messages['categories'] = 'Categorias';
$messages['articles'] = 'Artigos';

$messages['num_reads'] = 'Visualiação';
$messages['contact_me'] = 'Contato';
$messages['required'] = 'Requerido';

$messages['size'] = 'Tamanho';
$messages['format'] = 'Formato';
$messages['dimensions'] = 'Dimensão';
$messages['bits_per_sample'] = 'Bits per sample';
$messages['sample_rate'] = 'Taxa da amostragem';
$messages['number_of_channels'] = 'Número de canais';
$messages['length'] = 'Largura';

/// Strings added in LT 1.2.4 ///
$messages['audio_codec'] = 'Audio codec';
$messages['video_codec'] = 'Video codec';

/// Strings added in LT 1.2.5 ///
$messages['error_rdf_syndication_not_allowed'] = 'Error: Feeds are disabled for this blog.';

?>
