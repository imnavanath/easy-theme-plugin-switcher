<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.0.0
 *
 * @package    Easy_Theme_Plugin_Switcher
 * @subpackage Easy_Theme_Plugin_Switcher/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Easy_Theme_Plugin_Switcher
 * @subpackage Easy_Theme_Plugin_Switcher/classes
 * @author     Navanath Bhosale <navanath.bhosale95@gmail.com>
 */

/**
 * Class Easy_Theme_Plugin_Switcher_Loader.
 *
 * @since 1.0.0
 */
class Easy_Theme_Plugin_Switcher_Loader {

	/**
	 * Member Variable
	 *
	 * @var instance
	 */
    private static $instance = null;

    /**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->define_constants();

		// Activation hook.
		register_activation_hook( EASY_THEME_PLUGIN_SWITCHER_FILE, array( $this, 'activation_reset' ) );

		// deActivation hook.
		register_deactivation_hook( EASY_THEME_PLUGIN_SWITCHER_FILE, array( $this, 'deactivation_reset' ) );

		add_action( 'plugins_loaded', array( $this, 'load_plugin' ), 99 );

        add_action( 'admin_init', array( $this, 'init' ) );
    }

    /**
	 * Initialize update compatibility.
	 *
	 * @since x.x.x
	 * @return void
	 */
	public function init() {

		do_action( 'easy_wp_theme_plugin_switcher_before_update' );

		// Get auto saved version number.
		$saved_version = get_option( 'easy-wp-tp-switcher-version', false );

		// Update auto saved version number.
		if ( ! $saved_version ) {
			update_option( 'easy-wp-tp-switcher-version', EASY_THEME_PLUGIN_SWITCHER_VER );
			return;
		}

		// If equals then return.
		if ( version_compare( $saved_version, EASY_THEME_PLUGIN_SWITCHER_VER, '=' ) ) {
			return;
		}

		// Update auto saved version number.
		update_option( 'easy-wp-tp-switcher-version', EASY_THEME_PLUGIN_SWITCHER_VER );

		do_action( 'easy_wp_theme_plugin_switcher_after_update' );
    }

    /**
	 * Defines all constants
	 *
	 * @since 1.0.0
	 */
	public function define_constants() {
		define( 'EASY_THEME_PLUGIN_SWITCHER_VER', '1.0.0' );
		define( 'EASY_THEME_PLUGIN_SWITCHER_BASE', plugin_basename( EASY_THEME_PLUGIN_SWITCHER_FILE ) );
		define( 'EASY_THEME_PLUGIN_SWITCHER_ROOT', dirname( EASY_THEME_PLUGIN_SWITCHER_BASE ) );
		define( 'EASY_THEME_PLUGIN_SWITCHER_DIR', plugin_dir_path( EASY_THEME_PLUGIN_SWITCHER_FILE ) );
		define( 'EASY_THEME_PLUGIN_SWITCHER_URL', plugins_url( '/', EASY_THEME_PLUGIN_SWITCHER_FILE ) );
	}
    
    /**
	 * Loads plugin files.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	function load_plugin() {

        // Load textdomain for internationalization.
        $this->load_textdomain();

        // Load plugin core files.
		$this->load_core_files();
    }

    /**
	 * Load Core Files for Easy WP Theme Plugin Switcher.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_core_files() {

        include_once EASY_THEME_PLUGIN_SWITCHER_DIR . 'classes/class-wp-tp-switcher-config.php';   
    }

    /**
	 * Load Easy WP Theme Plugin Switcher Text Domain.
	 * This will load the translation textdomain depending on the file priorities.
	 *      1. Global Languages /wp-content/languages/easy-theme-plugin-switcher/ folder
	 *      2. Local dorectory /wp-content/plugins/easy-theme-plugin-switcher/languages/ folder
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function load_textdomain() {
		/**
		 * Filters the languages directory path to use for Easy WP Theme Plugin Switcher.
		 *
		 * @param string $lang_dir The languages directory path.
		 */
		$lang_dir = apply_filters( 'wp_widget_styler_domain_loader', EASY_THEME_PLUGIN_SWITCHER_ROOT . '/languages/' );
		load_plugin_textdomain( 'easy-wp-tp-switcher', false, $lang_dir );
    }
    
    /**
	 * Activation Reset
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function activation_reset() { }

	/**
	 * Deactivation Reset
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function deactivation_reset() { }
}

new Easy_Theme_Plugin_Switcher_Loader();
