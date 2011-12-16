<?php
/**
 * 
 * @package: phpBB 3.0.9 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/themes/phpBB/includes
 * @version: $Id: wp_phpbb_bridge_core.php, v0.0.9 2011/10/01 11:10:01 leviatan21 Exp $
 * @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
 * @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
 * 
 */

/**
 * @ignore
 */

if (!defined('IN_WP_PHPBB_BRIDGE'))
{
	exit;
}

class bridge
{
	/**
	 * Bridge configuration member
	 *
	 * @var bridge_config
	 */
	public static $config;

	/**
	 * Reads a configuration file with an assoc. config array
	 *
	 * @param boolean $force	force to update WP settings
	 */
	public static function set_config($force = false)
	{
		global $wp_phpbb_bridge_config;

		// Some default options
		$wp_phpbb_bridge_settings = array('error' => false, 'message' => '', 'action' => '');
		$plugins = (array) get_option('active_plugins', array());
		$theme	 = get_option('template');
		// bypass our own settings
		$active	 = get_option('wp_phpbb_bridge', $wp_phpbb_bridge_config['phpbb_bridge']);
		// bypass our own settings
		$path	 = get_option('phpbb_root_path', $wp_phpbb_bridge_config['phpbb_root_path']);
		if (defined('PHPBB_INAJAX') && PHPBB_INAJAX == true)
		{
			$path = '../../../' . $path;
		}

		// Measn the plugin is not enabbled yet!
		// or the plugin is not set yet!
		if (!in_array('wp_phpbb3_bridge_options.php', $plugins) || $active == '' || $path == '')
		{
			// Get the proper error and message
			$wp_phpbb_bridge_settings = self::wp_phpbb_bridge_check($active, $path, $theme);

			// must check that the user has the required capability
			if (current_user_can('manage_options') && !in_array('wp_phpbb3_bridge_options.php', $plugins))
			{
				$redir = admin_url('plugins.php');
				$wp_phpbb_bridge_settings['action'] = '<a href="' . $redir . '" title="' . esc_attr__('Activate Bridge', 'wp_phpbb3_bridge') . '">' . __('Activate Bridge', 'wp_phpbb3_bridge') . '</a>';
			}

			wp_die($wp_phpbb_bridge_settings['message'] . '<br />' . $wp_phpbb_bridge_settings['action']);
		}

		// Check against WP settings
		$wp_phpbb_bridge_settings = self::wp_phpbb_bridge_check($active, $path, $theme);

		// If checks fails, display the proper message
		if ($wp_phpbb_bridge_settings['error'])
		{
			wp_die(__('<h2>Error in WordPress Settings</h2>', 'wp_phpbb3_bridge') . '<br />' . $wp_phpbb_bridge_settings['message'] . '<br />' . $wp_phpbb_bridge_settings['action']);
		}

		if (defined('WP_ADMIN') && WP_ADMIN == true)
		{
			define('PHPBB_ROOT_PATH', '../' . $path);
		}
		else
		{
			define('PHPBB_ROOT_PATH', $path);
		}

		self::$config = $wp_phpbb_bridge_config;

		// Make that phpBB itself understands out paths
		global $phpbb_root_path, $phpEx;

		$phpbb_root_path = PHPBB_ROOT_PATH;
		$phpEx = PHP_EXT;
	}

	/**
	 * Check the Bridge settings...
	 *
	 * @param (bolean) $active
	 * @param (string) $path
	 * @param (string) $theme
	 * @return (array)
	 */
	public static function wp_phpbb_bridge_check($active = false, $path = '../phpBB/', $theme = '')
	{
		$error = false;
		$message = '';
		$action = '';

		if (!$active)
		{
			$error = true;
			$message .= __('The "BRIDGE phpBB & WordPress" is deactivated', 'wp_phpbb3_bridge');
		}

		if ($path)
		{
			if (defined('WP_ADMIN') && WP_ADMIN == true)
			{
				$path =  '../' . $path;
			}

			if (!@file_exists($path . 'config.php') || (@!is_dir($path) && @is_file($path)))
			{
				$error = true;
				$message .= sprintf(__("Could not find path to your board. Please check your settings and try again.<br /><samp>%s</samp> was specified as the source path.<br /><br />Cannot activate bridge.", 'wp_phpbb3_bridge'), $path);
			}
		}

		if ($error)
		{
			// must check that the user has the required capability
			if (current_user_can('manage_options'))
			{
				global $wp_phpbb_bridge_config;

				$redir = admin_url('admin.php');
				$redir = add_query_arg(array('page' => 'wp_phpbb3_bridge', 'wp_phpbb3_bridge' => '1', 'wp_phpbb_root_path' => stripslashes($wp_phpbb_bridge_config['phpbb_root_path'])), $redir);

				$action .= '<a href="' . $redir . '" title="' . esc_attr__('Configure Bridge', 'wp_phpbb3_bridge') . '">' . __('Configure Bridge', 'wp_phpbb3_bridge') . '</a>';
			}
			else
			{
				$action .= __('Please notify the system administrator or webmaster', 'wp_phpbb3_bridge');
			}
		}

		if ($theme != '' && $theme != 'phpBB')
		{
			$error = true;
			$message .= __('The "phpBB" theme is deactivated', 'wp_phpbb3_bridge');

			if (current_user_can('switch_themes'))
			{
				$redir = admin_url('themes.php');
				$action .= '<a href="' . $redir . '" title="' . esc_attr__('Activate theme', 'wp_phpbb3_bridge') . '">' . __('Activate theme', 'wp_phpbb3_bridge') . '</a>';
			}
			else
			{
				$action .= __('Please notify the system administrator or webmaster', 'wp_phpbb3_bridge');		
			}
		}

		return array('error' => $error, 'message' => $message, 'action' => $action);
	}
}

/**
 * phpBB class that will be used in place of globalising these variables.
 * 
 * Based off : Titania 0.3.11
 * * File : titania/includes/core/phpbb.php
 */
class phpbb
{
	/** @var auth phpBB Auth class */
	public static $auth;

	/** @var cache phpBB Cache class */
	public static $cache;

	/** @var config phpBB Config class */
	public static $config;

	/** @var db phpBB DBAL class */
	public static $db;

	/** @var template phpBB Template class */
	public static $template;

	/** @var user phpBB User class */
	public static $user;

	/**
	 * Absolute Wordpress and phpBB Path
	 *
	 * @var string
	 */
	public static $absolute_phpbb_script_path;
	public static $absolute_wordpress_script_path;

