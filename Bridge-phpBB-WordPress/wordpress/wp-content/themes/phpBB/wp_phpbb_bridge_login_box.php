<?php
/**
 * 
 * @package: phpBB 3.0.9 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/themes/phpBB/
 * @version: $Id: wp_phpbb_bridge_login_box.php, v0.0.9 2011/12/10 11:12:10 leviatan21 Exp $
 * @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
 * @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
 * 
 */

/**
* @ignore
**/

// Basic Wordpress files and options
define('SHORTINIT', true);
define('ABSPATH', dirname(__FILE__) . '/../../../');
define('TEMPLATEPATH', dirname(__FILE__) . '');
require_once(ABSPATH . 'wp-config.php');

// Basic phpBB files and options
if (!defined('IN_WP_PHPBB_BRIDGE'))
{
	@define('PHPBB_INAJAX', true);
//	@define('PHPBB_INCLUDED', true);
	global $wp_phpbb_bridge_config, $phpbb_root_path, $phpEx;
	global $auth, $config, $db, $template, $user, $cache;

	require_once('includes/wp_phpbb_bridge.php'); 
	include(PHPBB_ROOT_PATH . 'includes/functions_user.' . PHP_EXT);
}

// Start session management
phpbb::$user->session_begin();
phpbb::$auth->acl(phpbb::$user->data);
phpbb::$user->setup();

phpbb::$user->add_lang(array('common', 'ucp', 'mods/wp_phpbb_bridge'));

$error = array();

$wp_user_id = request_var('wp_user_id', 0);
$phpbb_session_id = request_var('sid', '');

if ($wp_user_id != 0)
{
	// Get the WP user data, Do not use get_userdata() here because we will have a name function conflict
	$wp_user_data = wp_phpbb_get_userdata($wp_user_id);
//	print_r("wp_phpbb_get_userdata=(");print_r($wp_user_data);print_r(")<br />");

	if (is_array($wp_user_data) && isset($wp_user_data['WPphpBBlogin']) && $wp_user_data['WPphpBBlogin'] != '')
	{
		$data_decrypt = wp_phpbb_decrypt($wp_user_data['WPphpBBlogin']);

		if ($data_decrypt != '')
		{
			$data_unserialize = unserialize($data_decrypt);
			if (is_array($data_unserialize))
			{
				$mode			= isset($data_unserialize['mode'])			? $data_unserialize['mode'] : '';
				$autologin		= isset($data_unserialize['rememberme'])	? $data_unserialize['rememberme'] : 0;
			//	$sid			= isset($data_unserialize['sid'])			? $data_unserialize['sid'] : '';
			//	$wp_user_id		= isset($data_unserialize['WPuser_id'])		? $data_unserialize['WPuser_id'] : 0;
				$wp_user_login	= isset($data_unserialize['WPuser_login'])	? utf8_normalize_nfc($data_unserialize['WPuser_login']) : '';
				$wp_user_pass	= isset($data_unserialize['WPuser_pass'])	? $data_unserialize['WPuser_pass'] : '';
				$wp_user_email	= isset($data_unserialize['WPuser_email'])	? strtolower($data_unserialize['WPuser_email']) : '';

			// Check if we have all needed values - Start

				// Are we in the ajax login ?
				if ($mode != 'loginajax')
				{
					$error[] =  phpbb::$user->lang['FORM_INVALID'];
				}

				// Exist the user login name and is valid ?
				if ($wp_user_login == '' || $wp_user_login != $wp_user_data['user_login'])
				{
					$error[] = phpbb::$user->lang['ERR_UNABLE_TO_LOGIN'];
				}

				// Exist the user password and is valid ?
				if ($wp_user_pass == '' || !wp_check_password($wp_user_pass, $wp_user_data['user_pass'], $wp_user_data['ID']))
				{
					$error[] = phpbb::$user->lang['ERR_UNABLE_TO_LOGIN'];
				}

				// Is there any user email and is valid ?
				if ($wp_user_email == '' || $wp_user_email != $wp_user_data['user_email'])
				{
					$error[] = phpbb::$user->lang['EMAIL_INVALID_EMAIL'];
				}
			// Check if we have all needed values - End
			}
			else
			{
				$error[] = phpbb::$user->lang['WP_INVALID_UNSERIALIZE_VALUE'];
			}
		}
		else
		{
			$error[] = phpbb::$user->lang['WP_INVALID_ENCRYPT_VALUE'];
		}
	}
	else
	{
		$error[] = phpbb::$user->lang['WP_INVALID_LOGIN_VALUE'];
	}
}
else
{
	$error[] = phpbb::$user->lang['WP_INVALID_USERID_VALUE'];
}

