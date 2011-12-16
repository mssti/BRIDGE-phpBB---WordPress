<?php
/**
 * 
 * @package: phpBB 3.0.9 :: BRIDGE phpBB & WordPress -> root/includes/acp/
 * @version: $Id: acp_wp_phpbb_bridge.php, v0.0.9 2011/08/25 11:08:25 leviatan21 Exp $
 * @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
 * @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
 * 
 */
/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package acp
*/
class acp_wp_phpbb_bridge
{
	var $u_action;
	var $new_config = array();

	function main($id, $mode)
	{
		global $db, $user, $template;
		global $config, $phpbb_root_path, $phpEx;

		$user->add_lang('mods/acp_wp_phpbb_bridge');

		$action	= request_var('action', '');
		$submit = (isset($_POST['submit'])) ? true : false;

		$form_key = 'acp_board';
		add_form_key($form_key);

		/** Set some default values so the user haven't to run any install - Start **/
		if (!$submit)
		{
			$this->obtain_config();
		}
		/** Set some default values so the user haven't to run any install - End **/

		/**
		*	Validation types are:
		*		string, int, bool,
		*		script_path (absolute path in url - beginning with / and no trailing slash),
		*		rpath (relative), rwpath (realtive, writable), path (relative path, but able to escape the root), wpath (writable)
		*/
		switch ($mode)
		{
			case 'manage':
/**
				$display_vars = array(
					'title'   => 'WP_PHPBB_BRIDGE_MANAGE',
					'vars'   => array(
						'legend1'	=> 'WP_PHPBB_BRIDGE_BASIC',
						'wp_phpbb_bridge_version'			=> array('lang'	=> 'WP_PHPBB_BRIDGE_VERSION',									'type' => 'custom',			'method' => 'wp_phpbb_bridge_version', 'params' => array('{CONFIG_VALUE}'), 'explain' => false),
				//		'wp_phpbb_bridge_disable'			=> array('lang' => 'WP_PHPBB_BRIDGE_DISABLE',			'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
						'wp_phpbb_bridge_wordpress_path'	=> array('lang' => 'WP_PHPBB_BRIDGE_WORDPRESS_PATH',	'validate' => 'path',	'type' => 'text:100:255',	'explain' => true),

						'legend2'	=> 'WP_PHPBB_BRIDGE_POST',
						'wp_phpbb_bridge_post_disable'		=> array('lang' => 'WP_PHPBB_BRIDGE_POST_DISABLE',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),

						'legend3'	=> 'WP_PHPBB_BRIDGE_PORTAL',
						'wp_phpbb_bridge_portal_disable'	=> array('lang' => 'WP_PHPBB_BRIDGE_PORTAL_DISABLE',	'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
						'wp_phpbb_bridge_portal_title'		=> array('lang' => 'WP_PHPBB_BRIDGE_PORTAL_TITLE',		'validate' => 'string',	'type' => 'text:100:255',	'explain' => true),
						'wp_phpbb_bridge_portal_style'		=> array('lang' => 'WP_PHPBB_BRIDGE_PORTAL_STYLE',		'validate' => 'int',	'type' => 'custom',			'method' => 'wp_phpbb_bridge_portal_styles', 'explain' => true,), // 'append' => ' ' . $user->lang['WP_PHPBB_BRIDGE_PORTAL_STYLE_EXPLAIN2']),
						'wp_phpbb_bridge_portal_limit'		=> array('lang' => 'WP_PHPBB_BRIDGE_PORTAL_LIMIT',		'validate' => 'int:5',	'type' => 'text:3:4',		'explain' => true),

						'legend4'	=> 'ACP_SUBMIT_CHANGES',
					)
				);
**/
				// Here, we check which add-on is available to set
				$file_wp_phpbb_bridge_insert_post	= (file_exists($phpbb_root_path . 'wp_phpbb_bridge_insert_post.' . $phpEx)) ? true : false;
				$file_wp_phpbb_bridge_portal 		= (file_exists($phpbb_root_path . 'wp_phpbb_bridge_portal.' . $phpEx)) ? true : false;

				if ($file_wp_phpbb_bridge_insert_post || $file_wp_phpbb_bridge_portal)
				{
					$display_vars_wp_phpbb_bridge_basic = array(
						'legend1'	=> 'WP_PHPBB_BRIDGE_BASIC',
						'wp_phpbb_bridge_version'			=> array('lang'	=> 'WP_PHPBB_BRIDGE_VERSION',									'type' => 'custom',			'method' => 'wp_phpbb_bridge_version', 'params' => array('{CONFIG_VALUE}'), 'explain' => false),
					//	'wp_phpbb_bridge_disable'			=> array('lang' => 'WP_PHPBB_BRIDGE_DISABLE',			'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
						'wp_phpbb_bridge_wordpress_path'	=> array('lang' => 'WP_PHPBB_BRIDGE_WORDPRESS_PATH',	'validate' => 'path',	'type' => 'text:100:255',	'explain' => true),
					);

					$display_vars_wp_phpbb_bridge_submit = array(
						'legend4'	=> 'ACP_SUBMIT_CHANGES',
					);

					$display_vars_wp_phpbb_bridge_insert_post = (!$file_wp_phpbb_bridge_insert_post) ? array(
						'legend2'	=> 'WP_PHPBB_BRIDGE_POST',
						'wp_phpbb_bridge_insert_post_error'	=> array('lang' => 'WP_PHPBB_BRIDGE_MISSING_FILE', 		'type' => 'custom',		'method' => 'wp_phpbb_bridge_missingfile', 'params' =>  array('wp_phpbb_bridge_insert_post.'),	'explain' => false),
					) : array(
						'legend2'	=> 'WP_PHPBB_BRIDGE_POST',
						'wp_phpbb_bridge_post_disable'		=> array('lang' => 'WP_PHPBB_BRIDGE_POST_DISABLE',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
					);

					$display_vars_wp_phpbb_bridge_portal = (!$file_wp_phpbb_bridge_portal) ? array(
						'legend3'	=> 'WP_PHPBB_BRIDGE_PORTAL',
						'wp_phpbb_bridge_portal_error'		=> array('lang' => 'WP_PHPBB_BRIDGE_MISSING_FILE', 		'type' => 'custom',		'method' => 'wp_phpbb_bridge_missingfile', 'params' =>  array('wp_phpbb_bridge_portal.'),	'explain' => false),
					) : array(
						'legend3'	=> 'WP_PHPBB_BRIDGE_PORTAL',
						'wp_phpbb_bridge_portal_disable'	=> array('lang' => 'WP_PHPBB_BRIDGE_PORTAL_DISABLE',	'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
						'wp_phpbb_bridge_portal_title'		=> array('lang' => 'WP_PHPBB_BRIDGE_PORTAL_TITLE',		'validate' => 'string',	'type' => 'text:100:255',	'explain' => true),
						'wp_phpbb_bridge_portal_style'		=> array('lang' => 'WP_PHPBB_BRIDGE_PORTAL_STYLE',		'validate' => 'int',	'type' => 'custom',			'method' => 'wp_phpbb_bridge_portal_styles', 'explain' => true,),
						'wp_phpbb_bridge_portal_limit'		=> array('lang' => 'WP_PHPBB_BRIDGE_PORTAL_LIMIT',		'validate' => 'int:5',	'type' => 'text:3:4',		'explain' => true),
					);

					$display_vars = array(
						'title'	=> 'WP_PHPBB_BRIDGE_MANAGE',
						'vars'	=> array_merge($display_vars_wp_phpbb_bridge_basic, $display_vars_wp_phpbb_bridge_insert_post, $display_vars_wp_phpbb_bridge_portal, $display_vars_wp_phpbb_bridge_submit),
					);
				}
				else
				{
					trigger_error($user->lang['WP_PHPBB_BRIDGE_MISSING_FILE'] . '<br /><br />' . $this->wp_phpbb_bridge_missingfile('wp_phpbb_bridge_insert_post', '#ffffff') . $this->wp_phpbb_bridge_missingfile('wp_phpbb_bridge_portal', '#ffffff'), E_USER_WARNING);
				}

			break;

			default:
				trigger_error('NO_MODE', E_USER_ERROR);
			break;
		}

		if (isset($display_vars['lang']))
		{
			$user->add_lang($display_vars['lang']);
		}

		$this->new_config = $config;
		$cfg_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc(request_var('config', array('' => ''), true)) : $this->new_config;
		$error = array();

		// We validate the complete config if whished
		validate_config_vars($display_vars['vars'], $cfg_array, $error);

		if ($submit && !check_form_key($form_key))
		{
			$error[] = $user->lang['FORM_INVALID'];
		}

		// Do not write values if there is an error
		if (sizeof($error))
		{
			$submit = false;
		}

		// We go through the display_vars to make sure no one is trying to set variables he/she is not allowed to...
		foreach ($display_vars['vars'] as $config_name => $null)
		{
			if (!isset($cfg_array[$config_name]) || strpos($config_name, 'legend') !== false)
			{
				continue;
			}

			$this->new_config[$config_name] = $config_value = $cfg_array[$config_name];

			if ($submit)
			{
				set_config($config_name, $config_value);
			}
		}

		if ($submit)
		{
			add_log('admin', 'LOG_WP_PHPBB_BRIDGE_SETTINGS');

			trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		$this->tpl_name = 'acp_board';
		$this->page_title = $display_vars['title'];

		$template->assign_vars(array(
			'L_TITLE'			=> $user->lang[$display_vars['title']],
			'L_TITLE_EXPLAIN'	=> $user->lang[$display_vars['title'] . '_EXPLAIN'],

			'S_ERROR'			=> (sizeof($error)) ? true : false,
			'ERROR_MSG'			=> implode('<br />', $error),

			'U_ACTION'			=> $this->u_action)
		);

		// Output relevant page
		foreach ($display_vars['vars'] as $config_key => $vars)
		{
			if (!is_array($vars) && strpos($config_key, 'legend') === false)
			{
				continue;
			}

			if (strpos($config_key, 'legend') !== false)
			{
				$template->assign_block_vars('options', array(
					'S_LEGEND'		=> true,
					'LEGEND'		=> (isset($user->lang[$vars])) ? $user->lang[$vars] : $vars)
				);

				continue;
			}

			$type = explode(':', $vars['type']);

			$l_explain = '';
			if ($vars['explain'] && isset($vars['lang_explain']))
			{
				$l_explain = (isset($user->lang[$vars['lang_explain']])) ? $user->lang[$vars['lang_explain']] : $vars['lang_explain'];
			}
			else if ($vars['explain'])
			{
				$l_explain = (isset($user->lang[$vars['lang'] . '_EXPLAIN'])) ? $user->lang[$vars['lang'] . '_EXPLAIN'] : '';
			}

			$content = build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars);

			if (empty($content))
			{
				continue;
			}

			$template->assign_block_vars('options', array(
				'KEY'			=> $config_key,
				'TITLE'			=> (isset($user->lang[$vars['lang']])) ? $user->lang[$vars['lang']] : $vars['lang'],
				'S_EXPLAIN'		=> $vars['explain'],
				'TITLE_EXPLAIN'	=> $l_explain,
				'CONTENT'		=> build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars),
				)
			);

			unset($display_vars['vars'][$config_key]);
		}
	}

	function wp_phpbb_bridge_missingfile($file = '', $color = '#BC2A4D')
	{
		global $phpEx;

		return '<strong style="color: ' . $color . ';">root/' . $file . $phpEx . '</strong><br />';
	}

	/**
	* @description: Check if the mod is up to date
	* 
	* @param: $current_version
	**/
	function wp_phpbb_bridge_version($current_version = '')
	{
		global $module, $cache, $user;
		global $phpEx;

		// First we need to know owr version, got it from the files
		$parent_module = "{$module->p_class}_{$module->p_name}_info";
		$module_file = "{$module->include_path}{$module->p_class}/info/{$module->p_class}_{$module->p_name}." . $phpEx;

		if (!class_exists($parent_module))
		{
			include($module_file);
		}
		$module_info = new $parent_module();
		$module_data = $module_info->module();
		$current_version = $module_data['version'];

		// Now check version against mssti web
		$message = '';
		$version_up_to_date = true;
		$version_check_link = '';

		$info = $cache->get('wp_phpbb_bridge_versioncheck');

		if ($info === false)
		{
			$errstr = '';
			$errno = 0;

			$info = get_remote_file('www.mssti.com', '/phpbb3/store/updatecheck', 'wp_phpbb_bridge.txt', $errstr, $errno);

			// There was not possible to contact to the site or file
			if ($info === false)
			{
				$cache->destroy('wp_phpbb_bridge_versioncheck');
				// Blue color text
				return '<strong style="color: #327AA5;">' . $current_version . '</strong> <img src="./images/file_not_modified.gif" alt="" /> ' . $user->lang['VERSIONCHECK_FAIL'];
			}

			$cache->put('wp_phpbb_bridge_versioncheck', $info, 86400);
		}

		if ($info !== false)
		{
			$latest_version_info = explode("\n", $info);
			$latest_version = strtolower(trim($latest_version_info[0]));
			$current_version = strtolower(trim($current_version));
			$version_up_to_date = version_compare($current_version, $latest_version, '<') ? false : true;
			$version_check_link = ($version_up_to_date) ? '' : $latest_version_info[1];
		}

		if ($version_up_to_date)
		{
			// Green color text
			return '<strong style="color: #228822;">' . $current_version . '</strong> <img src="./images/file_up_to_date.gif" alt="" />';
		}
		else
		{
			// Dark red color text
			return ' <strong style="color: #BC2A4D;">' . $current_version . '</strong> <img src="./images/file_conflict.gif" alt="" /> ' . sprintf($user->lang['WP_PHPBB_BRIDGE_NOT_UP_TO_DATE'], '<a href="' . str_replace('&', '&amp;', $version_check_link) . '" onclick="window.open(this.href);return false;">', '</a>', '<strong style="color: #228822;">' . $latest_version . '</strong>');
		}
	}

	/**
	* Get config values
	*/
	function obtain_config()
	{
		global $config;
		global $user;

		$wp_phpbb_portal = array(
		//	config option						=> default value
		//	'wp_phpbb_bridge_disable'			=> true,
			'wp_phpbb_bridge_wordpress_path'	=> '../wordpress/',

			'wp_phpbb_bridge_post_disable'		=> true,

			'wp_phpbb_bridge_portal_disable'	=> true,
			'wp_phpbb_bridge_portal_title'		=> (isset($user->lang['ACP_WP_PHPBB_BRIDGE_PORTAL']) ? $user->lang['ACP_WP_PHPBB_BRIDGE_PORTAL'] : ''),
			'wp_phpbb_bridge_portal_style'		=> 0,	//  0 => 'style', 1 => 'compact', 2 => 'post'
			'wp_phpbb_bridge_portal_limit'		=> 10,
		);

		foreach ($wp_phpbb_portal as $config_name => $config_value)
		{
			if (!isset($config[$config_name]))
			{
				$config[$config_name] = $config_value;
			}
		}
	}

	/**
	* Select portal wordpress style
	*/
	function wp_phpbb_bridge_portal_styles($value, $key = '')
	{
		$radio_ary = array(0 => 'WP_PHPBB_BRIDGE_PORTAL_BLOCK_STYLE', 1 => 'WP_PHPBB_BRIDGE_PORTAL_BLOCK_COMPACT', 2 => 'WP_PHPBB_BRIDGE_PORTAL_BLOCK_POST');

		return h_radio('config[' . $key . ']', 	$radio_ary, $value, $key);
	}
}

?>