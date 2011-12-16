<?php
/**
 * 
 * @package: phpBB 3.0.8 :: BRIDGE phpBB & WordPress -> root/language/es/mods :: [es][Spanish]
 * @version: $Id: wp_phpbb_bridge.php, v0.0.9 2011/10/25 11:10:25 leviatan21 Exp $
 * @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
 * @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
 * 
 */

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. "Message %d" is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., "Click %sHERE%s" is fine
// Reference : http://www.phpbb.com/mods/documentation/phpbb-documentation/language/index.php#lang-use-php
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	// the page title numbering
	'WP_PAGE_NUMBER'			=> 'Página %s',

	// footer
	'WP_RSS_NOTES'				=> '%1$s y %2$s',
	'WP_RSS_ENRIES_LINK'		=> 'Entradas (RSS)',
	'WP_RSS_COMMENTS_LINK'		=> 'Comentarios (RSS)',
	'WP_DEBUG_NOTE'				=> '%d consultas. %s segundos.',

	// Navbar
	'WP_TITLE_WEB'				=> 'Sitio',
	'WP_TITLE_WEB_EXPLAIN '		=> 'Click aqui para ir al sitio',
	'WP_TITLE_BLOG'				=> 'Blog',
	'WP_TITLE_BLOG_EXPLAIN'		=> 'Click aqui para ir al Blog',
	'WP_TITLE_FORUM'			=> 'Foro',
	'WP_TITLE_FORUM_EXPLAIN'	=> 'Click aqui para ir al Foro',
	'WP_ADMIN_PANEL'			=> 'Escritorio',

	// Sidebar
	'WP_AUTHOR_TITLE'			=> 'Autor',
	'WP_FORUM_POSTS'			=> 'Mensajes en el foro',
	'WP_BLOG_POSTS'				=> 'Mensajes en el blog',
	'WP_SEARCH_USER_POSTS'		=> 'Buscar mensajes del usuario',
	'WP_TITLE_PAGES'			=> 'Páginas',
	'WP_TITLE_ARCHIVES'			=> 'Archivos',
	'WP_TITLE_CATEGORIES'		=> 'Categorías',	
	'WP_TITLE_TAGS'				=> 'Etiquetas',
	'WP_TITLE_TAG_CLOUD'		=> 'Nube de etiquetas',
	'WP_TITLE_BOOKMARKS'		=> 'Marcadores',
	'WP_TITLE_META'				=> 'Meta',
	'WP_TITLE_RECENT_TOPICS'	=> 'Temas recientes',

	// Login/Logout
	'WP_LOGIN_FAILED'			=> 'Intento de conección fallido, la petición no coincide con su sesión. Por favor contacte con el administrador del foro si continúa experimentando problemas.',
	'WP_LOGIN_WAIT'				=> 'Por favor, esperar',
	'WP_INVALID_UNSERIALIZE'	=> 'El campo “Wordpress user login” no tiene datos.',
	'WP_INVALID_ENCRYPT_VALUE'	=> 'El campo “Encriptado” no tiene datos.',
	'WP_INVALID_LOGIN_VALUE'	=> 'El campo “Wordpress user login” tiene un valor no válido.',
	'WP_INVALID_USERID_VALUE'	=> 'El campo “Wordpress user id” tiene un valor no válido.',

	// Search
