<?php
/**
 * 
 * @package: phpBB 3.0.8 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/theme/prosilver
 * @version: $Id: functions.php, v0.0.3-pl1 2011/07/02 11:07:02 leviatan21 Exp $
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

/**
 * Extra layout 2 columns
 */
function wp_phpbb_stylesheet()
{
//	$blog_stylesheet = '<link rel="stylesheet" type="text/css" media="all" href="' . wp_do_action('bloginfo', 'stylesheet_url') . '" />' . "\n";

	$blog_stylesheet = '<style type="text/css">
/** Style on-the-fly **/
.section-blog #container {
	margin-right: -' . ((int) phpbb::$config['wp_phpbb_bridge_left_column_width'] + 10) . 'px;
}
.section-blog #content {
	margin-right: ' . ((int) phpbb::$config['wp_phpbb_bridge_left_column_width'] + 10) . 'px;
}
.section-blog #primary {
	width: ' . (int) phpbb::$config['wp_phpbb_bridge_left_column_width'] . 'px;
}
</style>' . "\n";

	echo $blog_stylesheet;
}

/**
 * Insert some js files
 */
function wp_phpbb_javascript()
{
	$blog_javascript = '<script type="text/javascript" src="' . get_bloginfo('stylesheet_directory') . '/js/javascript.js"></script>' . "\n";
	// jQuery for resply to comments
	if (is_single())
	{
	//	$blog_javascript .= '<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>' . "\n";
	//	$blog_javascript .= '<script type="text/javascript" src="http://dev.jquery.com/view/trunk/plugins/validate/jquery.validate.js"></script>' . "\n";
	//	$blog_javascript .= '<script type="text/javascript" src="' . get_bloginfo('stylesheet_directory') . '/js/jquery-1.5.0.min.js"></script>' . "\n";
	//	$blog_javascript .= '<script type="text/javascript" src="' . get_bloginfo('stylesheet_directory') . '/js/jquery.validate.js"></script>' . "\n";
		wp_register_script('jquery', get_bloginfo('stylesheet_directory') . '/js/jquery-1.5.0.min.js', false, '1.5.0');
		wp_register_script('jquery-validate', get_bloginfo('stylesheet_directory') . '/js/jquery.validate.js', array('jquery'), '1.5.2', true);
		wp_enqueue_script('jquery-validate');
		wp_print_scripts('jquery-validate');
	}
	echo $blog_javascript;
}

/**
 * Register widgetized area, and available widdgets for the bridge.
 */
function wp_phpbb_widgets_init()
{
	// Register Single Sidebar
	register_sidebar(
		array(
			'id'			=> 'wp_phpbb-widget-area',
			'name'			=> __('Primary Widget Area', 'wp_phpbb_bridge'),
			'description'	=> __('The primary widget area.', 'wp_phpbb_bridge'),
			'before_widget'	=> "\n" . '<div class="panel bg3">' . "\r\t" . '<div class="inner"><span class="corners-top"><span></span></span>' . "\n\t\t",
			'after_widget'	=> "\n\t" . '<span class="corners-bottom"><span></span></span></div>' . "\r" . '</div>' . "\n",
			'before_title'	=> '<h3>',
			'after_title'	=> '</h3>' . "\n",
		)
	);
	
	unregister_widget('WP_Nav_Menu_Widget');

	register_widget('WP_Widget_phpbb_recet_topics');

/**
//	register_widget('WP_Widget_Pages');
//	unregister_widget('WP_Widget_Pages');

//	register_widget('WP_Widget_Calendar');
	unregister_widget('WP_Widget_Calendar');

//	register_widget('WP_Widget_Archives');
//	unregister_widget('WP_Widget_Archives');

//	register_widget('WP_Widget_Links');
	unregister_widget('WP_Widget_Links');

//	register_widget('WP_Widget_Meta');
//	unregister_widget('WP_Widget_Meta');

//	register_widget('WP_Widget_Search');
//	unregister_widget('WP_Widget_Search');

//	register_widget('WP_Widget_Text');
	unregister_widget('WP_Widget_Text');

//	register_widget('WP_Widget_Categories');
//	unregister_widget('WP_Widget_Categories');

//	register_widget('WP_Widget_Recent_Posts');
	unregister_widget('WP_Widget_Recent_Posts');

//	register_widget('WP_Widget_Recent_Comments');
	unregister_widget('WP_Widget_Recent_Comments');

//	register_widget('WP_Widget_RSS');
	unregister_widget('WP_Widget_RSS');

//	register_widget('WP_Widget_Tag_Cloud');
//	unregister_widget('WP_Widget_Tag_Cloud');

//	register_widget('WP_Nav_Menu_Widget');
	unregister_widget('WP_Nav_Menu_Widget');

	unregister_widget('WP_Widget_phpbb_recet_topics');
	register_widget('WP_Widget_phpbb_recet_topics');
**/
}

