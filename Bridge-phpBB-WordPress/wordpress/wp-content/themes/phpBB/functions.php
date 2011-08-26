<?php
/**
 * 
 * @package: phpBB 3.0.9 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/themes/phpBB
 * @version: $Id: functions.php, v0.0.8 2011/08/25 11:08:25 leviatan21 Exp $
 * @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
 * @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
 * 
 */

/**
* @ignore
**/

// Hide WordPress Admin Bar
add_filter('show_admin_bar', '__return_false');

load_theme_textdomain('wp_phpbb3_bridge', TEMPLATEPATH . '/languages' );

// http://wordpress.org/extend/plugins/dynamic-content-gallery-plugin/
// add_theme_support('post-thumbnails');

/**
 * Extra layout 2 columns
 */
function wp_phpbb_stylesheet()
{
	$blog_stylesheet = '<style type="text/css">
/** Style on-the-fly **/
.section-blog #wp-phpbb-bridge-container {
	margin-right: -' . ((int) phpbb::$config['wp_phpbb_bridge_widgets_column_width'] + 10) . 'px;
}
.section-blog #content {
	margin-right: ' . ((int) phpbb::$config['wp_phpbb_bridge_widgets_column_width'] + 10) . 'px;
}
.section-blog #wp-phpbb-bridge-primary {
	width: ' . (int) phpbb::$config['wp_phpbb_bridge_widgets_column_width'] . 'px;
}
</style>' . "\n";

	echo $blog_stylesheet;
}

/**
 * Insert some js files
 */
function wp_phpbb_javascript()
{
	// javascript for general proposes
	wp_register_script('javascript', get_bloginfo('stylesheet_directory') . '/js/javascript.js', false, WP_PHPBB_BRIDGE_VERSION);
	wp_enqueue_script('javascript');
	wp_print_scripts('javascript');

	// jQuery for resply to comments
	if (is_single())
	{
		wp_register_script('jquery', get_bloginfo('stylesheet_directory') . '/js/jquery-1.5.0.min.js', false, '1.5.0');
		wp_register_script('jquery-validate', get_bloginfo('stylesheet_directory') . '/js/jquery.validate.js', array('jquery'), '1.5.2', true);
		wp_enqueue_script('jquery-validate');
		wp_print_scripts('jquery-validate');
	}
}

/**
 * Pagination routine, generates page number sequence
 * 
 * Based off : phpbb3.0.8
 * File : phpbb/includes/functions.php
 */
function wp_generate_pagination($base_url, $num_items, $per_page, $on_page)
{
	$seperator = '<span class="page-sep">' . phpbb::$user->lang['COMMA_SEPARATOR'] . '</span>';
	$total_pages = ceil($num_items / $per_page);

	if ($total_pages == 1 || !$num_items)
	{
		return false;
	}

	global $wp_rewrite;
	global $paged;

	$paged = $on_page;
	$page_delim = 'cpage=';
	$url_delim = (strpos($base_url, '?') === false) ? '?' : ((strpos($base_url, '?') === strlen($base_url) - 1) ? '' : '&amp;');
	$url_delim2 = '';

	if ($wp_rewrite->using_permalinks())
	{
		$page_delim = 'comment-page-';
		$url_delim = '';
		$url_delim2 = '/#comments';
	}

	$page_string = ($on_page == 1) ? '<strong>1</strong>' : '<a href="' . $base_url . '">1</a>';
	$max_pages = min(ceil($num_items / $total_pages), 4);

	if ($total_pages > 5)
	{
		$start_cnt = min(max(1, $on_page - $max_pages), $total_pages - 5);
		$end_cnt = max(min($total_pages, $on_page + $max_pages), 6);

		$page_string .= ($start_cnt > 1) ? ' ... ' : $seperator;

		for ($i = $start_cnt + 1; $i < $end_cnt; $i++)
		{
			$page_string .= ($i == $on_page) ? '<strong>' . $i . '</strong>' : '<a href="' . $base_url . "{$url_delim}{$page_delim}" . $i . $url_delim2 . '">' . $i . '</a>';
			if ($i < $end_cnt - 1)
			{
				$page_string .= $seperator;
			}
		}

		$page_string .= ($end_cnt < $total_pages) ? ' ... ' : $seperator;
	}
	else
	{
		$page_string .= $seperator;

		for ($i = 2; $i < $total_pages; $i++)
		{
			$page_string .= ($i == $on_page) ? '<strong>' . $i . '</strong>' : '<a href="' . $base_url . "{$url_delim}{$page_delim}" . $i . $url_delim2 . '">' . $i . '</a>';
			if ($i < $total_pages)
			{
				$page_string .= $seperator;
			}
		}
	}

	$page_string .= ($on_page == $total_pages) ? '<strong>' . $total_pages . '</strong>' : '<a href="' . $base_url . "{$url_delim}{$page_delim}" . $total_pages . '">' . $total_pages . '</a>';

	return $page_string;
}

