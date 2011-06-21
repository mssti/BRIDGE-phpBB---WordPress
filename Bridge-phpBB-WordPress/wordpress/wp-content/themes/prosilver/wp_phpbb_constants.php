<?php
/**
* @ignore
**/
if (!defined('IN_WP_PHPBB_BRIDGE'))
{
	exit;
}
/**
 * http://wordpress.org/support/topic/plugin-wp-phpbb-bridge-path-to-configphp-on-localhost-xampp#post-1952700
 * 
 * The right path is the full path to you config.php
 * In example in my blog is somethink like that:
 * /home/my_blog/public_html/phpBB3/config.php
 * in your case may is somethink like that
 * /home/mydomain/public_html/phpBB3/config.php
 * but I can't be sure about your server a I don't know the configuration of your server.
 * If you are not sure what is the full path to your phpBB3 then create a file "ie: mypath.php" into the folder phpBB3 and enter the following code in it:
 * echo $_SERVER['SCRIPT_FILENAME'];
 * The above code will return to you the full path to phpBB3 + the file name "mypath.php". Replace the "mypath.php" with the "config.php" and then copy all that path to use it in the plugin configuration.
 * Hope this help you :)
 **/
define('PHPBB_ROOT_PATH', './../foro/');
// define('PHPBB_ROOT_PATH', 'D:/MisWebs/phpbb/quickinstall/boards/wp_phpbb_bridge/foro/');

define('PERMISSION_FORUM_ID', 2);

?>