// Register sidebars by running wp_phpbb_widgets_init() on the widgets_init hook.
add_action('widgets_init', 'wp_phpbb_widgets_init');

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
			'description' => __('Allows you to display a list of recent topics within a specific forum id\'s.', 'wp_phpbb_bridge'),
		);

		// Create the widget
		$this->WP_Widget('phpbb3-topics-widget', __('phpBB3 Topics Widget', 'wp_phpbb_bridge'), $widget_ops);
	}

	function form($instance)
	{
		$instance = wp_parse_args($instance, $this->defaults);

		?>
		<div class="widget-content">
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php echo _e('Title:', 'wp_phpbb_bridge'); ?></label>
				<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('forums'); ?>"><?php echo _e('Forums:', 'wp_phpbb_bridge'); ?></label>
				<input name="<?php echo $this->get_field_name('forums'); ?>" type="text" id="<?php echo $this->get_field_id('forums'); ?>" value="<?php echo esc_attr($instance['forums']); ?>" />
				<small><?php _e('Enter the id of the forum you like to get topics from. You can get topics from more than one forums by seperating the forums id with commas. ex: 3,5,6,12', 'wp_phpbb_bridge'); ?></small>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('total'); ?>"><?php echo _e('Total results:', 'wp_phpbb_bridge'); ?></label>
				<input name="<?php echo $this->get_field_name('total'); ?>" type="text" id="<?php echo $this->get_field_id('total'); ?>" value="<?php echo $instance['total']; ?>" />
			</p>
			<p>
				<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('showForum'); ?>" name="<?php echo $this->get_field_name('showForum'); ?>" value="1" <?php if ($instance['showForum']) { echo ' checked="checked" '; } ?> />
				<label for="<?php echo $this->get_field_id('showForum'); ?>"><?php echo _e('Display forum name', 'wp_phpbb_bridge'); ?></label>
			</p>
			<p>
				<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('showUsername'); ?>" name="<?php echo $this->get_field_name('showUsername'); ?>" value="1" <?php if ($instance['showUsername']) { echo ' checked="checked" '; } ?> />
				<label for="<?php echo $this->get_field_id('showUsername'); ?>"><?php echo _e('Display author name', 'wp_phpbb_bridge'); ?></label>
			</p>
			<p>
				<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('showTotalViews'); ?>" name="<?php echo $this->get_field_name('showTotalViews'); ?>" value="1" <?php if ($instance['showTotalViews']) { echo ' checked="checked" '; } ?> />
				<label for="<?php echo $this->get_field_id('showTotalViews'); ?>"><?php echo _e('Display total views', 'wp_phpbb_bridge'); ?></label>
			</p>
			<p>
				<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('showTotalPosts'); ?>" name="<?php echo $this->get_field_name('showTotalPosts'); ?>" value="1" <?php if ($instance['showTotalPosts']) { echo ' checked="checked" '; } ?> />
				<label for="<?php echo $this->get_field_id('showTotalPosts'); ?>"><?php echo _e('Display total replies', 'wp_phpbb_bridge'); ?></label>
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
add_action('publish_post', 'wp_phpbb_phpbb_posting', 10, 2);

/**
 * Called whenever a new entry is published in the Wordpress.
 *
 * @param unknown_type $post_ID
 * @param unknown_type $post
 */
function wp_phpbb_phpbb_posting($post_ID, $post)
{
	if ($post->post_status != 'publish')
	{
		return false;
	}
	global $table_prefix, $wp_user;

	if (!defined('IN_WP_PHPBB_BRIDGE'))
	{
		global $phpbb_root_path, $phpEx;
		global $auth, $config, $db, $template, $user, $cache;
		include(TEMPLATEPATH . '/includes/wp_phpbb_bridge.php');
	}

	if (!phpbb::$config['wp_phpbb_bridge_post_forum_id'])
	{
		return false;
	}

	// To be sure, get some forum data from the first forum (should be forum id 2).
	$sql = 'SELECT forum_id, forum_parents, forum_name
			FROM ' . FORUMS_TABLE . '
			WHERE forum_id = ' . phpbb::$config['wp_phpbb_bridge_post_forum_id'];
	$result = phpbb::$db->sql_query($sql);
	$forum_row = phpbb::$db->sql_fetchrow($result);
	phpbb::$db->sql_freeresult($result);

	if (!$forum_row)
	{
		return false;
	}

	include(PHPBB_ROOT_PATH . 'includes/functions_posting.' . PHP_EXT);
	if (!class_exists('bitfield'))
	{
	//	include(PHPBB_ROOT_PATH . 'includes/bbcode.' . PHP_EXT);
		include(PHPBB_ROOT_PATH . 'includes/functions_content.' . PHP_EXT);
	}
	include(PHPBB_ROOT_PATH . 'includes/message_parser.' . PHP_EXT);
	$message_parser = new parse_message();

	// Define some initial variables
	$topic_id = $post_id = 0;
	$poll = array();
	$message_prefix = '';
	$message_tail = '';
	$subject_prefix = '';

	// Get the post link
	$entry_link = get_permalink($post_ID);

	// Get the post text
	$message = $post->post_content;

	// Get the post subject
	$subject = $post->post_title;

	// if have "read more", cut it!
	if ($post->post_excerpt)
	{
		$message = $post->post_excerpt;

		if (preg_match('/<!--more(.*?)?-->/', $message, $matches))
		{
			$message = explode($matches[0], $message, 2);
			$message = $message[0];
		}
	}

	// Sanitize the post text
	$message = utf8_normalize_nfc($message, '', true);

	// Add a Post prefix for the blog (if we have a language string filled)
	if (phpbb::$user->lang['WP_POST_BLOG_PREFIX'] != '')
	{
		$message_prefix .= sprintf(phpbb::$user->lang['WP_POST_BLOG_PREFIX'], '[url=' . $entry_link . ']', '[/url]');
	}

	// Add a Post tail for the blog (if we have a language string filled)
	if (phpbb::$user->lang['WP_POST_BLOG_TAIL'] != '' && ($entry_tags || $entry_cats))
	{
		$entry_tags = get_the_tag_list(phpbb::$user->lang['WP_TITLE_TAGS'] . ': ', ', ', '<br />');
		$entry_cats = sprintf(phpbb::$user->lang['WP_POSTED_IN'] , get_the_category_list(', '));

		$message_tail .= phpbb::$user->lang['WP_POST_BLOG_TAIL'] . (($entry_tags) ? $entry_tags : '') . (($entry_tags && $entry_cats) ? " | " : '') . (($entry_cats) ? $entry_cats : '') . "\n";
	}

	// if have "read more", again add the link to the entry
	if ($post->post_excerpt)
	{
		$message_tail .= '[url=' . $entry_link . ']' . phpbb::$user->lang['WP_READ_MORE'] . '[/url]';
	}

	$message = (($message_prefix) ? $message_prefix . "\n\n" : '') . $message . (($message_tail) ? "\n\n" . $message_tail : '');

	// Sanitize the post subject
	$subject = utf8_normalize_nfc($subject, '', true);

	// Add a subject prefix for the blog (if we have a language string filled)
	if (phpbb::$user->lang['WP_SUBJECT_BLOG_PREFIX'] != '')
	{
		$subject_prefix = phpbb::$user->lang['WP_SUBJECT_BLOG_PREFIX'];
	}

	$subject = $subject_prefix . $subject;

	// Setup the settings we need to send to submit_post
	$data = wp_phpbb_post_data($message, $subject, $topic_id, $post_id, phpbb::$user->data, $forum_row, $message_parser);

	submit_post('post', $subject, phpbb::$user->data['username'], POST_NORMAL, $poll, $data, true);
}

// Setup the settings we need to send to submit_post
function wp_phpbb_post_data($message, $subject, $topic_id, $post_id, $user_row, $forum_row, $message_parser)
{
	$message = wp_phpbb_html_to_bbcode($message);
	$message_parser->message = $message;
	$message_parser->parse(true, true, true);

	return array(
		'topic_title'			=> $subject,
		'topic_first_post_id'	=> 0,
		'topic_last_post_id'	=> 0,
		'topic_time_limit'		=> 0,
		'topic_attachment'		=> 0,
		'post_id'				=> $post_id,
		'topic_id'				=> $topic_id,
		'forum_id'				=> (int) $forum_row['forum_id'],
		'icon_id'				=> 0,

		'enable_sig'			=> true,
		'enable_bbcode'			=> true,
		'enable_smilies'		=> true,
		'enable_urls'			=> true,
		'enable_indexing'		=> true,
		'post_time'				=> time(),
		'post_checksum'			=> '',
		'post_edit_reason'		=> '',
		'post_edit_user'		=> 0,
		'forum_parents'			=> $forum_row['forum_parents'],
		'forum_name'			=> $forum_row['forum_name'],

		'notify'				=> false,
		'notify_set'			=> false,
		'poster_id'				=> $user_row['user_id'],
		'poster_ip'				=> '0.0.0.0',
		'bbcode_bitfield'		=> $message_parser->bbcode_bitfield,
		'bbcode_uid'			=> $message_parser->bbcode_uid,
		'message'				=> $message_parser->message,
		'message_md5'			=> (string) md5($message),
		'attachment_data'		=> $message_parser->attachment_data,
		'filename_data'			=> $message_parser->filename_data,

		'post_edit_locked'		=> false,
		'topic_approved'		=> true,
		'post_approved'			=> true,
		'force_approved_state'	=> true,

		// Just in case 
		'seo_desc'				=> '',
		'seo_key'				=> '',
		'seo_post_key'			=> '',
		'topic_seo_title'		=> '',
	);
}

/**
 * Function convert HTML to BBCode 
 * 	Cut down from DeViAnThans3's version Originally (C) DeViAnThans3 - 2005 (GPL v2)
 * 	We have made several changes and fixes. 
 */
function wp_phpbb_html_to_bbcode(&$string)
{
	// Strip slashes !
	$string = stripslashes($string);
	$string = strip_tags($string, '<p><a><img><br><strong><em><blockquote><b><u><i><ul><ol><li><code>');

	$from = array(
		'~<i>(.*?)</i>~is',
		'~<span.*?font-style: italic.*?' . '>(.*?)</span>~is',
		'~<span.*?text-decoration: underline.*?' . '>(.*?)</span>~is',
		'~<em(.*?)>(.*?)</em>~is',
		'~<b(.*?)>(.*?)</b>~is',
		'~<strong(.*?)>(.*?)</strong>~is',
		'~<u(.*?)>(.*?)</u>~is',
		'~<code(.*?)>(.*?)</code>~is',
		'~<blockquote(.*?)>(.*?)</blockquote>~is',
		'~<img.*?src="(.*?)".*?' . '>~is',
		'~<a.*?href="(.*?)".*?' . '>(.*?)</a>~is',
		'~<p(.*?)>(.*?)</p>~is',
		'~<br(.*?)>~is',
		'~<li(.*?)>(.*?)</li>~is',
		'~<ul(.*?)>(.*?)</ul>~is',
		'~<ol(.*?)>(.*?)</ol>~is',
	);

	$to = array(
		'[i]\\1[/i]',
		'[i]\\1[/i]',
		'[u]\\1[/u]',
		'[i]\\2[/i]',
		'[b]\\2[/b]',
		'[b]\\2[/b]',
		'[u]\\2[/u]',
		'[code]\\2[/code]',
		'[quote]\\2[/quote]',
		'[img]\\1[/img]',
		'[url=\\1]\\2[/url]',
		'\\2', 		//	'\\2[br][br]',
		'',			//	'[br]',
		"\n" . '[*]\\2',
		'[list]\\2[/list]',
		'[list=1]\\2[/list]',
	);

	$string = preg_replace($from, $to, $string); 
//	$string = str_replace("<br />", "[br]", $string); 
	$string = str_replace("&nbsp;", " ", $string); 

	// kill any remaining
	$string = htmlspecialchars(strip_tags($string)); 

	// prettify estranged tags
	$string = str_replace('&amp;lt;', '<', $string);
	$string = str_replace('&amp;gt;', '>', $string);
	$string = str_replace('&lt;', '<', $string);
	$string = str_replace('&gt;', '>', $string);
	$string = str_replace('&quot;', '"', $string);
	$string = str_replace('&amp;', '&', $string);

	return $string;
} 

?>