/**
 * Generate topic pagination
 * 
 * Based off : phpbb3.0.8
 * File : phpbb/includes/functions_display.php
 */
function wp_topic_generate_pagination($url, $replies, $per_page)
{
	$url_delim = (strpos($url, '?') === false) ? '?' : ((strpos($url, '?') === strlen($url) - 1) ? '' : '&amp;');

	if (($replies + 1) > $per_page)
	{
		$total_pages = ceil(($replies + 1) / $per_page);
		$pagination = '';

		$times = 1;
		for ($j = 0; $j < $replies + 1; $j += $per_page)
		{
			$pagination .= '<a href="' . $url . ($j == 0 ? '' : "{$url_delim}cpage=" . $times) . '">' . $times . '</a>';
			if ($times == 1 && $total_pages > 5)
			{
				$pagination .= ' ... ';

				// Display the last three pages
				$times = $total_pages - 3;
				$j += ($total_pages - 4) * $per_page;
			}
			else if ($times < $total_pages)
			{
				$pagination .= '<span class="page-sep">' . phpbb::$user->lang['COMMA_SEPARATOR'] . '</span>';
			}
			$times++;
		}
	}
	else
	{
		$pagination = '';
	}

	return $pagination;
}

/**
 * Capture the output of a function, which simply echo's a string. 
 * 	Capture the echo into a variable without actually echo'ing the string. 
 * 	You can do so by leveraging PHP's output buffering functions. Here's how you do it:
 *
 * @param string $tag The name of the action to be executed.
 * @param mixed $arg,... Optional additional arguments which are passed on to the functions hooked to the action.
 * @return null Will return null if $tag does not exist in $wp_filter array
 */
function wp_do_action($tag)
{
	// Retrieve arguments list
    $_args = func_get_args();

    // Delete the first argument which is the class name
    $_className = array_shift($_args);

	ob_start();

	call_user_func_array($tag, $_args);

	$echo = ob_get_contents();

	ob_end_clean();

	return $echo;
}

// Register sidebars by running wp_phpbb_widgets_init() on the widgets_init hook.
add_action('widgets_init', 'wp_phpbb_widgets_init');
/**
 * Register widgetized area, and available widdgets for the bridge.
 */
function wp_phpbb_widgets_init()
{
	// Register Single Sidebar
	register_sidebar(
		array(
			'id'			=> 'wp_phpbb-widget-area',
			'name'			=> __('Primary Widget Area', 'wp_phpbb3_bridge'),
			'description'	=> __('The primary widget area.', 'wp_phpbb3_bridge'),
			'before_widget'	=> "\n" . '<div class="panel bg3">' . "\r\t" . '<div class="inner"><span class="corners-top"><span></span></span>' . "\n\t\t",
			'after_widget'	=> "\n\t" . '<span class="corners-bottom"><span></span></span></div>' . "\r" . '</div>' . "\n",
			'before_title'	=> '<h3>',
			'after_title'	=> '</h3>' . "\n",
		)
	);

	unregister_widget('WP_Nav_Menu_Widget');

	register_widget('WP_Widget_phpbb_recet_topics');
}

