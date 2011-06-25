<?php
/**
 * 
 * @package: phpBB 3.0.8 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/theme/prosilver
 * @version: $Id: index.php, v 0.0.1 2011/06/20 11:06:20 leviatan21 Exp $
 * @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
 * @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
 * 
 */

require_once('includes/wp_phpbb_plugin.php'); 

phpbb::page_header(phpbb::$user->lang['SEARCH']);

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
			'MESSAGE'			=> get_the_excerpt(),
		//	'MESSAGE'			=> (!post_password_required()) ? wp_the_content('<br /><div class="notice">' . __('[Read more...]') . '</div>') : get_the_excerpt(),

			'POST_TAGS'			=> get_the_tag_list(phpbb::$user->lang['WP_TITLE_TAGS'] . ': ', ', ', '<br />'),
			'POST_CATS'			=> sprintf(phpbb::$user->lang['WP_POSTED_IN'] , get_the_category_list(', ')),
		//	'U_FOLLOW_FEED'		=> sprintf(phpbb::$user->lang['WP_FOLLOW_FEED'], get_post_comments_feed_link($post_id)),
			// Both Comments and Pings are open
		//	'U_YES_COMMENT_YES_PING'	=> (('open' == $post-> comment_status) && ('open' == $post->ping_status)) ? sprintf(phpbb::$user->lang['WP_YES_COMMENT_YES_PING'], get_trackback_url()) : '',
			// Only Pings are Open
		//	'U_NO_COMMENT_YES_PING'		=> (!('open' == $post-> comment_status) && ('open' == $post->ping_status)) ? sprintf(phpbb::$user->lang['WP_NO_COMMENT_YES_PING'], get_trackback_url()) : '',
			// Comments are open, Pings are not
		//	'U_YES_COMMENT_NO_PING'		=> (('open' == $post-> comment_status) && !('open' == $post->ping_status)) ? phpbb::$user->lang['WP_YES_COMMENT_NO_PING'] : '',
			// Neither Comments, nor Pings are open
		//	'U_NO_COMMENT_NO_PING'		=> (!('open' == $post-> comment_status) && !('open' == $post->ping_status))? phpbb::$user->lang['WP_NO_COMMENT_NO_PING'] : '',

		//	'POST_COMENT'		=> wp_comments_popup_link(phpbb::$user->lang['WP_NO_COMMENTS'], phpbb::$user->lang['WP_ONE_COMMENT'], phpbb::$user->lang['WP_COMMENTS']),
		);

//		$autor = phpbb::phpbb_the_autor_full($post->post_author);
//		$topicrow = array_merge($topicrow, $autor);

		// Dump vars into template
		phpbb::$template->assign_block_vars('topicrow', $topicrow);
	}
}
else
{
/**	
	<h2 class="center"><?php _e('Not Found'); ?></h2>
	<p class="center"><?php _e('Sorry, but you are looking for something that isn&#8217;t here.'); ?></p>
	<?php include (TEMPLATEPATH . "/searchform.php"); ?>
**/
}

// Assign index specific vars
phpbb::$template->assign_vars(array(
	// We use the same template as index
	'IN_SINGLE'			=> false,
	'IN_SEARCH'			=> true,
	'IN_ERROR'			=> !$have_posts,
));

phpbb::page_sidebar();

phpbb::page_footer();

?>