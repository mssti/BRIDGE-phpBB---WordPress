<?php
/**
 * 
 * @package: phpBB 3.0.9 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/themes/phpBB
 * @version: $Id: sidebar.php, v0.0.9 2011/10/25 11:10:25 leviatan21 Exp $
 * @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
 * @license: http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
 * 
 */

/**
 * @ignore
 */

if (!defined('IN_WP_PHPBB_BRIDGE'))
{
	exit;
}

if (is_active_sidebar('wp_phpbb-widget-area'))
{
	$dynamic_sidebar = wp_do_action('dynamic_sidebar', 'wp_phpbb-widget-area');

	if ($dynamic_sidebar)
	{
		// Styling the menu
		$dynamic_sidebar = str_replace(array('<ul>', "<ul class='xoxo blogroll'>", '<ul id="recentcomments">'), '<ul class="menu">', $dynamic_sidebar);
		// Styling the search block
		$dynamic_sidebar = str_replace('id="searchsubmit"', 'id="searchsubmit" class="button2"', $dynamic_sidebar);
		$dynamic_sidebar = str_replace('id="s" ', 'id="s" class="inputbox search" ', $dynamic_sidebar);

		// Make sure we set up the sidebar style
		if (!did_action('wp_phpbb_stylesheet'))
		{
			// Extra layout 2 columns
			add_action('wp_head', 'wp_phpbb_stylesheet');
		}

		phpbb::$template->assign_vars(array(
			'DYNAMIC_SIDEBAR'	=> $dynamic_sidebar,
		));
	}
}

?>