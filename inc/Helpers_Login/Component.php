<?php
/**
 * Logger\Helpers_Login\Component class
 *
 * @since 1.0.0
 *
 * @package Logger
 */

namespace Logger\Helpers_Login;

use Logger\Component_Interface;
use function Logger\Logger;
use function add_action;
use function add_filter;
use function wp_enqueue_style;
use function get_template_directory_uri;
use function get_theme_file_uri;
use function get_theme_file_path;
use function home_url;
use function get_option;
use function DOMDocument;
use function get_template_part;

/**
 * Class for customising Default Login Screen.
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
        return 'helpers_login';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'login_enqueue_scripts', [ $this, 'LoggerEnqueueLoginCss' ], 10 );
        add_filter( 'login_headerurl', [ $this, 'LoggerChangeLoginLogoUrl' ] );
        add_filter( 'login_headertext', [ $this, 'LoggerChangeLoginLogoAltText' ] );

        add_action( 'login_header', [ $this, 'startPageOutputBuffering' ] );
        add_action( 'login_footer', [ $this, 'endPageOutputBuffering' ] );

        // add_filter( 'enable_login_autofocus', '__return_false' );

        add_filter(
            'login_errors',
            function ( $a ) {
                return null;
            }
        );
    }

    public function write_log($log) {
        if (is_array($log) || is_object($log)) {
        error_log(print_r($log, true));
        } else {
        error_log($log);
        }
    }

    /**
     * Enqueue login css
     */
    public function LoggerEnqueueLoginCss()
    {
        $css_uri = get_theme_file_uri( '/assets/dist/styles/' );
        $css_dir = get_theme_file_path( '/assets/dist/styles/' );

        $css_files = [
            'Logger-login-screen' => [
                'file'   => 'login.css',
            ],
        ];
        foreach ( $css_files as $handle => $data ) {
            $src     = $css_uri . $data['file'];
            $version = Logger()->getAssetVersion( $css_dir . $data['file'] );

            wp_enqueue_style( $handle, $src, [], $version, false );
        }

        wp_dequeue_style( 'login' );
    }

    /**
     * Changing the logo link from wordpress.org to this site.
     */
    public function LoggerChangeLoginLogoUrl()
    {
        return home_url();
    }

    /**
     * Changing the alt text on the logo to show your site name
     */
    public function LoggerChangeLoginLogoAltText()
    {
        return get_option( 'blogname' );
    }

    /**
     * Alter Output of the page
     *
     * @param string $buffer Page content as a string.
     *
     * @return $buffer
     */
    public function callback( $buffer )
    {

        // $buffer = Logger()->sanitizeOutput( $buffer );


        $buffer = include( dirname(__DIR__) . '/Helpers_Login/login-form.php' );

        $this->write_log($test);
        // $buffer = preg_replace( '/<p>(.*?)<\/p>/', '<div class="form-field">$1</div>', $buffer );
        // $buffer = preg_replace( '/<p class="(.*?)">(.*?)<\/p>/', '<div class="$1">$2</div>', $buffer );
        // $buffer = str_replace( '<br />', '', $buffer );

        // $buffer = preg_replace(
        //     '/<div class="form-field"><label for="(.*?)">(.*?)<input (.*?)><\/label><\/div>/',
        //     '<div class="form-field"><input $3 placeholder="$2" \/><label for="$1">$2<\/label></div>',
        //     $buffer
        // );

        // $dom = new \DOMDocument( '1.0', 'UTF-8' );
        // $internalErrors = libxml_use_internal_errors( true );
        // $dom->loadHTML( $buffer );
        // $dom->preserveWhiteSpace = false; //phpcs:ignore
        // $dom->loadHTML( $buffer );
        // $dom->formatOutput = true; //phpcs:ignore
        // libxml_use_internal_errors( $internalErrors );
        // $buffer = $dom->saveXML( $dom->documentElement ); //phpcs:ignore

        return $buffer;
    }

    /**
     * Inititate Output buffering for the whole page.
     *
     * @return void
     */
    public function startPageOutputBuffering()
    {
        ob_start( array( 'self', 'callback' ) );
    }

    /**
     * End Output beffering for the whole page
     *
     * @return void
     */
    public function endPageOutputBuffering()
    {
        ob_end_flush();
    }


}
