<?php
/**
 * 
 * @package: phpBB 3.0.9 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/themes/phpBB
 * @version: $Id: search.php, v0.0.9 2011/10/25 11:10:25 leviatan21 Exp $
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
			'MINI_POST_IMG'		=> $user->img('icon_post_target', 'POST'),
			'U_MINI_POST'		=> apply_filters('the_permalink', get_permalink()),
			'POST_SUBJECT'		=> get_the_title(),
			'MESSAGE'			=> get_the_excerpt(),

			'POST_TAGS'			=> get_the_tag_list(phpbb::$user->lang['WP_TITLE_TAGS'] . ': ', ', ', '<br />'),
			'POST_CATS'			=> sprintf(phpbb::$user->lang['WP_POSTED_IN'] , get_the_category_list(', ')),
			'POST_COMENT'		=> wp_do_action('comments_popup_link', phpbb::$user->lang['WP_NO_COMMENTS'], phpbb::$user->lang['WP_ONE_COMMENT'], phpbb::$user->lang['WP_COMMENTS']),
		);

		// Dump vars into template
		phpbb::$template->assign_block_vars('topicrow', $topicrow);
	}
}

// Assign index specific vars
phpbb::$template->assign_vars(array(
	// We use the same template as index
	'IN_SINGLE'			=> false,
	'IN_SEARCH'			=> true,
	'IN_ERROR'			=> !$have_posts,
));

phpbb::page_sidebar();

phpbb::page_header();

phpbb::page_footer();

/**
 * Adds a pretty "Jump to entry" link to custom post excerpts.
 * 
 * @return string Excerpt with a pretty "Continue Reading" link
 */
add_filter('get_the_excerpt', 'wp_phpbb_the_excerpt');
function wp_phpbb_the_excerpt($output)
{
	$output .= '<ul class="searchresults">
			<li><a href="'. get_permalink() . '" class="' . ((phpbb::$user->lang['DIRECTION'] == 'ltr') ? 'right' : 'left') . '">' . phpbb::$user->lang['WP_JUMP_TO_POST'] . '</a></li>
		</ul>';
	return $output;
}

?>