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
use function wp_dequeue_style;
use function get_theme_file_uri;
use function get_theme_file_path;

/**
 * Class for customising Default Login Screen.
 *
 * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/
 */
class Component implements Component_Interface {

    private $action_type;
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
        add_action( "login_form_login", [$this, 'startOutputBufferingActionLogin' ], 10, 0 );
        add_action( "login_form_lostpassword", [$this, 'startOutputBufferingActionLostPassword' ], 10, 0 );
        add_action( "login_form_retrievepassword", [$this, 'startOutputBufferingActionRetrievePassword' ], 10, 0 );
        add_action( "login_form_register", [$this, 'startOutputBufferingActionRegister' ], 10, 0 );
        add_action( 'login_footer', [ $this, 'endPageOutputBuffering' ] );

        add_filter( 'enable_login_autofocus', '__return_false' );

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
     * Alter Output of the page where login_form_($action) = login
     */
    public function startOutputBufferingActionLogin() {
        $this->action_type = 'login';
        ob_start();
    }

    /**
     * Alter Output of the page where login_form_($action) = lostpassword
     */
    public function startOutputBufferingActionLostPassword() {
        $this->action_type = 'lostpassword';
        ob_start();
    }

    /**
     * Alter Output of the page where login_form_($action) = retrievepassword
     */
    public function startOutputBufferingActionRetrievePassword() {
        $this->action_type = 'retrievepassword';
        ob_start();
    }

    /**
     * Alter Output of the page where login_form_($action) = register
     */
    public function startOutputBufferingActionRegister() {
        $this->action_type = 'register';
        ob_start();
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
     * End Output beffering for the whole login page
     *
     * @return void
     */
    public function endPageOutputBuffering()
    {

        if( 'login' === $this->action_type ) {

            $buffer = ob_get_clean();
            $buffer_new = substr( $buffer, 0, strpos($buffer, "<div"));
            $buffer_new .= include( dirname(__DIR__) . '/Helpers_Login/login-form.php' );

            echo $buffer_new;
        } elseif( 'lostpassword' === $this->action_type || 'retrievepassword' === $this->action_type ) {

            $buffer = ob_get_clean();
            $buffer_new = substr( $buffer, 0, strpos($buffer, "<div"));
            $buffer_new .= include( dirname(__DIR__) . '/Helpers_Login/lostpassword-form.php' );

            echo $buffer_new;
        } elseif( 'register' === $this->action_type ) {

            $buffer = ob_get_clean();
            $buffer_new = substr( $buffer, 0, strpos($buffer, "<div"));
            $buffer_new .= include( dirname(__DIR__) . '/Helpers_Login/register-form.php' );

            echo $buffer_new;
        }
    }
}
