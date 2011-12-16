<?php
/**
 * 
 * @package: phpBB 3.0.9 :: BRIDGE phpBB & WordPress -> root/language/en/mods :: [en][English]
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
	'WP_PAGE_NUMBER'			=> 'Page %s',

	// footer
	'WP_RSS_NOTES'				=> '%1$s and %2$s',
	'WP_RSS_ENRIES_LINK'		=> 'Entries (RSS)',
	'WP_RSS_COMMENTS_LINK'		=> 'Comments (RSS)',
	'WP_DEBUG_NOTE'				=> '%d queries. %s seconds.',

	// Navbar
	'WP_TITLE_WEB'				=> 'Web',
	'WP_TITLE_WEB_EXPLAIN '		=> 'Click here to go to the Web',
	'WP_TITLE_BLOG'				=> 'Blog',
	'WP_TITLE_BLOG_EXPLAIN'		=> 'Click here to go to the Blog',
	'WP_TITLE_FORUM'			=> 'Forum',
	'WP_TITLE_FORUM_EXPLAIN'	=> 'Click here to go to the Forum',
	'WP_ADMIN_PANEL'			=> 'Dashboard',

	// Sidebar
	'WP_AUTHOR_TITLE'			=> 'Author',
	'WP_FORUM_POSTS'			=> 'Forum posts',
	'WP_BLOG_POSTS'				=> 'Blog posts',
	'WP_SEARCH_USER_POSTS'		=> 'Search user’s posts',
	'WP_TITLE_PAGES'			=> 'Pages',
	'WP_TITLE_ARCHIVES'			=> 'Archives',
	'WP_TITLE_CATEGORIES'		=> 'Categories',	
	'WP_TITLE_TAGS'				=> 'Tags',
	'WP_TITLE_TAG_CLOUD'		=> 'Cloud tags',
	'WP_TITLE_BOOKMARKS'		=> 'Bookmarks',
	'WP_TITLE_META'				=> 'Meta',
	'WP_TITLE_RECENT_TOPICS'	=> 'Recent Topics',

	// Login/Logout
	'WP_LOGIN_FAILED'			=> 'You were not logged in, as the request did not match your session. Please contact the board administrator if you continue to experience problems.',
	'WP_LOGIN_WAIT'				=> 'Please wait',
	'WP_INVALID_UNSERIALIZE'	=> 'The field “Wordpress user login” has no valid data.',
	'WP_INVALID_ENCRYPT_VALUE'	=> 'The field “ciphered” has no valid data.',
	'WP_INVALID_LOGIN_VALUE'	=> 'The field “Wordpress user login” has an invalid value.',
	'WP_INVALID_USERID_VALUE'	=> 'The field “Wordpress user id” has an invalid value.',

	// Search
//	'WP_TITLE_SEARCH'				=> 'Blog Search',
	'WP_SEARCH_NOT_FOUND'			=> 'Not Found',
	'WP_SEARCH_NOT_FOUND_EXPLAIN'	=> 'Sorry, but you are looking for something that isn’t here.',
	'WP_JUMP_TO_POST'				=> 'Jump to entrie',

	// WP entries
	'WP_POST_NOT_FOUND_EXPLAIN'	=> 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.',
	'WP_READ_MORE'				=> 'Read full entry »',
	'WP_POSTED_IN'				=> 'Posted in: %s',
	'WP_FOLLOW_FEED'			=> 'You can follow any responses to this entry through the <a href="%s" class="wp_icon-feed">feed</a>.',
	'WP_YES_COMMENT_YES_PING'	=> 'You can <a href="%1$s#respond">leave a response</a>, or <a href="%2$s" rel="trackback">trackback</a> from your own site.',
	'WP_NO_COMMENT_YES_PING'	=> 'Responses are currently closed, but you can <a href="%s" rel="trackback">trackback</a> from your own site.',
	'WP_YES_COMMENT_NO_PING'	=> 'You can skip to the end and leave a response. Pinging is currently not allowed.',
	'WP_NO_COMMENT_NO_PING'		=> 'Both comments and pings are currently closed.',

	'WP_POST_TOPIC'				=> 'Create a new entry',
	'WP_NO_COMMENTS'			=> 'No Comments',
	'WP_ONE_COMMENT'			=> '1 Comment',
	'WP_COMMENTS'				=> '%s Comments',
	'WP_COMMENTS_ON'			=> 'Comment on %s',
	'WP_COMMENTS_OFF'			=> 'Comments Off',
	'WP_COMMENTS_PASSWORED'		=> 'Enter your password to view comments.',
	'WP_COMMENTS_TO'			=> ' to “%s” ',
	// Index & Topics navigation
	'PREVIOUS_ENTRIE'			=> '« Previous Entrie',
	'NEXT_ENTRIE'				=> 'Next Entrie » ',
	// Comment pagination
	'WP_PAGINATION'				=> 'Pages',
	'WP_PAGINATION_PREVIOUS'	=> 'Older Comments',
	'WP_PAGINATION_NEXT'		=> 'Newer Comments',

	// Moderation actions
	'WP_COMMENT_APPROVE'				=> 'Approve',
	'WP_COMMENT_APPROVE_EXPLAIN'		=> 'Approve this comment',
	'WP_COMMENT_UNAPPROVE'				=> 'Unapprove',
	'WP_COMMENT_UNAPPROVE_EXPLAIN'		=> 'Unapprove this comment',
	'WP_COMMENT_UNAPPROVED'				=> 'This comment is waiting for approval',
	'WP_COMMENT_EDIT'					=> 'Edit',
	'WP_COMMENT_EDIT_EXPLAIN'			=> 'Edit comment',
	'WP_COMMENT_REPLY'					=> 'Reply',
	'WP_COMMENT_REPLY_EXPLAIN'			=> 'Reply to this comment',
	'WP_COMMENT_SPAM'					=> 'Spam',
	'WP_COMMENT_SPAM_EXPLAIN'			=> 'Mark this comment as spam',
	'WP_COMMENT_REPORTED_NOTE'			=> 'This comment is maked as Spam',
	'WP_COMMENT_UNSPAM'					=> 'Not Spam',
	'WP_COMMENT_UNSPAM_EXPLAIN'			=> 'Mark this comment as not Spam',
	'WP_COMMENT_TRASH'					=> 'Trash',
	'WP_COMMENT_TRASH_EXPLAIN'			=> 'Move this comment to the trash',
	'WP_COMMENT_UNTRASH'				=> 'Not Trash',
	'WP_COMMENT_UNTRASH_EXPLAIN'		=> 'Restore this comment from the trash',
	'WP_COMMENT_UNTRASHED_NOTE'			=> 'This comment is in the Trash',
	'WP_COMMENT_DELETE'					=> 'Delete',
	'WP_COMMENT_DELETE_EXPLAIN'			=> 'Delete Permanently',

	// Comment form
	'WP_LOGIN_NEED'						=> 'You must be <a href="%s">logged in</a> to post a comment.',
	'WP_LOGGED_IN'						=> 'Logged in as',
	'WP_LOGGED_AS_OUT'					=> '%1$s. <a href="%2$s" title="Log out of this account">Log out?</a>',
	'WP_REQUIRED_FIELDS'				=> 'Required fields are marked as :',
	'WP_USERNAME_REQUIRED_NOTE'			=> '* Please, fill the field "Username" with your name or nickname.',
	'WP_EMAIL_REQUIRED_NOTE'			=> '* Please, fill the field "E-mail address" with a valid email.',
	'WP_EMAIL_REQUIRED_MINLENGTH'		=> '* The field "E-mail address" must contain at least 10 characters.',
	'WP_WEBSITE_REQUIRED_NOTE'			=> '* Please, fill the field "Website" with a valid url.',
	'WP_WEBSITE_REQUIRED_MINLENGTH'		=> '* The field "Website" must contain at least 10 characters.',
	'WP_MESSAGE_REQUIRED_NOTE'			=> '* Please, fill the field "Message body" with a valid comment.',
	'WP_MESSAGE_REQUIRED_MINLENGTH'		=> '* The field "Message body" must contain at least 20 characters.',

	'WP_EMAIL_NOTE'						=> 'Your email address will not be published.',
	'WP_ALLOWED_TAGS'					=> 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s',

	'WP_ERROR_GENERAL'					=> 'Not Found',
	'WP_ERROR_404'						=> 'Apologies, but the page you requested could not be found. Perhaps searching will help.',
	'WP_TITLE_ARCHIVE_EXPLAIN'			=> 'You are currently browsing the <a href="%1$s/">%2$s</a> blog archives.',
	'WP_TITLE_CATEGORIES_EXPLAIN'		=> 'You are currently browsing the archives for the <em>%s</em> category.',
	'WP_TITLE_ARCHIVE_DAY_EXPLAIN'		=> 'You are currently browsing the <a href="%1$s/">%2$s</a> blog archives for the day %3$s.',
	'WP_TITLE_ARCHIVE_MONTH_EXPLAIN'	=> 'You are currently browsing the <a href="%1$s/">%2$s</a> blog archives for %3$s.',
	'WP_TITLE_ARCHIVE_YEAR_EXPLAIN'		=> 'You are currently browsing the <a href="%1$s/">%2$s</a> blog archives for the year %3$s.',
	'WP_TITLE_ARCHIVE_SEARCH_EXPLAIN' 	=> 'You have searched the <a href="%1$s/">%2$s</a> blog archives for <strong>&#8216;%3$s&#8217;</strong>.',

	// pbpbb posting 
	'WP_BLOG_SUBJECT_PREFIX'			=> '[BLOG]: ',
	'WP_BLOG_POST_PREFIX'				=> 'This is a [b]Blog entry[/b]. To read the original post, please Click » %1$s HERE %2$s',
	'WP_BLOG_POST_TAIL'					=> '[b]Entry details: [/b]',

	// WordPress posting 
	'WP_FORUM_SUBJECT_PREFIX'			=> '[FORUM]: ',
	'WP_FORUM_POST_PREFIX'				=> 'This is a <strong>Forum entry</strong>. To read the original post, please Click » %1$s HERE %2$s',
	'WP_FORUM_POST_TAIL'				=> '<strong>Entry details: </strong>',
));

?>