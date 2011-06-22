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

function is_odd($number)
{
	return ($number % 2) ? true : false; // false = even, true = odd
}

function wp_get_register()
{
	$link = '';

	if (!is_user_logged_in())
	{
		if (get_option('users_can_register'))
		{
		//	$link = '<a href="' . get_option('siteurl') . '/wp-login.php?action=register">' . __('Register') . '</a>';
			$link = '<a href="' . get_option('siteurl') . '/?action=register">' . __('Register') . '</a>';
		}
		else
		{
			if (phpbb::$config['require_activation'] != USER_ACTIVATION_DISABLE)
			{
			//	phpbb::append_sid("ucp", 'mode=register');
				$link = '<a href="' . get_option('siteurl') . '/?action=register">' . __('Log in') . '</a>';
			}
		}
	}
	else
	{
		$link = '<a href="' . get_option('siteurl') . '/wp-admin/index.php">' . __('Site Admin') . '</a>';
	}

	return apply_filters('register', $link);
}

function wp_get_loginout()
{
	if (!is_user_logged_in())
	{
	//	$link = '<a href="' . get_option('siteurl') . '/wp-login.php">' . __('Log in') . '</a>';
		$link = '<a href="' . get_option('siteurl') . '/?action=login">' . __('Log in') . '</a>';
	}
	else
	{
	//	$link = '<a href="' . get_option('siteurl') . '/wp-login.php?action=logout">' . __('Log out') . '</a>';
		$link = '<a href="' . get_option('siteurl') . '/?action=logout">' . __('Log out') . '</a>';
	}

	return apply_filters('loginout', $link);
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
 * Display edit post link for post.
 *
 * @since 1.0.0
 *
 * @param string $link Optional. Anchor text.
 * @param string $before Optional. Display before edit link.
 * @param string $after Optional. Display after edit link.
 * @param int $id Optional. Post ID.
 * 
 * Based off : wordpress 3.1.3
 * File : wordpress/wp-includes/link-template.php
 */
function wp_edit_post_link( $link = null, $before = '', $after = '', $id = 0 ) {
	if ( !$post = &get_post( $id ) )
		return;

	if ( !$url = get_edit_post_link( $post->ID ) )
		return;

	return $url;
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
		$link = __('Edit This');

	return get_edit_comment_link($comment->comment_ID);
}

function wp_comment_text( $comment_ID = 0 ) {
	$comment = get_comment( $comment_ID );
	return apply_filters( 'comment_text', get_comment_text( $comment_ID ), $comment );
}

function wp_adjacent_post_link($format, $link, $in_same_cat = false, $excluded_categories = '', $previous = true)
{
	if ($previous && is_attachment())
	{
		$post = & get_post($GLOBALS['post']->post_parent);
	}
	else
	{
		$post = get_adjacent_post($in_same_cat, $excluded_categories, $previous);
	}

	if (!$post)
	{
		return '';
	}

	$title = $post->post_title;

//	if ( empty($post->post_title) )
//	{
//		$title = $previous ? __('Previous Post') : __('Next Post');
//	}

	$pre_title	= '<strong>' . (($previous) ? __('Previous Post') : __('Next Post') ) . ' : </strong>';
	$title		= apply_filters('the_title', $title, $post);
	$string		= '<a href="'.get_permalink($post).'">';
	$link		= str_replace('%title', $title, $link);
	$link		= $pre_title . $string . $link . '</a>';

	$format = str_replace('%link', $link, $format);

	return $format;
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

	if ( false === $zero ) $zero = __( 'No Comments' );
	if ( false === $one ) $one = __( '1 Comment' );
	if ( false === $more ) $more = __( '% Comments' );
	if ( false === $none ) $none = __( 'Comments Off' );

	$number = get_comments_number( $id );

	if ( 0 == $number && !comments_open() && !pings_open() ) {
		return '<span' . ((!empty($css_class)) ? ' class="' . esc_attr( $css_class ) . '"' : '') . '>' . $none . '</span>';
	}

	if ( post_password_required() ) {
		return __('Enter your password to view comments.');
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

	$echo .= ' title="' . esc_attr( sprintf( __('Comment on %s'), $title ) ) . '">';
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
		$output = str_replace('%', number_format_i18n($number), ( false === $more ) ? __('% Comments') : $more);
	elseif ( $number == 0 )
		$output = ( false === $zero ) ? __('No Comments') : $zero;
	else // must be one
		$output = ( false === $one ) ? __('1 Comment') : $one;

	return apply_filters('comments_number', $output, $number);
}

function wp_dashboard_comments($comment)
{
	$actions_string = '';
	if (current_user_can('edit_comment', $comment->comment_ID))
	{
		// preorder it: Approve | Reply | Edit | Spam | Trash
		$actions = array(
			'approve' => '',
			'unapprove' => '',
//			'reply' => '',
//			'edit' => '',
			'spam' => '',
			'trash' => '',
			'delete' => ''
		);

		$del_nonce = esc_html( '_wpnonce=' . wp_create_nonce( "delete-comment_$comment->comment_ID" ) );
		$approve_nonce = esc_html( '_wpnonce=' . wp_create_nonce( "approve-comment_$comment->comment_ID" ) );

		$approve_url = esc_url(get_option('home') . "/wp-admin/comment.php?action=approvecomment&p=$comment->comment_post_ID&c=$comment->comment_ID&$approve_nonce");
		$unapprove_url = esc_url(get_option('home') . "/wp-admin/comment.php?action=unapprovecomment&p=$comment->comment_post_ID&c=$comment->comment_ID&$approve_nonce");
//		$edir_url = esc_url(get_option('home') . "comment.php?action=editcomment&amp;c={$comment->comment_ID}");
		$spam_url = esc_url(get_option('home') . "/wp-admin/comment.php?action=spamcomment&p=$comment->comment_post_ID&c=$comment->comment_ID&$del_nonce");
		$trash_url = esc_url(get_option('home') . "/wp-admin/comment.php?action=trashcomment&p=$comment->comment_post_ID&c=$comment->comment_ID&$del_nonce");
		$delete_url = esc_url(get_option('home') . "/wp-admin/comment.php?action=deletecomment&p=$comment->comment_post_ID&c=$comment->comment_ID&$del_nonce");

		$actions['approve'] = "<a href='$approve_url' title='" . esc_attr__( 'Approve this comment' ) . "'><span>" . __( 'Approve' ) . '</span></a>';
		$actions['unapprove'] = "<a href='$unapprove_url'  title='" . esc_attr__( 'Unapprove this comment' ) . "'><span>" . __( 'Unapprove' ) . '</span></a>';
//		$actions['edit'] = "<a href='$edir_url' title='" . esc_attr__('Edit comment') . "'><span>". __('Edit') . '</span></a>';
//		$actions['reply'] = '<a onclick="commentReply.open(\''.$comment->comment_ID.'\',\''.$comment->comment_post_ID.'\');return false;" title="'.esc_attr__('Reply to this comment').'" href="#"><span>' . __('Reply') . '</span></a>';
		$actions['spam'] = "<a href='$spam_url' title='" . esc_attr__( 'Mark this comment as spam' ) . "'><span>" . /* translators: mark as spam link */  _x( 'Spam', 'verb' ) . '</span></a>';
		if (!EMPTY_TRASH_DAYS)
		{
			$actions['delete'] = "<a href='$delete_url'title='" . esc_attr__( 'Delete Permanently' ) . "'><span>" . __('Delete Permanently') . '</span></a>';
		}
		else
		{
			$actions['trash'] = "<a href='$trash_url' title='" . esc_attr__( 'Move this comment to the trash' ) . "'><span>" . _x('Trash', 'verb') . '</span></a>';
		}

//		$actions = apply_filters( 'comment_row_actions', array_filter($actions), $comment );

		foreach ($actions as $action => $link)
		{
			if (!$link) { continue; }
		//	$actions_string .= "<span class='$action'>$sep$link</span>";
			$actions_string .= '<li class="wp-' . strtolower($action) . '-icon">' .$link . '</li>';
		}
	}
	
	return $actions_string;
}

?>