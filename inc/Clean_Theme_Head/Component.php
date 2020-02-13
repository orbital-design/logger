<?php
/**
 * Logger\Clean_Theme_Head\Component class
 *
 * @since 1.0.0
 *
 * @package Logger
 */

namespace Logger\Clean_Theme_Head;

use Logger\Component_Interface;
use function add_action;
use function remove_action;
use function add_filter;
use function has_filter;
use function remove_filter;
use function is_admin;
use function wp_scripts;
use function unregister_taxonomy_for_object_type;
use function add_data;

/**
 * Class for adding basic theme support, most of which is mandatory to be implemented by all themes.
 *
 * Exposes template tags:
 * * `logger()->getVersion()`
 * * `logger()->getAssetVersion( string $filepath )`
 */
class Component implements Component_Interface {

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'clean_theme_head';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'init', [ $this, 'wpHeadCleanUp' ] );
        add_action( 'init', [ $this, 'unregisterPostTags' ] );
        add_filter( 'style_loader_tag', [ $this, 'cleanStyleTags' ] );
    }

    /**
     * Cleanup Head
     *
     * WordPress head is a mess. Let's clean it up by removing all the junk we don't need.
     */
    public function wpHeadCleanUp()
    {
        remove_action( 'wp_head', 'feed_links_extra', 3 );                                  // Remove the links to the extra feeds such as category feeds.
        remove_action( 'wp_head', 'feed_links', 2 );                                        // Remove the links to the general feeds: Post and Comment Feed.
        remove_action( 'wp_head', 'rsd_link' );                                             // Remove the link to the Really Simple Discovery service endpoint.
        remove_action( 'wp_head', 'wlwmanifest_link' );                                     // Remove the link to the Windows Live Writer manifest file.
        remove_action( 'wp_head', 'index_rel_link' );                                       // Remove index link.
        remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );                          // Remove previous link.
        remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );                           // Remove start link.
        remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );               // Remove links for adjacent posts.
        remove_action( 'wp_head', 'wp_generator' );                                         // Remove WP version.
        remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );                             // Remove any shortlinks injected into wp_head.
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );                        // Remove any oEmbed discovery links.
        remove_action( 'wp_head', 'wp_oembed_add_host_js' );                                // Remove the JS needed to communicate with the embedded iframes.
        remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );                         // Remove the REST API link tag into page header.

        add_filter( 'use_default_gallery_style', '__return_false' );

        if ( has_filter( 'wp_head', 'wp_widget_recent_comments_style' ) ) {
            remove_filter( 'wp_head', 'wp_widget_recent_comments_style' );                  // Remove injected CSS for recent comments widget
        }

        add_action('wp_head', function () {                                                 // Remove injected CSS from recent comments widget
            $pattern = '/.*' . preg_quote(esc_url(get_feed_link('comments_' . get_default_feed())), '/') . '.*[\r\n]+/';
            echo preg_replace($pattern, '', ob_get_clean());
        }, 3, 0);

        // Move jQuery to footer.
        if ( ! is_admin() ) {
            add_action( 'wp_head', [ $this, 'sendJqueryToFooter' ], 1, 0 );
        }
    }

    /**
     * Get rid of tags on posts.
     *
     * @return void
     */
    public function unregisterPostTags()
    {

        unregister_taxonomy_for_object_type( 'post_tag', 'post' );
    }

    /**
     * Move jQuery Scripts to footer
     *
     * @return void
     */
    public function sendJqueryToFooter()
    {
        global $wp_scripts;
        $wp_scripts->add_data( 'jquery', 'group', 1 );
    }

    /**
     * cleanStyleTags
     *
     * Clean up output of stylesheet <link> tags
     *
     * src: https://github.com/flyntwp/flynt/blob/master/inc/cleanHead.php
     */
    public function cleanStyleTags($input) {
        preg_match_all(
            "!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(.*)' type='text/css' media='(.*)' />!",
            $input,
            $matches
        );
        if (empty($matches[2])) {
            return $input;
        }
        // Only display media if it is meaningful
        $media = $matches[3][0] !== '' && $matches[3][0] !== 'all' ? ' media="' . $matches[3][0] . '"' : '';
        return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n";
    }
}
