<?php
/**
 * 
 * @package: phpBB 3.0.8 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/theme/prosilver
 * @version: $Id: wp_phpbb_constants.php, v0.0.3-pl1 2011/07/02 11:07:02 leviatan21 Exp $
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
	*
	* @param	string	$phpbb_root_path	Path relative from the wordpress root path.
	*/
//	'phpbb_root_path' => '../phpBB/',
//	Example for localhost :	
	'phpbb_root_path' => '../foro/',

	/**
	* Relative path from the server root (generate_board_url(true))
	*
	* @param	string	Path to the phpBB folder
	*/
//	'phpbb_script_path' => 'phpBB/',
//	Example for localhost :	
	'phpbb_script_path' => 'phpbb/quickinstall/boards/wp_phpbb_bridge/foro/',

	/**
	* Relative path from the server root (generate_board_url(true))
	*
	* @param	string	Path to the wordpress folder
	*/
//	'wordpress_script_path' => 'wordpress/',
//	Example for localhost :	
	'wordpress_script_path' => 'phpbb/quickinstall/boards/wp_phpbb_bridge/wordpress/',
);

/**
 * Extra 
 */
define('WP_PHPBB_BRIDGE_PERMISSIONS_FORUM_ID', 2);
define('WP_PHPBB_BRIDGE_LEFT_COLUMN_WIDTH', 300);
define('WP_PHPBB_BRIDGE_COMMENTS_AVATAR_WIDTH', 32);
define('WP_PHPBB_BRIDGE_POST_FORUM_ID', 0);

?>