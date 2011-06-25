<?php
/**
 * 
 * @package: phpBB 3.0.8 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/theme/prosilver
 * @version: $Id: wp_phpbb_plugin.php, v 0.0.1 2011/06/20 11:06:20 leviatan21 Exp $
 * @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
 * @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
 * 
 */

/**
* @ignore
**/
define('IN_WP_PHPBB_BRIDGE', true);
define('WP_PHPBB_BRIDGE_ROOT', TEMPLATEPATH . '/');
define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1));
define('WP_TABLE_PREFIX', $table_prefix);

// Make this variable global before initialize phpbb
$wp_user = wp_get_current_user();

// Include the initial functions to phpBB
if (!file_exists(WP_PHPBB_BRIDGE_ROOT . 'includes/wp_phpbb_common.' . PHP_EXT))
{
	die('<p>No phpBB installation found. Check the "WP phpBB Bidge" configuration file.</p>');
}
require(WP_PHPBB_BRIDGE_ROOT . 'includes/wp_phpbb_common.' . PHP_EXT);

// $filename = strtolower(basename($_SERVER['SCRIPT_FILENAME']));
$action = request_var('action', '');
$redirect = request_var('redirect', get_option('home'));
$redirect_to = request_var('redirect_to', $redirect);

switch ($action)
{
	case 'login':
		phpbb::login_box($redirect_to);
	break;

	case 'logout':
		if (phpbb::$user->data['user_id'] != ANONYMOUS)
		{
			phpbb::$user->session_kill();
			phpbb::$user->session_begin();
		}

		redirect($redirect_to);
	break;
}

/**
* Below this there are a compendium of functions modified from WordPress 
*	by changing the echo for return
*	and porting the language strings to phpbb 
**/

/**
 * Load the correct database class file.
 *
 * This function is used to load the database class file either at runtime or by
 * wp-admin/setup-config.php. We must globalize $wpdb to ensure that it is
 * defined globally by the inline code in wp-db.php.
 *
 * @since 2.5.0
 * @global $wpdb WordPress Database Object
 * 
 * Based off : wordpress 3.1.3
 * File : wordpress/wp-includes/load.php
 */
function phpbb_get_wp_db()
{
	global $wpdb;

	require_once(ABSPATH . WPINC . '/wp-db.php');
	if ( file_exists(WP_CONTENT_DIR . '/db.php'))
	{
		require_once(WP_CONTENT_DIR . '/db.php');
	}

	$wpdb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);

	$wpdb ->set_prefix(WP_TABLE_PREFIX);

	return $wpdb;
}

/**
 * Display the post content.
 *
 * @since 0.71
 *
 * @param string $more_link_text Optional. Content for when there is more text.
 * @param string $stripteaser Optional. Teaser content before the more text.
 * 
 * Based off : wordpress 3.1.3
 * File : wordpress/wp-includes/post-template.php
 */
function wp_the_content($more_link_text = null, $stripteaser = 0) {
	$content = get_the_content($more_link_text, $stripteaser);
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
	return $content;
}

/**
 * Display or retrieve edit comment link with formatting.
 *
 * @since 1.0.0
 *
 * @param string $link Optional. Anchor text.
 * @param string $before Optional. Display before edit link.
 * @param string $after Optional. Display after edit link.
 * @return string|null HTML content, if $echo is set to false.
 * 
 * Based off : wordpress 3.1.3
 * File : wordpress/wp-includes/link-template.php
 */
function wp_edit_comment_link( $link = null, $before = '', $after = '' ) {
	global $comment;

	if ( !current_user_can( 'edit_comment', $comment->comment_ID ) )
		return;

	if ( null === $link )
		$link = phpbb::$user->lang['WP_COMMENT_EDIT'];

	return get_edit_comment_link($comment->comment_ID);
}

/**
 * Displays the text of the current comment.
 *
 * @since 0.71
 * @uses apply_filters() Passes the comment content through the 'comment_text' hook before display
 * @uses get_comment_text() Gets the comment content
 *
 * @param int $comment_ID The ID of the comment for which to print the text. Optional.
 * 
 * Based off : wordpress 3.1.3
 * File : wordpress/wp-includes/comment-template.php
 */
function wp_comment_text( $comment_ID = 0 ) {
	$comment = get_comment( $comment_ID );
	return apply_filters( 'comment_text', get_comment_text( $comment_ID ), $comment );
}

/**
 * Display adjacent post link.
 *
 * Can be either next post link or previous.
 *
 * @since 2.5.0
 *
 * @param string $format Link anchor format.
 * @param string $link Link permalink format.
 * @param bool $in_same_cat Optional. Whether link should be in same category.
 * @param string $excluded_categories Optional. Excluded categories IDs.
 * @param bool $previous Optional, default is true. Whether display link to previous post.
 * 
 * Based off : wordpress 3.1.3
 * File : wordpress/wp-includes/link-template.php
 */
