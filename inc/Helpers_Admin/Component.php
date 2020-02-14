<?php
/**
 * Logger\Helpers_Admin\Component class
 *
 * @since 1.0.0
 *
 * @package Logger
 */

namespace Logger\Helpers_Admin;

use Logger\Component_Interface;
use function Logger\logger;
use function add_action;
use function add_filter;
use function wp_enqueue_style;
use function get_template_directory_uri;
use function get_theme_file_uri;
use function get_theme_file_path;
use function remove_meta_box;

/**
 * Class for customising the Admin Dashboard.
 *
 * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/
 */
class Component implements Component_Interface {

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'helpers_admin';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'admin_menu', [ $this, 'loggerDisableDefaultDashboardWidgets' ] );
        add_filter( 'admin_footer_text', [ $this, 'loggerCustomAdminFooter' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'loggerEnqueueAdminCss' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueueFontAwesome' ] );

    }

    /**
     * DASHBOARD WIDGETS
     *
     * Disable default dashboard widgets
     */
    public function loggerDisableDefaultDashboardWidgets()
    {
        remove_meta_box( 'dashboard_right_now', 'dashboard', 'core' );       // Right Now Widget.
        remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'core' ); // Comments Widget.
        remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'core' );  // Incoming Links Widget.
        remove_meta_box( 'dashboard_plugins', 'dashboard', 'core' );         // Plugins Widget.

        remove_meta_box( 'dashboard_quick_press', 'dashboard', 'core' );     // Quick Press Widget.
        remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'core' );   // Recent Drafts Widget.
        remove_meta_box( 'dashboard_primary', 'dashboard', 'core' );         // Remove Primary Dachboard.
        remove_meta_box( 'dashboard_secondary', 'dashboard', 'core' );       // Remove Secondary Dachboard.
    }

    /**
     * Customise Admin Footer
     */
    public function loggerCustomAdminFooter()
    {
        echo '<span id="footer-thankyou">Built with <span class="heart" style="color: red; margin-right: 5px; font-size: 16px;">&#9829;</span> using the <strong>Logger</strong> framework by <a href="https://www.orbital.co.uk" class="orbital" style="font-weight: 900; color: #FF6600; font-style: initial; font-family: helvetica;" target="_blank">Orbital</a></span> in sunny Bournemouth.';
    }

    /**
     * Enqueue login css
     */
    public function loggerEnqueueAdminCss()
    {
        $css_uri = get_theme_file_uri( '/assets/dist/styles/' );
        $css_dir = get_theme_file_path( '/assets/dist/styles/' );

        $css_files = [
            'logger-admin-screen' => [
                'file'   => 'admin.css',
            ],
        ];
        foreach ( $css_files as $handle => $data ) {
            $src     = $css_uri . $data['file'];
            $version = logger()->getAssetVersion( $css_dir . $data['file'] );

            wp_enqueue_style( $handle, $src, [], $version, false );
        }
    }

    /**
     * Enqueue Font Awesome
     *
     * @return void
     */
    public function enqueueFontAwesome()
    {
        $logger_fa_toggle = get_theme_mod( 'logger_toggle_font_awesome' );
        $logger_fa_url    = get_theme_mod( 'logger_url_font_awesome' );
        if ( 1 === $logger_fa_toggle || ! empty( $logger_fa_url ) ) {
            wp_enqueue_script( 'font-awesome', esc_url( $logger_fa_url ), array(), '5.9.8', 'true' );
        }
    }
}
