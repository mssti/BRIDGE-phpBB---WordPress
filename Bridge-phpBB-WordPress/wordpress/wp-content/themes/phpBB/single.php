<?php
/**
 * 
 * @package: phpBB 3.0.9 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/themes/phpBB
 * @version: $Id: single.php, v0.0.9 2011/10/25 11:10:25 leviatan21 Exp $
 * @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
 * @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
 * 
 */

/**
* @ignore
**/

require_once('includes/wp_phpbb_bridge.php'); 

$postrow = $commentrow = $autor = array();

$topic_title = $topic_link = '';

$post_id = 0;
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
			// Generate urls for letting the moderation control panel being accessed in different modes
			'S_POST_ACTIONS'	=> (current_user_can('delete_post', $post_id) || current_user_can('edit_post', $post_id)) ? true : false, //	'publish_posts' or 'edit_posts' is for create
			'U_POST_EDIT'		=> get_edit_post_link($post_id),

			// This both links looks similar, but the return is quite differente according the EMPTY_TRASH_DAYS 
			'U_POST_DELETE'		=> (!EMPTY_TRASH_DAYS) ? get_delete_post_link($post_id) : '',
			'U_POST_TRASH'		=> (EMPTY_TRASH_DAYS) ? get_delete_post_link($post_id) : '',

			'MINI_POST_IMG'		=> phpbb::$user->img('icon_post_target', 'POST'),
			'U_MINI_POST'		=> apply_filters('the_permalink', get_permalink()) . "#post-$post_id",
			'POST_SUBJECT'		=> get_the_title(),
			'MESSAGE'			=> wp_do_action('the_content'),

			'POST_TAGS'			=> get_the_tag_list(phpbb::$user->lang['WP_TITLE_TAGS'] . ': ', ', ', ''),
			'POST_CATS'			=> sprintf(phpbb::$user->lang['WP_POSTED_IN'], get_the_category_list(', ')),
			'U_FOLLOW_FEED'		=> sprintf(phpbb::$user->lang['WP_FOLLOW_FEED'], get_post_comments_feed_link($post_id)),
			// Both Comments and Pings are open
			'U_YES_COMMENT_YES_PING'	=> (('open' == $post->comment_status) && ('open' == $post->ping_status)) ? sprintf(phpbb::$user->lang['WP_YES_COMMENT_YES_PING'], get_permalink(), get_trackback_url()) : '',
			// Only Pings are Open
			'U_NO_COMMENT_YES_PING'		=> (!('open' == $post->comment_status) && ('open' == $post->ping_status)) ? sprintf(phpbb::$user->lang['WP_NO_COMMENT_YES_PING'], get_trackback_url()) : '',
			// Comments are open, Pings are not
			'U_YES_COMMENT_NO_PING'		=> (('open' == $post->comment_status) && !('open' == $post->ping_status)) ? phpbb::$user->lang['WP_YES_COMMENT_NO_PING'] : '',
			// Neither Comments, nor Pings are open
			'U_NO_COMMENT_NO_PING'		=> (!('open' == $post->comment_status) && !('open' == $post->ping_status))? phpbb::$user->lang['WP_NO_COMMENT_NO_PING'] : '',
		);

		$topic_title = $postrow['POST_SUBJECT'];
		$topic_link = $postrow['U_MINI_POST'];

		$autor = phpbb::phpbb_the_autor_full($post->post_author, false, true);
		$postrow = array_merge($postrow, $autor);

		// Dump vars into template
		phpbb::$template->assign_block_vars('postrow', $postrow);
	}

	// Let us decide which comments text display and for who can see it
	// The user who is viewing is an administrator
	if ($is_admin = current_user_can('level_8'))
	{
		add_filter('query', 'wp_phpbb_query_filter');
	}

	// Loads the comment template
	comments_template('/comments.php', true);

	// comments are opened
	if ($post->comment_status == 'open')
	{
		$comment_author       = utf8_normalize_nfc(request_var('author', phpbb::$user->data['username'], true));
		$comment_author_email = strtolower(request_var('email', phpbb::$user->data['user_email']));
		$comment_author_url   = strtolower(request_var('url', phpbb::$user->data['user_website']));
		$comment_content      = utf8_normalize_nfc(request_var('comment', '', true));

		// Assign posting specific vars
		phpbb::$template->assign_vars(array(
			'S_LOGGED_AS'			=> sprintf(phpbb::$user->lang['WP_LOGGED_AS_OUT'], phpbb::$user->data['username'], get_option('siteurl') . '/?action=logout'),
			'U_ACTION'				=> get_option('siteurl') . '/wp-comments-post.php',
			'S_REPLYTO'				=> wp_nonce_field('replyto-comment', '_ajax_nonce-replyto-comment', false, false),
			'S_UNFILTEREDHTML'		=> (current_user_can('unfiltered_html')) ? wp_nonce_field('unfiltered-html-comment_' . $post_id, '_wp_unfiltered_html_comment', false, false) : '',

			'COMMENT_AUTHOR'			=> $comment_author,
			'COMMENT_AUTHOR_EMAIL'		=> $comment_author_email,
			'COMMENT_AUTHOR_ULR'		=> $comment_author_url,
			'COMMENT_MESSAGE'			=> $comment_content,
			'COMMENT_TO_POST_ID'		=> $post_id,
			'REQUIRED_FIELDS'			=> get_option('require_name_email'),

			'LA_USERNAME_REQUIRED_NOTE'		=> addslashes(phpbb::$user->lang['WP_USERNAME_REQUIRED_NOTE']),
			'LA_EMAIL_REQUIRED_NOTE'		=> addslashes(phpbb::$user->lang['WP_EMAIL_REQUIRED_NOTE']),
			'LA_EMAIL_REQUIRED_MINLENGTH'	=> addslashes(phpbb::$user->lang['WP_EMAIL_REQUIRED_MINLENGTH']),
			'LA_WEBSITE_REQUIRED_NOTE'		=> addslashes(phpbb::$user->lang['WP_WEBSITE_REQUIRED_NOTE']),
			'LA_WEBSITE_REQUIRED_MINLENGTH'	=> addslashes(phpbb::$user->lang['WP_WEBSITE_REQUIRED_MINLENGTH']),
			'LA_MESSAGE_REQUIRED_NOTE'		=> addslashes(phpbb::$user->lang['WP_MESSAGE_REQUIRED_NOTE']),
			'LA_MESSAGE_REQUIRED_MINLENGTH'	=> addslashes(phpbb::$user->lang['WP_MESSAGE_REQUIRED_MINLENGTH']),

			'L_COMMENT_ALLOWED_TAGS'		=> sprintf(phpbb::$user->lang['WP_ALLOWED_TAGS'], ' <code>' . allowed_tags() . '</code>'),
		));
	}

	// Pagination : Are there comments to navigate through?
	add_filter('get_comments_number', 'wp_phpbb_update_comment_count');
	$total_comments = (int) get_comments_number($post_id);
	$comments_per_page = (int) get_option('comments_per_page');

	if ($total_comments > 1 && $comments_per_page)
	{
		$base_url = apply_filters('the_permalink', get_permalink());

		$on_page = request_var('cpage', 1);
		// if $wp_rewrite->using_permalinks() - Start
		$location = redirect_canonical('', false);
		preg_match('#comment-page-([0-9]{1,})/?$#', $location, $temp_page);
		$on_page = (isset($temp_page) && !empty($temp_page[1])) ? $temp_page[1] : $on_page;
		// if $wp_rewrite->using_permalinks() - End

		phpbb::$template->assign_vars(array(
			'PAGINATION' 	=> wp_generate_pagination($base_url, $total_comments, $comments_per_page, $on_page),
			'PAGE_NUMBER' 	=> sprintf(phpbb::$user->lang['PAGE_OF'], $on_page, max(ceil($total_comments / $comments_per_page), 1)),
			'TOTAL_POSTS'	=> ($total_comments == 1) ? phpbb::$user->lang['VIEW_TOPIC_POST'] : sprintf(phpbb::$user->lang['VIEW_TOPIC_POSTS'], $total_comments),

			'PREVIOUS_PAGE'	=> get_previous_comments_link(phpbb::$user->lang['WP_PAGINATION_PREVIOUS']),
			'NEXT_PAGE'		=> get_next_comments_link(phpbb::$user->lang['WP_PAGINATION_NEXT'] . '&nbsp;'),
		));
	}

	// Assign post specific vars
	phpbb::$template->assign_vars(array(
		'IN_SINGLE'				=> true,
		'U_TOPIC'				=> $topic_link,
		'TOPIC_SUBJECT'			=> $topic_title,

		// Reply
		'S_IS_LOCKED'			=> ($post->comment_status == 'open') ? false : true,
		'S_DISPLAY_REPLY_INFO'	=> ($post->comment_status == 'open' && (phpbb::$auth->acl_get('f_reply', phpbb::$config['wp_phpbb_bridge_permissions_forum_id']) || phpbb::$user->data['user_id'] == ANONYMOUS)) ? true : false,
		'S_DISPLAY_NOTE'		=> (get_option('comment_registration') && phpbb::$user->data['user_id'] == ANONYMOUS) ? sprintf(phpbb::$user->lang['WP_LOGIN_NEED'], get_option('siteurl') . '/?action=login') : '',

		// Icons
		'REPLY_IMG'				=> ($post->comment_status == 'open') ? phpbb::$user->img('button_topic_reply', 'REPLY_TO_TOPIC') : phpbb::$user->img('button_topic_locked', 'TOPIC_LOCKED'),

		'EDIT_IMG' 				=> phpbb::$user->img('icon_post_edit', 'EDIT_POST'),
		'DELETE_IMG' 			=> phpbb::$user->img('icon_post_delete', 'DELETE_POST'),
		'TRASH_IMG' 			=> phpbb::wp_imageset('icon_wp_trash', 'WP_COMMENT_TRASH_EXPLAIN', 'TRASH_IMG_CLASS'),
		'UNTRASH_IMG' 			=> phpbb::wp_imageset('icon_wp_untrash', 'WP_COMMENT_UNTRASH_EXPLAIN', 'UNTRASH_IMG_CLASS'),
		'SPAM_IMG' 				=> phpbb::wp_imageset('icon_wp_spam', 'WP_COMMENT_SPAM_EXPLAIN', 'SPAM_IMG_CLASS'),
		'UNSPAM_IMG'			=> phpbb::wp_imageset('icon_wp_nospam', 'WP_COMMENT_UNSPAM_EXPLAIN', 'UNSPAM_IMG_CLASS'),
		'APPROVE_IMG'			=> phpbb::wp_imageset('icon_wp_approve', 'WP_COMMENT_APPROVE_EXPLAIN', 'APPROVE_IMG_CLASS'),
		'UNAPPROVE_IMG'			=> phpbb::wp_imageset('icon_wp_unapprove', 'POST_UNAPPROVED', 'UNAPPROVE_IMG_CLASS'),

		'REPORTED_IMG'			=> phpbb::$user->img('icon_topic_reported', 'POST_REPORTED'),
		'UNAPPROVED_IMG'		=> phpbb::$user->img('icon_topic_unapproved', 'POST_UNAPPROVED'),

		// Pagination
		'POST_REPLIES'			=> wp_do_action('comments_number' , phpbb::$user->lang['WP_NO_COMMENTS'], phpbb::$user->lang['WP_ONE_COMMENT'], phpbb::$user->lang['WP_COMMENTS']) . sprintf(phpbb::$user->lang['WP_COMMENTS_TO'], $topic_title),
		'PREVIOUS_ENTRIE'		=> wp_do_action('adjacent_post_link', phpbb::$user->lang['PREVIOUS_ENTRIE'] . ' %link', '%title', false, '', true),
		'NEXT_ENTRIE'			=> wp_do_action('adjacent_post_link', '%link ' . phpbb::$user->lang['NEXT_ENTRIE'], '%title', false, '', false),
		'S_LOG_REDIRECT'		=> build_hidden_fields(array('redirect' => apply_filters('the_permalink', get_permalink($post_id)))),
	));
}

