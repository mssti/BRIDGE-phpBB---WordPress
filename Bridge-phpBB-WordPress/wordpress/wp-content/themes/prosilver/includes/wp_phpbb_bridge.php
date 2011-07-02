<?php
/**
 * 
 * @package: phpBB 3.0.8 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/theme/prosilver
 * @version: $Id: wp_phpbb_bridge.php, v0.0.3-pl1 2011/07/02 11:07:02 leviatan21 Exp $
 * @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
 * @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
 * 
 */

/**
* @ignore
**/

/**
 * Hierarchy :
 * 
 * functions.php
 * includes/wp_phpbb_bridge.php
 * includes/wp_phpbb_constants.php
 * includes/wp_phpbb_core.php
 * index.php
 */

define('IN_WP_PHPBB_BRIDGE', true);
define('WP_PHPBB_BRIDGE_ROOT', TEMPLATEPATH . '/');
define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1));
define('WP_TABLE_PREFIX', $table_prefix);

// Make this variable global before initialize phpbb
$wp_user = wp_get_current_user();

// Version number (only used for the installer)
@define('WP_PHPBB_BRIDGE_VERSION', '0.0.1');

// Without this we cannot include phpBB 3.0.x scripts.
if (!defined('IN_PHPBB'))
{
	define('IN_PHPBB', true);
}

// Include the constant for the path to phpBB
if (!file_exists(WP_PHPBB_BRIDGE_ROOT . 'includes/wp_phpbb_constants.' . PHP_EXT))
{
	die('<p>No "Bridge" constant found. Check the "' . WP_PHPBB_BRIDGE_ROOT . 'includes/wp_phpbb_constants.' . PHP_EXT . '" file.</p>');
}
require(WP_PHPBB_BRIDGE_ROOT . 'includes/wp_phpbb_constants.' . PHP_EXT);

// Some often used path constants
if (!defined('PHPBB_ROOT_PATH'))
{
	if (defined('WP_ADMIN') && WP_ADMIN == true)
	{
		define('PHPBB_ROOT_PATH', '../' . $wp_phpbb_bridge_config['phpbb_root_path']);
	}
	else
	{
		define('PHPBB_ROOT_PATH', $wp_phpbb_bridge_config['phpbb_root_path']);
	}
}

// Make that phpBB itself understands out paths
$phpbb_root_path = PHPBB_ROOT_PATH;
$phpEx = PHP_EXT;

// Include core classes
if (!file_exists(WP_PHPBB_BRIDGE_ROOT . 'includes/wp_phpbb_core.' . PHP_EXT))
{
	die('<p>No "Bridge" core found. Check the "'. WP_PHPBB_BRIDGE_ROOT . 'includes/wp_phpbb_core.' . PHP_EXT . '" file.</p>');
}
require(WP_PHPBB_BRIDGE_ROOT . 'includes/wp_phpbb_core.' . PHP_EXT);

// Include common phpBB files and functions.
if (!file_exists(PHPBB_ROOT_PATH . 'common.' . PHP_EXT))
{
	die('<p>No "phpBB" common found. Check the "' . PHPBB_ROOT_PATH . 'common.' . PHP_EXT . '" file.</p>');
}
require(PHPBB_ROOT_PATH . 'common.' . PHP_EXT);

if (!defined('PHPBB_USE_BOARD_URL_PATH'))
{
	@define('PHPBB_USE_BOARD_URL_PATH', true);
}

// Initialise phpbb
phpbb::initialise();

phpbb::$user->add_lang(array('viewtopic', 'posting', 'mods/wp_phpbb_bridge'));

@define('PHPBB_INCLUDED', true);

$action = request_var('action', '');
if ($action != '')
{
	$redirect = request_var('redirect', get_option('home'));
	$redirect_to = request_var('redirect_to', $redirect);

	switch ($action)
	{
		case 'login':
			phpbb::login_box($redirect_to);
		break;

		case 'logout':
			if (phpbb::$user->data['user_id'] != ANONYMOUS)
			{
				phpbb::$user->session_kill();
				phpbb::$user->session_begin();
			}

			redirect($redirect_to);
		break;
	}
}

?>