<?php
/**
 * 
 * @package: phpBB 3.0.8 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/theme/prosilver
 * @version: $Id: single.php, v 0.0.1 2011/06/20 11:06:20 leviatan21 Exp $
 * @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
 * @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
 * 
 */

require_once('wp_phpbb_plugin.php'); 

phpbb::page_header(phpbb::$user->lang['INDEX']);

$postrow = $commentrow = $autor = array();
$topic_title = $topic_link = '';
if (have_posts())
{
	while (have_posts())
	{
		the_post();
		// Retrieve the ID of the current item in the WordPress Loop
		$post_id = get_the_ID();
		// Retrieve the time at which the post was written. returns timestamp
		$post_date_time = get_post_time('U', false, $post_id, false);

		//
		$postrow = array(
			'POST_ID'			=> $post_id,
			'POST_DATE'			=> phpbb::$user->format_date($post_date_time, false, true),
			'U_POST_EDIT'		=> wp_edit_post_link(phpbb::$user->lang['WP_POST_EDIT'], '', '', $post_id),
			'MINI_POST_IMG'		=> $user->img('icon_post_target', 'POST'),
			'U_MINI_POST'		=> apply_filters('the_permalink', get_permalink()),
			'POST_SUBJECT'		=> get_the_title(),
			'MESSAGE'			=> wp_the_content(),
		//	'MESSAGE'			=> (!post_password_required()) ? wp_the_content('<br /><div class="notice">' . __('[Read more...]') . '</div>') : get_the_excerpt(),

			'POST_TAGS'			=> get_the_tag_list(phpbb::$user->lang['WP_TITLE_TAGS'] . ': ', ', ', ''),
			'POST_CATS'			=> sprintf(phpbb::$user->lang['WP_POSTED_IN'], get_the_category_list(', ')),
			'U_FOLLOW_FEED'		=> sprintf(phpbb::$user->lang['WP_FOLLOW_FEED'], get_post_comments_feed_link($post_id)),
			// Both Comments and Pings are open
			'U_YES_COMMENT_YES_PING'	=> (('open' == $post-> comment_status) && ('open' == $post->ping_status)) ? sprintf(phpbb::$user->lang['WP_YES_COMMENT_YES_PING'], get_trackback_url()) : '',
			// Only Pings are Open
			'U_NO_COMMENT_YES_PING'		=> (!('open' == $post-> comment_status) && ('open' == $post->ping_status)) ? sprintf(phpbb::$user->lang['WP_NO_COMMENT_YES_PING'], get_trackback_url()) : '',
			// Comments are open, Pings are not
			'U_YES_COMMENT_NO_PING'		=> (('open' == $post-> comment_status) && !('open' == $post->ping_status)) ? phpbb::$user->lang['WP_YES_COMMENT_NO_PING'] : '',
			// Neither Comments, nor Pings are open
			'U_NO_COMMENT_NO_PING'		=> (!('open' == $post-> comment_status) && !('open' == $post->ping_status))? phpbb::$user->lang['WP_NO_COMMENT_NO_PING'] : '',

		//	'POST_COMENT'		=> wp_comments_popup_link(phpbb::$user->lang['WP_NO_COMMENTS'], phpbb::$user->lang['WP_ONE_COMMENT'], phpbb::$user->lang['WP_COMMENTS']),
			'POST_PAGINATION'	=> wp_link_pages(array('before' => '<p><strong>' . phpbb::$user->lang['WP_PAGINATION'] . ':</strong> ', 'after' => '</p>', 'next_or_number' => 'number', 'echo' => 0)),
		);

		$topic_title = $postrow['POST_SUBJECT'];
		$topic_link = $postrow['U_MINI_POST'];

		$autor = phpbb::phpbb_the_autor_full($post->post_author);
		$postrow = array_merge($postrow, $autor);

		// Dump vars into template
		phpbb::$template->assign_block_vars('postrow', $postrow);
	}

	$post_ID = request_var('p', $post->ID);

	$comments = get_approved_comments($post_ID);
	if ($comments)
	{
		foreach ($comments as $comment)
		{
			// Retrieve the ID of the current item in the WordPress Loop
			$comment_id = $comment->comment_ID;
			// Retrieve the time at which the post was written. returns timestamp
			$post_date_time = get_comment_time('U', false, false);

			//
			$commentrow = array(
				'S_COMMENT'			=> true,
				'POST_ID'			=> $comment_id,
				'POST_DATE'			=> phpbb::$user->format_date($post_date_time, false, true),
				'U_POST_EDIT'		=> get_edit_comment_link($comment_id),
				'MINI_POST_IMG'		=> $user->img('icon_post_target', 'POST'),
				'S_POST_UNAPPROVED'	=> ($comment->comment_approved == '0') ? true : false,
				'MESSAGE'			=> wp_comment_text($comment_id),
			);

			$autor = phpbb::phpbb_the_autor_full($comment->user_id, false, true);
			$commentrow = array_merge($commentrow, $autor);

			// Dump vars into template
			phpbb::$template->assign_block_vars('postrow', $commentrow);
		}
	}

	// comments are opened
	if ($post->comment_status == 'open')
	{
	//	global $wp_query, $withcomments, $post, $wpdb, $id, $comment, $user_login, $user_ID, $user_identity, $overridden_cpage;

	//	do_action('comment_form', $post_ID);
	//	wp_nonce_field('replyto-comment', '_ajax_nonce-replyto-comment', false, false);
	//	wp_comment_form_unfiltered_html_nonce();
	//	if (current_user_can('unfiltered_html'))
	//	{
	//		wp_nonce_field('unfiltered-html-comment_' . $post_ID, '_wp_unfiltered_html_comment', false, false);
	//	}
	}

	$comment_author       = utf8_normalize_nfc(request_var('author', phpbb::$user->data['username'], true));
	$comment_author_email = strtolower(request_var('email', phpbb::$user->data['user_email']));
	$comment_author_url   = strtolower(request_var('url', phpbb::$user->data['user_website']));
	$comment_content      = utf8_normalize_nfc(request_var('comment', '', true));

	// Assign post specific vars
	phpbb::$template->assign_vars(array(
		'IS_SINGLE'				=> true,
		'U_TOPIC'				=> $topic_link,
		'TOPIC_SUBJECT'			=> $topic_title,
		'S_IS_LOCKED'			=> ($post->comment_status == 'open') ? false : true,
		'S_DISPLAY_REPLY_INFO'	=> ($post->comment_status == 'open' && (phpbb::$auth->acl_get('f_reply', PERMISSION_FORUM_ID) || phpbb::$user->data['user_id'] == ANONYMOUS)) ? true : false,
		'S_DISPLAY_NOTE'		=> (get_option('comment_registration') && phpbb::$user->data['user_id'] == ANONYMOUS) ? phpbb::$user->lang['WP_LOGIN_NEED'] : '',
		'S_LOGGED_AS'			=> sprintf( phpbb::$user->lang['WP_LOGGED_AS_OUT'], admin_url('profile.php'), $user_identity, wp_logout_url(apply_filters('the_permalink', get_permalink($post_ID)))),
		'U_ACTION'				=> get_option('siteurl') . '/wp-comments-post.php',
		'S_REPLYTO'				=> wp_nonce_field('replyto-comment', '_ajax_nonce-replyto-comment', false, false),
		'S_UNFILTEREDHTML'		=> (current_user_can('unfiltered_html')) ? wp_nonce_field('unfiltered-html-comment_' . $post_ID, '_wp_unfiltered_html_comment', false, false) : '',

	//	'REPORT_IMG'			=> phpbb::$user->img('icon_post_report', 'REPORT_POST'),
	//	'REPORTED_IMG'			=> phpbb::$user->img('icon_topic_reported', 'POST_REPORTED'),
		'UNAPPROVED_IMG'		=> phpbb::$user->img('icon_topic_unapproved', 'POST_UNAPPROVED'),
	//	'WARN_IMG'				=> phpbb::$user->img('icon_user_warn', 'WARN_USER'),

		'COMMENT_AUTHOR'		=> $comment_author,
		'COMMENT_AUTHOR_EMAIL'	=> $comment_author_email,
		'COMMENT_AUTHOR_ULR'	=> $comment_author_url,
		'COMMENT_MESSAGE'		=> $comment_content,
		'COMMENT_TO_POST_ID'	=> $post_ID,
		'REQUIRED_FIELDS'		=> get_option('require_name_email'),
		
		'LA_USERNAME_REQUIRED_NOTE'			=> addslashes(sprintf(phpbb::$user->lang['WP_USERNAME_REQUIRED_NOTE'], phpbb::$user->lang['USERNAME'])),
		'LA_EMAIL_REQUIRED_NOTE'			=> addslashes(sprintf(phpbb::$user->lang['WP_EMAIL_REQUIRED_NOTE'], phpbb::$user->lang['EMAIL_ADDRESS'])),
		'LA_EMAIL_REQUIRED_MINLENGTH'		=> addslashes(sprintf(phpbb::$user->lang['WP_EMAIL_REQUIRED_MINLENGTH'], phpbb::$user->lang['EMAIL_ADDRESS'])),
		'LA_WEBSITE_REQUIRED_NOTE'			=> addslashes(sprintf(phpbb::$user->lang['WP_WEBSITE_REQUIRED_NOTE'], phpbb::$user->lang['WEBSITE'])),
		'LA_WEBSITE_REQUIRED_MINLENGTH'		=> addslashes(sprintf(phpbb::$user->lang['WP_WEBSITE_REQUIRED_MINLENGTH'], phpbb::$user->lang['WEBSITE'])),
		'LA_MESSAGE_REQUIRED_NOTE'			=> addslashes(sprintf(phpbb::$user->lang['WP_MESSAGE_REQUIRED_NOTE'], phpbb::$user->lang['MESSAGE_BODY'])),
		'LA_MESSAGE_REQUIRED_MINLENGTH'		=> addslashes(sprintf(phpbb::$user->lang['WP_MESSAGE_REQUIRED_MINLENGTH'], phpbb::$user->lang['MESSAGE_BODY'])),

	
		'L_COMMENT_ALLOWED_TAGS'=> sprintf(phpbb::$user->lang['WP_ALLOWED_TAGS'], ' <code>' . allowed_tags() . '</code>'),

		'POST_REPLIES'		=> wp_comments_number(phpbb::$user->lang['WP_NO_COMMENTS'], phpbb::$user->lang['WP_ONE_COMMENT'], phpbb::$user->lang['WP_COMMENTS']) . sprintf(phpbb::$user->lang['WP_COMMENTS_TO'], $topic_title),
		'WP_NEXT_POST'		=> wp_adjacent_post_link('&laquo; %link', '%title', false, '', true),
		'WP_PREVIOUS_POST'	=> wp_adjacent_post_link('%link &raquo;', '%title', false, '', false),
	));
}

phpbb::page_sidebar();

phpbb::page_footer();

?>