// Add a filter for the widget title by running wp_phpbb_widget_title() on the widget_title hook
add_filter('widget_title', 'wp_phpbb_widget_title');
/**
 * If the widget have no title, just add a "nonbreacking space" instead
 *
 * @param (string) $title
 * @return (string) $title or space
 */
function wp_phpbb_widget_title($title)
{
	$title = (!empty($title)) ? $title : '&nbsp;';
	return $title;
}

/**
 * Enter description here...
 *
 * based off the WP add-on by Jason Sanborn <jsanborn@simplicitypoint.com> http://www.e-xtnd.it/wp-phpbb-bridge/
 */
class WP_Widget_phpbb_recet_topics extends WP_Widget
{
	// Defaults Settings
	var $defaults = array(
		'title'				=> 'Recent topics',
		'forums'			=> '0',
		'total'				=> 10,
		'showForum'			=> 0,
		'showUsername'		=> 0,
		'showTotalViews'	=> 0,
		'showTotalPosts'	=> 0,
	);

	function WP_Widget_phpbb_recet_topics()
	{
		// Widget settings.
		$widget_ops = array(
			'classname' => 'wp_phpbb_recet_topics',
			'description' => __('Allows you to display a list of recent topics within a specific forum id\'s.', 'wp_phpbb3_bridge'),
		);

		// Create the widget
		$this->WP_Widget('phpbb3-topics-widget', __('phpBB3 Topics Widget', 'wp_phpbb3_bridge'), $widget_ops);
	}

	function form($instance)
	{
		$instance = wp_parse_args($instance, $this->defaults);

		?>
		<div class="widget-content">
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php echo _e('Title:', 'wp_phpbb3_bridge'); ?></label>
				<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('forums'); ?>"><?php echo _e('Forums:', 'wp_phpbb3_bridge'); ?></label>
				<input name="<?php echo $this->get_field_name('forums'); ?>" type="text" id="<?php echo $this->get_field_id('forums'); ?>" value="<?php echo esc_attr($instance['forums']); ?>" />
				<small><?php _e('Enter the id of the forum you like to get topics from. You can get topics from more than one forums by seperating the forums id with commas. ex: 3,5,6,12', 'wp_phpbb3_bridge'); ?></small>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('total'); ?>"><?php echo _e('Total results:', 'wp_phpbb3_bridge'); ?></label>
				<input name="<?php echo $this->get_field_name('total'); ?>" type="text" id="<?php echo $this->get_field_id('total'); ?>" value="<?php echo $instance['total']; ?>" />
			</p>
			<p>
				<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('showForum'); ?>" name="<?php echo $this->get_field_name('showForum'); ?>" value="1" <?php if ($instance['showForum']) { echo ' checked="checked" '; } ?> />
				<label for="<?php echo $this->get_field_id('showForum'); ?>"><?php echo _e('Display forum name', 'wp_phpbb3_bridge'); ?></label>
			</p>
			<p>
				<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('showUsername'); ?>" name="<?php echo $this->get_field_name('showUsername'); ?>" value="1" <?php if ($instance['showUsername']) { echo ' checked="checked" '; } ?> />
				<label for="<?php echo $this->get_field_id('showUsername'); ?>"><?php echo _e('Display author name', 'wp_phpbb3_bridge'); ?></label>
			</p>
			<p>
				<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('showTotalViews'); ?>" name="<?php echo $this->get_field_name('showTotalViews'); ?>" value="1" <?php if ($instance['showTotalViews']) { echo ' checked="checked" '; } ?> />
				<label for="<?php echo $this->get_field_id('showTotalViews'); ?>"><?php echo _e('Display total views', 'wp_phpbb3_bridge'); ?></label>
			</p>
			<p>
				<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('showTotalPosts'); ?>" name="<?php echo $this->get_field_name('showTotalPosts'); ?>" value="1" <?php if ($instance['showTotalPosts']) { echo ' checked="checked" '; } ?> />
				<label for="<?php echo $this->get_field_id('showTotalPosts'); ?>"><?php echo _e('Display total replies', 'wp_phpbb3_bridge'); ?></label>
			</p>
		</div>
		<?php
	}

