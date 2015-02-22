<?php

// set this to the encoding that should be used to display the pages correctly
$messages['encoding'] = 'iso-8859-1';
$messages['locale_description'] = 'Indonesian locale file for LifeType';
// locale format, see Locale::formatDate for more information
$messages['date_format'] = '%d-%m-%Y %H.%M';

// days of the week
$messages['days'] = Array( 'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu' );
// -- compatibility, do not touch -- //
$messages['Monday'] = $messages['days'][1];
$messages['Tuesday'] = $messages['days'][2];
$messages['Wednesday'] = $messages['days'][3];
$messages['Thursday'] = $messages['days'][4];
$messages['Friday'] = $messages['days'][5];
$messages['Saturday'] = $messages['days'][6];
$messages['Sunday'] = $messages['days'][0];

// abbreviations
$messages['daysshort'] = Array( 'Mn', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb' );
// -- compatibility, do not touch -- //
$messages['Mo'] = $messages['daysshort'][1];
$messages['Tu'] = $messages['daysshort'][2];
$messages['We'] = $messages['daysshort'][3];
$messages['Th'] = $messages['daysshort'][4];
$messages['Fr'] = $messages['daysshort'][5];
$messages['Sa'] = $messages['daysshort'][6];
$messages['Su'] = $messages['daysshort'][0];

// months of the year
$messages['months'] = Array( 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember' );
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
$messages['message'] = 'Message';
$messages['error'] = 'Error';
$messages['date'] = 'Date';

// miscellaneous texts
$messages['of'] = 'dari';
$messages['recently'] = 'akhir-akhir ini...';
$messages['comments'] = 'komentar';
$messages['comment on this'] = 'Komentar';
$messages['my_links'] = 'Pranala saya';
$messages['archives'] = 'arsip';
$messages['search'] = 'pencarian';
$messages['calendar'] = 'Kalender';
$messages['search_s'] = 'Pencarian';
$messages['search_this_blog'] = 'Cari di blog ini:';
$messages['about_myself'] = 'Siapa saya?';
$messages['permalink_title'] = 'Pranala tetap ke arsip';
$messages['permalink'] = 'Pranala Tetap';
$messages['posted_by'] = 'Dikirim oleh';
$messages['reply_string'] = 'Bls: ';
$messages['reply'] = 'Balas';
$messages['category'] = 'Kategori';

// add comment form
$messages['add_comment'] = 'Tambah komentar';
$messages['comment_topic'] = 'Topik';
$messages['comment_text'] = 'Teks';
$messages['comment_username'] = 'Nama Anda';
$messages['comment_email'] = 'Alamat pos-el Anda (bila ada)';
$messages['comment_url'] = 'Halaman pribadi Anda (bila ada)';
$messages['comment_send'] = 'Kirim';
$messages['comment_added'] = 'Komentar ditambahkan!';
$messages['comment_add_error'] = 'Terjadi kesalahan saat menambahkan komentar';
$messages['article_does_not_exist'] = 'Artikel tidak ada';
$messages['no_posts_found'] = 'Kiriman tidak ditemukan';
$messages['user_has_no_posts_yet'] = 'Pengguna belum memiliki kiriman apapun';
$messages['back'] = 'Kembali';
$messages['post'] = 'Kirim';
$messages['trackbacks_for_article'] = 'Jalirbaliki untuk artikel: ';
$messages['trackback_excerpt'] = 'Kutipan';
$messages['trackback_weblog'] = 'Weblog';
$messages['search_results'] = 'Hasil Pencarian';
$messages['search_matching_results'] = 'Kiriman ini cocok dengan syarat pencarian Anda: ';
$messages['search_no_matching_posts'] = 'Tidak ada kiriman yang cocok ditemukan';
$messages['read_more'] = '(Selengkapnya)';
$messages['syndicate'] = 'Sindikasikan';
$messages['main'] = 'Utama';
$messages['about'] = 'Tentang';
$messages['download'] = 'Unduh';
$messages['error_incorrect_email_address'] = 'Alamat pos-el tidak benar';

////// error messages /////
$messages['error_fetching_article'] = 'Artikel yang Anda sebutkan tidak ditemukan.';
$messages['error_fetching_articles'] = 'Artikel tidak dapat diambil.';
$messages['error_fetching_category'] = 'Terjadi kesalahan dalam pengambilan kategori';
$messages['error_trackback_no_trackback'] = 'Tidak ada jalur balikan ditemukan untuk artikel ini.';
$messages['error_incorrect_article_id'] = 'Pengenal artikel tidak benar.';
$messages['error_incorrect_blog_id'] = 'Pengenal blog tidak benar.';
$messages['error_comment_without_text'] = 'Anda harus sedikitnya mencantumkan beberapa teks.';
$messages['error_comment_without_name'] = 'Anda harus sedikitnya memberikan nama atau panggilan Anda.';
$messages['error_adding_comment'] = 'Terjadi kesalahan saat menambahkan komentar.';
$messages['error_incorrect_parameter'] = 'Parameter tidak bentar.';
$messages['error_parameter_missing'] = 'Satu parameter rusak dari permintaan ini.';
$messages['error_comments_not_enabled'] = 'Fitur pengomentaran dimatikan dalam situs ini.';
$messages['error_incorrect_search_terms'] = 'Syarat pencarian tidak benar';
$messages['error_no_search_results'] = 'Tidak ada hal dalam syarat pencarian yang cocok ditemukan';
$messages['error_no_albums_defined'] = 'Tidak ada album tersedia dalam blog ini.';
$messages['error_incorrect_category_id'] = 'Pengenal kategori tidak benar atau tidak ada sesuatu yang dipilih';
$messages['error_fetching_resource'] = 'Berkas yang Anda sebutkan tidak ditemukan.';
$messages['error_incorrect_user'] = 'Pengguna tidak benar';

$messages['form_authenticated'] = 'Terotentikasi';
$messages['posted_in'] = 'Dikirim dalam';

$messages['previous_post'] = 'Sebelumnya';
$messages['next_post'] = 'Selanjutnya';
$messages['comment_default_title'] = '(Tak Berjudul)';
$messages['guestbook'] = 'Buku Tamu';
$messages['trackbacks'] = 'Jalur Balikan';
$messages['menu'] = 'Menu';
$messages['albums'] = 'Album';
$messages['admin'] = 'Admin';
$messages['links'] = 'Pranala';
$messages['categories'] = 'Kategori';
$messages['articles'] = 'Artikel';

$messages['num_reads'] = 'Dilihat';
$messages['contact_me'] = 'Hubungi Saya';
$messages['required'] = 'Dibutuhkan';

$messages['size'] = 'Ukuran';
$messages['format'] = 'Format';
$messages['dimensions'] = 'Dimensi';
$messages['bits_per_sample'] = 'Bit tiap sampel';
$messages['sample_rate'] = 'Rate sampel';
$messages['number_of_channels'] = 'Jumlah saluran';
$messages['length'] = 'Panjang';

/// Strings added in LT 1.2.4 ///
$messages['audio_codec'] = 'Kodek audio';
$messages['video_codec'] = 'Kodek video';

/// Strings added in LT 1.2.5 ///
$messages['error_rdf_syndication_not_allowed'] = 'Kesalahan: Feed dinonaktifkan pada blog ini.';

?>