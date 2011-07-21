<?php
/** Wordpress Header
 * 
 * Plugin Name: BRIDGE phpBB & WordPress
 * Plugin URI: http://www.mssti.com/
 * Description: Synchronize users from phpBB 3.0.x in WordPress.
 * Version: 0.0.6
 * Author: leviatan21
 * Author URI: http://www.mssti.com/
 * License: GNU
 */

/** phpbb Header
 * 
 * @package: phpBB 3.0.8 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/plugins
 * @version: $Id: wp_phpbb3_bridge_options.php, v0.0.6 2011/07/21 11:07:21 leviatan21 Exp $
 * @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
 * @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
 * 
 */

/**
 * Copyright 2011 leviatan21 (info@mssti.com)
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
* @ignore
**/

// add the admin options page
add_action('admin_menu', 'wp_phpbb3_bridge_admin_add_page');

// set text domain
load_plugin_textdomain('wp_phpbb3_bridge_options', false, false);

/**
 * create custom plugin settings menu
 * 
 * What this does is quite simple, really:
 *	a. It adds a link under the settings menu called “Custom Plugin Menu”.
 * 	b. When you click it, you go to a page with a title of “Custom Plugin Page”.
 * 	c. You must have the “manage_options” capability to get there though (admins only).
 * 	d. The link this will be will in fact be /wp-admin/options-general.php?page=plugin (so “plugin” needs to be something only you will use).
 * 	e. And the content of the page itself will be generated by the “plugin_options_page” function.
 */
function wp_phpbb3_bridge_admin_add_page()
{
	add_menu_page(
		'BRIDGE phpBB & WordPress Plugin Menu',
		'BRIDGE phpBB & WordPress Plugin Page',
		'manage_options',
		'wp_phpbb3_bridge_options',
		'wp_phpbb3_bridge_options_page'
	);
}