	/**
	 * Static Constructor.
	 */
	public static function initialise()
	{
		global $wpdb;
		$wpdb = self::wp_phpbb_get_wp_db();
		
		global $auth, $config, $db, $template, $user, $cache;

		self::$auth		= &$auth;
		self::$config	= &$config;
		self::$db		= &$db;
		self::$template	= &$template;
		self::$user		= &$user;
		self::$cache	= &$cache;

		// Set the absolute wordpress/phpbb path
		self::$absolute_phpbb_script_path = generate_board_url(true) . '/' . get_option('phpbb_script_path', bridge::$config['phpbb_script_path']);
		self::$absolute_wordpress_script_path = generate_board_url(true) . '/' . get_option('wordpress_script_path', bridge::$config['wordpress_script_path']);

		// enhance phpbb $config data with WP $config data
		self::wp_get_config();

		// Start session management
		if (!defined('PHPBB_INCLUDED'))
		{
			self::$user->session_begin();
			self::$auth->acl(self::$user->data);
			self::$user->setup();
		}

		$action = request_var('action', '');
		if (($action != 'logout' || $action != 'log-out') && !defined('PHPBB_INAJAX'))
	//	if ($action != 'logout' || $action != 'log-out')
		{
			self::wp_phpbb_sanitize_user();
		}

	}

	/**
	 * Load the correct database class file.
	 *
	 * This function is used to load the database class file either at runtime or by
	 * wp-admin/setup-config.php. We must globalize $wpdb to ensure that it is
	 * defined globally by the inline code in wp-db.php.
	 *
	 * @since 2.5.0
	 * @global $wpdb WordPress Database Object
	 * 
	 * Based off : wordpress 3.1.3
	 * File : wordpress/wp-includes/load.php
	 */
	public static function wp_phpbb_get_wp_db()
	{
		global $wpdb;

		require_once(ABSPATH . WPINC . '/wp-db.php');

		if (@file_exists(WP_CONTENT_DIR . '/db.php'))
		{
			require_once(WP_CONTENT_DIR . '/db.php');
		}

		$wpdb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);

		$wpdb->set_prefix(WP_TABLE_PREFIX);