	function update($new_instance, $old_instance)
	{
		$instance = array(
			'title'				=> strip_tags($new_instance['title']),
			'forums'			=> (isset($new_instance['forums']) && $new_instance['forums']) ? strip_tags($new_instance['forums']) : '0',
			'total'				=> (isset($new_instance['total']) && $new_instance['total']) ? absint($new_instance['total']) : 5,
			'showForum'			=> (isset($new_instance['showForum']) && $new_instance['showForum']) ? 1 : 0,
			'showUsername'		=> (isset($new_instance['showUsername']) && $new_instance['showUsername']) ? 1 : 0,
			'showTotalViews'	=> (isset($new_instance['showTotalViews']) && $new_instance['showTotalViews']) ? 1 : 0,
			'showTotalPosts'	=> (isset($new_instance['showTotalPosts']) && $new_instance['showTotalPosts']) ? 1 : 0
		);

		return $instance;
	}

	function widget($args, $instance)
	{
		// Only run this widget on index page
		if (is_home() || is_front_page())
		{
		//	echo $before_widget . $before_title . $title . $after_title;
			phpbb::phpbb_recet_topics($instance, $this->defaults);
		//	echo $after_widget;
		}
	}
}

/**
 * Hooks a function on to a specific action.
 *
 * @param string $tag The name of the action to which the $function_to_add is hooked.
 * @param callback $function_to_add The name of the function you wish to be called.
 * @param int $priority optional. Used to specify the order in which the functions associated with a particular action are executed (default: 10). Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
 * @param int $accepted_args optional. The number of arguments the function accept (default 1).
 */
$wp_phpbb_posting = (int) get_option('wp_phpbb_bridge_post_forum_id');

if ($wp_phpbb_posting)
{
	add_action('publish_post', 'wp_phpbb_posting', 10, 2);
}

/**
 * After delete or trash an entry restur to the index page, instead the same page (that do not exist anymore)
 */
add_action('after_delete_post', 'wp_phpbb_trasheddelete_post_handler', 10, 1);
add_action('trashed_post', 'wp_phpbb_trasheddelete_post_handler', 10, 1);
function wp_phpbb_trasheddelete_post_handler($post_id)
{
	$wp_phpbb_posting = (int) get_option('wp_phpbb_bridge_post_forum_id');

	// Handle delete mode...
	if ($wp_phpbb_posting)
	{
		global $table_prefix, $wp_user;

		if (!defined('IN_WP_PHPBB_BRIDGE'))
		{
			global $wp_phpbb_bridge_config, $phpbb_root_path, $phpEx;
			global $auth, $config, $db, $template, $user, $cache;
			include(TEMPLATEPATH . '/includes/wp_phpbb_bridge.php');
		}

		$post_data = array();

		// We are ading a new entry or we are editting ?
		$phpbb_post_id = get_post_meta($post_id, 'phpbb_post_id', true );	//	$phpbb_post_id=array('forum_id' => 2, 'topic_id' => 47, 'post_id' => 74);
		if (!empty($phpbb_post_id))
		{
			$sql = 'SELECT f.*, t.*, p.*
				FROM ' . FORUMS_TABLE . ' f, ' . TOPICS_TABLE . ' t, ' . POSTS_TABLE . ' p
				WHERE p.post_id = ' . (int) $phpbb_post_id['post_id'] . '
					AND t.topic_id = p.topic_id
					AND f.forum_id = t.forum_id';
			$result = phpbb::$db->sql_query($sql);
			$post_data = phpbb::$db->sql_fetchrow($result);
			phpbb::$db->sql_freeresult($result);
		}
		
		if ($post_data)
		{
			if (!function_exists('delete_post'))
			{
				include(PHPBB_ROOT_PATH . 'includes/functions_posting.' . PHP_EXT);
			}
			delete_post($post_data['forum_id'], $post_data['topic_id'], $post_data['post_id'], $post_data);
		}
	}

	if (defined('WP_ADMIN') && WP_ADMIN == true)
	{
	}
	else
	{
    	wp_redirect(get_option('siteurl'));
	    exit;
	}    	
}

