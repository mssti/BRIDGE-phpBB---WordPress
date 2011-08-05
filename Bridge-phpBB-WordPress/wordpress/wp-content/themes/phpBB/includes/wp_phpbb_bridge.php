<?php
/**
 * 
 * @package: phpBB 3.0.9 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/themes/phpBB/includes
 * @version: $Id: wp_phpbb_bridge.php, v0.0.7 2011/08/04 11:08:04 leviatan21 Exp $
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
$wp_user = wp_get_current_user();

// Version number (only used for the installer)
@define('WP_PHPBB_BRIDGE_VERSION', '0.0.7');

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

phpbb::$user->add_lang(array('viewtopic', 'posting', 'mods/wp_phpbb_bridge'));

@define('PHPBB_INCLUDED', true);

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
	$params[0] = array(
		'before_widget'	=> "\n\t\t" . '<table class="tablebg" cellspacing="1">',
		'after_widget'	=> "\n\t\t\t\t" . '</td>' . "\n\t\t\t" . '</tr>' . "\n\t\t" . '</table>' . "\n\t\t" . '<br clear="all" />' . "\n",
		'before_title'	=> "\n\t\t\t" . '<tr>' . "\n\t\t\t\t" . '<th>',
		'after_title'	=> '</th>' . "\n\t\t\t" . '</tr>' . "\n\t\t\t" . '<tr>' . "\n\t\t\t\t" . '<td nowrap="nowrap" class="row2">' . "\n",
	);

	return $params;
}

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