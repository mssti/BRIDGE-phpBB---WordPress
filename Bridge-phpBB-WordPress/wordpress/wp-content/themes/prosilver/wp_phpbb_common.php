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
define('WP_PHPBB_BRIDGE_VERSION', '0.0.1');

@define('PHPBB_USE_BOARD_URL_PATH', true);

// Without this we cannot include phpBB 3.0.x scripts.
if (!defined('IN_PHPBB'))
{
	define('IN_PHPBB', true);
}

// Include core classes
if (!file_exists(WP_PHPBB_BRIDGE_ROOT . 'wp_phpbb_core.' . PHP_EXT))
{
	die('<p>No phpBB installation found. Check the "WP phpBB Bidge" configuration file.</p>');
}

require(WP_PHPBB_BRIDGE_ROOT . 'wp_phpbb_core.' . PHP_EXT);

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

phpbb::$user->add_lang('mods/wp_phpbb_plugin');

?>