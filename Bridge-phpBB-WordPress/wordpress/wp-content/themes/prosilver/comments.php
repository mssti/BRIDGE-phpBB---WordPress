<?php
/**
 * 
 * @package: phpBB 3.0.8 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/theme/prosilver
 * @version: $Id: comments.php, v0.0.3 2011/06/28 11:06:28 leviatan21 Exp $
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

add_filter('previous_comments_link_attributes',	'prosilver_previous_comments_link_attributes');
add_filter('next_comments_link_attributes', 'prosilver_next_comments_link_attributes');

function prosilver_previous_comments_link_attributes()
{
	return ' class="left-box ' . ((phpbb::$user->lang['DIRECTION'] == 'ltr') ? 'left' : 'right') . '" ';
}

function prosilver_next_comments_link_attributes()
{
	return ' class="right-box ' . ((phpbb::$user->lang['DIRECTION'] == 'ltr') ? 'right' : 'left') . '" ';
}

/**
 * Loop through and list the comments. Tell wp_list_comments()
 * to use twentyten_comment() to format the comments.
 */
$defaults = array(
	'walker' => null,
	'max_depth' => '',
	'style' => 'div',
	'callback' => 'prosilver_comment',
	'end-callback' => 'prosilver_end_el',
	'type' => 'all',
	'page' => '',
	'per_page' => '',
	'avatar_size' => phpbb::$config['wp_phpbb_bridge_comments_avatar_width'],
	'reverse_top_level' => null,
	'reverse_children' => ''
);
wp_list_comments($defaults);

function prosilver_end_el()
{
	return;
}

function prosilver_comment($comment, $args, $depth)
{
	global $post;
	$post_id = request_var('p', $post->ID);

	// Retrieve the ID of the current item in the WordPress Loop
	$comment_id = $comment->comment_ID;

	// Retrieve the time at which the post was written. returns timestamp
	$post_date_time = get_comment_time('U', false, false);

	/**
 	* The status of a comment by ID.
 	*
 	* @since 1.0.0
 	*
 	* @param int $comment_id Comment ID
 	* @return string|bool Status might be 'trash', 'approved', 'unapproved', 'spam'. False on failure.
	*/
	$status = wp_get_comment_status($comment_id);

	// Creates a random, one time use token.
	$del_nonce = esc_html('_wpnonce=' . wp_create_nonce("delete-comment_$comment_id"));
	$approve_nonce = esc_html('_wpnonce=' . wp_create_nonce("approve-comment_$comment_id"));

	//
	$commentrow = array(
		'S_COMMENT'			=> true,
		'POST_ID'			=> $comment_id,
		'POST_DATE'			=> phpbb::$user->format_date($post_date_time, false, true),
		'POST_TYPE'			=> $comment->comment_type,

		// Generate urls for letting the moderation control panel being accessed in different modes
		'S_POST_ACTIONS'	=> (current_user_can('edit_comment', $comment_id) || current_user_can('moderate_comments')),
		'U_POST_EDIT'		=> admin_url("comment.php?action=editcomment&amp;c=$comment_id"),
		'U_POST_DELETE'		=> (!EMPTY_TRASH_DAYS) ? admin_url("comment.php?action=deletecomment&amp;p=$post_id&amp;c=$comment_id&amp;$del_nonce") : '',
		'U_POST_TRASH'		=> (EMPTY_TRASH_DAYS) ? admin_url("comment.php?action=trashcomment&amp;p=$post_id&amp;c=$comment_id&amp;$del_nonce") : '',
		'U_POST_SPAM'		=> ($status != 'spam') ? admin_url("comment.php?action=spamcomment&amp;p=$post_id&amp;c=$comment_id&amp;$del_nonce") : '',
		'U_POST_UNSPAM'		=> ($status =='spam') ? admin_url("comment.php?action=unspamcomment&amp;p=$post_id&amp;c=$comment_id&amp;$del_nonce") : '',
		'U_POST_APPROVE'	=> ($status == 'unapproved') ? admin_url("comment.php?action=approvecomment&amp;p=$post_id&amp;c=$comment_id&amp;$approve_nonce") : '',
		'U_POST_UNAPPROVE'	=> ($status != 'unapproved') ? admin_url("comment.php?action=unapprovecomment&amp;p=$post_id&amp;c=$comment_id&amp;$approve_nonce") : '',

		'MINI_POST_IMG'		=> phpbb::$user->img('icon_post_target', 'POST'),
		'U_MINI_POST'		=> apply_filters('the_permalink', get_permalink()) . "#comment-$comment_id",
		'S_POST_UNAPPROVED'	=> ($status == 'unapproved') ? true : false,
		'S_POST_REPORTED'	=> ($status == 'spam') ? true : false,
		'MESSAGE'			=> wp_do_action('comment_text', $comment_id),
	);

	$autor = phpbb::phpbb_the_autor_full($comment->user_id, false, true);
	$commentrow = array_merge($commentrow, $autor);

	// Dump vars into template
	phpbb::$template->assign_block_vars('postrow', $commentrow);
}

?>