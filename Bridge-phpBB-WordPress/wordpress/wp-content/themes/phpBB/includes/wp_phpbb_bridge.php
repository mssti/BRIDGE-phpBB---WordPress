<?php
/**
 * 
 * @package: phpBB 3.0.9 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/themes/phpBB/includes
 * @version: $Id: wp_phpbb_bridge.php, v0.0.9 2011/10/01 11:10:01 leviatan21 Exp $
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
 * includes/wp_phpbb_bridge_constants.php
 * includes/wp_phpbb_bridge_core.php
 * index.php
 */

define('IN_WP_PHPBB_BRIDGE', true);
define('WP_PHPBB_BRIDGE_ROOT', TEMPLATEPATH . '/');
define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1));
define('WP_TABLE_PREFIX', $table_prefix);

// Make this variable global before initialize phpbb
if (function_exists('wp_get_current_user'))
{
	wp_set_current_user(0);
	$wp_user = wp_get_current_user();
}

// Version number (only used for the installer)
@define('WP_PHPBB_BRIDGE_VERSION', '0.0.9');

// Without this we cannot include phpBB 3.0.x scripts.
if (!defined('IN_PHPBB'))
{
	define('IN_PHPBB', true);
}

// Include the constant for the path to phpBB
if (!file_exists(WP_PHPBB_BRIDGE_ROOT . 'includes/wp_phpbb_bridge_constants.' . PHP_EXT))
{
	die('<p>No "Bridge" constant found. Check the "' . WP_PHPBB_BRIDGE_ROOT . 'includes/wp_phpbb_bridge_constants.' . PHP_EXT . '" file.</p>');
}
require(WP_PHPBB_BRIDGE_ROOT . 'includes/wp_phpbb_bridge_constants.' . PHP_EXT);

// Include core classes
if (!file_exists(WP_PHPBB_BRIDGE_ROOT . 'includes/wp_phpbb_bridge_core.' . PHP_EXT))
{
	die('<p>No "Bridge" core found. Check the "'. WP_PHPBB_BRIDGE_ROOT . 'includes/wp_phpbb_bridge_core.' . PHP_EXT . '" file.</p>');
}
require(WP_PHPBB_BRIDGE_ROOT . 'includes/wp_phpbb_bridge_core.' . PHP_EXT);

// Initialise settings
bridge::set_config();

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

phpbb::$user->add_lang(array('viewtopic', 'posting', 'ucp', 'mods/wp_phpbb_bridge'));

@define('PHPBB_INCLUDED', true);

/**
 * a hook function to fix the ACP & DEBUG url
 * 
 * The hook is called in the WordPress root/wp-content/themes/phpBB/includes/wp_phpbb_bridge_core.php file at the page_footer() function
 */
function wp_phpbb_u_acp()
{
	phpbb::$template->assign_vars(array(
		'U_ACP'	=> phpbb::append_sid("adm/index", false, true, phpbb::$user->session_id),
	));

	if (isset(phpbb::$template->_tpldata['.'][0]['DEBUG_OUTPUT']))
	{
		phpbb::$template->_tpldata['.'][0]['DEBUG_OUTPUT'] = str_replace(' | <a href="' . build_url() . '&amp;explain=1">Explain</a>', '', phpbb::$template->_tpldata['.'][0]['DEBUG_OUTPUT']);
	//	phpbb::$template->_tpldata['.'][0]['DEBUG_OUTPUT'] = str_replace(build_url(), phpbb::$absolute_phpbb_script_path, phpbb::$template->_tpldata['.'][0]['DEBUG_OUTPUT']);
	}
}

/**
* Register sidebars style by running wp_phpbb_dynamic_sidebar_params() on the dynamic_sidebar_params hook.
* We do it here because in functions.php we do not have the class phpbb initialized
* 	and we need the template name/type to set the widget style
**/
add_filter('dynamic_sidebar_params', 'wp_phpbb_dynamic_sidebar_params');
/**
 * Ovewite the widget style if the style is not based off ProSilver
 *
 * @param (array)	$params = each widget settings
 * @return (array)	$params = ovewrited settings
 */
