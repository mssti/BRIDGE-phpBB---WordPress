<?php
/**
 * 
 * @package: phpBB 3.0.8 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/theme/prosilver
 * @version: $Id: comments.php, v0.0.5 2011/07/12 11:07:12 leviatan21 Exp $
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

add_filter('previous_comments_link_attributes',	'wp_phpbb_previous_comments_link_attributes');
add_filter('next_comments_link_attributes', 'wp_phpbb_next_comments_link_attributes');

function wp_phpbb_previous_comments_link_attributes()
{
	return ' class="left-box ' . ((phpbb::$user->lang['DIRECTION'] == 'ltr') ? 'left' : 'right') . '" ';
}

function wp_phpbb_next_comments_link_attributes()
{
	return ' class="right-box ' . ((phpbb::$user->lang['DIRECTION'] == 'ltr') ? 'right' : 'left') . '" ';
}

/**
 * Loop through and list the comments. Tell wp_list_comments()
 * to use phpbb_comment_loop() to format the comments.
 */
$defaults = array(
	'walker' => null,
	'max_depth' => '',
	'style' => 'div',
	'callback' => 'wp_phpbb_comment_loop',
	'end-callback' => 'wp_phpbb_comment_end_el',
	'type' => 'all',
	'page' => '',
	'per_page' => '',
	'avatar_size' => phpbb::$config['wp_phpbb_bridge_comments_avatar_width'],
	'reverse_top_level' => null,
	'reverse_children' => ''
);
wp_list_comments($defaults);

function wp_phpbb_comment_end_el()
{
	return;
}

function wp_phpbb_comment_loop($comment, $args)
{
	// Retrieve the ID of the current item in the WordPress Loop
	$comment_id = $comment->comment_ID;

	// Retrieve the ID of the current post in the WordPress Loop
	$post_id = $comment->comment_post_ID;

	// Retrieve the time at which the post was written. returns timestamp
	$post_date_time = get_comment_time('U', false, false);

	// The status of a comment by ID.
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
		'U_POST_EDIT'		=> admin_url("comment.php?action=editcomment&amp;c=$comment_id&amp;noredir=1"),
		'U_POST_DELETE'		=> (!EMPTY_TRASH_DAYS) ? admin_url("comment.php?action=deletecomment&amp;p=$post_id&amp;c=$comment_id&amp;noredir=1&amp;$del_nonce") : '',
		'U_POST_TRASH'		=> (EMPTY_TRASH_DAYS) ? admin_url("comment.php?action=trashcomment&amp;p=$post_id&amp;c=$comment_id&amp;noredir=1&amp;$del_nonce") : '',
		'U_POST_SPAM'		=> ($status != 'spam') ? admin_url("comment.php?action=spamcomment&amp;p=$post_id&amp;c=$comment_id&amp;noredir=1&amp;$del_nonce") : '',
		'U_POST_UNSPAM'		=> ($status =='spam') ? admin_url("comment.php?action=unspamcomment&amp;p=$post_id&amp;c=$comment_id&amp;noredir=1&amp;$del_nonce") : '',
		'U_POST_APPROVE'	=> ($status == 'unapproved') ? admin_url("comment.php?action=approvecomment&amp;p=$post_id&amp;c=$comment_id&amp;noredir=1&amp;$approve_nonce") : '',
		'U_POST_UNAPPROVE'	=> ($status != 'unapproved') ? admin_url("comment.php?action=unapprovecomment&amp;p=$post_id&amp;c=$comment_id&amp;noredir=1&amp;$approve_nonce") : '',

		'MINI_POST_IMG'		=> phpbb::$user->img('icon_post_target', 'POST'),
		'U_MINI_POST'		=> apply_filters('the_permalink', get_permalink()) . "#comment-$comment_id",
		'S_POST_UNAPPROVED'	=> ($status == 'unapproved') ? true : false,
		'S_POST_REPORTED'	=> ($status == 'spam') ? true : false,
		'MESSAGE'			=> wp_phpbb_comment_text_filter(wp_do_action('comment_text', $comment_id), $comment->user_id, $status),
	);

	$autor = phpbb::phpbb_the_autor_full($comment->user_id, false, true);
	$commentrow = array_merge($commentrow, $autor);

	// Dump vars into template
	phpbb::$template->assign_block_vars('postrow', $commentrow);
}

/**
 * Let us decide which comments text display and for who can see it
 * 	This works because the filter added at single.php : add_filter('query', 'query_filter');
 * 	We do not user "add_filter('comment_text', 'filter_comment_text');" because we need some extra arguments
 *
 * @param (string) $comment_text : 
 * @param (int) $comment_user_id : Comment ID
 * @param (string|bool) $starus : Status might be 'trash', 'approved', 'unapproved', 'spam'. False on failure.
 * @return (string) $comment_text
 */
function wp_phpbb_comment_text_filter($comment_text, $comment_user_id, $status)
{
	// There is nothing to worry about if the comment is approved
	if ($status == 'approved')
	{
		return $comment_text;
	}
	// The user who is viewing is an administrator
	if ($is_admin = current_user_can('level_8'))
	{
	}
	// The user who is viewing is the comment author
	else if (phpbb::$user->data['wp_user']['ID'] == $comment_user_id)
	{
	}
	else
	{
		$comment_text = '';
	}

	return $comment_text;
}

?>