function wp_adjacent_post_link($format, $link, $in_same_cat = false, $excluded_categories = '', $previous = true) {
	if ( $previous && is_attachment() )
		$post = & get_post($GLOBALS['post']->post_parent);
	else
		$post = get_adjacent_post($in_same_cat, $excluded_categories, $previous);

	if ( !$post )
		return;

	$title = $post->post_title;

	if ( empty($post->post_title) )
		$title = $previous ? phpbb::$user->lang['PREVIOUS_ENTRIE'] : phpbb::$user->lang['NEXT_ENTRIE'];

	$title = apply_filters('the_title', $title, $post->ID);
	$date = mysql2date(get_option('date_format'), $post->post_date);
	$rel = $previous ? 'prev' : 'next';

	$string = '<a href="'.get_permalink($post).'" rel="'.$rel.'">';
	$link = str_replace('%title', $title, $link);
	$link = str_replace('%date', $date, $link);
	$link = $string . $link . '</a>';

	$format = str_replace('%link', $link, $format);

	$adjacent = $previous ? 'previous' : 'next';
	return apply_filters( "{$adjacent}_post_link", $format, $link );
}

/**
 * Displays the link to the comments popup window for the current post ID.
 *
 * Is not meant to be displayed on single posts and pages. Should be used on the
 * lists of posts
 *
 * @since 0.71
 * @uses $wpcommentspopupfile
 * @uses $wpcommentsjavascript
 * @uses $post
 *
 * @param string $zero The string to display when no comments
 * @param string $one The string to display when only one comment is available
 * @param string $more The string to display when there are more than one comment
 * @param string $css_class The CSS class to use for comments
 * @param string $none The string to display when comments have been turned off
 * @return null Returns null on single posts and pages.
 * 
 * Based off : wordpress 3.1.3
 * File : wordpress/wp-includes/comment-template.php
 */
function wp_comments_popup_link( $zero = false, $one = false, $more = false, $css_class = '', $none = false ) {
	global $wpcommentspopupfile, $wpcommentsjavascript;

	$id = get_the_ID();

	if ( false === $zero ) $zero = phpbb::$user->lang['WP_NO_COMMENTS'];
	if ( false === $one ) $one = phpbb::$user->lang['WP_ONE_COMMENT'];
	if ( false === $more ) $more = phpbb::$user->lang['WP_COMMENTS'];
	if ( false === $none ) $none = phpbb::$user->lang['WP_COMMENTS_OFF'];

	$number = get_comments_number( $id );

	if ( 0 == $number && !comments_open() && !pings_open() ) {
		return '<span' . ((!empty($css_class)) ? ' class="' . esc_attr( $css_class ) . '"' : '') . '>' . $none . '</span>';
	}

	if ( post_password_required() ) {
		return phpbb::$user->lang['WP_COMMENTS_PASSWORED'];
	}

	$echo = '<a href="';
	if ( $wpcommentsjavascript ) {
		if ( empty( $wpcommentspopupfile ) )
			$home = home_url();
		else
			$home = get_option('siteurl');
		$echo .= $home . '/' . $wpcommentspopupfile . '?comments_popup=' . $id;
		$echo .= '" onclick="wpopen(this.href); return false"';
	} else { // if comments_popup_script() is not in the template, display simple comment link
		if ( 0 == $number )
			$echo .= get_permalink() . '#respond';
		else
			get_comments_link();
		$echo .= '"';
	}

	if ( !empty( $css_class ) ) {
		$echo .= ' class="'.$css_class.'" ';
	}
	$title = the_title_attribute( array('echo' => 0 ) );

	$echo .= apply_filters( 'comments_popup_link_attributes', '' );

	$echo .= ' title="' . esc_attr( sprintf(phpbb::$user->lang['WP_COMMENTS_ON'], $title ) ) . '">';
	$echo .= wp_comments_number( $zero, $one, $more );
	$echo .= '</a>';

	return $echo;
}

/**
 * Display the language string for the number of comments the current post has.
 *
 * @since 0.71
 * @uses apply_filters() Calls the 'comments_number' hook on the output and number of comments respectively.
 *
 * @param string $zero Text for no comments
 * @param string $one Text for one comment
 * @param string $more Text for more than one comment
 * @param string $deprecated Not used.
 * 
 * Based off : wordpress 3.1.3
 * File : wordpress/wp-includes/comment-template.php
 */
function wp_comments_number( $zero = false, $one = false, $more = false, $deprecated = '' ) {
	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '1.3' );

	$number = get_comments_number();

	if ( $number > 1 )
		$output = str_replace('%', number_format_i18n($number), ( false === $more ) ? phpbb::$user->lang['WP_COMMENTS'] : $more);
	elseif ( $number == 0 )
		$output = ( false === $zero ) ? phpbb::$user->lang['WP_NO_COMMENTS'] : $zero;
	else // must be one
		$output = ( false === $one ) ? phpbb::$user->lang['WP_ONE_COMMENT'] : $one;

	return apply_filters('comments_number', $output, $number);
}

?>