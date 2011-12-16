<?php
/**
 * 
 * @package: phpBB 3.0.9 :: BRIDGE phpBB & WordPress -> WordPress root/
 * @version: $Id: wp-phpbb-bridge-insert-post.php, v0.0.9 2011/10/20 11:10:20 leviatan21 Exp $
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

if (!defined('PHPBB_USE_BOARD_URL_PATH'))
{
	@define('PHPBB_USE_BOARD_URL_PATH', true);
}

$user->add_lang('mods/wp_phpbb_bridge');

$message_prefix = '';
$message_tail = '';
$subject_prefix = '';

$post_content = wp_phpbb_bridge_force_parse($update_message, $data);

// Determine board url
$board_url = generate_board_url() . '/';
$forum_url = append_sid("{$board_url}viewtopic.$phpEx", array('f' => $data['forum_id']));
$topic_url = append_sid("{$board_url}viewtopic.$phpEx", array('f' => $data['forum_id'], 't' => $data['topic_id']));

// This variable indicates if the user is able to post or put into the queue - it is used later for all code decisions regarding approval
$post_approval = 1;

// Check the permissions for post approval. Moderators are not affected.
if ((!$auth->acl_get('f_noapprove', $data['forum_id']) && !$auth->acl_get('m_approve', $data['forum_id']) && empty($data['force_approved_state'])) || (isset($data['force_approved_state']) && !$data['force_approved_state']))
{
	$post_approval = 0;
}

// Add a Subject prefix for the blog (if we have a language string filled)
if ($user->lang['WP_FORUM_SUBJECT_PREFIX'] != '')
{
	$data['topic_title'] = $user->lang['WP_FORUM_SUBJECT_PREFIX'] . $data['topic_title'];
}

// Add a Post prefix for the blog (if we have a language string filled)
if ($user->lang['WP_FORUM_POST_PREFIX'] != '')
{
	$post_content = sprintf($user->lang['WP_FORUM_POST_PREFIX'], '<a href="' . $topic_url . '">', '</a>') . "\n\n" . $post_content;
}

// Add a Post tail for the blog (if we have a language string filled)
if ($user->lang['WP_FORUM_POST_TAIL'] != '')
{
	$entry_cats = sprintf($user->lang['WP_POSTED_IN'] , '<a href="' . $forum_url . '">' . $data['forum_name'] . '</a>');

	if ($entry_cats)
	{
		$post_content = $post_content . "\n\n" . $user->lang['WP_FORUM_POST_TAIL'] . $entry_cats . "\n";
	}
}

$wp_post_data = array(
	'post_status'	=> ($post_approval) ? 'publish' : 'pending',
	'post_author'	=> ($user->data['user_id'] == ANONYMOUS) ? 0 : (($user->data['user_id'] == 2) ? 1 : $user->data['user_id']),
	'post_content'	=> $post_content,
	'post_title'	=> $data['topic_title'],
);

$phpbb_forum_id = (isset($data['forum_id']) && $data['forum_id']) ? $data['forum_id'] : 0;
$phpbb_topic_id = (isset($data['topic_id']) && $data['topic_id']) ? $data['topic_id'] : 0;
$phpbb_post_id = (isset($data['post_id']) && $data['post_id']) ? $data['post_id'] : 0;
$wp_meta_value = array('forum_id' => $phpbb_forum_id, 'topic_id' => $phpbb_topic_id, 'post_id' => $phpbb_post_id);

// Avoid to load again 
define('IN_WP_PHPBB_BRIDGE', false);

// Make sure that the WordPress bootstrap has run before continuing.
require(dirname(__FILE__) . '/wp-load.php');

// Disables showing of database errors.
$wpdb->hide_errors();

// Avoid to call the wp_phpbb_posting function from this bridge
remove_action('publish_post', 'wp_phpbb_posting', 10, 2);

// Are we updating or creating ?
$update = false;
if ($mode == 'edit' && ($phpbb_forum_id != 0 && $phpbb_topic_id != 0 && $phpbb_post_id != 0))
{
	$wp_postid = wp_phpbb_bridge_get_post_by_meta('phpbb_post_id', $wp_meta_value);

	if ($wp_postid != false)
	{
		$update = true;
		$wp_post_data['ID'] = $wp_postid;
	}
}

// Default Post Category
if ($mode == 'post')
{
	$wp_post_data['post_category'] = array(get_option('default_category'));
}

/**
 * @param array $postarr Elements that make up post to insert.
 * @param bool $wp_error Optional. Allow return of WP_Error on failure.
 * @return int|WP_Error The value 0 or WP_Error on failure. The post ID on success.
**/
$wp_post_ID = wp_insert_post($wp_post_data, false);

// We could post, update post meta data and add the phpbb post ID
if (($wp_post_ID && !$update) && ($phpbb_forum_id != 0 && $phpbb_topic_id != 0 && $phpbb_post_id != 0))
{
	add_post_meta($wp_post_ID, 'phpbb_post_id', $wp_meta_value, true);
}

/**
 * Get the html from the post text
 *
 * @param (bolean)	$update_message		Check checksum ... don't re-parse message if the same
 * @param (array)	$postdata			An array with all the post data
 * @return (string)	bbcodes parsed
**/
function wp_phpbb_bridge_force_parse($update_message = false, $postdata)
{
	$postdata['bbcode_options'] = (($postdata['enable_bbcode']) ? OPTION_FLAG_BBCODE : 0) + (($postdata['enable_smilies']) ? OPTION_FLAG_SMILIES : 0) + (($postdata['enable_urls']) ? OPTION_FLAG_LINKS : 0);

	if (!$update_message)
	{
		$postdata['bbcode_uid'] = '';
		$postdata['bbcode_bitfield'] = '';
		$postdata['bbcode_options'] = 0;

		generate_text_for_storage($postdata['message'], $postdata['bbcode_uid'], $postdata['bbcode_bitfield'], $postdata['bbcode_options'], $postdata['enable_bbcode'], $postdata['enable_urls'], $postdata['enable_smilies']);
	}

	return generate_text_for_display($postdata['message'], $postdata['bbcode_uid'], $postdata['bbcode_bitfield'], 7);
}

/**
 * Rerieve a WP post by a meta data value 
 *
 * @param (string)	$meta_key
 * @param (array)	$meta_value
 * @return (bolean|integer)	false on failure, post ID on success
**/
function wp_phpbb_bridge_get_post_by_meta($meta_key = 'phpbb_post_id', $meta_value)
{
	global $wpdb;

	// expected_slashed ($meta_key)
	$meta_key = stripslashes($meta_key);
	$meta_value = stripslashes_deep($meta_value);
	$meta_type = 'post';
	$meta_value = sanitize_meta( $meta_key, $meta_value, $meta_type );
	$meta_value = maybe_serialize( $meta_value );

	$meta_post_id = $wpdb->get_var("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '{$meta_key}' AND meta_value = '{$meta_value}'");

	if (!empty($meta_post_id))
	{
		return (int) $meta_post_id;
	}
	return false;
}

?>