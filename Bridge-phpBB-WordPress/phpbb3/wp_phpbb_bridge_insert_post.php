<?php
/**
 * 
 * @package: phpBB 3.0.9 :: BRIDGE phpBB & WordPress Portal add-on -> root/
 * @version: $Id: wp_phpbb_bridge_insert_post.php, v0.0.9 2011/10/27 11:10:27 leviatan21 Exp $
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

// DEVELOPERS PLEASE NOTE
// 1) This file can't be embed inside a class or function
//	And must be called before $template->set_filenames()
//	Must be directly included from a file in the root folder of your phpBB board
// 2) Same thing for the child file :
//	Must be directly included from this file in the root folder of your phpBB board
//	and it must be in the root folder of your WordPress blog
if (isset($config['wp_phpbb_bridge_post_disable']) && $config['wp_phpbb_bridge_post_disable'])
{
	if (isset($config['wp_phpbb_bridge_wordpress_path']))
	{
		$wordpress_root_path = trim($config['wp_phpbb_bridge_wordpress_path']);

		// Check / at the end
		if (substr($wordpress_root_path, -1, 1) != '/')
		{
			$wordpress_root_path = $wordpress_root_path . '/';
		}

		if (file_exists($wordpress_root_path . 'wp-phpbb-bridge-insert-post.php'))
		{
			include($wordpress_root_path . 'wp-phpbb-bridge-insert-post.php');
		}
	}
}

?>