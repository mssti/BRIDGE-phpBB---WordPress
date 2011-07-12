<?php
/**
 * 
 * @package: phpBB 3.0.8 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/theme/prosilver
 * @version: $Id: index.php, v0.0.5 2011/07/12 11:07:12 leviatan21 Exp $
 * @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
 * @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
 * 
 */

/**
* @ignore
**/

require_once('includes/wp_phpbb_bridge.php'); 

$topicrow = $autor = array();

$have_posts = false;

if (have_posts())
{
	$have_posts = true;

	while (have_posts())
	{
		the_post();
		// Retrieve the ID of the current item in the WordPress Loop
		$post_id = get_the_ID();
		// Retrieve the time at which the post was written. returns timestamp
		$post_date_time = get_post_time('U', false, $post_id, false);

		//
		$topicrow = array(
			'POST_ID'			=> $post_id,
			'POST_DATE'			=> phpbb::$user->format_date($post_date_time, false, true),
			'U_POST_EDIT'		=> get_edit_post_link($post_id),
			'MINI_POST_IMG'		=> $user->img('icon_post_target', 'POST'),
			'U_MINI_POST'		=> apply_filters('the_permalink', get_permalink()),
			'POST_SUBJECT'		=> get_the_title(),
			'MESSAGE'			=> wp_do_action('the_content', phpbb::$user->lang['WP_READ_MORE']),

			'POST_TAGS'			=> get_the_tag_list(phpbb::$user->lang['WP_TITLE_TAGS'] . ': ', ', ', '<br />'),
			'POST_CATS'			=> sprintf(phpbb::$user->lang['WP_POSTED_IN'] , get_the_category_list(', ')),
			'POST_COMENT'		=> wp_do_action('comments_popup_link', phpbb::$user->lang['WP_NO_COMMENTS'], phpbb::$user->lang['WP_ONE_COMMENT'], phpbb::$user->lang['WP_COMMENTS']),
			'PAGINATION'		=> wp_topic_generate_pagination(apply_filters('the_permalink', get_permalink()), (int) get_comments_number($post_id), (int) get_option('comments_per_page')),
			'U_FOLLOW_FEED'		=> sprintf(phpbb::$user->lang['WP_FOLLOW_FEED'], get_post_comments_feed_link($post_id)),
			// Both Comments and Pings are open
			'U_YES_COMMENT_YES_PING'	=> (('open' == $post-> comment_status) && ('open' == $post->ping_status)) ? sprintf(phpbb::$user->lang['WP_YES_COMMENT_YES_PING'], get_permalink(), get_trackback_url()) : '',
			// Only Pings are Open
			'U_NO_COMMENT_YES_PING'		=> (!('open' == $post-> comment_status) && ('open' == $post->ping_status)) ? sprintf(phpbb::$user->lang['WP_NO_COMMENT_YES_PING'], get_trackback_url()) : '',
			// Comments are open, Pings are not
			'U_YES_COMMENT_NO_PING'		=> (('open' == $post-> comment_status) && !('open' == $post->ping_status)) ? phpbb::$user->lang['WP_YES_COMMENT_NO_PING'] : '',
			// Neither Comments, nor Pings are open
			'U_NO_COMMENT_NO_PING'		=> (!('open' == $post-> comment_status) && !('open' == $post->ping_status))? phpbb::$user->lang['WP_NO_COMMENT_NO_PING'] : '',
		);

		$autor = phpbb::phpbb_the_autor_full($post->post_author, false);
		$topicrow = array_merge($topicrow, $autor);

		// Dump vars into template
		phpbb::$template->assign_block_vars('topicrow', $topicrow);
	}
}

// Assign index specific vars
phpbb::$template->assign_vars(array(
	'IN_SINGLE'			=> false,
	'IN_ERROR'			=> !$have_posts,
	// Display navigation to next/previous pages when applicable 
	'NEXT_ENTRIE'		=> ($wp_query->max_num_pages > 1) ? get_next_posts_link(phpbb::$user->lang['NEXT_ENTRIE']) : '',
	'PREVIOUS_ENTRIE'	=> ($wp_query->max_num_pages > 1) ? get_previous_posts_link(phpbb::$user->lang['PREVIOUS_ENTRIE']) : '',
));

/** Recent Topics is managed within WP widgets : WP_Widget_phpbb_recet_topics **/
phpbb::page_sidebar();

phpbb::page_header(phpbb::$user->lang['INDEX']);

phpbb::page_footer();

?>