<?php
/**
 * Easy WP Theme Plugin Switcher
 *
 * @since             1.0.0
 * @package           Easy_Theme_Plugin_Switcher
 * @author            Navanath Bhosale <navanath.bhosale95@gmail.com>
 * @link              #
 *
 * @wordpress-plugin
 * Plugin Name:       Easy Theme Plugin Switcher
 * Plugin URI:        https://wordpress.org/plugins/easy-theme-plugin-switcher/
 * Description:       Switch your WP themes, plugins with one click. The plugin will reduce your several clicks to one click for theme switching & plugins activations / deactivations.
 * Version:           1.0.0
 * Author:            Navanath Bhosale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       easy-wp-tp-switcher
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently active plugin file.
 */
define( 'EASY_THEME_PLUGIN_SWITCHER_FILE', __FILE__ );

/**
 * The core plugin class that is used to define admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'classes/class-easy-theme-plugin-switcher-loader.php';
