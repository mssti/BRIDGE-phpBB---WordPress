<?php
/**
 * 
 * @package: phpBB 3.0.8 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/theme/prosilver
 * @version: $Id: wp_phpbb_common.php, v 0.0.1 2011/06/20 11:06:20 leviatan21 Exp $
 * @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
 * @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
 * 
 */

/**
* @ignore
**/
if (!defined('IN_WP_PHPBB_BRIDGE'))
{
	exit;
}

// Version number (only used for the installer)
@define('WP_PHPBB_BRIDGE_VERSION', '0.0.1');

if (!defined('PHPBB_USE_BOARD_URL_PATH'))
{
	@define('PHPBB_USE_BOARD_URL_PATH', true);
}

// Without this we cannot include phpBB 3.0.x scripts.
if (!defined('IN_PHPBB'))
{
	define('IN_PHPBB', true);
}

// Include the constant for the path to phpBB
if (!file_exists(WP_PHPBB_BRIDGE_ROOT . 'includes/wp_phpbb_constants.' . PHP_EXT))
{
	die('<p>No phpBB installation found. Check the "WP phpBB Bidge" configuration file.</p>');
}
require(WP_PHPBB_BRIDGE_ROOT . 'includes/wp_phpbb_constants.' . PHP_EXT);

// Make that phpBB itself understands out paths
$phpbb_root_path = PHPBB_ROOT_PATH;
$phpEx = PHP_EXT;

// Include core classes
if (!file_exists(WP_PHPBB_BRIDGE_ROOT . 'includes/wp_phpbb_core.' . PHP_EXT))
{
	die('<p>No phpBB installation found. Check the "WP phpBB Bidge" configuration file.</p>');
}
require(WP_PHPBB_BRIDGE_ROOT . 'includes/wp_phpbb_core.' . PHP_EXT);

// Include common phpBB files and functions.
if (!file_exists(PHPBB_ROOT_PATH . 'common.' . PHP_EXT))
{
	die('<p>No phpBB installation found. Check the "WP phpBB Bidge" configuration file.</p>');
}

if (!defined('PHPBB_INCLUDED'))
{
	require(PHPBB_ROOT_PATH . 'common.' . PHP_EXT);
}

// Initialise phpbb
phpbb::initialise();

phpbb::$user->add_lang(array('posting', 'mods/wp_phpbb_plugin'));

@define('PHPBB_INCLUDED', true);

/**
 * Force some variables
 * We do this instead made an ACP module for phpBB to manage this bridge configurations
 */

// For the moment the ID of you forum where to use permissions ( like $auth->acl_get('f_reply') )
define('PERMISSION_FORUM_ID', 2);

define('BLOG_LEFT_COLUMN_WIDTH', 250);

define('COMMENT_AVATAR_WIDTH', 32);

?>