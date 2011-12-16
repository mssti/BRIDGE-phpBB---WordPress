<?php
/**
 * 
 * @package: phpBB 3.0.9 :: BRIDGE phpBB & WordPress -> root/includes/acp/info
 * @version: $Id: acp_wp_phpbb_bridge.php, v0.0.9 2011/08/25 11:08:25 leviatan21 Exp $
 * @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
 * @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
 * 
 */

/**
* @package module_install
*/
class acp_wp_phpbb_bridge_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_wp_phpbb_bridge',
			'title'		=> 'ACP_WP_PHPBB_BRIDGE',
			'version'	=> '0.0.9',
			'modes'		=> array(
				'manage'		=> array('title' => 'ACP_WP_PHPBB_BRIDGE', 'auth' => 'acl_a_board', 'cat' => array('ACP_GENERAL_TASKS')),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>