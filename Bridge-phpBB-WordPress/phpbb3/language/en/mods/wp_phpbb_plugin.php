<?php
/**
 * 
 * @package: phpBB 3.0.8 :: BRIDGE phpBB & WordPress -> root/language/en/mods :: [en][English]
 * @version: $Id: wp_phpbb_plugin.php, v 0.0.1 2011/06/20 11:06:20 leviatan21 Exp $
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
	// footer
	'WP_RSS_NOTES'				=> '%1$s and %2$s',
	'WP_RSS_ENRIES_LINK'		=> 'Entries (RSS)',
	'WP_RSS_COMMENTS_LINK'		=> 'Comments (RSS)',
	'WP_DEBUG_NOTE'				=> '%d queries. %s seconds.',

	// Navbar
	'WP_TITLE_WEB'				=> 'Web',
	'WP_TITLE_WEB_EXPLAIN '		=> 'Click aqui para ir a la web',
	'WP_TITLE_BLOG'				=> 'Blog',
	'WP_TITLE_BLOG_EXPLAIN'		=> 'Click aqui para ir al blog',
	'WP_TITLE_FORUM'			=> 'Foro',
	'WP_TITLE_FORUM_EXPLAIN'	=> 'Click aqui para ir al foro',

	// Sidebar
	'WP_AUTHOR_TITLE'			=> 'Author',
	'WP_READ_TOPICS'			=> 'Ver todas las entradas de %s &rarr;',
	'WP_TITLE_PAGES'			=> 'Pages',
	'WP_TITLE_ARCHIVES'			=> 'Archives',
	'WP_TITLE_CATEGORIES'		=> 'Categories',	
	'WP_TITLE_TAGS'				=> 'Tags',
	'WP_TITLE_TAG_CLOUD'		=> 'Cloud tags',
	'WP_TITLE_BOOKMARKS'		=> 'Bookmarks',
	'WP_TITLE_META'				=> 'Meta',

	// WP entries
	'WP_POST_EDIT'				=> 'Editar entrada',
	'WP_READ_MORE'				=> 'Leer entrada completa »',
	'WP_POSTED_IN'				=> 'Publicado en: %s',
	'WP_FOLLOW_FEED'			=> 'You can follow any responses to this entry through the <a href="%s">RSS 2.0</a> feed.',
	'WP_YES_COMMENT_YES_PING'	=> 'You can <a href="#respond">leave a response</a>, or <a href="%s" rel="trackback">trackback</a> from your own site.',
	'WP_NO_COMMENT_YES_PING'	=> 'Responses are currently closed, but you can <a href="%s" rel="trackback">trackback</a> from your own site.',
	'WP_YES_COMMENT_NO_PING'	=> 'You can skip to the end and leave a response. Pinging is currently not allowed.',
	'WP_NO_COMMENT_NO_PING'		=> 'Both comments and pings are currently closed.',

	'WP_NO_COMMENTS'	=> 'No Comments',
	'WP_ONE_COMMENT'	=> '1 Comment',
	'WP_COMMENTS'		=> '% Comments',
	'WP_COMMENTS_TO'	=> ' to “%s” ',
	'WP_OLDER_ENTRIES'	=> '&laquo; Older Entries',
	'WP_NEWER_ENTRIES'	=> 'Newer Entries &raquo;',
	'WP_PAGINATION'		=> 'Pages',

	// Comment form
	'WP_LOGIN_NEED'						=> 'You must be <a href="%s">logged in</a> to post a comment.',
	'WP_LOGGED_IN'						=> 'Logged in as',
	'WP_LOGGED_AS_OUT'					=> '<a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>',
	'WP_REQUIRED_FIELDS'				=> 'Required fields are marked as :',
	'WP_USERNAME_REQUIRED_NOTE'			=> '* Por favor, complete el campo "%s" con su nombre o apodo.',
	'WP_EMAIL_REQUIRED_NOTE'			=> '* Por favor, complete el campo "%s" con una dirección de email válida.',
	'WP_EMAIL_REQUIRED_MINLENGTH'		=> '* El el campo "%s" debe contener al menos 10 Caracteres.',
	'WP_WEBSITE_REQUIRED_NOTE'			=> '* Por favor, complete el campo "%s" con una url válida.',
	'WP_WEBSITE_REQUIRED_MINLENGTH'		=> '* El el campo "%s" debe contener al menos 10 Caracteres.',
	'WP_MESSAGE_REQUIRED_NOTE'			=> '* Por favor, complete el campo "%s" con un comentario válido.',
	'WP_MESSAGE_REQUIRED_MINLENGTH'		=> '* El el campo "%s" debe contener al menos 20 Caracteres.',
	
	'WP_EMAIL_NOTE'						=> 'Your email address will not be published.',
	'WP_ALLOWED_TAGS'					=> 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s',

	
	'WP_ERROR_GENERAL'					=> 'Not Found',
	'WP_ERROR_404'						=> 'Apologies, but the page you requested could not be found. Perhaps searching will help.',
	'WP_TITLE_ARCHIVE_EXPLAIN'			=> 'You are currently browsing the <a href="%1$s/">%2$s</a> blog archives.',
	'WP_TITLE_CATEGORIES_EXPLAIN'		=> 'You are currently browsing the archives for the <em>%s</em> category.',
	'WP_TITLE_ARCHIVE_DAY_EXPLAIN'		=> 'You are currently browsing the <a href="%1$s/">%2$s</a> blog archives for the day %3$s.',
	'WP_TITLE_ARCHIVE_MONTH_EXPLAIN'	=> 'You are currently browsing the <a href="%1$s/">%2$s</a> blog archives for %3$s.',
	'WP_TITLE_ARCHIVE_YEAR_EXPLAIN'		=> 'You are currently browsing the <a href="%1$s/">%2$s</a> blog archives for the year %3$s.',
	'WP_TITLE_ARCHIVE_SEARCH_EXPLAIN' 	=> 'You have searched the <a href="%1$s/">%2$s</a> blog archives for <strong>&#8216;%3$s&#8217;</strong>. If you are unable to find anything in these search results, you can try one of these links.',

));
?>