function wp_phpbb_dynamic_sidebar_params($params)
{
	/**
	 * An array to hardcode styles that needs the SubSilver2 layout
	 */
	$base_subsilver = array('subsilver2');

	/**
	* Most styles based off Prosilver needs to parse the css files
	* 	if the actual style need it, we do not change the settings
	* 	because we already set it to use prosilver html & style
	* 	when run the function wp_phpbb_widgets_init() at functions.php
	**/

	// The template name to set the widget style
	if (!class_exists('phpbb') || (phpbb::$user->theme['parse_css_file'] && !in_array(strtolower(phpbb::$user->theme['template_path']), $base_subsilver)))
	{
		return $params;
	}

	// Interpolate the phpbb style theme based off SubSilver2
	$params[0]['before_widget'] = "\n\t\t" . '<table class="tablebg" cellspacing="1">';
	$params[0]['after_widget'] = "\n\t\t\t\t" . '</td>' . "\n\t\t\t" . '</tr>' . "\n\t\t" . '</table>' . "\n\t\t" . '<br clear="all" />' . "\n";
	$params[0]['before_title'] = "\n\t\t\t" . '<tr>' . "\n\t\t\t\t" . '<th>';
	$params[0]['after_title'] = '</th>' . "\n\t\t\t" . '</tr>' . "\n\t\t\t" . '<tr>' . "\n\t\t\t\t" . '<td nowrap="nowrap" class="row2">' . "\n";

	return $params;
}

/**
 * A function with a very simple but powerful method to encrypt a string with a given key.
 * 
 * 	Usage : $sring_encrypted = encrypt("String to Encrypt", "Secret Key");
 * 	Based off : http://www.emm-gfx.net/2008/11/encriptar-y-desencriptar-cadena-php/
 * 	Updated to work in WP by leviatan21
 *
 * @param (string)	$string		String to Encrypt
 * @param (string)	$key		Secret Key			( Options : AUTH_KEY, SECURE_AUTH_KEY, LOGGED_IN_KEY, NONCE_KEY, AUTH_SALT, SECURE_AUTH_SALT, LOGGED_IN_SALT, NONCE_SALT )
 * @return (string)	encrypted string
 */
function wp_phpbb_encrypt($string = '', $key = SECURE_AUTH_SALT)
{
	// Load pluggable functions.
	if (!function_exists('wp_salt'))
	{
		require(ABSPATH . WPINC . '/pluggable.php');
	}

	$result = '';
//	$key = "Secret Key";
//	$key = phpbb::$user->session_id;
	$key = wp_salt($key);
//	$key = utf8_normalize_nfc(request_var('key', $key, true));

	for ($i = 0; $i < strlen($string); $i++)
	{
		$char	 = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key)) -1, 1);
		$char	 = chr(ord($char) + ord($keychar));
		$result .= $char;
	}
	return base64_encode($result);
}

/**
 * A function with a very simple but powerful method to decrypt a string with a given key.
 * 
 * 	Usage : $sring_decrypt = decrypt("String to decrypt", "Secret Key");
 * 	Based off : http://www.emm-gfx.net/2008/11/encriptar-y-desencriptar-cadena-php/
 * 	Updated to work in WP by leviatan21
 *
 * @param (string)	$string		String to decrypt
 * @param (string)	$key		Secret Key			( Options : AUTH_KEY, SECURE_AUTH_KEY, LOGGED_IN_KEY, NONCE_KEY, AUTH_SALT, SECURE_AUTH_SALT, LOGGED_IN_SALT, NONCE_SALT )
 * @return (string)	decrypted string
*/
function wp_phpbb_decrypt($string = '', $key = SECURE_AUTH_SALT)
{
	// Load pluggable functions.
	if (!function_exists('wp_salt'))
	{
		require(ABSPATH . WPINC . '/pluggable.php');
	}

	$result = '';
//	$key = "Secret Key";
//	$key = phpbb::$user->session_id;
	$key = wp_salt($key);
//	$key = utf8_normalize_nfc(request_var('key', $key, true));
	$string = base64_decode($string);

	for ($i = 0; $i < strlen($string); $i++)
	{
		$char	 = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key)) -1, 1);
		$char	 = chr(ord($char) - ord($keychar));
		$result .= $char;
	}
	return $result;
}

?>