/**
 * Called whenever a new entry is published in the Wordpress.
 *
 * @param integer $post_ID
 * @param object $post
 */
function wp_phpbb_posting($post_ID, $post)
{
	if ($post->post_status != 'publish')
	{
		return false;
	}

	global $table_prefix, $wp_user;

	if (!defined('IN_WP_PHPBB_BRIDGE'))
	{
		global $wp_phpbb_bridge_config, $phpbb_root_path, $phpEx;
		global $auth, $config, $db, $template, $user, $cache;
		include(TEMPLATEPATH . '/includes/wp_phpbb_bridge.php');
	}

	if (!phpbb::$config['wp_phpbb_bridge_post_forum_id'])
	{
		return false;
	}

	// Define some initial variables
	$mode = 'post';
	$forum_id = $topic_id = $post_id = 0;
	$post_data = $poll = array();
	$message_prefix = '';
	$message_tail = '';
	$subject_prefix = '';

	// We need to know some basic information in all cases before we do anything.

	// We are ading a new entry or we are editting ?
	$phpbb_post_id = get_post_meta($post_ID, 'phpbb_post_id', true );	//	$phpbb_post_id=array('forum_id' => 2, 'topic_id' => 47, 'post_id' => 74);
	if (!empty($phpbb_post_id))
	{
		$mode = 'edit';
		$forum_id = $phpbb_post_id['forum_id'];
		$topic_id = $phpbb_post_id['topic_id'];
		$post_id = $phpbb_post_id['post_id'];	

		$sql = 'SELECT f.*, t.*, p.*
			FROM ' . FORUMS_TABLE . ' f, ' . TOPICS_TABLE . ' t, ' . POSTS_TABLE . ' p
			WHERE p.post_id = ' . (int) $post_id . '
				AND t.topic_id = p.topic_id
				AND f.forum_id = t.forum_id';
		$result = phpbb::$db->sql_query($sql);
		$post_data = phpbb::$db->sql_fetchrow($result);
		phpbb::$db->sql_freeresult($result);
	}
	else
	{
		$sql = 'SELECT *
			FROM ' . FORUMS_TABLE . '
			WHERE forum_id = ' . (int) phpbb::$config['wp_phpbb_bridge_post_forum_id'];
		$result = phpbb::$db->sql_query($sql);
		$post_data = phpbb::$db->sql_fetchrow($result);
		phpbb::$db->sql_freeresult($result);
	}

	if (!$post_data)
	{
		return false;
	}

	if (!function_exists('submit_post'))
	{
		include(PHPBB_ROOT_PATH . 'includes/functions_posting.' . PHP_EXT);
	}
	if (!class_exists('bitfield'))
	{
		include(PHPBB_ROOT_PATH . 'includes/functions_content.' . PHP_EXT);
	}
	if (!class_exists('parse_message'))
	{
		include(PHPBB_ROOT_PATH . 'includes/message_parser.' . PHP_EXT);
	}
	$message_parser = new parse_message();

	// Get the post link
	$entry_link = get_permalink($post_ID);

	// Get the post text
	$message = $post->post_content;

	// if have "read more", cut it!
	if (preg_match('/<!--more(.*?)?-->/', $message, $matches))
	{
		list($main, $extended) = explode($matches[0], $message, 2);
		// Strip leading and trailing whitespace
		$main = preg_replace('/^[\s]*(.*)[\s]*$/', '\\1', $main);
		$message = $main . "\n\n" . '[url=' . $entry_link . ']' . phpbb::$user->lang['WP_READ_MORE'] . '[/url]';
	}

	// Get the post subject
	$subject = $post->post_title;

	// Add a Post prefix for the blog (if we have a language string filled)
	if (phpbb::$user->lang['WP_POST_BLOG_PREFIX'] != '')
	{
		$message_prefix .= sprintf(phpbb::$user->lang['WP_POST_BLOG_PREFIX'], '[url=' . $entry_link . ']', '[/url]');
	}

	// Add a Post tail for the blog (if we have a language string filled)
	if (phpbb::$user->lang['WP_POST_BLOG_TAIL'] != '')
	{
		$entry_tags = get_the_tag_list(phpbb::$user->lang['WP_TITLE_TAGS'] . ': ', ', ', "\n\n");
		$entry_cats = sprintf(phpbb::$user->lang['WP_POSTED_IN'] , get_the_category_list(', '));

		if ($entry_tags || $entry_cats)
		{
			$message_tail .= phpbb::$user->lang['WP_POST_BLOG_TAIL'] . (($entry_tags) ? $entry_tags : '') . (($entry_tags && $entry_cats) ? " | " : '') . (($entry_cats) ? $entry_cats : '') . "\n";
		}
	}

	$message = (($message_prefix) ? $message_prefix . "\n\n" : '') . $message . (($message_tail) ? "\n\n" . $message_tail : '');

	// Sanitize the post text
	$message = utf8_normalize_nfc(request_var('message', $message, true));
	// Sanitize the post subject
	$subject = utf8_normalize_nfc(request_var('subject', $subject, true));

	// Add a subject prefix for the blog (if we have a language string filled)
	if (phpbb::$user->lang['WP_SUBJECT_BLOG_PREFIX'] != '')
	{
		$subject_prefix = phpbb::$user->lang['WP_SUBJECT_BLOG_PREFIX'];
	}

	$subject = $subject_prefix . $subject;

	// Setup the settings we need to send to submit_post
	global $data;
	$data = wp_phpbb_post_data($message, $subject, $topic_id, $post_id, phpbb::$user->data, $post_data, $message_parser);

	submit_post($mode, $subject, phpbb::$user->data['username'], POST_NORMAL, $poll, $data, true);

	// Update post meta data and add the phpbb post ID
	if ($mode == 'post')
	{
		$phpbb_forum_id = (isset($data['forum_id']) && $data['forum_id']) ? $data['forum_id'] : 0;
		$phpbb_topic_id = (isset($data['topic_id']) && $data['topic_id']) ? $data['topic_id'] : 0;
		$phpbb_post_id = (isset($data['post_id']) && $data['post_id']) ? $data['post_id'] : 0;
		if ($phpbb_forum_id != 0 && $phpbb_topic_id != 0 && $phpbb_post_id != 0)
		{
			add_post_meta($post_ID, 'phpbb_post_id', array('forum_id' => $phpbb_forum_id, 'topic_id' => $phpbb_topic_id, 'post_id' => $phpbb_post_id), true);
		}
	}
}

