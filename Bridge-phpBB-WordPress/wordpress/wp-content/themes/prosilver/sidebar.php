<?php
/**
 * 
 * @package: phpBB 3.0.8 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/theme/prosilver
 * @version: $Id: sidebar.php, v0.0.3 2011/06/28 11:06:28 leviatan21 Exp $
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

if (is_active_sidebar('prosilver-widget-area'))
{
	$dynamic_sidebar = wp_do_action('dynamic_sidebar', 'primary-widget-area');

	$dynamic_sidebar = str_replace(array('<ul>', "<ul class='xoxo blogroll'>", '<ul id="recentcomments">'), '<ul class="menu">', $dynamic_sidebar);
	$dynamic_sidebar = str_replace('id="searchsubmit"', 'id="searchsubmit" class="button2"', $dynamic_sidebar);

	phpbb::$template->assign_vars(array(
		'DYNAMIC_SIDEBAR'	=> $dynamic_sidebar,
	));
}

?>