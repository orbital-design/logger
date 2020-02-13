<?php
/**
 * Logger\Google_Fonts\Component class
 *
 * @package Logger
 */

namespace Logger\Google_Fonts;

use Logger\Component_Interface;
use function Logger\logger;
use function add_action;
use function add_filter;
use function wp_enqueue_style;
use function wp_style_is;
use function apply_filters;
use function add_query_arg;

/**
 * Class for managing scripts & stylesheets.
 */
class Component implements Component_Interface {

    /**
     * Associative array of Google Fonts to load, as $font_name => $font_variants pairs.
     *
     * Do not access this property directly, instead use the `getGoogleFonts()` method.
     *
     * @var array
     */
    protected $google_fonts;

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'google_fonts';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueueGoogleFonts' ] );
        add_action( 'after_setup_theme', [ $this, 'enqueueGoogleFontsInEditor' ] );
        add_filter( 'wp_resource_hints', [ $this, 'filterGoogleFontsResourceHints' ], 10, 2 );
    }

    /**
     * Registers or enqueues stylesheets.
     *
     * Stylesheets that are global are enqueued.
     */
    public function enqueueGoogleFonts()
    {
        // Enqueue Google Fonts.
        $google_fonts_url = $this->getGoogleFontsUrl();
        if ( ! empty( $google_fonts_url ) ) {
            wp_enqueue_style( 'logger-fonts', $google_fonts_url, [], null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
        }
    }

    /**
     * Enqueue Google Fonts in WordPress Block Editor
     */
    public function enqueueGoogleFontsInEditor()
    {

        // Enqueue Google Fonts.
        $google_fonts_url = $this->getGoogleFontsUrl();
        if ( ! empty( $google_fonts_url ) ) {
            add_editor_style( $this->getGoogleFontsUrl() );
        }
    }

    /**
     * Adds preconnect resource hint for Google Fonts.
     *
     * @param array  $urls          URLs to print for resource hints.
     * @param string $relation_type The relation type the URLs are printed.
     * @return array URLs to print for resource hints.
     */
    public function filterGoogleFontsResourceHints( array $urls, string $relation_type ) : array
    {
        if ( 'preconnect' === $relation_type && wp_style_is( 'logger-fonts', 'queue' ) ) {
            $urls[] = [
                'href' => 'https://fonts.gstatic.com',
                'crossorigin',
            ];
        }

        return $urls;
    }

    /**
     * Returns Google Fonts used in theme.
     *
     * @return array Associative array of $font_name => $font_variants pairs.
     */
    protected function getGoogleFonts() : array
    {
        if ( is_array( $this->google_fonts ) ) {
            return $this->google_fonts;
        }

        $google_fonts = [
            'IBM+Plex+Sans'  => [ '200', '300', '400', '600', '700' ],
            'IBM+Plex+Serif' => [ '300', '400', '500' ],
        ];

        /**
         * Filters default Google Fonts.
         *
         * @param array $google_fonts Associative array of $font_name => $font_variants pairs.
         */
        $this->google_fonts = (array) apply_filters( 'logger_google_fonts', $google_fonts );

        return $this->google_fonts;
    }

    /**
     * Returns the Google Fonts URL to use for enqueuing Google Fonts CSS.
     *
     * Uses `latin` subset by default. To use other subsets, add a `subset` key to $query_args and the desired value.
     *
     * @return string Google Fonts URL, or empty string if no Google Fonts should be used.
     */
    protected function getGoogleFontsUrl() : string
    {
        $google_fonts = $this->getGoogleFonts();

        if ( empty( $google_fonts ) ) {
            return '';
        }

        $font_families = [];

        foreach ( $google_fonts as $font_name => $font_variants ) {
            if ( ! empty( $font_variants ) ) {
                if ( ! is_array( $font_variants ) ) {
                    $font_variants = explode( ',', str_replace( ' ', '', $font_variants ) );
                }

                $font_families[] = $font_name . ':' . implode( ',', $font_variants );
                continue;
            }

            $font_families[] = $font_name;
        }

        $query_args = [
            'family'  => implode( '|', $font_families ),
            'display' => 'swap',
        ];

        return add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
    }
}