function wp_phpbb3_bridge_options_page()
{
	// must check that the user has the required capability
	if (!current_user_can('manage_options'))
	{
		wp_die(__('You do not have sufficient permissions to access this page.', 'wp_phpbb3_bridge_options'));
	}

	// Include the constant for the path to phpBB
	define('IN_WP_PHPBB_BRIDGE', true);
	if (!file_exists(WP_CONTENT_DIR . '/themes/prosilver/includes/wp_phpbb_bridge_constants.php'))
	{
		wp_die('<p>No "Bridge" constant found. Check the "' . WP_CONTENT_DIR . '/themes/prosilver/includes/wp_phpbb_bridge_constants.php" file.</p>');
	}
	require(WP_CONTENT_DIR . '/themes/prosilver/includes/wp_phpbb_bridge_constants.php');

	// Some default options
	$submit	= (isset($_POST['submit'])) ? true : false;

	$active									= get_option('wp_phpbb_bridge', $wp_phpbb_bridge_config['phpbb_bridge']);

	$phpbb_root_path						= get_option('phpbb_root_path', $wp_phpbb_bridge_config['phpbb_root_path']);
	$phpbb_script_path						= get_option('phpbb_script_path', $wp_phpbb_bridge_config['phpbb_script_path']);
	$wordpress_script_path					= get_option('wordpress_script_path', $wp_phpbb_bridge_config['wordpress_script_path']);

	$wp_phpbb_bridge_permissions_forum_id	= get_option('wp_phpbb_bridge_permissions_forum_id', $wp_phpbb_bridge_config['wp_phpbb_bridge_permissions_forum_id']);
	$wp_phpbb_bridge_post_forum_id			= get_option('wp_phpbb_bridge_post_forum_id', $wp_phpbb_bridge_config['wp_phpbb_bridge_post_forum_id']);
	$wp_phpbb_bridge_widgets_column_width	= get_option('wp_phpbb_bridge_widgets_column_width', $wp_phpbb_bridge_config['wp_phpbb_bridge_widgets_column_width']);
	$wp_phpbb_bridge_comments_avatar_width	= get_option('wp_phpbb_bridge_comments_avatar_width', $wp_phpbb_bridge_config['wp_phpbb_bridge_comments_avatar_width']);

	if ($submit)
	{
		$active									= (isset($_POST['wp_phpbb_bridge']))												? $_POST['wp_phpbb_bridge']								: $active;

		$phpbb_root_path						= (isset($_POST['phpbb_root_path']) && $_POST['phpbb_root_path'] != '')				? trim($_POST['phpbb_root_path'])						: $phpbb_root_path;
		$phpbb_script_path						= (isset($_POST['phpbb_script_path']) && $_POST['phpbb_script_path'] != '')			? trim($_POST['phpbb_script_path'])						: $phpbb_script_path;
		$wordpress_script_path					= (isset($_POST['wordpress_script_path']) && $_POST['wordpress_script_path'] != '')	? trim($_POST['wordpress_script_path'])					: $wordpress_script_path;

		$wp_phpbb_bridge_permissions_forum_id	= (isset($_POST['wp_phpbb_bridge_permissions_forum_id']))							? (int) $_POST['wp_phpbb_bridge_permissions_forum_id']	: $wp_phpbb_bridge_permissions_forum_id;
		$wp_phpbb_bridge_post_forum_id 			= (isset($_POST['wp_phpbb_bridge_post_forum_id']))									? (int) $_POST['wp_phpbb_bridge_post_forum_id']			: $wp_phpbb_bridge_post_forum_id;
		$wp_phpbb_bridge_widgets_column_width	= (isset($_POST['wp_phpbb_bridge_widgets_column_width']))							? (int) $_POST['wp_phpbb_bridge_widgets_column_width']	: $wp_phpbb_bridge_widgets_column_width;
		$wp_phpbb_bridge_comments_avatar_width	= (isset($_POST['wp_phpbb_bridge_comments_avatar_width']))							? (int) $_POST['wp_phpbb_bridge_comments_avatar_width']	: $wp_phpbb_bridge_comments_avatar_width;
	}

	$_phpbb_root_path = wp_phpbb3_bridge_check_path('phpbb_root_path', $phpbb_root_path, 'config.php', false);
	if (!$_phpbb_root_path)
	{
		$active = false;
		$submit = false;
	?>	<div id="message" class="error fade" style="padding: .5em; background-color: #BC2A4D; color: #FFFFFF; font-weight: bold;">
			<p> <?php printf(__("Could not find path to your board. Please check your settings and try again.<br /><samp>%s</samp> was specified as the source path.<br /><br />Cannot activate bridge.", 'wp_phpbb3_bridge_options'), $phpbb_root_path); ?> </p>
		</div>	<?php
	}
	else
	{
		$phpbb_root_path = $_phpbb_root_path;
	}

	$_phpbb_script_path = wp_phpbb3_bridge_check_path('phpbb_script_path', $phpbb_script_path, 'config.php', true);
	if (!$_phpbb_script_path)
	{
		$active = false;
		$submit = false;
	?>	<div id="message" class="error fade" style="padding: .5em; background-color: #BC2A4D; color: #FFFFFF; font-weight: bold;">
			<p> <?php printf(__('Could not find "Server root path to phpBB". Please check your settings and try again.<br /><samp>%s</samp> was specified as the source path.<br /><br />Cannot activate bridge.', 'wp_phpbb3_bridge_options'), $phpbb_script_path); ?> </p>
		</div>	<?php
	}
	else
	{
		$phpbb_script_path = $_phpbb_script_path;
	}

	$_wordpress_script_path = wp_phpbb3_bridge_check_path('wordpress_script_path', $wordpress_script_path, 'wp-config.php', true);
	if (!$_wordpress_script_path)
	{
		$active = false;
		$submit = false;
	?>	<div id="message" class="error fade" style="padding: .5em; background-color: #BC2A4D; color: #FFFFFF; font-weight: bold;">
			<p> <?php printf(__('Could not find "Server root path to WordPress". Please check your settings and try again.<br /><samp>%s</samp> was specified as the source path.<br /><br />Cannot activate bridge.', 'wp_phpbb3_bridge_options'), $wordpress_script_path); ?> </p>
		</div>	<?php
	}
	else
	{
		$wordpress_script_path = $_wordpress_script_path;
	}

	if ($submit)
	{
		update_option('wp_phpbb_bridge', $active);

		update_option('phpbb_root_path', $phpbb_root_path);
		update_option('phpbb_script_path', $phpbb_script_path);
		update_option('wordpress_script_path', $wordpress_script_path);

		update_option('wp_phpbb_bridge_permissions_forum_id', $wp_phpbb_bridge_permissions_forum_id);
		update_option('wp_phpbb_bridge_post_forum_id', $wp_phpbb_bridge_post_forum_id);
		update_option('wp_phpbb_bridge_widgets_column_width', $wp_phpbb_bridge_widgets_column_width);
		update_option('wp_phpbb_bridge_comments_avatar_width', $wp_phpbb_bridge_comments_avatar_width);
	?>
		<div id="message" class="updated fade" style="padding: .5em; background-color: #228822; color: #FFFFFF; font-weight: bold;">
			<p> <?php _e('Options saved.', 'wp_phpbb3_bridge_options'); ?> </p>
		</div>
	<?php
	}
?>
	<div class="wrap">
		<form method="post" action="">
			<h2><img class="icon16" src="<?php echo esc_url( admin_url('images/generic.png')); ?>" /> BRIDGE phpBB & WordPress</h2>
			<table class="form-table">
				<tr>
					<th>
						<label for="wp_phpbb_bridge"> <?php _e('Enable Bridge:', 'wp_phpbb3_bridge_options'); ?></label>
					</th>
					<td>
						<input type="radio" name="wp_phpbb_bridge" value="1" <?php echo (($active) ? 'id="wp_phpbb_bridge" checked="checked" ' : '') ?> /> <?php _e('Yes', 'wp_phpbb3_bridge_options'); ?>
						&nbsp;
						<input type="radio" name="wp_phpbb_bridge" value="0" <?php echo ((!$active) ? 'id="wp_phpbb_bridge" checked="checked" ' : '') ?> /> <?php _e('No', 'wp_phpbb3_bridge_options'); ?>
						<br />
						<span class="description"><?php _e('This will make the Bridge unavailable to use.', 'wp_phpbb3_bridge_options'); ?></span>
					</td>
				</tr>

				<tr>
					<th>
						<label for="phpbb_root_path"> <?php _e('Path to phpBB:', 'wp_phpbb3_bridge_options'); ?> (*)</label>
					</th>
					<td>
						<input type="text" name="phpbb_root_path" id="phpbb_root_path" style="width: 95%" value="<?php echo $phpbb_root_path; ?>" />
						<br />
						<span class="description"><?php _e('The path where phpBB is located <strong>relative</strong> to the domain name.', 'wp_phpbb3_bridge_options'); ?></span>
						<br />
						<?php _e('<b>Example :</b> <code>../phpBB/</code>&nbsp;<b>The Blog is at:</b> <code>http://www.mydomain.tld/wordpress/</code><b>The Forum is at:</b> <code>http://www.example.com/phpBB/</code>', 'wp_phpbb3_bridge_options'); ?>
					</td>
				</tr>

				<tr>
					<th>
						<label for="phpbb_script_path"> <?php _e('Server root path to phpBB:', 'wp_phpbb3_bridge_options'); ?> (*)</label>
					</th>
					<td>
						<input type="text" name="phpbb_script_path" id="phpbb_script_path" style="width: 95%" value="<?php echo $phpbb_script_path; ?>" />
						<br />&nbsp;
						<span class="description"><?php _e('Relative path from the server root.', 'wp_phpbb3_bridge_options'); ?></span>
						<br />
						<?php _e('<b>Example :</b> <code>phpBB/</code>&nbsp;<b>The Blog is at:</b> <code>http://www.mydomain.tld/wordpress/</code><b>The Forum is at:</b> <code>http://www.example.com/phpBB/</code>', 'wp_phpbb3_bridge_options'); ?>
					</td>
				</tr>

				<tr>
					<th>
						<label for="wordpress_script_path"> <?php _e('Server root path to WordPress:', 'wp_phpbb3_bridge_options'); ?> (*)</label>
					</th>
					<td>
						<input type="text" name="wordpress_script_path" id="wordpress_script_path" style="width: 95%" value="<?php echo $wordpress_script_path; ?>" />
						<br />
						<span class="description"><?php _e('Relative path from the server root.', 'wp_phpbb3_bridge_options'); ?></span>
						<br />
						<?php _e('<b>Example :</b> <code>wordpress/</code>&nbsp;<b>The Blog is at:</b> <code>http://www.mydomain.tld/wordpress/</code><b>The Forum is at:</b> <code>http://www.example.com/phpBB/</code>', 'wp_phpbb3_bridge_options'); ?>
					</td>
				</tr>

				<tr>
					<th>
						<label for="wp_phpbb_bridge_permissions_forum_id"> <?php _e('Permissions forum ID:', 'wp_phpbb3_bridge_options'); ?></label>
					</th>
					<td>
						<input type="text" name="wp_phpbb_bridge_permissions_forum_id" id="wp_phpbb_bridge_permissions_forum_id" value="<?php echo $wp_phpbb_bridge_permissions_forum_id; ?>" />
						<br />
						<span class="description"><?php _e('The number of your Forum (not Category) where to use permissions.', 'wp_phpbb3_bridge_options'); ?></span>
					</td>
				</tr>

				<tr>
					<th>
						<label for="wp_phpbb_bridge_post_forum_id"> <?php _e('Post forum ID:', 'wp_phpbb3_bridge_options'); ?></label>
					</th>
					<td>
						<input type="text" name="wp_phpbb_bridge_post_forum_id" id="wp_phpbb_bridge_post_forum_id" value="<?php echo $wp_phpbb_bridge_post_forum_id; ?>" />
						<br />
						<span class="description"><?php _e('The number of you forum where to post a new entry whenever is published in the Wordpress.', 'wp_phpbb3_bridge_options'); ?></span>
					</td>
				</tr>

				<tr>
					<th>
						<label for="wp_phpbb_bridge_widgets_column_width"> <?php _e('Widgets column width:', 'wp_phpbb3_bridge_options'); ?></label>
					</th>
					<td>
						<input type="text" name="wp_phpbb_bridge_widgets_column_width" id="wp_phpbb_bridge_widgets_column_width" value="<?php echo $wp_phpbb_bridge_widgets_column_width; ?>" />
						<br />
						<span class="description"><?php _e('The right column width, in pixels.', 'wp_phpbb3_bridge_options'); ?></span>
					</td>
				</tr>

				<tr>
					<th>
						<label for="wp_phpbb_bridge_comments_avatar_width"> <?php _e('Comments avatars width:', 'wp_phpbb3_bridge_options'); ?></label>
					</th>
					<td>
						<input type="text" name="wp_phpbb_bridge_comments_avatar_width" id="wp_phpbb_bridge_comments_avatar_width" value="<?php echo $wp_phpbb_bridge_comments_avatar_width; ?>" />
						<br />
						<span class="description"><?php _e('The width size of avatars in comments, in pixels.', 'wp_phpbb3_bridge_options'); ?></span>
					</td>
				</tr>

			</table>
			<?php submit_button(null, 'primary', 'submit');  ?>
		</form>
	</div>
	<div class="wrap">(*)
		<span class="description">
			If you are not sure what is the full path to your phpBB3 then create a file "ie: mypath.php" into the folder phpBB3 and enter the following code in it:
			 <code>echo $_SERVER['SCRIPT_FILENAME'];</code> You will get someting like this :<br /><code><?php echo $_SERVER['SCRIPT_FILENAME']; ?></code>
			 <br />
			 The above code will return to you the full path to phpBB3 + the file name "mypath.php".
			 <br />
			 Place the "mypath.php" with the "config.php" and then copy all that path to use it in the plugin configuration.
			 <br />
			 Hope this help you :)
		</span>
	</div>

