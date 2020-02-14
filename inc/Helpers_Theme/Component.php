<?php
/**
 * Logger\Helpers_Theme\Component class
 *
 * @since 1.0.0
 *
 * @package Logger
 */

namespace Logger\Helpers_Theme;

use Logger\Component_Interface;
use Logger\Templating_Component_Interface;
use function add_action;
use function add_filter;
use function add_theme_support;
use function is_singular;
use function pings_open;
use function esc_url;
use function get_bloginfo;
use function wp_scripts;
use function wp_get_theme;
use function get_template;

/**
 * Class for adding basic theme support, most of which is mandatory to be implemented by all themes.
 *
 * Exposes template tags:
 * * `logger()->getVersion()`
 * * `logger()->getAssetVersion( string $filepath )`
 * * `logger()->sanitizeOutput( string $buffer )`
 */
class Component implements Component_Interface, Templating_Component_Interface {

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'helpers_theme';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'after_setup_theme', [ $this, 'actionEssentialThemeSupport' ] );
        add_filter( 'embed_defaults', [ $this, 'filterEmbedDimensions' ] );
        add_filter( 'theme_scandir_exclusions', [ $this, 'filterScandirExclusionsForOptionalTemplates' ] );
        add_filter( 'script_loader_tag', [ $this, 'filterScriptLoaderTag' ], 10, 2 );
        add_filter( 'excerpt_more', [ $this, 'replaceExcertString' ] );
        add_filter( 'get_the_archive_title', [ $this, 'hideTheArchiveTitle' ] );
    }

    /**
     * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `logger()`.
     *
     * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
     *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
     *               adding support for further arguments in the future.
     */
    public function templateTags() : array
    {
        return [
            'getVersion'              => [ $this, 'getVersion' ],
            'getAssetVersion'         => [ $this, 'getAssetVersion' ],
            'sanitizeOutput'          => [ $this, 'sanitizeOutput' ],
        ];
    }

    /**
     * Adds theme support for essential features.
     */
    public function actionEssentialThemeSupport()
    {
        // Ensure WordPress manages the document title.
        add_theme_support( 'title-tag' );

        // Ensure WordPress theme features render in HTML5 markup.
        add_theme_support(
            'html5',
            [
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
            ]
        );

        // Add support for responsive embedded content.
        add_theme_support( 'responsive-embeds' );

        add_theme_support( 'post-thumbnails' );
    }

    /**
     * Sets the embed width in pixels, based on the theme's design and stylesheet.
     *
     * @param array $dimensions An array of embed width and height values in pixels (in that order).
     * @return array Filtered dimensions array.
     */
    public function filterEmbedDimensions( array $dimensions ) : array
    {
        $dimensions['width'] = 720;
        return $dimensions;
    }

    /**
     * Excludes any directory named 'optional' from being scanned for theme template files.
     *
     * @link https://developer.wordpress.org/reference/hooks/theme_scandir_exclusions/
     *
     * @param array $exclusions the default directories to exclude.
     * @return array Filtered exclusions.
     */
    public function filterScandirExclusionsForOptionalTemplates( array $exclusions ) : array
    {
        return array_merge(
            $exclusions,
            [ 'optional' ]
        );
    }

    /**
     * Adds async/defer attributes to enqueued / registered scripts.
     *
     * If #12009 lands in WordPress, this function can no-op since it would be handled in core.
     *
     * @link https://core.trac.wordpress.org/ticket/12009
     *
     * @param string $tag    The script tag.
     * @param string $handle The script handle.
     * @return string Script HTML string.
     */
    public function filterScriptLoaderTag( string $tag, string $handle ) : string
    {

        foreach ( [ 'async', 'defer' ] as $attr ) {
            if ( ! wp_scripts()->get_data( $handle, $attr ) ) {
                continue;
            }

            // Prevent adding attribute when already added in #12009.
            if ( ! preg_match( ":\s$attr(=|>|\s):", $tag ) ) {
                $tag = preg_replace( ':(?=></script>):', " $attr", $tag, 1 );
            }

            // Only allow async or defer, not both.
            break;
        }

        return $tag;
    }

    /**
     * Gets the theme version.
     *
     * @return string Theme version number.
     */
    public function getVersion() : string
    {
        static $theme_version = null;

        if ( null === $theme_version ) {
            $theme_version = wp_get_theme( get_template() )->get( 'Version' );
        }

        return $theme_version;
    }

    /**
     * Gets the version for a given asset.
     *
     * Returns filemtime when WP_DEBUG is true, otherwise the theme version.
     *
     * @param string $filepath Asset file path.
     * @return string Asset version number.
     */
    public function getAssetVersion( string $filepath ) : string
    {
        if ( WP_DEBUG ) {
            return (string) filemtime( $filepath );
        }

        return $this->getVersion();
    }

    /**
     * Sanitize Output
     *
     * @param string $buffer string of html.
     *
     * @return $buffer Modified HTML
     */
    public function sanitizeOutput( $buffer )
    {

        $search = array(
            '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
            '/[^\S ]+\</s',     // strip whitespaces before tags, except space
            '/(\s)+/s',         // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/', // Remove HTML comments
        );

        $replace = array(
            '>',
            '<',
            '\\1',
            '',
        );

        $buffer = preg_replace( $search, $replace, $buffer );

        return $buffer;
    }

    /**
     * Alter Excert [...]
     *
     * @param str $more $more text.
     *
     * @return void
     */
    public function replaceExcertString( $more )
    {
        return '...';
    }

    function hideTheArchiveTitle( $title )
    {

        // Skip if the site isn't LTR, this is visual, not functional.
        // Should try to work out an elegant solution that works for both directions.
        if ( is_rtl() ) {
            return $title;
        }

        // Split the title into parts so we can wrap them with spans.
        $title_parts = explode( ': ', $title, 2 );

        // Glue it back together again.
        if ( ! empty( $title_parts[1] ) ) {
            $title = wp_kses(
                $title_parts[1],
                array(
                    'span' => array(
                        'class' => array(),
                    ),
                )
            );
            // $title = '<span class="screen-reader-text">' . esc_html( $title_parts[0] ) . ': </span>' . $title;
        }

        return $title;

    }
}