// Setup the settings we need to send to submit_post
function wp_phpbb_post_data($message, $subject, $topic_id, $post_id, $user_row, $post_data, $message_parser)
{
	$message = wp_phpbb_html_to_bbcode($message);

	$message_parser->message = $message;
	$message_parser->parse(true, true, true);

	$data = array(
		'post_id'				=> $post_id,
		'topic_id'				=> $topic_id,
		'forum_id'				=> (int) $post_data['forum_id'],
		'icon_id'				=> (isset($post_data['enable_sig'])) ? (bool) $post_data['enable_sig'] : true,
		'topic_status'			=> 1,
		'topic_title'			=> $subject,

		'topic_type'			=> POST_NORMAL,
		'enable_sig'			=> (isset($post_data['enable_sig'])) ? $post_data['enable_sig'] : true,
		'enable_bbcode'			=> (isset($post_data['enable_bbcode'])) ? $post_data['enable_bbcode'] : true,
		'enable_smilies'		=> (isset($post_data['enable_smilies'])) ? $post_data['enable_smilies'] : true,
		'enable_urls'			=> (isset($post_data['enable_urls'])) ? $post_data['enable_urls'] : true,
		'post_time'				=> time(),

		'notify'				=> (isset($post_data['notify'])) ? $post_data['notify'] : false,
		'notify_set'			=> (isset($post_data['notify_set'])) ? $post_data['notify_set'] : false,
		'poster_id'				=> $user_row['user_id'],
		'bbcode_bitfield'		=> $message_parser->bbcode_bitfield,
		'bbcode_uid'			=> $message_parser->bbcode_uid,
		'message'				=> $message_parser->message,
		'message_md5'			=> (string) md5($message_parser->message),

		'post_edit_locked'		=> (isset($post_data['post_edit_locked'])) ? $post_data['post_edit_locked'] : false,
		'force_approved_state'	=> (isset($post_data['force_approved_state'])) ? $post_data['force_approved_state'] : true,

		// Just in case 
		'seo_desc'				=> (isset($post_data['seo_desc'])) ? $post_data['seo_desc'] : '',
		'seo_key'				=> (isset($post_data['seo_key'])) ? $post_data['seo_key'] : '',
		'seo_post_key'			=> (isset($post_data['seo_post_key'])) ? $post_data['seo_post_key'] : '',
		'topic_seo_title'		=> (isset($post_data['topic_seo_title'])) ? $post_data['topic_seo_title'] : '',
	);

	// Merge the data we grabbed from the forums/topics/posts tables
	$data = array_merge($post_data, $data);
	return $data;
}