<?php
	wp_phpbb3_bridge_template($active);
}

function wp_phpbb3_bridge_template($active = true)
{
	$error	 = false;
	$message = '';
	$action	 = '';
	$theme	 = strtolower(get_option('template'));

	if ($active)
	{
		if ($theme != 'prosilver')
		{
			$error = true;
			$message .= __('The "Prosilver" theme is deactivated', 'wp_phpbb3_bridge_options');

			if (current_user_can('switch_themes'))
			{
				$redir = admin_url('themes.php');
				$action .= '<a href="' . $redir . '" title="' . esc_attr__('Activate theme', 'wp_phpbb3_bridge_options') . '" style="color: #FFFFFF; font-weight: bold;">' . __('Activate theme', 'wp_phpbb3_bridge_options') . '</a>';
			}
			else
			{
				$action .= __('Please notify the system administrator or webmaster', 'wp_phpbb3_bridge_options');		
			}
		}	
	}
	else
	{
		if ($theme == 'prosilver')
		{
			$error = true;
			$message .= __('The "Prosilver" theme is activated', 'wp_phpbb3_bridge_options');

			if (current_user_can('switch_themes'))
			{
				$redir = admin_url('themes.php');
				$action .= '<a href="' . $redir . '" title="' . esc_attr__('Deactivate theme', 'wp_phpbb3_bridge_options') . '" style="color: #FFFFFF; font-weight: bold;">' . __('Deactivate theme', 'wp_phpbb3_bridge_options') . '</a>';
			}
			else
			{
				$action .= __('Please notify the system administrator or webmaster', 'wp_phpbb3_bridge_options');		
			}
		}		
	}

	if ($error)
	{
		echo '<div id="message" class="error fade" style="padding: .5em; background-color: #BC2A4D; color: #FFFFFF; font-weight: bold;">
			<p>' . $message . '<br />' . $action . '</p>
		</div>';
	}
}

