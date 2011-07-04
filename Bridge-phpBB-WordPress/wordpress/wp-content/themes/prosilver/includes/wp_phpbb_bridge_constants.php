<?php
/**
 * 
 * @package: phpBB 3.0.8 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/theme/prosilver
 * @version: $Id: wp_phpbb_bridge_constants.php, v0.0.4 2011/07/04 11:07:04 leviatan21 Exp $
 * @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
 * @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
 * 
 */

/**
* @ignore
**/
if (!defined('IN_WP_PHPBB_BRIDGE'))
{
	exit;
}
/**
 * The right path is the full path to you config.php
 * 
 * If you are not sure what is the full path to your phpBB3 then create a file "ie: mypath.php" into the folder phpBB3 and enter the following code in it:
 * echo $_SERVER['SCRIPT_FILENAME'];
 * The above code will return to you the full path to phpBB3 + the file name "mypath.php".
 * Place the "mypath.php" with the "config.php" and then copy all that path to use it in the plugin configuration.
 * Hope this help you :)
 */

$wp_phpbb_bridge_config = array(
	/**
	* Relative path to the phpBB installation.
	*	True for enable the Bridge and false for disable the Bridge
	*
	* @param	boolean	$phpbb_bridge 
	*/

	'phpbb_bridge'	=> true,
	/**
	* Relative path to the phpBB installation.
	*	Path relative from the wordpress root path.
	*
	* @param	string	$phpbb_root_path
	*/
	'phpbb_root_path' => '../phpBB/',
	//	Example for localhost :	
	//	'phpbb_root_path' => '../foro/',

	/**
	* Relative path from the server root (generate_board_url(true))
	*	Path to the phpBB folder
	*
	* @param	string	$phpbb_script_path
	*/
	'phpbb_script_path' => 'phpBB/',
	//	Example for localhost :	
	//	'phpbb_script_path' => 'phpbb/quickinstall/boards/wp_phpbb_bridge/foro/',

	/**
	* Relative path from the server root (generate_board_url(true))
	*	Path to the wordpress folder
	*
	* @param	string	$wordpress_script_path
	*/
	'wordpress_script_path' => 'wordpress/',
	//	Example for localhost :	
	//	'wordpress_script_path' => 'phpbb/quickinstall/boards/wp_phpbb_bridge/wordpress/',

	/**
	 * The ID of you forum where to use permissions ( like $auth->acl_get('f_reply') )
	 *
	 * @param integer $wp_phpbb_bridge_permissions_forum_id
	 */
	'wp_phpbb_bridge_permissions_forum_id'	=> 2,

	/**
	 * The ID of you forum where to post a new entry whenever is published in the Wordpress
	 *
	 * @param integer $wp_phpbb_bridge_post_forum_id
	 */
	'wp_phpbb_bridge_post_forum_id'			=> 0,

	/**
	 * The left column width, in pixels
	 *
	 * @param integer $wp_phpbb_bridge_left_column_width
	 */
	'wp_phpbb_bridge_left_column_width'		=> 300,

	/**
	 * The width size of avatars in comments, in pixels
	 *
	 * @param integer $wp_phpbb_bridge_comments_avatar_width
	 */
	'wp_phpbb_bridge_comments_avatar_width'	=> 32,
);

?>