<?php
/**
 * 
 * @package: phpBB 3.0.8 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/theme/prosilver
 * @version: $Id: functions.php, v0.0.3 2011/06/28 11:06:28 leviatan21 Exp $
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

function wp_prosilver_stylesheet()
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
</style>';

	echo $blog_stylesheet;
}

function wp_prosilver_javascript()
{
	$blog_javascript = '<script type="text/javascript" src="' . get_bloginfo('stylesheet_directory') . '/js/javascript.js"></script>' . "\n";

	// jQuery for resply to comments
	if (is_single())
	{
	//	wp_deregister_script( 'jquery' );
	//	wp_register_script( 'jquery', 'http://code.jquery.com/jquery-latest.js');
	//	wp_register_script( 'jquery', get_bloginfo('stylesheet_directory') .'/js/jquery.validate.js');
	//	wp_enqueue_script( 'jquery' );

	//	$blog_javascript .= '<script type="text/javascript" src="'. $blog_path .'/wp-includes/js/jquery/jquery.js"></script>' . "\n";
		$blog_javascript .= '<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>' . "\n";
	//	$blog_javascript .= '<script type="text/javascript" src="http://dev.jquery.com/view/trunk/plugins/validate/jquery.validate.js"></script>' . "\n";
		$blog_javascript .= '<script type="text/javascript" src=" '. get_bloginfo('stylesheet_directory') .'/js/jquery.validate.js"></script>' . "\n";
	}

	echo $blog_javascript;
}

/**
 * Register widgetized areas, and available widdgets for the bridge.
 */
function wp_prosilver_widgets_init()
{
	// Register Single Sidebar
	register_sidebar(
		array(
			'id'			=> 'prosilver-widget-area',
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

/** Register sidebars by running wp_prosilver_widgets_init() on the widgets_init hook. */
add_action('widgets_init', 'wp_prosilver_widgets_init');

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
			'classname' => 'phpbb_recet_topics',
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

?>