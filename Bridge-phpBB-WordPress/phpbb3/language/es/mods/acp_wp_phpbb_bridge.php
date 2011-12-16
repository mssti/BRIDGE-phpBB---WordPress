<?php
/**
 * 
 * @package: phpBB 3.0.9 :: BRIDGE phpBB & WordPress -> root/language/es/mods :: [es][Spanish]
 * @version: $Id: acp_wp_phpbb_bridge.php, v0.0.9 2011/10/25 11:10:25 leviatan21 Exp $
 * @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
 * @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
 * 
 */

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. "Message %d" is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., "Click %sHERE%s" is fine
// Reference : http://www.phpbb.com/mods/documentation/phpbb-documentation/language/index.php#lang-use-php
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'WP_PHPBB_BRIDGE_MANAGE'			=> 'Configuración PUENTE phpBB & WordPress',
	'WP_PHPBB_BRIDGE_MANAGE_EXPLAIN'	=> 'Bienvenido a la sección de configuración de el PUENTE phpBB y WordPress.<br />Aquí puede determinar el funcionamiento básico del Puente en relación con phpBB',

	'WP_PHPBB_BRIDGE_MISSING_FILE'		=> 'El módulo no está disponible para ajustes. Falta el archivo',
	'WP_PHPBB_BRIDGE_VERSION'			=> 'BRIDGE versión',
	'WP_PHPBB_BRIDGE_NOT_UP_TO_DATE'	=> 'Su versiín del <strong>PUENTE phpBB & WordPress</strong> no está actualizada, Haga click %sAQUI%s para leer acerca de la nueva versión %s.',

	'WP_PHPBB_BRIDGE_BASIC'				=> 'Configuración básica',
//	'WP_PHPBB_BRIDGE_DISABLE'					=> '',
//	'WP_PHPBB_BRIDGE_DISABLE_EXPLAIN'			=> '',
	'WP_PHPBB_BRIDGE_WORDPRESS_PATH'			=> 'Ruta a WordPress',
	'WP_PHPBB_BRIDGE_WORDPRESS_PATH_EXPLAIN'	=> 'La ruta dónde está ubicado WordPress, relativo al directorio raíz de su foro phpBB.<br />Por ejemplo: <samp>../wordpress/</samp>.',

	'WP_PHPBB_BRIDGE_POST'				=> 'Publicación cruzada',
	'WP_PHPBB_BRIDGE_POST_DISABLE'				=> 'Mostrar Publicación cruzada al crear un tema',
	'WP_PHPBB_BRIDGE_POST_DISABLE_EXPLAIN'		=> 'Los usuarios con permiso para hacer temas Anuncios, tiene la opción para seleccionar publicarlo en WordPress, al mismo tiempo.',

	'WP_PHPBB_BRIDGE_PORTAL'			=> 'Entradas recientes en WordPress',
	'WP_PHPBB_BRIDGE_PORTAL_DISABLE'			=> 'Mostrar el portal de WordPress en la página índice del foro',
	'WP_PHPBB_BRIDGE_PORTAL_DISABLE_EXPLAIN'	=> 'Si está desactivado el listado de blog no se mostrará.',
	'WP_PHPBB_BRIDGE_PORTAL_TITLE'				=> 'Título del portal de WordPress',
	'WP_PHPBB_BRIDGE_PORTAL_TITLE_EXPLAIN'		=> 'Un nombre que se utilizará para el <strong>bloque WordPress</strong>',
	'WP_PHPBB_BRIDGE_PORTAL_STYLE'				=> 'Estilo del Bloque',
	'WP_PHPBB_BRIDGE_PORTAL_STYLE_EXPLAIN'		=> 'Seleccione como se verá el <strong>bloque WordPress block</strong>',
	'WP_PHPBB_BRIDGE_PORTAL_LIMIT'				=> 'Número de artículos',
	'WP_PHPBB_BRIDGE_PORTAL_LIMIT_EXPLAIN'		=> 'El número máximo de entradas de WordPress para mostrar.',
	'WP_PHPBB_BRIDGE_PORTAL_BLOCK_STYLE'		=> 'Como Temas',
	'WP_PHPBB_BRIDGE_PORTAL_BLOCK_COMPACT'		=> 'Como lista',
	'WP_PHPBB_BRIDGE_PORTAL_BLOCK_POST'			=> 'Como mensajes ( Viendo la parte del texto, hasta <samp>&lt;!--more--&gt;</samp> )',
));

?>