		return $wpdb;
	}

	/**
	 * Update phpbb user data with wp user data
	 * 	And update wp user data with phpbb user data
	 *
	 * based off the WP add-on by Jason Sanborn <jsanborn@simplicitypoint.com> http://www.e-xtnd.it/wp-phpbb-bridge/
	 */
	public static function wp_phpbb_sanitize_user()
	{
		global $wp_user;

		$wp_user_id = self::wp_phpbb_get_userid();

	//	if ($wp_user_id != 0)
	//	if ($wp_user_id <= 0 && is_user_logged_in())
		if ($wp_user_id <= 0 && self::wp_phpbb_user_logged())
		{
			wp_logout();
			wp_redirect('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		}
		else if ($wp_user_id > 0 && $wp_user_id != $wp_user->ID)
		{
			wp_set_current_user($wp_user_id);
			wp_set_auth_cookie($wp_user_id, true, false);
		}

		// Get the WP user data
		$wp_user_data = get_userdata($wp_user_id);	//$wp_user_data = get_userdata($wp_user->ID);

		if (!isset($wp_user_data->phpbb_userid) || $wp_user_data->phpbb_userid == 0 || $wp_user_data->phpbb_userid != self::$user->data['user_id'])
		{
			$wp_user_data->phpbb_userid = self::$user->data['user_id'];
			update_metadata('user', $wp_user_id, 'phpbb_userid', $wp_user_data->phpbb_userid);
		}

		// If all went fine, we doesnt' needed this values anymore (added at functions.php -> function wp_phpbb_phpbb_loginbox_head())
		if (isset($wp_user_data->phpbb_userid) && isset($wp_user_data->WPphpBBlogin))
		{
			delete_user_meta($wp_user_id, 'WPphpBBlogin');
		}

		$default_userdata = array(
			'ID'			=> $wp_user_id,
			'phpbb_userid'	=> self::$user->data['user_id'],
			'user_url'		=> self::$user->data['user_website'],
			'user_email'	=> self::$user->data['user_email'],
			'nickname'		=> self::$user->data['username'],
			'jabber'		=> self::$user->data['user_jabber'],
			'aim'			=> self::$user->data['user_aim'],
			'yim'			=> self::$user->data['user_yim'],
		);

		self::$user->data['wp_user'] = array_merge($default_userdata, (array) $wp_user_data);
	//	print_r(self::$user->data);
	}

	/**
	 * Get the WP user ID, based off the phpBB user ID
	 *
	 * @param (integer)	 	$wp_user_id	a default value, in WP the anonymous user is ID 0
	 * @return (integer)	$wp_user_id
	 */
	public static function wp_phpbb_get_userid($wp_user_id = 0)
	{
		global $wpdb;

		if (self::$user->data['user_type'] == USER_FOUNDER && self::$user->data['user_id'] == self::$config['wp_phpbb_bridge_forum_founder_user_id'])
		{
			return (int) self::$config['wp_phpbb_bridge_blog_founder_user_id'];
		}

		if (self::$user->data['user_type'] == USER_NORMAL || self::$user->data['user_type'] == USER_FOUNDER)
		{
		//	$id_list = $wpdb->get_col($wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'phpbb_userid' AND meta_value = %d", self::$user->data['user_id']));
			$usermeta_id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'phpbb_userid' AND meta_value = %s", self::$user->data['user_id']));

		//	if (empty($id_list))
			if (!$usermeta_id)
			{
		//		return (int) $wp_user_id;
				$users_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_login = %s", self::$user->data['username']));

				if ($users_id)
				{
					return (int) $users_id;
				}
			}
			else
			{
		//		return (int) $id_list[0];
				return (int) $usermeta_id;
			}
		}

		return $wp_user_id;
	}

	/**
	 * Get all available usuer data from wordpress tables
	 * 	An alternative alternative to the WP get_userdata(), because we will have a name function conflict
	 *
	 * @param (integer)		$wp_user_id
	 * @return (array)	
	public static function wp_phpbb_get_userdata($wp_user_id)
	{
		global $wpdb;
		
		$wp_user_id = (int) $wp_user_id;
		$wpuser = array();

		$users = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->users WHERE ID = %d LIMIT 1", $wp_user_id));

		if (!empty($users))
		{
			foreach($users as $id => $value)
			{
				$wpuser[$id] = $value;
			}
		}
		else
		{
			$wpuser = array(
				'user_nicename'	=>  '',
				'ID'			=> 0,
			);
		}

		$usermeta = $wpdb->get_results($wpdb->prepare("SELECT meta_key, meta_value FROM $wpdb->usermeta WHERE user_id = %d ", $wp_user_id));

		if (!empty($usermeta))
		{
			foreach($usermeta as $key => $value)
			{
				$wpuser[$value->meta_key] = $value->meta_value;
			}
		}

		return $wpuser;
	}
	 */

	/**
	* Set and Force some variables
	* We do this instead made an ACP module for phpBB to manage this bridge configurations
	*/
	public static function wp_get_config()
	{
		$wp_phpbb_bridge_forum_founder_user_id	= (isset(bridge::$config['wp_phpbb_bridge_forum_founder_user_id'])	&& (int) bridge::$config['wp_phpbb_bridge_forum_founder_user_id']	!= 0) ? (int) bridge::$config['wp_phpbb_bridge_forum_founder_user_id']	: 2;
		$wp_phpbb_bridge_blog_founder_user_id	= (isset(bridge::$config['wp_phpbb_bridge_blog_founder_user_id'])	&& (int) bridge::$config['wp_phpbb_bridge_blog_founder_user_id']	!= 0) ? (int) bridge::$config['wp_phpbb_bridge_blog_founder_user_id']	: 2;
		$wp_phpbb_bridge_permissions_forum_id	= (isset(bridge::$config['wp_phpbb_bridge_permissions_forum_id'])	&& (int) bridge::$config['wp_phpbb_bridge_permissions_forum_id']	!= 0) ? (int) bridge::$config['wp_phpbb_bridge_permissions_forum_id']	: 0;
		$wp_phpbb_bridge_post_forum_id			= (isset(bridge::$config['wp_phpbb_bridge_post_forum_id'])			&& (int) bridge::$config['wp_phpbb_bridge_post_forum_id']			!= 0) ? (int) bridge::$config['wp_phpbb_bridge_post_forum_id']			: 0;
		$wp_phpbb_bridge_widgets_column_width	= (isset(bridge::$config['wp_phpbb_bridge_widgets_column_width'])	&& (int) bridge::$config['wp_phpbb_bridge_widgets_column_width']	!= 0) ? (int) bridge::$config['wp_phpbb_bridge_widgets_column_width']	: 300;
		$wp_phpbb_bridge_comments_avatar_width	= (isset(bridge::$config['wp_phpbb_bridge_comments_avatar_width'])	&& (int) bridge::$config['wp_phpbb_bridge_comments_avatar_width']	!= 0) ? (int) bridge::$config['wp_phpbb_bridge_comments_avatar_width']	: 32;

		self::$config = array_merge(self::$config, array(
			// Disable to call the function leave_newly_registered()
			'new_member_post_limit'					=> null,
			// The ID of user forum founder
			'wp_phpbb_bridge_forum_founder_user_id'	=> (int) $wp_phpbb_bridge_forum_founder_user_id,
			// The ID of user blog founder
			'wp_phpbb_bridge_blog_founder_user_id'	=> (int) $wp_phpbb_bridge_blog_founder_user_id,
			// For the moment the ID of you forum where to use permissions ( like $auth->acl_get('f_reply') )
			'wp_phpbb_bridge_permissions_forum_id'	=> (int) get_option('wp_phpbb_bridge_permissions_forum_id', $wp_phpbb_bridge_permissions_forum_id),
			// For the moment the ID of you forum where to post a new entry whenever is published in the Wordpress
			'wp_phpbb_bridge_post_forum_id'			=> (int) get_option('wp_phpbb_bridge_post_forum_id', $wp_phpbb_bridge_post_forum_id),
			// The left column width, in pixels
			'wp_phpbb_bridge_widgets_column_width'	=> (int) get_option('wp_phpbb_bridge_widgets_column_width', $wp_phpbb_bridge_widgets_column_width),
			// The width size of avatars in comments, in pixels
			'wp_phpbb_bridge_comments_avatar_width'	=> (int) get_option('wp_phpbb_bridge_comments_avatar_width', $wp_phpbb_bridge_comments_avatar_width),
		));
	}

	/**
	* Include a phpBB includes file
	*
	* @param string $file The name of the file
	* @param string|bool $function_check Bool false to ignore; string function name to check if the function exists (and not load the file if it does)
	* @param string|bool $class_check Bool false to ignore; string class name to check if the class exists (and not load the file if it does)
	* 
	* Based off : Titania 0.3.11
	* File : titania/includes/core/phpbb.php
	*/
	public static function _include($file, $function_check = false, $class_check = false)
	{
		if ($function_check !== false)
		{
			if (function_exists($function_check))
			{
				return;
			}
		}

		if ($class_check !== false)
		{
			if (class_exists($class_check))
			{
				return;
			}
		}

		// Make that phpBB itself understands out paths
		global $phpbb_root_path, $phpEx;
	//	$phpbb_root_path = PHPBB_ROOT_PATH;
	//	$phpEx = PHP_EXT;

		include(PHPBB_ROOT_PATH . 'includes/' . $file . '.' . PHP_EXT);
	}

	/**
	* Shortcut for phpbb's append_sid function (do not send the root path/phpext in the url part)
	*
	* @param mixed $url
	* @param mixed $params
	* @param mixed $is_amp
	* @param mixed $session_id
	* @return string
	* 
	* Based off : Titania 0.3.11
	* File : titania/includes/core/phpbb.php
	*/
	public static function append_sid($script, $params = false, $is_amp = true, $session_id = false)
	{
		return append_sid(self::$absolute_phpbb_script_path . $script . '.' . PHP_EXT, $params, $is_amp, $session_id);
	}

	/**
	 * @description: Generate the clock time
	 * @param: 				$gmepoch = a date;
	 * @return: (string) 	$time . $midnight = a formated hour HH:MM:SS am/pm
	 * @version: OK
	 */
	public static function clock($gmepoch = '')
	{
		$zone_offset = (int) self::$user->timezone + (int) self::$user->dst;

		$date = preg_split('/(H:i|g:i)/', self::$user->lang['default_dateformat']);

		$gmepoch = ($gmepoch) ? $gmepoch : time();

		$time = gmdate('H:i:s', $gmepoch + $zone_offset);

		list($h, $m, $s) = explode(':', $time);
		$midnight = ((int) $h > 12) ? ' pm' : ' am';

		self::$template->assign_vars(array(
			'CURRENT_DATE'	=> sprintf(self::$user->lang['CURRENT_TIME'], self::$user->format_date(time() + $zone_offset, $date[0], true)),
			'CURRENT_TIME'	=> $time . $midnight,
		));

		return true;
	}

	/**
	 * Page header function for phpBB stuff
	 */
	public static function page_header()
	{
		// Determine board url - we may need it later
		$board_url = generate_board_url(false) . '/';
		$web_path = (defined('PHPBB_USE_BOARD_URL_PATH') && PHPBB_USE_BOARD_URL_PATH) ? $board_url : PHPBB_ROOT_PATH;
		$blog_path = get_option('siteurl');

		/**
		 * Print the <title> tag based on what is being viewed.
		 */
		global $page, $paged;

		$wp_title = wp_title('|', false, 'right');

		// Add the blog name.
		$wp_title .= get_bloginfo('name', 'display');

		// Add the blog description for the home/front page.
		$site_description = get_bloginfo('description', 'display');
		if ($site_description && (is_home() || is_front_page()))
		{
			$wp_title .= " | $site_description";
		}

		// Add a page number if necessary:
		if ($paged || $page) //if ($paged >= 2 || $page >= 2)
		{
			$wp_title .= ' | ' . sprintf(self::$user->lang['WP_PAGE_NUMBER'], max($paged, $page));
		}

		// Do the phpBB page header stuff first
		page_header($wp_title, false);

		$redirect = request_var('redirect', get_option('siteurl'));
		
//		$u_login_logout = site_url('wp-login.php', 'login');
//		$u_login_popup = get_option('siteurl') . '/?action=popup';

		self::$template->assign_vars(array(
			'PHPBB_IN_FORUM'	=> false,
			'PHPBB_IN_WEB'		=> false,
			'PHPBB_IN_BLOG'		=> true,
			'PHPBB_IN_PASTEBIN'	=> false,
			'SCRIPT_NAME'		=> 'blog ' . self::wp_location(),
			'IN_HOME'			=> is_home(),

		//	'U_WEB'				=> append_sid($web_path),
			'U_INDEX'			=> append_sid($web_path),
			'U_BLOG'			=> append_sid($blog_path),

			'PAGE_TITLE'		=> $wp_title,
			'SITENAME'			=> get_bloginfo('name', 'display'),
			'SITE_DESCRIPTION'	=> get_bloginfo('description', 'display'),
			'BLOG_HEADER'		=> self::wp_page_header(),
			'S_DISPLAY_SEARCH'	=> false,
			'S_CLOCK'			=> self::clock(),

			'S_REGISTER_ENABLED'=> (self::$config['require_activation'] != USER_ACTIVATION_DISABLE && get_option('users_can_register')) ? true : false,
	//		'U_REGISTER_POPUP'	=> site_url("wp-login.php?action=register&amp;interim-login=1&amp;sid=" . phpbb::$user->session_id . "&amp;redirect_to=" . $redirect, 'login'),
			'U_REGISTER_POPUP'	=> site_url("wp-login.php?action=register&amp;interim-login=1&amp;sid=" . phpbb::$user->session_id . "&amp;redirect_to=wp-login.php?checkemail=registered&amp;interim-login=1", 'login'),
	//		'U_LOGIN_LOGOUT'	=> (!is_user_logged_in()) ? append_sid(get_option('siteurl') . '/', array('action' => 'login', 'redirect' => $redirect)) : append_sid(get_option('siteurl') . '/', array('action' => 'logout', 'redirect' => $redirect), true, self::$user->session_id),
			'S_LOGIN_REDIRECT'	=> build_hidden_fields(array('redirect' => $redirect)),
			'U_LOGIN_LOGOUT_POPUP'	=> (!self::wp_phpbb_user_logged()) ? site_url("wp-login.php?action=login&amp;interim-login=1&amp;sid=" . phpbb::$user->session_id . "&amp;redirect_to=" . $redirect, 'login') : site_url("wp-login.php?action=logout&amp;sid=" . phpbb::$user->session_id . "&amp;redirect_to=" . $redirect, 'login'),

			'U_WP_ACP'			=> (self::$user->data['user_type'] == USER_FOUNDER || current_user_can('level_8')) ? admin_url() : '',
			'U_POST_NEW_TOPIC'	=> (self::$user->data['user_type'] == USER_FOUNDER || current_user_can('level_8')) ? admin_url('post-new.php') : '',

			'BLOG_POST_IMG'		=> self::wp_imageset('button_blogpost_new', 'WP_POST_TOPIC', 'BLOG_POST_IMG_CLASS'),
			'POST_IMG'			=> self::$user->img('button_topic_new', 'POST_NEW_TOPIC'),
			'DYNAMIC_SIDEBAR_W'	=> (int) self::$config['wp_phpbb_bridge_widgets_column_width'],

			'WPT_TEMPLATE_PATH'	=> get_template_directory_uri(),

			'WPT_STYLESHEETPATH'=> get_stylesheet_directory(),
			'A_BASE_URL'		=> esc_url( get_home_url( null, '/wp-content/themes/phpBB')),

			'T_THEME_PATH'		=> "{$web_path}styles/" . self::$user->theme['theme_path'] . '/theme',
			'T_STYLESHEET_LINK'	=> (!self::$user->theme['theme_storedb']) ? "{$web_path}styles/" . self::$user->theme['theme_path'] . '/theme/stylesheet.css' : append_sid("{$web_path}style." . PHP_EXT, 'id=' . self::$user->theme['style_id'] . '&amp;lang=' . self::$user->data['user_lang']),
		));

		if (is_404() || is_category() || is_day() || is_month() || is_year() || is_search() || is_paged())
		{
			self::wp_notes();
		}
	}
	
	public static function wp_phpbb_user_logged()
	{
		$is_wp_user_logged_in = $is_phpbb_user_logged_in = (self::$user->data['user_id'] != ANONYMOUS) ? true : false;

		if (function_exists('is_user_logged_in'))
		{
			$is_wp_user_logged_in = (is_user_logged_in()) ? true : false;
		}		
		return ($is_phpbb_user_logged_in || $is_wp_user_logged_in) ? true : false;
	}

	public static function wp_location($location = 'index')
	{
		$m = get_query_var('m');
		$year = get_query_var('year');
		$monthnum = get_query_var('monthnum');
		$day = get_query_var('day');
		$search = get_query_var('s');

		// If there is a post
		if (is_single() || (is_home() && !is_front_page()) || (is_page() && !is_front_page()))
		{
			$location = 'single';
		}

		// If there's a category or tag
		if (is_category() || is_tag())
		{
			$location = 'category';
		}

		// If there's a taxonomy
		if (is_tax())
		{
			$location = 'taxonomy';
		}

		// If there's an author
		if (is_author())
		{
			$location = 'author';
		}

		// If there's a post type archive
		if ( is_post_type_archive() )
		{
			$location = 'archive';
		}

		// If there's a month
		if (is_archive() && !empty($m))
		{
			$location = 'archive month';
		}

		// If there's a year
		if (is_archive() && !empty($year))
		{
			$location = 'archive year';
		}

		// If it's a search
		if (is_search())
		{
			$location = 'search';
		}

		// If it's a 404 page
		if (is_404())
		{
			$location = 'wp-error';
		}

		return $location;
	}

	/**
	 * Enter description here...
	 * See also WordPress root/wp-content/theme/phpBB/functions.php
	 *
	 * @return unknown
	 */
	public static function wp_page_header()
	{
		$blog_header = "\n";
		$blog_header .= '<link rel="pingback" href="' . get_bloginfo('pingback_url') . '" />' . "\n";

		// Main layout 1 column
		$blog_header .= '<link rel="stylesheet" type="text/css" media="all" href="' . get_bloginfo('stylesheet_url') . '?ver=' . WP_PHPBB_BRIDGE_VERSION . '" />' . "\n";

		// Some js files
		add_action('wp_head', 'wp_phpbb_javascript');

		/* Always have wp_head() just before the closing </head>
		 * tag of your theme, or you will break many plugins, which
		 * generally use this hook to add elements to <head> such
		 * as styles, scripts, and meta tags.
		 */
		$blog_header .= wp_do_action('wp_head');

		return $blog_header;
	}

	public static function wp_notes()
	{
		$is_404 = $is_category = $is_day = $is_month = $is_year = $is_search = $is_paged = '';

		// If this is a 404 page
		if (is_404())
		{
			$error_404 = self::$user->lang['WP_ERROR_404'];
		}
		// If this is a category archive
		else if (is_category())
		{
			$is_category = sprintf(self::$user->lang['WP_TITLE_CATEGORIES_EXPLAIN'], single_cat_title('', false));
		}
		// If this is a yearly archive
		else if (is_day())
		{
			$is_day = sprintf(self::$user->lang['WP_TITLE_ARCHIVE_DAY_EXPLAIN'], get_bloginfo('url'), get_bloginfo('name'), get_the_time(__('l, F jS, Y', 'default')));
		}
		// If this is a monthly archive
		else if (is_month())
		{
			$is_month = sprintf(self::$user->lang['WP_TITLE_ARCHIVE_MONTH_EXPLAIN'], get_bloginfo('url'), get_bloginfo('name'), get_the_time(__('F, Y', 'default')));
		}
		//	If this is a yearly archive
		else if (is_year())
		{
			$is_year = sprintf(self::$user->lang['WP_TITLE_ARCHIVE_YEAR_EXPLAIN'], get_bloginfo('url'), get_bloginfo('name'), get_the_time('Y'));
		}
		//	If this is a monthly archive
		else if (is_search())
		{
			$is_search = sprintf(self::$user->lang['WP_TITLE_ARCHIVE_SEARCH_EXPLAIN'], get_bloginfo('url'), get_bloginfo('name'), get_search_query());
		}
		//	If this is a monthly archive
		else if (isset($_GET['paged']) && !empty($_GET['paged']))
		{
			$is_paged = sprintf(self::$user->lang['WP_TITLE_ARCHIVE_EXPLAIN'], get_bloginfo('url'), get_bloginfo('name'));
		}

		self::$template->assign_vars(array(
			'WP_NOTES_IS_404'		=> $is_404,
			'WP_NOTES_IS_CATEGORY'	=> $is_category,
			'WP_NOTES_IS_MONTH'		=> $is_month,
			'WP_NOTES_IS_YEAR'		=> $is_year,
			'WP_NOTES_IS_SEARCH'	=> $is_search,
			'WP_NOTES_IS_PAGED'		=> $is_paged,
		));
	}

	/**
	 * Page right collumn function handling the WP tasks
	 */
	public static function page_sidebar($post_id = 0)
	{
		// Author information 
		$post_id = request_var('p', $post_id);
		if (is_single() && $post_id)
		{
			$post = get_post($post_id);
			self::phpbb_the_autor_full($post->post_author, true, false);
		}

		get_sidebar();
	}

	/**
	 * Allows you to display a list of recent topics within a specific forum id's.
	 *
	 */
	public static function phpbb_recet_topics($instance, $defaults)
	{
		// Only run this widget on index page
		if (!is_home() || !is_front_page())
		{
			return false;
		}

		$instance = wp_parse_args($instance, $defaults);

		$instance['forums'] = explode(',', $instance['forums']);

		// We need to find at least one postable forum where the user can read
		if ($instance['forums'][0] == 0)
		{
		//	$forum_ids = array_keys(self::$auth->acl_getf('f_read', true));
			$instance['forums'] = array_keys(self::$auth->acl_getf('f_read', true));
		}
		// User cannot read any forums
		if (empty($instance['forums']))
		{
			return false;
		}

		$sql_array = array(
			'SELECT'	=> 'f.forum_id, f.forum_name,
							t.topic_id, t.forum_id, t.topic_title, t.topic_poster, t.topic_first_poster_name, t.topic_first_poster_colour, t.topic_last_post_id, t.topic_last_poster_id, t.topic_last_poster_name, t.topic_last_poster_colour, 
							t.topic_views, t.topic_replies, t.topic_replies_real, t.topic_time, t.topic_last_post_time, t.topic_status, t.topic_type, t.poll_start, 
							u.username, u.user_colour',
			'FROM'		=> array(
				TOPICS_TABLE => 't',
			),
			'LEFT_JOIN' => array(
				array(
					'FROM' => array(FORUMS_TABLE => 'f'),
					'ON' => 't.forum_id = f.forum_id',
				),
				array(
					'FROM' => array(USERS_TABLE => 'u'),
					'ON' => 't.topic_poster = u.user_id',
				),
			),
			'WHERE' => self::$db->sql_in_set('t.forum_id', $instance['forums']) . '
				AND t.topic_status <> ' . ITEM_MOVED . '
				AND t.topic_approved = 1
					OR t.forum_id = 0', //OR t.forum_id = 0, esta linea es para que muestre tambien los globales ya que el id del foro de estos es 0
			'ORDER_BY' => 't.topic_type DESC, t.topic_last_post_time DESC',
		);

		$sql = self::$db->sql_build_query('SELECT', $sql_array);
		$result = self::$db->sql_query_limit($sql, (int) $instance['total']);

		while ($row = self::$db->sql_fetchrow($result))
		{
			$topic_list[] = $row;
		}

		self::$db->sql_freeresult($result);

		if (!isset($topic_list) || !sizeof($topic_list))
		{
			return;
		}

		// Output the topics
		for ($i = 0, $end = sizeof($topic_list); $i < $end; ++$i)
		{
			$topic_data =& $topic_list[$i];
		/**
			$topic_forum_id = (int) $topic_data['forum_id'];

			// Replies
			$replies = (self::$auth->acl_get('m_approve', $topic_forum_id)) ? $topic_data['topic_replies_real'] : $topic_data['topic_replies'];

			$unread_topic = false;
			// Get folder img, topic status/type related information
			$folder_img = $folder_alt = $topic_type = '';
			topic_status($topic_data, $replies, $unread_topic, $folder_img, $folder_alt, $topic_type);
		**/
			// Dump vars into template
			self::$template->assign_block_vars('recettopicsrow', array(
				'TOPIC_TITLE' 	=> $topic_data['topic_title'],
				'U_VIEW_TOPIC'	=> self::append_sid("viewtopic", array('f' => $topic_data['forum_id'], 't' => $topic_data['topic_id'])),

				'FORUM_NAME' 	=> ($instance['showForum']) ? $topic_data['forum_name'] : '',
				'U_VIEW_FORUM'	=> self::append_sid("viewforum", array('f' => $topic_data['forum_id'])),

			//	'TOPIC_FOLDER_IMG_SRC'	=> self::$user->img($folder_img, $folder_alt, false, '', 'src'),
				'REPLIES'		=> ($instance['showTotalPosts']) ? $topic_data['topic_replies'] : '',
				'VIEWS'			=> ($instance['showTotalViews']) ? $topic_data['topic_views'] : '',

				'TOPIC_AUTHOR_FULL'		=> ($instance['showTotalPosts']) ? get_username_string('full', $topic_data['topic_poster'], $topic_data['topic_first_poster_name'], $topic_data['topic_first_poster_colour']) : '',
				'FIRST_POST_TIME'		=> self::$user->format_date($topic_data['topic_time']),

				'U_LAST_POST'			=> self::append_sid("viewtopic", array('f' => $topic_data['forum_id'], 't' => $topic_data['topic_id'], 'p'=> $topic_data['topic_last_post_id'] . '#p' . $topic_data['topic_last_post_id'])),
				'LAST_POST_AUTHOR_FULL'	=> get_username_string('full', $topic_data['topic_last_poster_id'], $topic_data['topic_last_poster_name'], $topic_data['topic_last_poster_colour']),
				'LAST_POST_TIME'		=> self::$user->format_date($topic_data['topic_last_post_time']),
			));
		}

		self::$template->assign_vars(array(
			'L_RECENT_TOPICS'	=> ($instance['title']) ? $instance['title'] : phpbb::$user->lang['WP_TITLE_RECENT_TOPICS'],
			'S_RECENT_TOPICS'	=> sizeof($topic_list),
			'LAST_POST_IMG'		=> self::$user->img('icon_topic_latest', 'VIEW_LATEST_POST'),
		));

		// Make sure we set up the sidebar style
		if (!did_action('wp_phpbb_stylesheet'))
		{
			// Extra layout 2 columns
			add_action('wp_head', 'wp_phpbb_stylesheet');
		}
 	}

	/**
	 * function handling the users data
	 */
	public static function phpbb_the_autor_full($wp_poster_id = 0, $dump = false, $is_commen = false)
	{
		global $user_cache;
		
		if (empty($user_cache) || !is_array($user_cache))
		{
			$user_cache = array();
		}

		$wp_poster_id = (int) $wp_poster_id;
		/*
		* Cache various user specific data ... so we don't have to recompute
		* this each time the same user appears on this page
		* unless we need to display the author data in the sidebar 
		*/
		if (!isset($user_cache[$wp_poster_id]) || !$is_commen)
		{
			$wp_poster_data = get_userdata($wp_poster_id);

			// In WP the anonymous user is ID 0, we change that to the phpbb anonymous user ID
			if ($wp_poster_id == 0 || !$wp_poster_data)
			{
		//		$wp_poster_data->display_name = $wp_poster_data->user_nicename = (!$wp_poster_data) ? self::$user->lang['GUEST'] : get_comment_author($wp_poster_id);
				$wp_poster_data->display_name = get_comment_author();
				$wp_poster_data->user_nicename = get_comment_author_link();
				$wp_poster_data->phpbb_userid = ANONYMOUS;
			}

			$poster_id = (int) $wp_poster_data->phpbb_userid;

			$sql = 'SELECT *
				FROM ' . USERS_TABLE . '
				WHERE user_id = ' . $poster_id;
			$result = self::$db->sql_query($sql);
			$row = self::$db->sql_fetchrow($result);
			self::$db->sql_freeresult($result);

			if (!$row)
			{
				return self::phpbb_the_autor_full(0, $dump, $is_commen);
			//	return array();
			}

			self::_include('functions_display', 'get_user_avatar');
			self::_include('bbcode', false, 'bbcode');

			$user_sig = '';
			$bbcode_bitfield = '';

			// We add the signature to every posters entry because enable_sig is post dependant
			if ($row['user_sig'] && self::$config['allow_sig'] && self::$user->optionget('viewsigs'))
			{
				$bbcode_bitfield = $bbcode_bitfield | base64_decode($row['user_sig_bbcode_bitfield']);
				// Instantiate BBCode if need be
				if ($bbcode_bitfield !== '')
				{
					$bbcode = new bbcode(base64_encode($bbcode_bitfield));
				}
				$row['user_sig'] = censor_text($row['user_sig']);

				if ($row['user_sig_bbcode_bitfield'])
				{
					$bbcode->bbcode_second_pass($row['user_sig'], $row['user_sig_bbcode_uid'], $row['user_sig_bbcode_bitfield']);
				}

				$row['sig'] = bbcode_nl2br($row['user_sig']);
				$row['sig'] = smiley_text($row['user_sig']);
			
				$user_sig = $row['sig'];
			}
		
			if ($is_commen || !$row['user_avatar_width'])
			{
				$row['user_avatar_width'] = $row['user_avatar_height'] = self::$config['wp_phpbb_bridge_comments_avatar_width'];
			}

			// IT'S A HACK! for images like avatar and rank
			global $phpbb_root_path;
			$phpbb_root_path = self::$absolute_phpbb_script_path;

			$user_cache[$wp_poster_id] = array(
				'author_full'		=> ($poster_id != ANONYMOUS) ? get_username_string('full', $poster_id, $row['username'], $row['user_colour']) : get_username_string('full', $poster_id, $wp_poster_data->user_nicename, $row['user_colour']),
				'author_colour'		=> ($poster_id != ANONYMOUS) ? get_username_string('colour', $poster_id, $row['username'], $row['user_colour']) : get_username_string('colour', $poster_id, $wp_poster_data->user_nicename, $row['user_colour']),
				'author_username'	=> ($poster_id != ANONYMOUS) ? get_username_string('username', $poster_id, $row['username'], $row['user_colour']) : get_username_string('username', $poster_id, $wp_poster_data->user_nicename, $row['user_colour']),
				'author_profile'	=> ($poster_id != ANONYMOUS) ? get_username_string('profile', $poster_id, $row['username'], $row['user_colour']) : get_username_string('profile', $poster_id, $wp_poster_data->user_nicename, $row['user_colour']),
				'username'			=> ($poster_id != ANONYMOUS) ? $row['username'] : $wp_poster_data->display_name ,
				'user_colour'		=> ($poster_id != ANONYMOUS) ? $row['user_colour'] :'',

			//	'online'			=> false,
				'avatar'			=> (self::$user->optionget('viewavatars')) ? get_user_avatar($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height']) : false,
				'avatar_width'		=> $row['user_avatar_width'],
				'avatar_height'		=> $row['user_avatar_height'],
				'rank_title'		=> '',
				'rank_image'		=> '',
				'rank_image_src'	=> '',
				'joined'			=> self::$user->format_date($row['user_regdate']),
				'posts'				=> $row['user_posts'],
				'from'				=> (!empty($row['user_from'])) ? $row['user_from'] : '',
				'warnings'			=> (isset($row['user_warnings'])) ? $row['user_warnings'] : 0,
				'age'				=> '',
				'sig'				=> $user_sig,

				'search'			=> (self::$auth->acl_get('u_search')) ? self::append_sid("search", "author_id=$poster_id&amp;sr=posts") : '',
				'viewonline'		=> $row['user_allow_viewonline'],
				'allow_pm'			=> $row['user_allow_pm'],

				'profile'			=> self::append_sid("memberlist", "mode=viewprofile&amp;u=$poster_id"),
				'email'				=> '',
				'icq_status_img'	=> '',
				'icq'				=> '',
				'www'				=> $row['user_website'],
				'aim'				=> ($row['user_aim'] && self::$auth->acl_get('u_sendim')) ? self::append_sid("memberlist", "mode=contact&amp;action=aim&amp;u=$poster_id") : '',
				'msn'				=> ($row['user_msnm'] && self::$auth->acl_get('u_sendim')) ? self::append_sid("memberlist", "mode=contact&amp;action=msnm&amp;u=$poster_id") : '',
				'yim'				=> ($row['user_yim']) ? 'http://edit.yahoo.com/config/send_webmesg?.target=' . urlencode($row['user_yim']) . '&amp;.src=pg' : '',
				'jabber'			=> ($row['user_jabber'] && self::$auth->acl_get('u_sendim')) ? self::append_sid("memberlist", "mode=contact&amp;action=jabber&amp;u=$poster_id") : '',
			);

			get_user_rank($row['user_rank'], $row['user_posts'], $user_cache[$wp_poster_id]['rank_title'], $user_cache[$wp_poster_id]['rank_image'], $user_cache[$wp_poster_id]['rank_image_src']);

			// Undo HACK! for images like avatar and rank
			$phpbb_root_path = PHPBB_ROOT_PATH;

			if ((!empty($row['user_allow_viewemail']) && self::$auth->acl_get('u_sendemail')) || self::$auth->acl_get('a_email'))
			{
				$user_cache[$wp_poster_id]['email'] = (self::$config['board_email_form'] && self::$config['email_enable']) ? self::append_sid("memberlist", "mode=email&amp;u=$poster_id") : ((self::$config['board_hide_emails'] && !self::$auth->acl_get('a_email')) ? '' : 'mailto:' . $row['user_email']);
			}

			if (!empty($row['user_icq']))
			{
				$user_cache[$wp_poster_id]['icq'] = 'http://www.icq.com/people/webmsg.php?to=' . $row['user_icq'];
				$user_cache[$wp_poster_id]['icq_status_img'] = '<img src="http://web.icq.com/whitepages/online?icq=' . $row['user_icq'] . '&amp;img=5" width="18" height="18" alt="" />';
			}

			if (self::$config['allow_birthdays'] && !empty($row['user_birthday']))
			{
				list($bday_day, $bday_month, $bday_year) = array_map('intval', explode('-', $row['user_birthday']));

				if ($bday_year)
				{
					$now = getdate(time() + self::$user->timezone + self::$user->dst - date('Z'));

					$diff = $now['mon'] - $bday_month;
					if ($diff == 0)
					{
						$diff = ($now['mday'] - $bday_day < 0) ? 1 : 0;
					}
					else
					{
						$diff = ($diff < 0) ? 1 : 0;
					}

					$user_cache[$wp_poster_id]['age'] = (int) ($now['year'] - $bday_year - $diff);
				}
			}

			if ($is_commen || $user_cache[$wp_poster_id]['avatar'] !== false)
			{
				// <img height="32" width="32" class="avatar avatar-32 photo avatar-default" src="http://0.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=32" alt="">
				$user_cache[$wp_poster_id]['avatar'] = str_replace('<img', '<img class="avatar avatar-32 photo avatar-default"', $user_cache[$wp_poster_id]['avatar']);
			}

			$user_cache[$wp_poster_id]['avatar'] = ($user_cache[$wp_poster_id]['avatar'] !== false) ? (($user_cache[$wp_poster_id]['avatar']) ? $user_cache[$wp_poster_id]['avatar'] : get_avatar($wp_poster_id, $user_cache[$wp_poster_id]['avatar_width'])) : '';
		}

		// Dump vars into template
		$autor = array(
			'POSTER_ID'				=> $wp_poster_id,
			'POST_AUTHOR_FULL'		=> $user_cache[$wp_poster_id]['author_full'],
			'POST_AUTHOR_COLOUR'	=> $user_cache[$wp_poster_id]['author_colour'],
			'POST_AUTHOR'			=> $user_cache[$wp_poster_id]['author_username'],
			'U_POST_AUTHOR'			=> $user_cache[$wp_poster_id]['author_profile'],
			'U_FORUM_POSTS_AUTHOR'	=> self::append_sid("search", array('author_id' => 2, 'sr' => 'posts')),
			'U_BLOG_POSTS_AUTHOR'	=> get_author_posts_url($wp_poster_id),
			'S_POSTS_AUTHOR'		=> get_the_author_posts(),

		//	'ONLINE_IMG'			=> ($poster_id == ANONYMOUS || !self::$config['load_onlinetrack']) ? '' : (($user_cache[$wp_poster_id]['online']) ? self::$user->img('icon_user_online', 'ONLINE') : self::$user->img('icon_user_offline', 'OFFLINE')),
		//	'S_ONLINE'				=> ($poster_id == ANONYMOUS || !self::$config['load_onlinetrack']) ? false : (($user_cache[$wp_poster_id]['online']) ? true : false),
			'POSTER_AVATAR'			=> $user_cache[$wp_poster_id]['avatar'],
			'RANK_TITLE'			=> $user_cache[$wp_poster_id]['rank_title'],
			'RANK_IMG'				=> $user_cache[$wp_poster_id]['rank_image'],
			'RANK_IMG_SRC'			=> $user_cache[$wp_poster_id]['rank_image_src'],
			'POSTER_JOINED'			=> $user_cache[$wp_poster_id]['joined'],
			'POSTER_POSTS'			=> $user_cache[$wp_poster_id]['posts'],
			'POSTER_FROM'			=> $user_cache[$wp_poster_id]['from'],
			'POSTER_WARNINGS'		=> $user_cache[$wp_poster_id]['warnings'],
			'POSTER_AGE'			=> $user_cache[$wp_poster_id]['age'],
			'SIGNATURE'				=> $user_cache[$wp_poster_id]['sig'],

			'PROFILE_IMG'		=> self::$user->img('icon_user_profile', 'READ_PROFILE'),
			'PM_IMG' 			=> self::$user->img('icon_contact_pm', 'SEND_PRIVATE_MESSAGE'),
			'EMAIL_IMG' 		=> self::$user->img('icon_contact_email', 'SEND_EMAIL'),
			'WWW_IMG' 			=> self::$user->img('icon_contact_www', 'VISIT_WEBSITE'),
			'ICQ_IMG' 			=> self::$user->img('icon_contact_icq', 'ICQ'),
			'AIM_IMG' 			=> self::$user->img('icon_contact_aim', 'AIM'),
			'MSN_IMG' 			=> self::$user->img('icon_contact_msnm', 'MSNM'),
			'YIM_IMG' 			=> self::$user->img('icon_contact_yahoo', 'YIM'),
			'JABBER_IMG'		=> self::$user->img('icon_contact_jabber', 'JABBER') ,

			'U_PROFILE'		=> $user_cache[$wp_poster_id]['profile'],
		//	'U_SEARCH'		=> $user_cache[$wp_poster_id]['search'],
		//	'U_PM'			=> ($poster_id != ANONYMOUS && self::$config['allow_privmsg'] && self::$auth->acl_get('u_sendpm') && ($user_cache[$wp_poster_id]['allow_pm'] || self::$auth->acl_gets('a_', 'm_') || self::$auth->acl_getf_global('m_'))) ? self::append_sid("ucp", 'i=pm&amp;mode=compose&amp;action=quotepost&amp;p=' . $row['post_id']) : '',
			'U_EMAIL'		=> $user_cache[$wp_poster_id]['email'],
			'U_WWW'			=> $user_cache[$wp_poster_id]['www'],
			'U_ICQ'			=> $user_cache[$wp_poster_id]['icq'],
			'U_AIM'			=> $user_cache[$wp_poster_id]['aim'],
			'U_MSN'			=> $user_cache[$wp_poster_id]['msn'],
			'U_YIM'			=> $user_cache[$wp_poster_id]['yim'],
			'U_JABBER'		=> $user_cache[$wp_poster_id]['jabber'],
		);

		// Dump vars into template ?
		if ($dump)
		{
			self::$template->assign_vars($autor);
		}

		return $autor;
	}

	/**
	 * Page footer function handling the phpBB tasks
	 */
	public static function page_footer($run_cron = true, $template_body = false)
	{
		self::$template->assign_vars(array(
			'BLOG_FOOTER'	=> self::wp_page_footer(),
		));

		self::$template->set_filenames(array(
			'body' => ($template_body !== false) ? $template_body : 'wordpress/index_body.html',
		));

		/**
		 * If the user is admin, we add a hook to fix the ACP & DEBUG url
		 * To works property the function wp_phpbb_u_acp() is located in the WordPress root/wp-content/themes/phpBB/includes/wp_phpbb_bridge.php file
		 */
		if (self::$auth->acl_get('a_') && !empty(self::$user->data['is_registered']))
		{
			global $phpbb_hook;
			$phpbb_hook->register(array('template', 'display'), 'wp_phpbb_u_acp');
		}

		// Do the phpBB page footer at least but do not run cron jobs
		page_footer(false);
	}

	public static function wp_page_footer()
	{
		$blog_footer  = '&nbsp;|&nbsp;Powered by <a href="http://wordpress.org/" title="Semantic Personal Publishing Platform" rel="generator" id="site-generator" onclick="window.open(this.href);return false;">WordPress</a>&nbsp;|&nbsp;Bridge by <a href="http://www.mssti.com/phpbb3" title="Micro Software &amp; Servicio Técnico Informático" onclick="window.open(this.href);return false;">.:: MSSTI ::.</a><br />';
		$blog_footer .= '<!-- If you\'d like to support WordPress, having the "powered by" link somewhere on your blog is the best way; it\'s our only promotion or advertising. -->' . "\n";
		$blog_footer .= sprintf(self::$user->lang['WP_RSS_NOTES'], '<a href="' . get_bloginfo('rss2_url') . '">' . self::$user->lang['WP_RSS_ENRIES_LINK'] . '</a>', '<a href="' . get_bloginfo('comments_rss2_url') . '">' . self::$user->lang['WP_RSS_COMMENTS_LINK'] . '</a>');

	//	$blog_footer .= wp_do_action('wp_footer');

		// Output page creation time
		if (defined('WP_DEBUG') and WP_DEBUG == true)
		{
			$blog_footer .=  '<br />' . sprintf(self::$user->lang['WP_DEBUG_NOTE'], get_num_queries(), timer_stop(0, 3));
		}

		return $blog_footer;
	}

	/**
	* Some images for the 
	* with fallback to a default the phpbb image
	*/
	function wp_imageset($img = 'all', $lang_var = '', $tpl_name = '')
	{
		static $imageset = array();

		if (empty($imageset))
		{
			$imageset = array(
				'icon_wp_trash' => array(
					'image_name' 				=> 'icon_wp_trash',
					'image_class'				=> 'wp-trash-icon',
					'image_name_fallback' 		=> 'icon_post_delete',
					'image_class_fallback'		=> 'wp-delete-icon',
				),
				'icon_wp_untrash' => array(
					'image_name' 				=> 'icon_wp_trash',
					'image_class'				=> 'wp-untrash-icon',
					'image_name_fallback' 		=> 'icon_post_delete',
					'image_class_fallback'		=> 'wp-undelete-icon',
				),
				'icon_wp_approve' => array(
					'image_name' 				=> 'icon_wp_approve',
					'image_class'				=> 'wp-approve-icon',
					'image_name_fallback' 		=> 'icon_post_report',
					'image_class_fallback'		=> 'wp-report-icon',
				),
				'icon_wp_unapprove' => array(
					'image_name' 				=> 'icon_wp_unapprove',
					'image_class'				=> 'wp-unapprove-icon',
					'image_name_fallback' 		=> 'icon_topic_unapproved',
					'image_class_fallback'		=> 'wp-noreport-icon',
				),
				'icon_wp_spam' => array(
					'image_name' 				=> 'icon_wp_spam',
					'image_class'				=> 'wp-spam-icon',
					'image_name_fallback' 		=> 'icon_post_report',
					'image_class_fallback'		=> 'wp-info-icon',
				),
				'icon_wp_nospam' => array(
					'image_name' 				=> 'icon_wp_spam',
					'image_class'				=> 'wp-nospam-icon',
					'image_name_fallback' 		=> 'icon_user_warn',
					'image_class_fallback'		=> 'wp-noinfo-icon',
				),
				'button_blogpost_new' => array(
					'image_name' 				=> 'button_blogpost_new',
					'image_class'				=> 'blogpostnew-icon',
					'image_name_fallback'		=> 'button_topic_new',
					'image_class_fallback'		=> 'wp-post-icon',
				),
			);
		}

		foreach ($imageset as $image => $img_data)
		{
			if ($img == $image)
			{
				$imagedata = self::$user->img($img_data['image_name'], $lang_var);
				if (!$imagedata != '')
				{
					$imagedata = self::$user->img($img_data['image_name_fallback'], $lang_var);
					$img_data['image_class'] = $img_data['image_class_fallback'];
				}
				self::$template->assign_var($tpl_name, $img_data['image_class']);

				// For Subsilver2
				return urldecode($imagedata);
			}
		}
	}
}

?>