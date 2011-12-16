<?php
/**
 * 
 * @package: phpBB 3.0.9 :: BRIDGE phpBB & WordPress -> WordPress root/
 * @version: $Id: wp-phpbb-bridge-portal.php, v0.0.9 2011/10/20 11:10:20 leviatan21 Exp $
 * @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
 * @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
 * 
 */

/**
* @ignore
**/
if (!defined('IN_PHPBB'))
{
	exit;
}

$config['wp_phpbb_bridge_portal_title']	= (isset($config['wp_phpbb_bridge_portal_title'])) ? $config['wp_phpbb_bridge_portal_title'] : (isset($user->lang['ACP_WP_PHPBB_BRIDGE_PORTAL_TITLE']) ? $user->lang['ACP_WP_PHPBB_BRIDGE_PORTAL_TITLE'] : '');
$config['wp_phpbb_bridge_portal_style']	= (isset($config['wp_phpbb_bridge_portal_style'])) ? $config['wp_phpbb_bridge_portal_style'] : 0;	// 0 => 'style', 1 => 'compact', 2 => 'post'
$config['wp_phpbb_bridge_portal_limit']	= (isset($config['wp_phpbb_bridge_portal_limit'])) ? $config['wp_phpbb_bridge_portal_limit'] : 10;

if (empty($wp))
{
	include($wordpress_root_path . 'wp-config.php');
}
wp();

$wp_recent_posts = wp_get_recent_posts( array(
	'numberposts' => $config['wp_phpbb_bridge_portal_limit'],
	'offset' => 0,
	'category' => 0,		// Category ID's for search in
	'orderby' => 'post_date',
	'order' => 'DESC',
	'include' => array(),	// Post ID's to include
	'exclude' => array(),	// Post ID's to skips
	'meta_key' => '',
	'meta_value' =>'',
	'post_type' => 'post',
	'post_status' => 'publish', //	'post_status' => 'draft, publish, future, pending, private',
	'suppress_filters' => true,
));

$wp_posts_list = 0;
if ($wp_recent_posts)
{
	$user->add_lang('mods/wp_phpbb_bridge');

	/* Start the Loop */
	foreach ($wp_recent_posts as $wp_post)
	{
		// get the ID of the current item in the WordPress Loop
		$wp_post_id = $wp_post['ID'];

	//	// set the ID of the current item in the WordPress Loop
	//	$post = get_post($wp_post_id);

		// Get the post author info.
		$author = get_userdata($wp_post['post_author']);

		// Retrieve the time at which the post was written. returns timestamp
		$post_date_u = mysql2date('U', $wp_post['post_date_gmt'], false);

		/**
		* The returned array has 'main' and 'extended' keys. 
		* Main has the text before the <code><!--more--></code>. 
		* The 'extended' key has the content after the <code><!--more--></code> comment.
		**/
		$post_content = get_extended($wp_post['post_content']);
		// Only show the short part of the text
		$more = (isset($post_content['extended']) && $post_content['extended'] != '') ? true : false;
		// The expert, also include the entrie title, but main don't, so we do it like WP does ;)
		$the_text = $post_content['main']; //($more) ? $post_content['main'] : "<strong>" . $wp_post['post_title'] . "</strong><br /><br />" . $post_content['main'];

		// Retrieve full permalink for current post ID.
		$wp_post['permalink'] = get_permalink($wp_post_id, false);

		// Dump vars into template
		$template->assign_block_vars('wordpress_row', array(
			'WP_FOLDER_IMG'		=> $user->img('forum_link', ''),
			'WP_FOLDER_IMG_SRC'	=> $user->img('forum_link', '', false, '', 'src'),
			'WP_MINI_POST_IMG'	=> $user->img('icon_post_target', 'WP_JUMP_TO_POST'),
			'WP_POST_SUBJECT'	=> $wp_post['post_title'],
			'U_WP_VIEW_POST'	=> $wp_post['permalink'],
			'WP_POST_DATE'		=> $user->format_date($post_date_u),
			'WP_POST_AUTHOR'	=> $author->display_name,
			'U_WP_POST_AUTHOR'	=> get_author_posts_url($author->ID, $author->nickname),
			'WP_MESSAGE'		=> bbcode_nl2br($the_text),
			'U_WP_READ_FULL'	=> ($more) ? $wp_post['permalink'] . "#more-$wp_post_id" : '',
			'WP_REPLIES'		=> $wp_post['comment_count'],
			'L_WP_COMMENTS'		=> ($wp_post['comment_count'] == 0) ? $user->lang['WP_NO_COMMENTS']: (($wp_post['comment_count'] == 1) ? $user->lang['WP_ONE_COMMENT'] : sprintf($user->lang['WP_COMMENTS'], $wp_post['comment_count'])),
			'U_WP_POST_REPLY'	=> ($wp_post['comment_status'] == 'open') ? $wp_post['permalink'] . '#reply-button' : '',
		));
		$wp_posts_list++;
	//	unset($post);
	}
}

// Assign specific vars
$template->assign_vars(array(
	'S_DISPLAY_WORDPRESS'	=> $wp_posts_list,
	'U_WORDPRESS'			=> get_option('siteurl'),
	'S_WORDPRESS_TITLE'		=> get_bloginfo('name', 'display'),
	'L_LATEST_WORDPRESS'	=> $config['wp_phpbb_bridge_portal_title'],
	'S_WORDPRESS_STYLE'		=> ($config['wp_phpbb_bridge_portal_style'] == 0) ? 'style' : (($config['wp_phpbb_bridge_portal_style'] == 1) ? 'compact' : 'post'),
));

// Avoid to load again 
define('WP_PHPBB_BRIDGE_PORTAL', true);

// destroy the global references to the WordPres class
$wp = null;
unset($wp);

?>