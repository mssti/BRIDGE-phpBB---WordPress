<?php
/**
 * 
 * @package: phpBB 3.0.8 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/theme/prosilver
 * @version: $Id: wp_phpbb_core.php, v 0.0.1 2011/06/20 11:06:20 leviatan21 Exp $
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

/**
 * phpBB class that will be used in place of globalising these variables.
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
	 * Static Constructor.
	 */
	public static function initialise()
	{
		global $wpdb;
		$wpdb = phpbb_get_wp_db();
		
		global $auth, $config, $db, $template, $user, $cache;

		self::$auth		= &$auth;
		self::$config	= &$config;
		self::$db		= &$db;
		self::$template	= &$template;
		self::$user		= &$user;
//		self::$cache	= &$cache;

		// Start session management
		if (!defined('PHPBB_INCLUDED'))
		{
			self::$user->session_begin();
			self::$auth->acl(self::$user->data);
			self::$user->setup();
		}
		self::wp_phpbb_sanitize_userid();
	}

	/**
	 * Update phpbb user data with wp user data
	 * 	Andupdate wp user data with phpbb user data
	 *
	 */
	public static function wp_phpbb_sanitize_userid()
	{
		global $wp_user;

		$userid = self::wp_get_userid();

		if ($userid <= 0 && is_user_logged_in())
		{
			wp_logout();
			wp_redirect('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		}
		else if ($userid > 0 && $userid != $wp_user->ID)
		{
			wp_set_current_user($userid);
			wp_set_auth_cookie($userid, true, false);
			self::wp_update_user($userid, $user);
		}

		// enhance phpbb user data with WP user data
		self::$user->data['wp_user'] = self::wp_get_userdata($userid);

	//	return self::$user->session_id;
	}

	public static function wp_get_userid()
	{
		global $wpdb;

		$userid = 0;

		if (self::$user->data['user_type'] == USER_NORMAL || self::$user->data['user_type'] == USER_FOUNDER)
		{
			$id_list = $wpdb->get_col($wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'phpbb_userid' AND meta_value = %d", self::$user->data['user_id']));

			if (empty($id_list))
			{
				$check_email = email_exists(self::$user->data['user_email']);

				if (self::$user->data['user_id'] == 2)
				{
					$userid = 1;
				}
				else if ($check_email)
				{
					$userid = $check_email;
				}
				else
				{
					$userid = wp_create_user(self::phpbb_get_username(), wp_generate_password(), self::$user->data['user_email']);
				}

				update_user_meta($userid, 'phpbb_userid', self::$user->data['user_id']);
			}
			else
			{
				$userid = $id_list[0];
			}
		}

		return $userid;
	}

	public static function wp_update_user($userid)
	{
		$userdata['ID'] = $userid;
		$userdata['user_url'] = self::$user->data['user_website'];
		$userdata['user_email'] = self::$user->data['user_email'];
		$userdata['nickname'] = self::$user->data['username'];
		$userdata['jabber'] = self::$user->data['user_jabber'];
		$userdata['aim'] = self::$user->data['user_aim'];
		$userdata['yim'] = self::$user->data['user_yim'];

		wp_update_user($userdata);
	}

	/**
	 * Get all available usuer data from wordpress tables
	 *
	 * @param integer $user_id
	 * @return array
	 */
	public static function wp_get_userdata($user_id)
	{
		global $wpdb;
		
		$user_id = (int) $user_id;
		$wpuser = array();

		$users = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE ID = %d LIMIT 1", $user_id ) );
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

		$usermeta = $wpdb->get_results( $wpdb->prepare("SELECT meta_key, meta_value FROM $wpdb->usermeta WHERE user_id = %d ", $user_id));
		if (!empty($usermeta))
		{
			foreach($usermeta as $key => $value)
			{
				$wpuser[$value->meta_key] = $value->meta_value;
			}
		}

		return $wpuser;
	}

	public static function phpbb_get_username($count = 0)
	{
		$new_username = ereg_replace("[^A-Za-z0-9]", "", self::$user->data['username']);
		$new_username = strtolower($new_username);

		if ($count > 0)
		{
			$new_username .= (string)$count;
		}

		if (username_exists($new_username))
		{
			$count++;
			$new_username = self::phpbb_get_username($count);
		}

		return $new_username;
	}

	/**
	* Shortcut for phpbb's append_sid function (do not send the root path/phpext in the url part)
	*
	* @param mixed $url
	* @param mixed $params
	* @param mixed $is_amp
	* @param mixed $session_id
	* @return string
	*/
	public static function append_sid($script, $params = false, $is_amp = true, $session_id = false)
	{
		return append_sid( PHPBB_ROOT_PATH . $script . '.' . PHP_EXT, $params, $is_amp, $session_id);
	}

	/**
	 * Page header function for phpBB stuff
	 *
	 * @param <string> $page_title
	 */
	public static function page_header($page_title = '')
	{
		// Determine board url - we may need it later
		$board_url = generate_board_url() . '/';
		$web_path = (defined('PHPBB_USE_BOARD_URL_PATH') && PHPBB_USE_BOARD_URL_PATH) ? $board_url : PHPBB_ROOT_PATH;
		$blog_path = get_option( 'siteurl' );

		// Do the phpBB page header stuff first
		page_header(phpbb::$user->lang['INDEX']);

		self::$template->assign_vars(array(
			'PHPBB_IN_FORUM'	=> false,
			'PHPBB_IN_WEB'		=> false,
			'PHPBB_IN_BLOG'		=> true,
			'PHPBB_IN_PASTEBIN'	=> false,
			'SCRIPT_NAME'		=> 'blog',
			'BLOG_LEFT_COLUMN'	=> BLOG_LEFT_COLUMN_WIDTH,

		//	'U_WEB'				=> append_sid($web_path),
			'U_INDEX'			=> append_sid($web_path),
			'U_BLOG'			=> append_sid($blog_path),

			'WP_USER_LOGGED_IN'	=> is_user_logged_in(),

			'WP_USER_NAME'		=> self::$user->data['wp_user']['user_nicename'],
			'WP_USER_ID'		=> self::$user->data['wp_user']['ID'],
			
			'PHPBB_USER_NAME'	=> self::$user->data['username'],
			'PHPBB_USER_ID'		=> self::$user->data['user_id'],

			'U_WP_ACP'			=> (self::$user->data['user_type'] == USER_FOUNDER) ? wp_register('<li class="icon-register rightside">', '</li>', false ) : '',
			'U_LOGIN'			=> wp_get_register(),
			'U_LOGOUT'			=> wp_get_loginout(),

		//	'PAGE_TITLE'		=> get_bloginfo('name'),
			'BLOG_HEADER'		=> self::wp_page_header($blog_path),
			'S_DISPLAY_SEARCH'	=> false,
			'S_CLOCK'			=> self::clock(),

			'T_THEME_PATH'			=> "{$web_path}styles/" . self::$user->theme['theme_path'] . '/theme',
			'T_STYLESHEET_LINK'		=> (!self::$user->theme['theme_storedb']) ? "{$web_path}styles/" . self::$user->theme['theme_path'] . '/theme/stylesheet.css' : append_sid("{$web_path}style." . PHP_EXT, 'id=' . self::$user->theme['style_id'] . '&amp;lang=' . self::$user->data['user_lang']),
		));
		
		/* Always have wp_head() just before the closing </head>
		 * tag of your theme, or you will break many plugins, which
		 * generally use this hook to add elements to <head> such
		 * as styles, scripts, and meta tags.
		 */
	//	wp_head();
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
		$midnight = ( (int) $h > 12) ? ' pm' : ' am';

		self::$template->assign_vars(array(
			'CURRENT_DATE'	=> sprintf(self::$user->lang['CURRENT_TIME'], self::$user->format_date(time() + $zone_offset, $date[0], true)),
			'CURRENT_TIME'	=> $time . $midnight,
		));

		return true;
	}

	public static function wp_page_header($blog_path = '')
	{
		$blog_header  = get_the_generator('xhtml') . "\n";
		$blog_header .= '<link rel="pingback" href="' . get_bloginfo('pingback_url') . '" />' . "\n";
		$blog_header .= '<link rel="alternate" type="application/rss+xml" title="' . get_bloginfo('name') . ' - RSS Feed' . '" href="' . get_bloginfo('rss2_url') . '" />' . "\n";

		$blog_header .= '<link rel="stylesheet" href="' . $blog_path . '/wp-admin/css/colors-classic.css" type="text/css" media="screen" />' . "\n";
		$blog_header .= '<link rel="stylesheet" href="' . get_bloginfo('stylesheet_directory') . '/style.css" type="text/css" media="screen" />' . "\n";

		$blog_header .= '<script type="text/javascript" src="' . get_bloginfo('stylesheet_directory') . '/js/javascript.js"></script>' . "\n";

		// jQuery
		if (is_single())
		{
		//	$blog_header .= '<script type="text/javascript" src="'. $blog_path .'/wp-includes/js/jquery/jquery.js"></script>' . "\n";
			$blog_header .= '<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>' . "\n";
		//	$blog_header .= '<script type="text/javascript" src="http://dev.jquery.com/view/trunk/plugins/validate/jquery.validate.js"></script>' . "\n";
			$blog_header .= '<script type="text/javascript" src=" '. get_bloginfo('stylesheet_directory') .'/js/jquery.validate.js"></script>' . "\n";
		}

		return $blog_header;
	}

	/**
	 * Page right collumn function handling the WP tasks
	 */
	public static function page_sidebar()
	{
		$wp_list_pages = $wp_get_archives = $wp_list_categories = $wp_tag_cloud = '';

		// Author information is disabled per default. Uncomment and fill in your details if you want to use it.
		$post_ID = request_var('p', 0);
		if (is_single() && $post_ID)
		{
			$post = get_post($post_ID);
			self::phpbb_the_autor_full($post->post_author, true, false);
		}

		$wp_list_pages = wp_list_pages(array('title_li' => '', 'echo' => 0));
		$wp_get_archives = wp_get_archives(array('type=monthly', 'echo' => 0));
		$wp_list_categories = wp_list_categories(array('title_li' => '', 'echo' => 0));
		$wp_tag_cloud = wp_tag_cloud(array('separator' => ", ", 'echo' => 0));

		$is_404 = $is_category = $is_day = $is_month = $is_year = $is_search = $is_paged = '';
		if (is_404() || is_category() || is_day() || is_month() || is_year() || is_search() || is_paged())
		{
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
		}

		$wp_list_bookmarks = $wp_register = $wp_meta = false;
	/**
		// If this is the frontpage
		if (is_home() || is_page())
		{
			$wp_list_bookmarks = wp_list_bookmarks(array('echo' => 0));
			$wp_register = wp_register('', false);
			$wp_loginout = wp_loginout('', false);
			$wp_meta = wp_meta();
		}
	**/
		self::$template->assign_vars(array(
			'SIDEBAR_WP_IS_404'				=> $is_404,
			'SIDEBAR_WP_IS_CATEGORY'		=> $is_category,
			'SIDEBAR_WP_IS_MONTH'			=> $is_month,
			'SIDEBAR_WP_IS_YEAR'			=> $is_year,
			'SIDEBAR_WP_IS_SEARCH'			=> $is_search,
			'SIDEBAR_WP_IS_PAGED'			=> $is_paged,

			'SIDEBAR_WP_PAGES_LIST'			=> $wp_list_pages,
			'SIDEBAR_WP_ARCHIVES_LIST'		=> $wp_get_archives,
			'SIDEBAR_WP_CATEGORIES'			=> $wp_list_categories,
		#	'SIDEBAR_WP_BOOKMARKS_LIST'		=> $wp_list_bookmarks,
		#	'SIDEBAR_WP_REGISTER'			=> $wp_register,
		#	'SIDEBAR_WP_LOGINOUT'			=> $wp_loginout,
		#	'SIDEBAR_WP_META'				=> $wp_meta,
		#	'SIDEBAR_WP_VALIDATOR'			=> '<a href="http://validator.w3.org/check/referer" title="' . __('This page validates as XHTML 1.0 Transitional') . '">' . __('Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr>') . '</a>',
		#	'SIDEBAR_WP_FRIENDS'			=> '<a href="http://gmpg.org/xfn/"><abbr title="' . __('XHTML Friends Network') . '">' . __('XFN') . '</abbr></a>',
		#	'SIDEBAR_WP_WORDPRESS'			=> '<a href="http://wordpress.org/" title="' . __('Powered by WordPress, state-of-the-art semantic personal publishing platform.') . '">WordPress</a>',
			'SIDEBAR_WP_TAG_CLOUD'			=> "<li>$wp_tag_cloud</li>",
		));
	}

	/**
	 * Page footer function handling the phpBB tasks
	 */
	public static function phpbb_the_autor_full($wp_poster_id = 0, $dump = false, $is_commen = false)
	{
		$wp_poster_id = (int) $wp_poster_id;
		
		$wp_poster_data = get_userdata($wp_poster_id);

		// In WP the anonymous user is ID 0, we change that to the phpbb anonymous user ID
		if ($wp_poster_id == 0)
		{
			$wp_poster_data->display_name = $wp_poster_data->user_nicename = get_comment_author($wp_poster_id);
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
			return '';
		}

		if (!function_exists('get_user_avatar'))
		{
			include(PHPBB_ROOT_PATH . 'includes/functions_display.' . PHP_EXT);
		}
		if (!class_exists('bbcode'))
		{
			include(PHPBB_ROOT_PATH . 'includes/bbcode.' . PHP_EXT);
		}

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
		
		if ($is_commen)
		{
			$row['user_avatar_width'] = $row['user_avatar_height'] = COMMENT_AVATAR_WIDTH;
		}

		$user_cache = array(
			'author_full'		=> ($poster_id != ANONYMOUS) ? get_username_string('full', $poster_id, $row['username'], $row['user_colour']) : get_username_string('full', $poster_id, $wp_poster_data->user_nicename, $row['user_colour']),
			'author_colour'		=> ($poster_id != ANONYMOUS) ? get_username_string('colour', $poster_id, $row['username'], $row['user_colour']) : get_username_string('colour', $poster_id, $wp_poster_data->user_nicename, $row['user_colour']),
			'author_username'	=> ($poster_id != ANONYMOUS) ? get_username_string('username', $poster_id, $row['username'], $row['user_colour']) : get_username_string('username', $poster_id, $wp_poster_data->user_nicename, $row['user_colour']),
			'author_profile'	=> ($poster_id != ANONYMOUS) ? get_username_string('profile', $poster_id, $row['username'], $row['user_colour']) : get_username_string('profile', $poster_id, $wp_poster_data->user_nicename, $row['user_colour']),
		//	'author_full'		=> ($poster_id != ANONYMOUS) ? get_username_string('full', $poster_id, $row['username'], $row['user_colour']) : '',
		//	'author_colour'		=> ($poster_id != ANONYMOUS) ? get_username_string('colour', $poster_id, $row['username'], $row['user_colour']) : '',
		//	'author_username'	=> ($poster_id != ANONYMOUS) ? get_username_string('username', $poster_id, $row['username'], $row['user_colour']) : '',
		//	'author_profile'	=> ($poster_id != ANONYMOUS) ? get_username_string('profile', $poster_id, $row['username'], $row['user_colour']) : '',
			'username'			=> ($poster_id != ANONYMOUS) ? $row['username'] : $wp_poster_data->display_name ,
			'user_colour'		=> ($poster_id != ANONYMOUS) ? $row['user_colour'] :'',

		//	'online'			=> false,
			'avatar'			=> (self::$user->optionget('viewavatars')) ? get_user_avatar($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height']) : false,
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

		get_user_rank($row['user_rank'], $row['user_posts'], $user_cache['rank_title'], $user_cache['rank_image'], $user_cache['rank_image_src']);

		if ((!empty($row['user_allow_viewemail']) && self::$auth->acl_get('u_sendemail')) || self::$auth->acl_get('a_email'))
		{
			$user_cache['email'] = (self::$config['board_email_form'] && self::$config['email_enable']) ? self::append_sid("memberlist", "mode=email&amp;u=$poster_id") : ((self::$config['board_hide_emails'] && !self::$auth->acl_get('a_email')) ? '' : 'mailto:' . $row['user_email']);
		}

		if (!empty($row['user_icq']))
		{
			$user_cache['icq'] = 'http://www.icq.com/people/webmsg.php?to=' . $row['user_icq'];
			$user_cache['icq_status_img'] = '<img src="http://web.icq.com/whitepages/online?icq=' . $row['user_icq'] . '&amp;img=5" width="18" height="18" alt="" />';
		}

		if (self::$config['allow_birthdays'] && !empty($row['user_birthday']))
		{
			list($bday_day, $bday_month, $bday_year) = array_map('intval', explode('-', $row['user_birthday']));

			if ($bday_year)
			{
				$diff = $now['mon'] - $bday_month;
				if ($diff == 0)
				{
					$diff = ($now['mday'] - $bday_day < 0) ? 1 : 0;
				}
				else
				{
					$diff = ($diff < 0) ? 1 : 0;
				}

				$user_cache['age'] = (int) ($now['year'] - $bday_year - $diff);
			}
		}

		if ($is_commen && $user_cache['avatar'] !== false)
		{
			// <img height="32" width="32" class="avatar avatar-32 photo avatar-default" src="http://0.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=32" alt="">
			$user_cache['avatar'] = str_replace('<img', '<img class="avatar avatar-32 photo avatar-default"', $user_cache['avatar']);
		}
		
		// Dump vars into template
		$autor = array(
			'POSTER_ID'				=> $poster_id,
		//	'POST_AUTHOR_FULL'		=> ($poster_id != ANONYMOUS) ? $user_cache['author_full'] : get_username_string('full', $poster_id, $row['username'], $row['user_colour'], $row['post_username']),
		//	'POST_AUTHOR_COLOUR'	=> ($poster_id != ANONYMOUS) ? $user_cache['author_colour'] : get_username_string('colour', $poster_id, $row['username'], $row['user_colour'], $row['post_username']),
		//	'POST_AUTHOR'			=> ($poster_id != ANONYMOUS) ? $user_cache['author_username'] : get_username_string('username', $poster_id, $row['username'], $row['user_colour'], $row['post_username']),
		//	'U_POST_AUTHOR'			=> ($poster_id != ANONYMOUS) ? $user_cache['author_profile'] : get_username_string('profile', $poster_id, $row['username'], $row['user_colour'], $row['post_username']),
			'POST_AUTHOR_FULL'		=> $user_cache['author_full'],
			'POST_AUTHOR_COLOUR'	=> $user_cache['author_colour'],
			'POST_AUTHOR'			=> $user_cache['author_username'],
			'U_POST_AUTHOR'			=> $user_cache['author_profile'],
			'U_POSTS_AUTHOR'		=> get_author_posts_url($wp_poster_id),
			'L_POSTS_AUTHOR'		=> sprintf(self::$user->lang['WP_READ_TOPICS'], $user_cache['author_username']),

		//	'ONLINE_IMG'			=> ($poster_id == ANONYMOUS || !self::$config['load_onlinetrack']) ? '' : (($user_cache['online']) ? self::$user->img('icon_user_online', 'ONLINE') : self::$user->img('icon_user_offline', 'OFFLINE')),
		//	'S_ONLINE'				=> ($poster_id == ANONYMOUS || !self::$config['load_onlinetrack']) ? false : (($user_cache['online']) ? true : false),
			'POSTER_AVATAR'			=> ($user_cache['avatar'] !== false) ? (($user_cache['avatar']) ? $user_cache['avatar'] : get_avatar($wp_poster_id, COMMENT_AVATAR_WIDTH)) : '',
			'RANK_TITLE'			=> $user_cache['rank_title'],
			'RANK_IMG'				=> $user_cache['rank_image'],
			'RANK_IMG_SRC'			=> $user_cache['rank_image_src'],
			'POSTER_JOINED'			=> $user_cache['joined'],
			'POSTER_POSTS'			=> $user_cache['posts'],
			'POSTER_FROM'			=> $user_cache['from'],
			'POSTER_WARNINGS'		=> $user_cache['warnings'],
			'POSTER_AGE'			=> $user_cache['age'],
			'SIGNATURE'				=> $user_cache['sig'],

			'ICQ_STATUS_IMG'		=> $user_cache['icq_status_img'],
			'U_PROFILE'		=> $user_cache['profile'],
		//	'U_SEARCH'		=> $user_cache['search'],
		//	'U_PM'			=> ($poster_id != ANONYMOUS && self::$config['allow_privmsg'] && self::$auth->acl_get('u_sendpm') && ($user_cache['allow_pm'] || self::$auth->acl_gets('a_', 'm_') || self::$auth->acl_getf_global('m_'))) ? self::append_sid("ucp", 'i=pm&amp;mode=compose&amp;action=quotepost&amp;p=' . $row['post_id']) : '',
			'U_EMAIL'		=> $user_cache['email'],
			'U_WWW'			=> $user_cache['www'],
			'U_ICQ'			=> $user_cache['icq'],
			'U_AIM'			=> $user_cache['aim'],
			'U_MSN'			=> $user_cache['msn'],
			'U_YIM'			=> $user_cache['yim'],
			'U_JABBER'		=> $user_cache['jabber'],
		);

		// Dump vars into template ?
		if ($dump)
		{
			self::$template->assign_vars($autor);
		}
		else
		{
			return $autor;
		}
	}

	/**
	 * Page footer function handling the phpBB tasks
	 */
	public static function page_footer($run_cron = true)
	{
		self::$template->assign_vars(array(
			'BLOG_FOOTER'	=> self::wp_page_footer(),
		));

		self::$template->set_filenames(array(
			'body' => 'wordpress/index_body.html')
		);

		// Do the phpBB page footer at least
		page_footer();
	}

	public static function wp_page_footer()
	{
		$blog_footer  = '&nbsp;|&nbsp;Powered by <a href="http://wordpress.org/" title="Semantic Personal Publishing Platform" rel="generator" id="site-generator" onclick="window.open(this.href);return false;">WordPress</a>&nbsp;|&nbsp;Bridge by <a href="http://www.mssti.com/phpbb3" title="Micro Software &amp; Servicio Técnico Informático" onclick="window.open(this.href);return false;">.:: MSSTI ::.</a><br />';
		$blog_footer .= '<!-- If you\'d like to support WordPress, having the "powered by" link somewhere on your blog is the best way; it\'s our only promotion or advertising. -->' . "\n";
		$blog_footer .= sprintf(self::$user->lang['WP_RSS_NOTES'], '<a href="' . get_bloginfo('rss2_url') . '">' . self::$user->lang['WP_RSS_ENRIES_LINK'] . '</a>', '<a href="' . get_bloginfo('comments_rss2_url') . '">' . self::$user->lang['WP_RSS_COMMENTS_LINK'] . '</a><br />');
		
		// Output page creation time
		if (defined('WP_DEBUG') and WP_DEBUG == true)
		{
			$blog_footer .= sprintf(self::$user->lang['WP_DEBUG_NOTE'], get_num_queries(), timer_stop(0, 3));
		}

		return $blog_footer;
	}

	function wp_phpbb_hook()
	{
		
	}

	/**
	* Generate login box or verify password
	*/
	function login_box($redirect = '', $l_explain = '', $l_success = '', $admin = false, $s_display = true)
	{
		self::_include('captcha/captcha_factory', 'phpbb_captcha_factory');
		self::$user->add_lang('ucp');

		$err = '';

		// Make sure user->setup() has been called
		if (empty(self::$user->lang))
		{
			self::$user->setup();
		}

		// Print out error if user tries to authenticate as an administrator without having the privileges...
		if ($admin && !self::$auth->acl_get('a_'))
		{
			// Not authd
			// anonymous/inactive users are never able to go to the ACP even if they have the relevant permissions
			if (self::$user->data['is_registered'])
			{
				add_log('admin', 'LOG_ADMIN_AUTH_FAIL');
			}
			trigger_error('NO_AUTH_ADMIN');
		}

		if (isset($_POST['login']))
		{
			// Get credential
			if ($admin)
			{
				$credential = request_var('credential', '');

				if (strspn($credential, 'abcdef0123456789') !== strlen($credential) || strlen($credential) != 32)
				{
					if (self::$user->data['is_registered'])
					{
						add_log('admin', 'LOG_ADMIN_AUTH_FAIL');
					}
					trigger_error('NO_AUTH_ADMIN');
				}

				$password	= request_var('password_' . $credential, '', true);
			}
			else
			{
				$password	= request_var('password', '', true);
			}

			$username	= request_var('username', '', true);
			$autologin	= (!empty($_POST['autologin'])) ? true : false;
			$viewonline = (!empty($_POST['viewonline'])) ? 0 : 1;
			$admin 		= ($admin) ? 1 : 0;
			$viewonline = ($admin) ? self::$user->data['session_viewonline'] : $viewonline;

			// Check if the supplied username is equal to the one stored within the database if re-authenticating
			if ($admin && utf8_clean_string(self::$username) != utf8_clean_string(self::$user->data['username']))
			{
				// We log the attempt to use a different username...
				add_log('admin', 'LOG_ADMIN_AUTH_FAIL');
				trigger_error('NO_AUTH_ADMIN_USER_DIFFER');
			}

			// If authentication is successful we redirect user to previous page
			$result = self::$auth->login($username, $password, $autologin, $viewonline, $admin);

			// If admin authentication and login, we will log if it was a success or not...
			// We also break the operation on the first non-success login - it could be argued that the user already knows
			if ($admin)
			{
				if ($result['status'] == LOGIN_SUCCESS)
				{
					add_log('admin', 'LOG_ADMIN_AUTH_SUCCESS');
				}
				else
				{
					// Only log the failed attempt if a real user tried to.
					// anonymous/inactive users are never able to go to the ACP even if they have the relevant permissions
					if (self::$user->data['is_registered'])
					{
						add_log('admin', 'LOG_ADMIN_AUTH_FAIL');
					}
				}
			}

			// The result parameter is always an array, holding the relevant information...
			if ($result['status'] == LOGIN_SUCCESS)
			{
				$redirect = request_var('redirect', '');

				if ($redirect)
				{
					$redirect = titania_url::unbuild_url($redirect);

					$base = $append = false;
					titania_url::split_base_params($base, $append, $redirect);

					redirect(titania_url::build_url($base, $append));
				}
				else
				{
					redirect(titania_url::build_url(titania_url::$current_page, titania_url::$params));
				}
			}

			// Something failed, determine what...
			if ($result['status'] == LOGIN_BREAK)
			{
				trigger_error($result['error_msg']);
			}

			// Special cases... determine
			switch ($result['status'])
			{
				case LOGIN_ERROR_ATTEMPTS:

					$captcha = phpbb_captcha_factory::get_instance(self::$config['captcha_plugin']);
					$captcha->init(CONFIRM_LOGIN);
					// $captcha->reset();

					// Parse the captcha template
					self::reset_template();
					self::$template->set_filenames(array(
						'captcha'	=> $captcha->get_template(),
					));

					// Correct confirm image link
					self::$template->assign_var('CONFIRM_IMAGE_LINK', self::append_sid('ucp', 'mode=confirm&amp;confirm_id=' . $captcha->confirm_id . '&amp;type=' . $captcha->type));

					self::$template->assign_display('captcha', 'CAPTCHA', false);

					titania::set_custom_template();

					$err = self::$user->lang[$result['error_msg']];
				break;

				case LOGIN_ERROR_PASSWORD_CONVERT:
					$err = sprintf(
						self::$user->lang[$result['error_msg']],
						(self::$config['email_enable']) ? '<a href="' . self::append_sid('ucp', 'mode=sendpassword') . '">' : '',
						(self::$config['email_enable']) ? '</a>' : '',
						(self::$config['board_contact']) ? '<a href="mailto:' . htmlspecialchars(self::$config['board_contact']) . '">' : '',
						(self::$config['board_contact']) ? '</a>' : ''
					);
				break;

				// Username, password, etc...
				default:
					$err = self::$user->lang[$result['error_msg']];

					// Assign admin contact to some error messages
					if ($result['error_msg'] == 'LOGIN_ERROR_USERNAME' || $result['error_msg'] == 'LOGIN_ERROR_PASSWORD')
					{
						$err = (!self::$config['board_contact']) ? sprintf(self::$user->lang[$result['error_msg']], '', '') : sprintf(self::$user->lang[$result['error_msg']], '<a href="mailto:' . htmlspecialchars(self::$config['board_contact']) . '">', '</a>');
					}

				break;
			}
		}

		// Assign credential for username/password pair
		$credential = ($admin) ? md5(unique_id()) : false;

		$s_hidden_fields = array(
			'sid'		=> self::$user->session_id,
		);

		if ($redirect)
		{
			$s_hidden_fields['redirect'] = $redirect;
		}

		if ($admin)
		{
			$s_hidden_fields['credential'] = $credential;
		}

		$s_hidden_fields = build_hidden_fields($s_hidden_fields);

		titania::page_header('LOGIN');

		self::$template->assign_vars(array(
			'LOGIN_ERROR'		=> $err,
			'LOGIN_EXPLAIN'		=> $l_explain,

			'U_SEND_PASSWORD' 		=> (self::$config['email_enable']) ? self::append_sid('ucp', 'mode=sendpassword') : '',
			'U_RESEND_ACTIVATION'	=> (self::$config['require_activation'] == USER_ACTIVATION_SELF && self::$config['email_enable']) ? self::append_sid('ucp', 'mode=resend_act') : '',
			'U_TERMS_USE'			=> self::append_sid('ucp', 'mode=terms'),
			'U_PRIVACY'				=> self::append_sid('ucp', 'mode=privacy'),

			'S_DISPLAY_FULL_LOGIN'	=> ($s_display) ? true : false,
			'S_HIDDEN_FIELDS' 		=> $s_hidden_fields,

			'S_ADMIN_AUTH'			=> $admin,
			'USERNAME'				=> ($admin) ? self::$user->data['username'] : '',

			'USERNAME_CREDENTIAL'	=> 'username',
			'PASSWORD_CREDENTIAL'	=> ($admin) ? 'password_' . $credential : 'password',
		));

		titania::page_footer(true, 'login_body.html');
	}

	/**
	* Update a user's postcount
	*
	* @param int $user_id The user_id
	* @param string $direction (+, -)
	* @param int $amount The amount to add or subtract
	*/
	public static function update_user_postcount($user_id, $direction = '+', $amount = 1)
	{
		$sql = 'UPDATE ' . USERS_TABLE . '
			SET user_posts = user_posts ' . (($direction == '+') ? '+' : '-') . ' ' . (int) $amount .
				(($direction == '+') ? ', user_lastpost_time = ' . time() : '') . '
			WHERE user_id = ' . (int) $user_id;
		self::$db->sql_query($sql);
	}
}


?>