// return and display messages if there is an error
if (sizeof($error))
{
	echo addslashes(implode('<br />', $error));	// print_r(addslashes(implode('<br />', $error)));
	exit_handler(); 
}

/**
 * If we have all needed values and 
 * the passed sid is equal to the real user session id
 * we can continue, else is an intent of hacking
 */

// We check if the user already exist
$user_id_ary = array();
$username_ary = array($wp_user_login);
$result = user_get_id_name($user_id_ary, $username_ary);

// IF the user do not exist, create it
if (!sizeof($user_id_ary) || $result !== false)
{
	$phpbb_user_id = wp_phpbb_user_add($wp_user_login, $wp_user_pass, $wp_user_email);
}
else
{
	// Drop the arrays
	$phpbb_user_id = array_shift($user_id_ary);
}

// If the user exist or it was created we sould have an user ID, so authenticate it 
if ($phpbb_user_id)
{
	$message = phpbb::$user->lang['LOGIN_REDIRECT'];

	// If authentication is successful we redirect user to previous page
	$result = phpbb::$auth->login($wp_user_login, $wp_user_pass, $autologin, true, false);

	// The result parameter is always an array, holding the relevant information...
	if ($result['status'] == LOGIN_SUCCESS)
	{
		$message = phpbb::$user->lang['LOGIN_REDIRECT'];
	}
	// Something failed, determine what...
	else
	{
		switch ($result['status'])
		{
			case LOGIN_BREAK:
		//	case LOGIN_ERROR_ATTEMPTS:
				$message = phpbb::$user->lang[$result['error_msg']];
			break;

		//	case LOGIN_ERROR_PASSWORD_CONVERT:
		//		$err = sprintf(
		//			phpbb::$user->lang[$result['error_msg']],
		//			(phpbb::$config['email_enable']) ? '<a href="' . append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=sendpassword') . '">' : '',
		//			(phpbb::$config['email_enable']) ? '</a>' : '',
		//			(phpbb::$config['board_contact']) ? '<a href="mailto:' . htmlspecialchars(phpbb::$config['board_contact']) . '">' : '',
		//			(phpbb::$config['board_contact']) ? '</a>' : ''
		//		);
		//	break;

			// Username, password, etc...
			default:
				$message = phpbb::$user->lang[$result['error_msg']];

				// Assign admin contact to some error messages
				if ($result['error_msg'] == 'LOGIN_ERROR_USERNAME' || $result['error_msg'] == 'LOGIN_ERROR_PASSWORD')
				{
					$message = (!phpbb::$config['board_contact']) ? sprintf(phpbb::$user->lang[$result['error_msg']], '', '') : sprintf(phpbb::$user->lang[$result['error_msg']], '<a href="mailto:' . htmlspecialchars(phpbb::$config['board_contact']) . '">', '</a>');
				}
			break;
		}
	}
}
else
{
	$message = phpbb::$user->lang['WP_LOGIN_FAILED'];
}

echo addslashes($message);	// print_r(addslashes($message));
exit_handler();

function wp_phpbb_get_userdata($wp_user_id)
{
	global $wpdb;

	$wp_user_data = array();

	$data_users = $wpdb->get_row( "SELECT * FROM {$wpdb->users} WHERE ID = '{$wp_user_id}'" );
//	print_r("wpuser_data1=(");print_r($data_users);print_r(")<br />");
	if (!empty($data_users))
	{
		foreach($data_users as $id => $value)
		{
			$wp_user_data[$id] = $value;
		}
	}
	else
	{
		$wp_user_data = array(
			'user_nicename'	=>  '',
			'ID'			=> 0,
		);
	}

	$data_usermeta = $wpdb->get_results( "SELECT * FROM {$wpdb->usermeta} WHERE user_id = '{$wp_user_id}'" );
//	print_r("wpuser_data2=(");print_r($data_usermeta);print_r(")<br />");
	if (!empty($data_usermeta))
	{
		foreach($data_usermeta as $key => $value)
		{
		//	$wp_user_data[$key] = $value;
			$wp_user_data[$value->meta_key] = $value->meta_value;
		}
	}

//	print_r("<hr />wp_user=(");print_r($wp_user_data);print_r(")<br />");
	return $wp_user_data;
}

/**
 * Clean all user caches
 *
 * @since 3.0.0
 *
 * @param int $id User ID
 */
function clean_user_cache($id) {
/**
	$user = new WP_User($id);

	wp_cache_delete($id, 'users');
	wp_cache_delete($user->user_login, 'userlogins');
	wp_cache_delete($user->user_email, 'useremail');
	wp_cache_delete($user->user_nicename, 'userslugs');
	wp_cache_delete('blogs_of_user-' . $id, 'users');
**/
}