function wp_phpbb3_bridge_check_path($var = '', $default = '', $file = '', $server_root = false)
{
	// We have to generate a full HTTP/1.1 header here since we can't guarantee to have any of the information
	// available as used by the redirect function
	$server_name = (!empty($_SERVER['HTTP_HOST'])) ? strtolower($_SERVER['HTTP_HOST']) : ((!empty($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME'] : getenv('SERVER_NAME'));
	$server_port = (!empty($_SERVER['SERVER_PORT'])) ? (int) $_SERVER['SERVER_PORT'] : (int) getenv('SERVER_PORT');
	$secure 	 = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 1 : 0;
	$script_path = (isset($_POST[$var]) && $_POST[$var]) ? trim($_POST[$var]) : $default;

	// Replace any number of consecutive backslashes and/or slashes with a single slash
	// (could happen on some proxy setups and/or Windows servers)
	$script_path = preg_replace('#[\\\\/]{2,}#', '/', $script_path);

	$url = (($secure) ? 'https://' : 'http://') . $server_name;

	if ($server_port && (($secure && $server_port <> 443) || (!$secure && $server_port <> 80)))
	{
		// HTTP HOST can carry a port number...
		if (strpos($server_name, ':') === false)
		{
			$url .= ':' . $server_port;
		}
	}

	// Add closing / if not present
	$script_path = ($script_path && substr($script_path, -1) != '/') ? $script_path . '/' : $script_path;

	$path = (($server_root) ? $_SERVER['DOCUMENT_ROOT'] . '/' : '../') . $script_path . $file;

	if (!file_exists($path))
	{
		return false;
	}

	return $script_path;
}

?>