/**
 * Function convert HTML to BBCode 
 * 	Cut down from DeViAnThans3's version Originally (C) DeViAnThans3 - 2005 (GPL v2)
 * 	and from rss.php & feed.php
 * 	We have made several changes and fixes. 
 */
function wp_phpbb_html_to_bbcode(&$string)
{
	// Strip slashes !
//	$string = stripslashes($string);

//	$string = strip_tags($string, '<p><a><img><br><strong><em><blockquote><b><u><i><ul><ol><li><code>');

	$from = array(
		"#<a.*?href=\'(.*?)\'.*?>(.*?)<\/a>#is",
		'#<a.*?href=\"(.*?)\".*?>(.*?)<\/a>#is',

		'#<img.*?src="(.*?)".*?\/>#is',
		'#<img.*?src="(.*?)".*?>#is',

		'#<code.*?>#is',
		'#<\/code>#is',

		'#<blockquote.*?>#is',
		'#<\/blockquote>#is',

		'#<(span|div) style=\"font-size: ([\-\+]?\d+)(px|em|\%);\">(.*?)<\/(span|div)>#is',

		'#<li.*?>#is',
		'#<\/li>#is',
		'#<ul.*?>#is',
		'#<\/ul>#is',
		'#<ol.*?>#is',
		'#<\/ol>#is',

		'#<(i|em).*?>#is',
		'#<\/(i|em)>#is',
		'#<(span|div) style=\"font-style: italic;.*?\">(.*?)<\/(span|div)>#is',

		'#<(b|strong).*?>#is',
		'#<\/(b|strong)>#is',

		'#<(u|ins).*?>#is',
		'#<\/(u|ins)>#is',
		'#<(span|div) style=\"text-decoration: underline;.*?\">(.*?)<\/(span|div)>#is',

		'#<(span|div) style=\"color: \#(.*?);\">(.*?)<\/(span|div)>#is',
		'#<font.*?color=\"([a-z\-]+)\".*?>(.*?)<\/font>#is',
		'#<font.*?color=\"\#(.*?)\".*?>(.*?)<\/font>#is',

		'#<p.*?>#is',
		'#<\/p>#is',
		'#<br.*?>#is',

		// treat "del" and "strike" as undeline
		'#<(del|strike).*?>#is',
		'#<\/(del|strike)>#is',
		
		'#<dt><\/dt>#is',
	);

	$to = array(
		'[url=\\1]\\2[/url]',
		'[url=\\1]\\2[/url]',

		'[img]\\1[/img]',
		'[img]\\1[/img]',

		'[code]',
		'[/code]',

		'[quote]',
		'[/quote]',

		"[size=\\2]\\4[/size]",

		'[*]',
		'',
		'[list]',
		'[/list]',
		'[list=1]',
		'[/list]',

		'[i]',
		'[/i]',
		'[i]\\2[/i]',

		'[b]',
		'[/b]',

		'[u]',
		'[/u]',
		'[u]\\2[/u]',

		'[color=#\\2]\\3[/color]',
		'[color=\\1]\\2[/color]',
		'[color=#\\1]\\2[/color]',

		'',
		"\n",
		"\n",

		'[u]',
		'[/u]',
		
		"\n",
	);

	$string = preg_replace($from, $to, $string);

	// Remove all JavaScript Event Handlers
	$string = preg_replace('#(onabort|onblur|onchange|onclick|ondblclick|onerror|onfocus|onkeydown|onkeypress|onkeyup|onload|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onreset|onresize|onselect|onsubmit|onunload)="(.*?)"#si', '', $string);

	// Remove embed and objects, but leaving a link to the video
	// Use (<|&lt;) and (>|&gt;) because can be contained into [code][/code]
	$string = preg_replace('/(<|&lt;)object[^>]*?>.*?(value|src)=(.*?)(^|[\n\t (>]).*?object(>|&gt;)/', ' <a href=$3 target="_blank"><strong>object</strong></a>',$string);
	$string = preg_replace('/(<|&lt;)embed[^>]*?>.*?(value|src)=(.*?)(^|[\n\t (>]).*?embed(>|&gt;)/', ' <a href=$3 target="_blank"><strong>embed</strong></a>',$string);

	// Potentially Malicious HTML Tags ?
	// Remove some specials html tag, because somewhere there are a mod to allow html tags ;)
	// Use (<|&lt;) and (>|&gt;) because can be contained into [code][/code]
	$string = preg_replace(
		array (
			'@(<|&lt;)head[^>]*?(>|&gt;).*?(<|&lt;)/head(>|&gt;)@siu',
			'@(<|&lt;)style[^>]*?(>|&gt;).*?(<|&lt;)/style(>|&gt;)@siu',
			'@(<|&lt;)script[^>]*?.*?(<|&lt;)/script(>|&gt;)@siu',
			'@(<|&lt;)applet[^>]*?.*?(<|&lt;)/applet(>|&gt;)@siu',
			'@(<|&lt;)noframes[^>]*?.*?(<|&lt;)/noframes(>|&gt;)@siu',
			'@(<|&lt;)noscript[^>]*?.*?(<|&lt;)/noscript(>|&gt;)@siu',
			'@(<|&lt;)noembed[^>]*?.*?(<|&lt;)/noembed(>|&gt;)@siu',
			'@(<|&lt;)iframe([^[]+)iframe(>|&gt;)@iu',
			'@(<|&lt;)/?((frameset)|(frame)|(iframe))@iu',
		),
		array (
			'[code]head[/code]',
			'[code]style[/code]',
			'[code]script[/code]',
			'[code]applet[/code]',
			'[code]noframes[/code]',
			'[code]noscript[/code]',
			'[code]noembed[/code]',
			'[code]iframe[/code]',
			'[code]frame[/code]',
		),
	$string);

	// prettify estranged tags
	$string = str_replace("&nbsp;", " ", $string); 
	$string = str_replace('&amp;lt;', '<', $string);
	$string = str_replace('&amp;gt;', '>', $string);
	$string = str_replace('&lt;', '<', $string);
	$string = str_replace('&gt;', '>', $string);
	$string = str_replace('&quot;', '"', $string);
	$string = str_replace('&amp;', '&', $string);

//	$string = htmlspecialchars($string); 
	// kill any remaining
	$string = strip_tags($string);

	// Other control characters
//	$string = preg_replace('#(?:[\x00-\x1F\x7F]+|(?:\xC2[\x80-\x9F])+)#', '', $string);

	return $string;
} 

?>