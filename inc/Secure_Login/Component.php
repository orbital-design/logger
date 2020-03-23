<?php
/**
 * Logger\Secure_Login\Component class
 *
 * @since 1.0.0
 *
 * @package Logger
 */

namespace Logger\Secure_Login;

use Logger\Component_Interface;
use function add_action;
use function remove_action;
use function add_filter;
use function remove_filter;

// TODO: Update Code comment below.
/**
 * Class for renaming wp-login.php.
 *
 * Adds actions to:
 * * `init`
 *
 * Removes Actions from:
 * * `admin_print_styles`
 * * `wp_head`
 * * `admin_print_scripts`
 * * `wp_print_styles`
 * * `wp_mail`
 * * `the_content_feed`
 * * `comment_text_rss`
 *
 * Manipulates Filters:
 * * `tiny_mce_plugins`
 */
class Component implements Component_Interface {

    /**
     * @var string
     */
    private $login_path = 'bridge/access';

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'secure_login';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        // TODO: Add a redirect for wp-admin from /access if user is logged in.



        $this->block_default_login_access();

        // If URL is correct, load in the wp-login.php.
        add_action( 'wp_loaded', [ $this, 'load_wp_login' ] );

        // If something else includes wp-login.php, redirect that too.
        add_action( 'login_init', [ $this, 'redirect_login_form_action' ], 0 );

        // Make sure that wp-login.php is never used in site urls or redirects.
        add_filter( 'site_url', [ $this, 'check_for_default_wp_login' ], 20, 1 );
        add_filter( 'network_site_url', [ $this, 'check_for_default_wp_login' ], 20, 1 );
        add_filter( 'wp_redirect', [ $this, 'check_for_default_wp_login' ], 20, 1 );

        if ( ! is_user_logged_in() ) {
            add_filter( 'wp_redirect', [ $this, 'protect_unauthorised_login_redirect' ], 50, 1 );
        }
        add_filter( 'register_url', [ $this, 'block_register_url_redirect' ], 20, 1 );
    }

    public function write_log($log) {
        if (is_array($log) || is_object($log)) {
        error_log(print_r($log, true));
        } else {
        error_log($log);
        }
    }

    /**
     */
    public function block_default_login_access() {

        // Block access to the admin area if the user isn't logged in & not ajax.
        $login_php_block = is_admin() && !wp_doing_ajax() && ! is_user_logged_in();

        // Prevent direct attempt to access any old login URLs.
        if ( ! $login_php_block ) {

            $url_path = trim( $this->get_path(), '/' );

            $possible_login_paths = [
                trim( home_url( 'wp-login.php', 'relative' ), '/' ),
                trim( site_url( 'wp-login.php', 'relative' ), '/' ),
                trim( home_url( 'wp-signup.php', 'relative' ), '/' ),
                trim( site_url( 'wp-signup.php', 'relative' ), '/' ),
                trim( home_url( 'login', 'relative' ), '/' ),
                trim( site_url( 'login', 'relative' ), '/' ),
                trim( home_url( 'admin', 'relative' ), '/' ),
                trim( site_url( 'admin', 'relative' ), '/' )
            ];

            $login_php_block = !empty( $url_path ) && ( in_array( $url_path, $possible_login_paths ) || preg_match( '/wp-login\.php/i', $url_path ) );
        }

        if ( $login_php_block ) {

            $this->wp_login_fail_redirect();
        }
    }

    /**
     * @param string $url_location
     * @return string
     */
    public function check_for_default_wp_login( $url_location ) {

        $redirect_path = parse_url( $url_location, PHP_URL_PATH );

        if ( strpos( $redirect_path, 'wp-login.php' ) !== false ) {

            $url_login = home_url( $this->login_path );
            $query_args = explode( '?', $url_location );

            if ( !empty( $query_args[ 1 ] ) ) {

                parse_str( $query_args[ 1 ], $query_args_new );
                $url_login = add_query_arg( $query_args_new, $url_login );
            }

            return $url_login;
        }
        return $url_location;
    }

    /**
     * @param string $url_location
     * @return string
     */
    public function protect_unauthorised_login_redirect( $url_location ) {

        if ( ! $this->is_login_url() ) {

            $redirect_path = trim( parse_url( $url_location, PHP_URL_PATH ), '/' );
            $redirect_hidden_url = ( $redirect_path == $this->login_path );

            if ( $redirect_hidden_url && ! is_user_logged_in() ) {

                $this->wp_login_fail_redirect();
            }
        }
        return $url_location;
    }

    /**
     * @param string $sUrl
     * @return string
     */
    public function block_register_url_redirect( $url ) {

        $url_path = $this->get_path();

        if ( strpos( $url_path, 'wp-register.php' ) ) {

            $this->wp_login_fail_redirect();
            die();
        }
        return $url;
    }

    /**
     */
    public function load_wp_login() {

        if ( $this->is_login_url() ) {

            @require_once( ABSPATH . 'wp-login.php' );
            die();
        }
    }

    public function redirect_login_form_action() {

        if ( ! $this->is_login_url() ) {

            $this->wp_login_fail_redirect();
            die();
        }
    }

    /**
     *
     */
    protected function wp_login_fail_redirect() {

        wp_redirect( home_url() );
        exit();
    }

    /**
     * @return string URI Path in lowercase
     */
    public function get_path() {

        return $this->get_uri_parts();
    }

    /**
     * @return array
     */
    public function get_uri_parts() {

        $path_uri = $_SERVER['REQUEST_URI'];

        if ( strpos( $path_uri, '?' ) !== false ) {

            list( $path_uri, $sQuery ) = explode( '?', $path_uri, 2 );
        }

        return $path_uri;
    }

    /**
     * @return bool
     */
    public function is_login_url() {

        $path_login = @parse_url( wp_login_url(), PHP_URL_PATH );

        return ( trim( $this->get_path(), '/' ) == trim( $path_login, '/' ) );
    }
}
