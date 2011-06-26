<?php
/**
 * 
 * @package: phpBB 3.0.8 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/theme/prosilver
 * @version: $Id: wp_phpbb_constants.php, v0.0.2 2011/06/26 11:06:26 leviatan21 Exp $
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
 * In example in my blog is somethink like that : /home/my_blog/public_html/phpBB3/config.php
 * in your case may is somethink like that :      /home/mydomain/public_html/phpBB3/config.php
 * but I can't be sure about your server a I don't know the configuration of your server.
 * If you are not sure what is the full path to your phpBB3 then create a file "ie: mypath.php" into the folder phpBB3 and enter the following code in it:
 * echo $_SERVER['SCRIPT_FILENAME'];
 * The above code will return to you the full path to phpBB3 + the file name "mypath.php". Replace the "mypath.php" with the "config.php" 
 * and then copy all that path to use it in the plugin configuration.
 * Hope this help you :)
 */

// The full path to you forum root ( where is the config.php file )
define('PHPBB_ROOT_PATH', './../foro/');
// define('PHPBB_ROOT_PATH', 'D:/MisWebs/phpbb/quickinstall/boards/wp_phpbb_bridge/foro/');

?>