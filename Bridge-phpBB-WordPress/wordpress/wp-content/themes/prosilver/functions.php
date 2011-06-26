<?php
/**
 * 
 * @package: phpBB 3.0.8 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/theme/prosilver
 * @version: $Id: functions.php, v0.0.2 2011/06/26 11:06:26 leviatan21 Exp $
 * @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
 * @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
 * 
 */

/**
* @ignore
**/

// Hide WordPress Admin Bar
add_filter( 'show_admin_bar', '__return_false' );

function wp_prosilver_stylesheet()
{
	$blog_stylesheet = '<link rel="stylesheet" type="text/css" media="all" href="' . wp_do_action('bloginfo', 'stylesheet_url') . '" />' . "\n";

	$blog_stylesheet .= '<style type="text/css">
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

?>