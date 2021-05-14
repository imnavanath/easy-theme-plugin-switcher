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
 * This is used to define markup, configurations, admin-specific hooks, and
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
 * Class Easy_Theme_Plugin_Switcher_Markup.
 *
 * @since 1.0.0
 */
class Easy_WP_TP_Switcher_Config {

    /**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

        // Register required Plugin AJAX actions.
        add_action( 'wp_ajax_easy_wp_plugin_activate', array( $this, 'single_plugin_activate' ) );
        add_action( 'wp_ajax_easy_wp_plugin_deactivate', array( $this, 'single_plugin_deactivate' ) );

        // Register required Theme AJAX action.
        add_action( 'wp_ajax_easy_wp_theme_activate', array( $this, 'required_theme_activate' ) );

        // Register required Selected Plugins AJAX actions.
        add_action( 'wp_ajax_easy_wp_selected_plugins_activate', array( $this, 'selected_plugins_activate' ) );
        add_action( 'wp_ajax_easy_wp_selected_plugins_deactivate', array( $this, 'selected_plugins_deactivate' ) );

        // Register required All Plugins AJAX actions.
        add_action( 'wp_ajax_easy_wp_all_plugins_activate', array( $this, 'all_plugins_activate' ) );
        add_action( 'wp_ajax_easy_wp_all_plugins_deactivate', array( $this, 'all_plugins_deactivate' ) );

        // Load admin styles & scripts.
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

        // Let's create switcher on admin screen.
        add_action( 'admin_footer', array( $this, 'load_easy_wp_tp_switcher' ) );
    }

    /**
     * Create WP_THEME_PLUGIN switcher.
     *
     * @since 1.0.0
     */
    public function load_easy_wp_tp_switcher() {

        $html = '';

        $active_theme = get_template();
        $installed_themes = wp_get_themes();

        $html .= '<div id="wp-switcher-sidenav" class="wp-tp-sidenav">';

        $html .= '<ul class="asstes-tab">';

            $html .= '<li data-wrapper="' . esc_html( 'wp-theme-wrapper' ) . '" data-role="' . esc_html( 'themes' ) . '" class="asstes-tab active-tab">' . esc_html( 'Themes', 'easy-wp-tp-switcher' ) . '</li>';

            $html .= '<li data-wrapper="' . esc_html( 'wp-plugin-wrapper' ) . '" data-role="' . esc_html( 'plugins' ) . '" class="asstes-tab">' . esc_html( 'Plugins', 'easy-wp-tp-switcher' ) . '</li>';

        $html .= '</ul>';

        if( is_array( $installed_themes ) && ! empty( $installed_themes ) ) {

            $html .= '<div class="wp-theme-wrapper active-asset">';
    
            foreach ( $installed_themes as $theme => $data ) {

                $theme_name = str_replace( '-', ' ', $theme );
                $html .= '<div class="wp-single-theme">';
                $html .= '<label for="' . esc_attr( $theme ) . '"> ' . ucwords( $theme_name ) . ' </label>';

                if( isset( $data->template ) && $data->template !== $active_theme ) {
                    $html .= '<span data-template="' . esc_attr( $data->template ) . '" data-stylesheet="' . esc_attr( $data->stylesheet ) . '" class="active-theme theme-action"> ' . esc_html( 'Activate', 'easy-wp-tp-switcher' ) . ' </span>';
                }

                $html .= '</div>';
            }

            $html .= '</div>';
        }

        // Ensure get_plugin_data function is loaded.
        if ( ! function_exists( 'get_plugin_data' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $installed_plugins = get_plugins();

        if( is_array( $installed_plugins ) && ! empty( $installed_plugins ) ) {

            $html .= '<div class="wp-plugin-wrapper">';

            foreach ( $installed_plugins as $plugin_path => $data ) {

                $html .= '<div class="wp-single-plugin"> <span> <input type="checkbox" data-slug="' . $plugin_path . '" id="' . esc_attr( $data['TextDomain'] ) . '" name="' . esc_attr( $data['TextDomain'] ) . '"/> </span>';
                $html .= '<label for="' . esc_attr( $data['TextDomain'] ) . '"> ' . ucwords( $data['Name'] ) . ' </label>';

                $plug_init = explode( "/", $plugin_path, 2 );

                if( is_plugin_active( $plugin_path ) ) {
                    $html .= '<span data-action="deactivate-plugin" data-slug="' . $plugin_path . '" data-init="' . $plug_init[0] . '" class="plugin-action deactivate-plugin"> ' . esc_html( 'Deactivate', 'easy-wp-tp-switcher' ) . ' </span>';
                } else {
                    $html .= '<span data-action="activate-plugin" data-slug="' . $plugin_path . '" data-init="' . $plug_init[0] . '" class="plugin-action activate-plugin"> ' . esc_html( 'Activate', 'easy-wp-tp-switcher' ) . ' </span>';
                }

                $html .= '</div>';
            }

            $html .= '<hr class="wp-assets-divider"/>';		

            $html .= '<div class="plugins-selection-wrapper"> <span data-action="" class="plugin-bulk-action all-activate-plugins"> ' . esc_html( 'Activate All', 'easy-wp-tp-switcher' ) . ' </span>';

            $html .= '<span data-action="" class="plugin-bulk-action selected-activate-plugins"> ' . esc_html( 'Activate Selected', 'easy-wp-tp-switcher' ) . ' </span> </div>';

            $html .= '<div class="plugins-all-wrapper"> <span data-action="" class="plugin-bulk-action all-deactivate-plugins"> ' . esc_html( 'Deactivate All', 'easy-wp-tp-switcher' ) . ' </span>';

            $html .= ' <span data-action="" class="plugin-bulk-action selected-deactivate-plugins"> ' . esc_html( 'Deactivate Selected', 'easy-wp-tp-switcher' ) . ' </span> </div>';

            $html .= '</div>';
        }

        $html .= '</div>';

        $html .= '<div id="wp-switcher-toggle-wrap"> <span class="load_switcher">&#9776;</span> </div>';

        echo $html;
    }

    /**
     * Load admin scripts.
     *
     * @since 1.0.0
     */
    public function enqueue_admin_scripts() {

        wp_enqueue_script( 'easy-wp-tp-switcher-script', EASY_THEME_PLUGIN_SWITCHER_URL . 'assets/js/script.js', array( 'jquery' ), EASY_THEME_PLUGIN_SWITCHER_VER, false );

        $options = array(
            'ajaxurl'                    => admin_url( 'admin-ajax.php' ),
            'switcher_nonce'             => wp_create_nonce( 'wp-switcher-manager-nonce' ),
            'switcher_activate_text'     => __( 'Activate', 'easy-wp-tp-switcher' ),
            'switcher_deactivate_text'   => __( 'Deactivate', 'easy-wp-tp-switcher' ),
            'switcher_all_activate_text'     => __( 'Activate All', 'easy-wp-tp-switcher' ),
            'switcher_all_deactivate_text'     => __( 'Deactivate All', 'easy-wp-tp-switcher' ),
            'switcher_selected_activate_text'     => __( 'Activate Selected', 'easy-wp-tp-switcher' ),
            'switcher_selected_deactivate_text'     => __( 'Deactivate Selected', 'easy-wp-tp-switcher' ),
            'switcher_activating_text'   => __( 'Activating', 'easy-wp-tp-switcher' ),
            'switcher_deactivating_text' => __( 'Deactivating', 'easy-wp-tp-switcher' ),
            'switcher_failed_text'       => __( 'Failed', 'easy-wp-tp-switcher' ),
        );

        wp_localize_script( 'easy-wp-tp-switcher-script', 'SwitcherLocalizer', $options );

        wp_enqueue_style( 'easy-wp-tp-switcher-admin', EASY_THEME_PLUGIN_SWITCHER_URL . 'assets/css/style.css', false, EASY_THEME_PLUGIN_SWITCHER_VER, 'all' );
    }

    /**
     * Required Theme Switcher
     *
     * @since 1.0.0
     */
    public function required_theme_activate() {

        $nonce = ( isset( $_POST['nonce'] ) ) ? sanitize_key( $_POST['nonce'] ) : '';

        if ( false === wp_verify_nonce( $nonce, 'wp-switcher-manager-nonce' ) ) {
            wp_send_json_error( esc_html_e( 'WordPress Nonce not validated.', 'easy-wp-tp-switcher' ) );
        }

        if ( ! current_user_can( 'switch_themes' ) || ! isset( $_POST['init'] ) || ! sanitize_text_field( wp_unslash( $_POST['init'] ) ) ) {
            wp_send_json_error(
                array(
                    'success' => false,
                    'message' => __( 'No theme specified', 'easy-wp-tp-switcher' ),
                )
            );
        }

        $theme_stylesheet = ( isset( $_POST['init'] ) ) ? sanitize_text_field( wp_unslash( $_POST['init'] ) ) : '';

        $switch_theme = switch_theme( $theme_stylesheet );

        if ( is_wp_error( $switch_theme ) ) {
            wp_send_json_error(
                array(
                    'success'  => false,
                    'message'  => $switch_theme->get_error_message(),
                )
            );
        }

        wp_send_json_success(
            array(
                'success'      => true,
                'message'      => __( 'Theme Successfully Activated', 'easy-wp-tp-switcher' ),
            )
        );
    }

    /**
     * Required Plugin Activate
     *
     * @since 1.0.0
     */
    public function single_plugin_activate() {

        $nonce = ( isset( $_POST['nonce'] ) ) ? sanitize_key( $_POST['nonce'] ) : '';

        if ( false === wp_verify_nonce( $nonce, 'wp-switcher-manager-nonce' ) ) {
            wp_send_json_error( esc_html_e( 'WordPress Nonce not validated.', 'easy-wp-tp-switcher' ) );
        }

        if ( ! current_user_can( 'install_plugins' ) || ! isset( $_POST['init'] ) || ! sanitize_text_field( wp_unslash( $_POST['init'] ) ) ) {
            wp_send_json_error(
                array(
                    'success' => false,
                    'message' => __( 'No plugin specified', 'easy-wp-tp-switcher' ),
                )
            );
        }

        $plugin_init = ( isset( $_POST['init'] ) ) ? sanitize_text_field( wp_unslash( $_POST['init'] ) ) : '';

        $activate = activate_plugin( $plugin_init, '', false, true );

        if ( is_wp_error( $activate ) ) {
            wp_send_json_error(
                array(
                    'success'               => false,
                    'message'               => $activate->get_error_message(),
                )
            );
        }

        wp_send_json_success(
            array(
                'success'               => true,
                'message'               => __( 'Plugin Successfully Activated', 'easy-wp-tp-switcher' ),
            )
        );
    }

    /**
     * Required Plugin Activate
     *
     * @since 1.0.0
     */
    public function single_plugin_deactivate() {

        $nonce = ( isset( $_POST['nonce'] ) ) ? sanitize_key( $_POST['nonce'] ) : '';

        if ( false === wp_verify_nonce( $nonce, 'wp-switcher-manager-nonce' ) ) {
            wp_send_json_error( esc_html_e( 'WordPress Nonce not validated.', 'easy-wp-tp-switcher' ) );
        }

        if ( ! current_user_can( 'install_plugins' ) || ! isset( $_POST['init'] ) || ! sanitize_text_field( wp_unslash( $_POST['init'] ) ) ) {
            wp_send_json_error(
                array(
                    'success' => false,
                    'message' => __( 'No plugin specified', 'easy-wp-tp-switcher' ),
                )
            );
        }

        $plugin_init = ( isset( $_POST['init'] ) ) ? sanitize_text_field( wp_unslash( $_POST['init'] ) ) : '';

        $deactivate = deactivate_plugins( $plugin_init, '', false );

        if ( is_wp_error( $deactivate ) ) {
            wp_send_json_error(
                array(
                    'success' => false,
                    'message' => $deactivate->get_error_message(),
                )
            );
        }

        wp_send_json_success(
            array(
                'success' => true,
                'message' => __( 'Plugin Successfully Deactivated', 'easy-wp-tp-switcher' ),
            )
        );
    }

    /**
     * Selected Plugins Activate
     *
     * @since 1.0.0
     */
    public function selected_plugins_activate() {

        $nonce = ( isset( $_POST['nonce'] ) ) ? sanitize_key( $_POST['nonce'] ) : '';

        if ( false === wp_verify_nonce( $nonce, 'wp-switcher-manager-nonce' ) ) {
            wp_send_json_error( esc_html_e( 'WordPress Nonce not validated.', 'easy-wp-tp-switcher' ) );
        }

        if ( ! current_user_can( 'install_plugins' ) || ! is_array( $_POST['init'] ) ) {
            wp_send_json_error(
                array(
                    'success' => false,
                    'message' => __( 'No plugins specified', 'easy-wp-tp-switcher' ),
                )
            );
        }

        $selected_plugins = ( is_array( $_POST['init'] ) && ! empty( $_POST['init'] ) ) ? $_POST['init'] : '';

        $error_message = '';
        $break_activation = false;

        foreach ( $selected_plugins as $key => $plugin ) {

            $activate = activate_plugin( $plugin, '', false, true );

            if ( is_wp_error( $activate ) ) {
                $break_activation = true;
                $error_message = $activate->get_error_message();
            }
        }

        if ( $break_activation ) {
            wp_send_json_error(
                array(
                    'success'               => false,
                    'message'               => $error_message,
                )
            );
        }

        wp_send_json_success(
            array(
                'success'               => true,
                'message'               => __( 'Plugins Successfully Activated', 'easy-wp-tp-switcher' ),
            )
        );
    }

    /**
     * Selected Plugins Deactivate
     *
     * @since 1.0.0
     */
    public function selected_plugins_deactivate() {

        $nonce = ( isset( $_POST['nonce'] ) ) ? sanitize_key( $_POST['nonce'] ) : '';

        if ( false === wp_verify_nonce( $nonce, 'wp-switcher-manager-nonce' ) ) {
            wp_send_json_error( esc_html_e( 'WordPress Nonce not validated.', 'easy-wp-tp-switcher' ) );
        }

        if ( ! current_user_can( 'install_plugins' ) || ! is_array( $_POST['init'] ) ) {
            wp_send_json_error(
                array(
                    'success' => false,
                    'message' => __( 'No plugins specified', 'easy-wp-tp-switcher' ),
                )
            );
        }

        $selected_plugins = ( is_array( $_POST['init'] ) && ! empty( $_POST['init'] ) ) ? $_POST['init'] : '';

        $deactivate = deactivate_plugins( $selected_plugins, '', false );

        if ( is_wp_error( $deactivate ) ) {
            wp_send_json_error(
                array(
                    'success' => false,
                    'message' => $deactivate->get_error_message(),
                )
            );
        }

        wp_send_json_success(
            array(
                'success'               => true,
                'message'               => __( 'Plugins Successfully Deactivated', 'easy-wp-tp-switcher' ),
            )
        );
    }

    /**
     * All Plugins Activate
     *
     * @since 1.0.0
     */
    public function all_plugins_activate() {

        $nonce = ( isset( $_POST['nonce'] ) ) ? sanitize_key( $_POST['nonce'] ) : '';

        if ( false === wp_verify_nonce( $nonce, 'wp-switcher-manager-nonce' ) ) {
            wp_send_json_error( esc_html_e( 'WordPress Nonce not validated.', 'easy-wp-tp-switcher' ) );
        }

        if ( ! current_user_can( 'install_plugins' ) ) {
            wp_send_json_error(
                array(
                    'success' => false,
                    'message' => __( 'No plugins specified', 'easy-wp-tp-switcher' ),
                )
            );
        }

        $error_message = '';
        $break_activation = false;
        $installed_plugins = get_plugins();

        if( is_array( $installed_plugins ) && ! empty( $installed_plugins ) ) {

            foreach ( $installed_plugins as $plugin_path => $data ) {

                $activate = activate_plugin( $plugin_path, '', false, true );

                if ( is_wp_error( $activate ) ) {
                    $break_activation = true;
                    $error_message = $activate->get_error_message();
                }
            }
        }

        if ( $break_activation ) {
            wp_send_json_error(
                array(
                    'success'               => false,
                    'message'               => $error_message,
                )
            );
        }

        wp_send_json_success(
            array(
                'success'               => true,
                'message'               => __( 'Plugins Successfully Activated', 'easy-wp-tp-switcher' ),
            )
        );        
    }

    /**
     * All Plugins Deactivate
     *
     * @since 1.0.0
     */
    public function all_plugins_deactivate() {

        $nonce = ( isset( $_POST['nonce'] ) ) ? sanitize_key( $_POST['nonce'] ) : '';

        if ( false === wp_verify_nonce( $nonce, 'wp-switcher-manager-nonce' ) ) {
            wp_send_json_error( esc_html_e( 'WordPress Nonce not validated.', 'easy-wp-tp-switcher' ) );
        }

        if ( ! current_user_can( 'install_plugins' ) ) {
            wp_send_json_error(
                array(
                    'success' => false,
                    'message' => __( 'No plugins specified', 'easy-wp-tp-switcher' ),
                )
            );
        }

        $error_message = '';
        $break_activation = false;
        $installed_plugins = get_plugins();
        $selected_plugins = array();

        if( is_array( $installed_plugins ) && ! empty( $installed_plugins ) ) {

            foreach ( $installed_plugins as $plugin_path => $data ) {

                $selected_plugins[] = $plugin_path;
            }
        }

        $deactivate = deactivate_plugins( $selected_plugins, '', false );

        if ( is_wp_error( $deactivate ) ) {
            wp_send_json_error(
                array(
                    'success' => false,
                    'message' => $deactivate->get_error_message(),
                )
            );
        }

        wp_send_json_success(
            array(
                'success'               => true,
                'message'               => __( 'Plugins Successfully Deactivated', 'easy-wp-tp-switcher' ),
            )
        );
    }
}

new Easy_WP_TP_Switcher_Config();