//	'WP_TITLE_SEARCH'				=> 'Blog Search',
	'WP_SEARCH_NOT_FOUND'			=> 'No encontrado',
	'WP_SEARCH_NOT_FOUND_EXPLAIN'	=> 'Lo sentimos, pero lo que está buscando no está aquí.',
	'WP_JUMP_TO_POST'				=> 'Saltar al mensaje',

	// WP entries
	'WP_POST_NOT_FOUND_EXPLAIN'	=> 'Disculpe, pero ningún resultado fue encontrado para el archivo solicitado. Tal vez la búsqueda le ayudará a encontrar un mensaje relacionado.',
	'WP_READ_MORE'				=> 'Lea el artículo completo »',
	'WP_POSTED_IN'				=> 'Publicado en: %s',
	'WP_FOLLOW_FEED'			=> 'Puede seguir cualquier respuesta a esta entrada a través del <a href="%s" class="wp_icon-feed">feed</a>.',
	'WP_YES_COMMENT_YES_PING'	=> 'Puede <a href="%1$s#respond">dejar una respuesta</a>, o <a href="%2$s" rel="trackback">hacer un seguimiento</a> desde tu propio sitio.',
	'WP_NO_COMMENT_YES_PING'	=> 'Las respuestas están actualmente cerradas, pero puedes <a href="%s" rel="trackback">hacer un seguimiento</a>desde tu propio sitio.',
	'WP_YES_COMMENT_NO_PING'	=> 'Puede saltar hasta el final y dejar una respuesta. Hacer Pings no está permitido.',
	'WP_NO_COMMENT_NO_PING'		=> 'Comentarios y pings están actualmente cerrados.',

	'WP_POST_TOPIC'				=> 'Crear un nuevo artículo',
	'WP_NO_COMMENTS'			=> 'Sin Comentarioss',
	'WP_ONE_COMMENT'			=> '1 Comentario',
	'WP_COMMENTS'				=> '%s Comentarios',
	'WP_COMMENTS_ON'			=> 'Comentario en %s',
	'WP_COMMENTS_OFF'			=> 'Comentarios desactivados',
	'WP_COMMENTS_PASSWORED'		=> 'Comentarios protegidos: Por favor, escriba su contraseña para ver los comentarios.',
	'WP_COMMENTS_TO'			=> ' para “%s” ',
	// Index & Topics navigation
	'PREVIOUS_ENTRIE'			=> '« Entrada anterior',
	'NEXT_ENTRIE'				=> 'Entrada siguiente » ',
	// Comment pagination
	'WP_PAGINATION'				=> 'Páginas',
	'WP_PAGINATION_PREVIOUS'	=> 'Comentarios más viejos',
	'WP_PAGINATION_NEXT'		=> 'Comentarios más nuevos',

	// Moderation actions
	'WP_COMMENT_APPROVE'				=> 'Aprovar',
	'WP_COMMENT_APPROVE_EXPLAIN'		=> 'Aprobar este comentario',
	'WP_COMMENT_UNAPPROVE'				=> 'Desaprovar',
	'WP_COMMENT_UNAPPROVE_EXPLAIN'		=> 'Desaprovar este comentario',
	'WP_COMMENT_UNAPPROVED'				=> 'Este comentario espera aprobación',
	'WP_COMMENT_EDIT'					=> 'Editar',
	'WP_COMMENT_EDIT_EXPLAIN'			=> 'Editar este comentario',
	'WP_COMMENT_REPLY'					=> 'Responder',
	'WP_COMMENT_REPLY_EXPLAIN'			=> 'Responder a este comentario',
	'WP_COMMENT_SPAM'					=> 'Spam',
	'WP_COMMENT_SPAM_EXPLAIN'			=> 'Marcar este comentario como spam',
	'WP_COMMENT_REPORTED_NOTE'			=> 'Este comentario estámarcado como spam',
	'WP_COMMENT_UNSPAM'					=> 'No es Spam',
	'WP_COMMENT_UNSPAM_EXPLAIN'			=> 'Marcar este comentario como no Spam',
	'WP_COMMENT_TRASH'					=> 'Papelera',
	'WP_COMMENT_TRASH_EXPLAIN'			=> 'Mover este comentario a la papelera',
	'WP_COMMENT_UNTRASH'				=> 'Recuperar papelera',
	'WP_COMMENT_UNTRASH_EXPLAIN'		=> 'Recuperar este comentario desde la papelera',
	'WP_COMMENT_UNTRASHED_NOTE'			=> 'Este comentario está en la papelera',
	'WP_COMMENT_DELETE'					=> 'Eliminar',
	'WP_COMMENT_DELETE_EXPLAIN'			=> 'Borrar permanentemente',

	// Comment form
	'WP_LOGIN_NEED'						=> 'Disculpa, debes <a href=\"%s\">iniciar sesión</a> para escribir un comentario.',
	'WP_LOGGED_IN'						=> 'Identificado como ',
	'WP_LOGGED_AS_OUT'					=> '%1$s. <a href="%2$s" title="Salir de esta cuenta">Desconectarme ?</a>',
	'WP_REQUIRED_FIELDS'				=> 'Los campos obligatorios están marcados como :',
	'WP_USERNAME_REQUIRED_NOTE'			=> '* Por favor, complete el campo "Nombre de Usuario" con su nombre o apodo.',
	'WP_EMAIL_REQUIRED_NOTE'			=> '* Por favor, complete el campo "Dirección de email" con una dirección de email válida.',
	'WP_EMAIL_REQUIRED_MINLENGTH'		=> '* El el campo "Dirección de email" debe contener al menos 10 Caracteres.',
	'WP_WEBSITE_REQUIRED_NOTE'			=> '* Por favor, complete el campo "Sitio web" con una url válida.',
	'WP_WEBSITE_REQUIRED_MINLENGTH'		=> '* El el campo "Sitio web" debe contener al menos 10 Caracteres.',
	'WP_MESSAGE_REQUIRED_NOTE'			=> '* Por favor, complete el campo "Cuerpo del mensaje" con un comentario válido.',
	'WP_MESSAGE_REQUIRED_MINLENGTH'		=> '* El el campo "Cuerpo del mensaje" debe contener al menos 20 Caracteres.',

	'WP_EMAIL_NOTE'						=> 'El Correo electrónico no será publicado.',
	'WP_ALLOWED_TAGS'					=> 'Puede usar las siguientes etiquetas y atributos <abbr title=\"HyperText Markup Language\">HTML</abbr>: %s',

	'WP_ERROR_GENERAL'					=> 'No encontrado',
	'WP_ERROR_404'						=> 'Disculpe, pero la página solicitada no se pudo encontrar. Tal vez la búsqueda será de ayuda. ',
	'WP_TITLE_ARCHIVE_EXPLAIN'			=> 'Está navegando por el archivo de <a href=\"%1$s/\">%2$s</a>.',
	'WP_TITLE_CATEGORIES_EXPLAIN'		=> 'Actualmente está navegando por el archivo de la categoría <em>%s</em>',
	'WP_TITLE_ARCHIVE_DAY_EXPLAIN'		=> 'Actualmente está navegando por el archivo del Blog <a href=\"%1$s/\">%2$s</a> del día %3$s.',
	'WP_TITLE_ARCHIVE_MONTH_EXPLAIN'	=> 'Actualmente está viendo el archivo del Blog <a href=\"%1$s/\">%2$s</a> de %3$s.',
	'WP_TITLE_ARCHIVE_YEAR_EXPLAIN'		=> 'Actualmente está viendo el archivo del Blog <a href=\"%1$s/\">%2$s</a> del año %3$s.',
	'WP_TITLE_ARCHIVE_SEARCH_EXPLAIN' 	=> 'Ha buscado en el archivo del sitio <a href=\"%1$s/\">%2$s</a> el término <strong>&#8216;%3$s&#8217;</strong>.',

	// pbpbb posting 
	'WP_BLOG_SUBJECT_PREFIX'			=> '[BLOG]: ',
	'WP_BLOG_POST_PREFIX'				=> 'Esta es una [b]entrada del Blog[/b]. Para leer el tema original, haga clic » %1$s AQUI %2$s',
	'WP_BLOG_POST_TAIL'					=> '[b]Detalles de la entrada: [/b]',

	// WordPress posting 
	'WP_FORUM_SUBJECT_PREFIX'			=> '[FORO]: ',
	'WP_FORUM_POST_PREFIX'				=> 'Esta es una <strong>Entrada del Foro</strong>. Para leer el tema original, haga clic » %1$s AQUI %2$s',
	'WP_FORUM_POST_TAIL'				=> '<strong>Detalles de la entrada: </strong>',
));

?>