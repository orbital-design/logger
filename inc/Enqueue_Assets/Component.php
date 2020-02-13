<?php
/**
 * Logger\Enqueue_Assets\Component class
 *
 * @package Logger
 */

namespace Logger\Enqueue_Assets;

use Logger\Component_Interface;
use function Logger\logger;
use function add_action;
use function wp_enqueue_style;
use function wp_enqueue_script;
use function wp_style_add_data;
use function get_theme_file_uri;
use function get_theme_file_path;
use function apply_filters;

/**
 * Class for managing scripts & stylesheets.
 */
class Component implements Component_Interface {

    /**
     * Associative array of CSS files, as $handle => $data pairs.
     * $data must be an array with keys 'file' (file path relative to 'assets/css' directory), and optionally 'global'
     * (whether the file should immediately be enqueued instead of just being registered) and 'preload_callback'
     * (callback function determining whether the file should be preloaded for the current request).
     *
     * Do not access this property directly, instead use the `getCssFiles()` method.
     *
     * @var array
     */
    protected $css_files;

    /**
     * Associative array of JS files, as $handle => $data pairs.
     * $data must be an array with keys 'file' (file path relative to 'assets/js' directory).
     *
     * Do not access this property directly, instead use the `getJsFiles()` method.
     *
     * @var array
     */
    protected $js_files;

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'enqueue_assets';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueueThemeStyles' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueueThemeScripts' ] );
        add_action( 'after_setup_theme', [ $this, 'enqueueThemeEditorStyles' ] );
    }

    /**
     * Registers or enqueues scripts.
     *
     * Scripts that are global are enqueued.
     */
    public function enqueueThemeScripts()
    {
        $js_uri = get_theme_file_uri( '/assets/dist/scripts/' );
        $js_dir = get_theme_file_path( '/assets/dist/scripts/' );

        $js_files = $this->getJsFiles();
        foreach ( $js_files as $handle => $data ) {
            $src     = $js_uri . $data['file'];
            $version = logger()->getAssetVersion( $js_dir . $data['file'] );
            $deps    = $data['deps'];
            $in_foot = $data['in_foot'];

            wp_enqueue_script( $handle, $src, $deps, $version, $in_foot );
        }

        if ( ! is_admin() ) {
            wp_dequeue_style( 'wp-block-library' );
        }
    }

    /**
     * Registers or enqueues stylesheets.
     *
     * Stylesheets that are global are enqueued.
     */
    public function enqueueThemeStyles()
    {
        $css_uri = get_theme_file_uri( '/assets/dist/styles/' );
        $css_dir = get_theme_file_path( '/assets/dist/styles/' );

        $css_files = $this->getCssFiles();
        foreach ( $css_files as $handle => $data ) {
            $src     = $css_uri . $data['file'];
            $version = logger()->getAssetVersion( $css_dir . $data['file'] );

            wp_enqueue_style( $handle, $src, [], $version, $data['media'] );

            wp_style_add_data( $handle, 'precache', true );
        }
    }

    /**
     * Enqueues WordPress theme styles for the editor.
     */
    public function enqueueThemeEditorStyles()
    {
        // Enqueue block editor stylesheet.
        add_editor_style( 'assets/css/editor/editor-styles.min.css' );
    }

    /**
     * Gets all JS files.
     *
     * @return array Associative array of $handle => $data pairs.
     */
    protected function getJsFiles() : array
    {
        if ( is_array( $this->js_files ) ) {
            return $this->js_files;
        }

        $js_files = [
            'logger-global' => [
                'file'    => 'base.js',
                'deps'    => array( 'jquery' ),
                'in_foot' => true,
            ],
        ];

        $this->js_files = [];
        foreach ( $js_files as $handle => $data ) {
            if ( is_string( $data ) ) {
                $data = [ 'file' => $data ];
            }

            if ( empty( $data['file'] ) ) {
                continue;
            }

            $this->js_files[ $handle ] = array_merge(
                [
                    'deps'    => [],
                    'in_foot' => true,
                ],
                $data
            );
        }

        return $this->js_files;
    }

    /**
     * Gets all CSS files.
     *
     * @return array Associative array of $handle => $data pairs.
     */
    protected function getCssFiles() : array
    {
        if ( is_array( $this->css_files ) ) {
            return $this->css_files;
        }

        $css_files = [
            'logger-global'     => [
                'file'   => 'base.css',
                'global' => true,
            ],
        ];

        /**
         * Filters default CSS files.
         *
         * @param array $css_files Associative array of CSS files, as $handle => $data pairs.
         *                         $data must be an array with keys 'file' (file path relative to 'assets/css'
         *                         directory), and optionally 'global' (whether the file should immediately be
         *                         enqueued instead of just being registered) and 'preload_callback' (callback)
         *                         function determining whether the file should be preloaded for the current request).
         */
        $css_files = apply_filters( 'logger_css_files', $css_files );

        $this->css_files = [];
        foreach ( $css_files as $handle => $data ) {
            if ( is_string( $data ) ) {
                $data = [ 'file' => $data ];
            }

            if ( empty( $data['file'] ) ) {
                continue;
            }

            $this->css_files[ $handle ] = array_merge(
                [
                    'global'           => false,
                    'media'            => 'all',
                ],
                $data
            );
        }

        return $this->css_files;
    }
}
