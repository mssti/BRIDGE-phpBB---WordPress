<?php
/**
 * 
 * @package: phpBB 3.0.9 :: BRIDGE phpBB & WordPress -> root/language/en/mods :: [en][English]
 * @version: $Id: acp_wp_phpbb_bridge.php, v0.0.9 2011/10/25 11:10:25 leviatan21 Exp $
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
	'WP_PHPBB_BRIDGE_MANAGE'			=> 'BRIDGE phpBB & WordPress manage',
	'WP_PHPBB_BRIDGE_MANAGE_EXPLAIN'	=> 'Welcome to BRIDGE phpBB & WordPress Management Section.<br />Here you can determine the basic operation of the Bridge in relation to phpBB.',

	'WP_PHPBB_BRIDGE_MISSING_FILE'		=> 'Module not available for setting. Missing file',
	'WP_PHPBB_BRIDGE_VERSION'			=> 'BRIDGE version script',
	'WP_PHPBB_BRIDGE_NOT_UP_TO_DATE'	=> 'Your <strong>BRIDGE phpBB & WordPress</strong> version is not up to date, Click %sHERE%s to read about the new version  %s.',

	'WP_PHPBB_BRIDGE_BASIC'				=> 'Basic settings',
//	'WP_PHPBB_BRIDGE_DISABLE'					=> '',
//	'WP_PHPBB_BRIDGE_DISABLE_EXPLAIN'			=> '',
	'WP_PHPBB_BRIDGE_WORDPRESS_PATH'			=> 'WordPress path',
	'WP_PHPBB_BRIDGE_WORDPRESS_PATH_EXPLAIN'	=> 'The path where WordPress is located, relative to your phpBB root directory.<br />e.g. <samp>../wordpress/</samp>.',

	'WP_PHPBB_BRIDGE_POST'				=> 'Cross-site Posting',
	'WP_PHPBB_BRIDGE_POST_DISABLE'				=> 'Display the Cross-site Posting when a topic is created',
	'WP_PHPBB_BRIDGE_POST_DISABLE_EXPLAIN'		=> 'Users with permission to made Announce topics, have the option to select to post it at WordPress at the same time.',

	'WP_PHPBB_BRIDGE_PORTAL'			=> 'Recent WordPress entries',
	'WP_PHPBB_BRIDGE_PORTAL_DISABLE'			=> 'Display the WordPress portal at the forum index page',
	'WP_PHPBB_BRIDGE_PORTAL_DISABLE_EXPLAIN'	=> 'If disabled the Blog listing is no longer displayed.',
	'WP_PHPBB_BRIDGE_PORTAL_TITLE'				=> 'WordPress Portal Title',
	'WP_PHPBB_BRIDGE_PORTAL_TITLE_EXPLAIN'		=> 'A name to use for the <strong>WordPress block</strong>',
	'WP_PHPBB_BRIDGE_PORTAL_STYLE'				=> 'Block style',
	'WP_PHPBB_BRIDGE_PORTAL_STYLE_EXPLAIN'		=> 'Select how the <strong>WordPress block</strong> will look like',
	'WP_PHPBB_BRIDGE_PORTAL_LIMIT'				=> 'Number of items',
	'WP_PHPBB_BRIDGE_PORTAL_LIMIT_EXPLAIN'		=> 'The maximum number of WordPress entries to display.',
	'WP_PHPBB_BRIDGE_PORTAL_BLOCK_STYLE'		=> 'Like Topics',
	'WP_PHPBB_BRIDGE_PORTAL_BLOCK_COMPACT'		=> 'Like Lists',
	'WP_PHPBB_BRIDGE_PORTAL_BLOCK_POST'			=> 'Like Posts (Displaying part of the text, up to <samp>&lt;!--more--&gt;</samp> )',
));

?>