/**
* Add a WP user to phpbb
**/
function wp_phpbb_user_add($user_name, $user_pass, $user_email)
{
	$error = array();
	$group_id = 2;
	$coppa = (isset($_REQUEST['coppa'])) ? ((!empty($_REQUEST['coppa'])) ? 1 : 0) : false;

	// Try to manually determine the timezone and adjust the dst if the server date/time complies with the default setting +/- 1
	$timezone = date('Z') / 3600;
	$is_dst = date('I');

	if (phpbb::$config['board_timezone'] == $timezone || phpbb::$config['board_timezone'] == ($timezone - 1))
	{
		$timezone = ($is_dst) ? $timezone - 1 : $timezone;

		if (!isset(phpbb::$user->lang['tz_zones'][(string) $timezone]))
		{
			$timezone = phpbb::$config['board_timezone'];
		}
	}
	else
	{
		$is_dst = phpbb::$config['board_dst'];
		$timezone = phpbb::$config['board_timezone'];
	}

	// Collect the user data
	// We do use of "wp_" on propose, to do not load the encrypted data here
	$data = array(
		'username'			=> utf8_normalize_nfc(request_var('wp_user_name', $user_name, true)),
		'new_password'		=> request_var('wp_user_pass', $user_pass, true),
		'password_confirm'	=> request_var('wp_user_pass', $user_pass, true),
		'email'				=> strtolower(request_var('wp_user_email', $user_email)),
		'email_confirm'		=> strtolower(request_var('wp_user_email', $user_email)),
		'lang'				=> basename(request_var('lang', phpbb::$config['default_lang'])),
		'tz'				=> request_var('tz', (float) $timezone),
	);

	// A bit of cache hacking to get around disallowed usernames,
	// should be rethought in future versions (#62685)
	phpbb::$cache->destroy('_disallowed_usernames');
	phpbb::$cache->put('_disallowed_usernames', array());
/**
 * Skip checks, WP ad phpBB have fifferent requeriments 
	// Check vars
	$error = validate_data($data, array(
		'username'			=> array(
			array('string', false, phpbb::$config['min_name_chars'], phpbb::$config['max_name_chars']),
			array('username', '')),
		'new_password'		=> array(
			array('string', false, phpbb::$config['min_pass_chars'], phpbb::$config['max_pass_chars']),
			array('password')),
		'password_confirm'	=> array('string', false, phpbb::$config['min_pass_chars'], phpbb::$config['max_pass_chars']),
		'email'				=> array(
			array('string', false, 6, 60),
			array('email')),
		'email_confirm'		=> array('string', false, 6, 60),
		'tz'				=> array('num', false, -14, 14),
		'lang'				=> array('match', false, '#^[a-z_\-]{2,}$#i'),
	));
**/
	if ($data['new_password'] != $data['password_confirm'])
	{
		$error[] = phpbb::$user->lang['NEW_PASSWORD_ERROR'];
	}
	if ($data['email'] != $data['email_confirm'])
	{
		$error[] = phpbb::$user->lang['NEW_EMAIL_ERROR'];
	}

	// Make sure that the username list is recached next time around
	phpbb::$cache->destroy('_disallowed_usernames');

	// Something went wrong
	if (!empty($error))
	{
		return false;
	}

	// Which group by default?
	$group_name = ($coppa) ? 'REGISTERED_COPPA' : 'REGISTERED';

	$sql = 'SELECT group_id
		FROM ' . GROUPS_TABLE . "
		WHERE group_name = '" . phpbb::$db->sql_escape($group_name) . "'
			AND group_type = " . GROUP_SPECIAL;
	$result = phpbb::$db->sql_query($sql);
	$row = phpbb::$db->sql_fetchrow($result);
	phpbb::$db->sql_freeresult($result);

	if ($row)
	{
		$group_id = $row['group_id'];
	}

	// Register the user
	$user_row = array(
		'username'				=> $data['username'],
		'user_password'			=> phpbb_hash($data['new_password']),
		'user_email'			=> $data['email'],
		'group_id'				=> (int) $group_id,
		'user_timezone'			=> (float) $data['tz'],
		'user_dst'				=> $is_dst,
		'user_lang'				=> $data['lang'],
		'user_type'				=> USER_NORMAL,
		'user_actkey'			=> '',
		'user_ip'				=> phpbb::$user->ip,
		'user_regdate'			=> time(),
		'user_inactive_reason'	=> 0,
		'user_inactive_time'	=> 0,
	);

	$user_id = user_add($user_row, false);
	$errors = (sizeof($error)) ? implode('<br />', $error) : '';

	return ($user_id) ? $user_id : (($errors) ? $errors : false);
}

?>