phpbb::page_sidebar($post_id);

phpbb::page_header();

phpbb::page_footer();

/**
 * Let us decide which comments text display and for who can see it
 * 	http://codex.wordpress.org/Custom_Queries
 *
 * @uses apply_filters() Calls 'wp_phpbb_query_filter' hook on the comment query
 * @param (string) $query : the query we are running now
 * @return (string) $query : a new query 
 */
function wp_phpbb_query_filter($query)
{
	$query_comment = array(
		// Do not delete extra spaces after "(" amd before ")"
		'from' 	=> "AND (comment_approved = '1' OR ( user_id = " . trim(phpbb::$user->data['wp_user']['ID']) . " AND comment_approved = '0' ) )",
		'to'	=> "",
	);
	/**
	From : query=(SELECT * FROM wp_comments WHERE comment_post_ID = 1 AND (comment_approved = '1' OR ( user_id = 1 AND comment_approved = '0' ) ) ORDER BY comment_date_gmt )
	To :   query=(SELECT * FROM wp_comments WHERE comment_post_ID = 1 ORDER BY comment_date_gmt)
	**/
	if (strpos($query, $query_comment['from']) !== false)
	{
		$query = str_replace($query_comment['from'], $query_comment['to'], $query);
		remove_filter('query', 'query_filter');
	}
	return $query;
}

/**
 * Adjust the comment count, according the users capabilities
 * 
 * @uses apply_filters() Calls 'wp_update_comment_count_now' hook in the WordPress root/wp-includes/comments.php
 * @param (int) $comment_count	The actual number of comments
 * @return (int)
 */
function wp_phpbb_update_comment_count($comment_count)
{
	global $wpdb;

	// Retrieve the ID of the current item in the WordPress Loop
	$post_id = get_the_ID();

	// The user who is viewing is an administrator
	if ($is_admin = current_user_can('level_8'))
	{
		$comment_count = (int) $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_post_ID = %d ", $post_id) );
	}
	else
	{
		global $wpdb;
	//	$new = (int) $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved = '1'", $post_id) );
		$comment_count = (int) $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_post_ID = %d AND ( comment_approved = '1' OR user_id = " . trim(phpbb::$user->data['wp_user']['ID']) . ") ", $post_id) );
	}

	return $comment_count;
}
?>