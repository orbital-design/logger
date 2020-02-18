<?php
/**
 * Logger\Helpers_Embeds\Component class
 *
 * @since 1.0.0
 *
 * @package Logger
 */

namespace Logger\Helpers_Embeds;

use Logger\Component_Interface;
use acf;
use function add_action;
use function acf_render_field_setting;

/**
 * Class for Extending Youtube Embeds
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
        return 'helpers_embeds';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action('oembed_result', [ $this, 'setNoCookieDomain' ], 10, 2);
        add_filter('embed_oembed_html', [ $this, 'setNoCookieDomain' ], 10, 2);
    }

    /**
     * Changes oEmbed YouTube URLs from youtube.com to youtube-nocookie.com in favor of GDPR.
     *
     * src: https://github.com/flyntwp/flynt/blob/master/inc/youtubeNoCookieEmbed.php
     */
    public function setNoCookieDomain($searchString, $url) {

        if (preg_match('#https?://(www\.)?youtube\.com#i', $searchString)) {
            $searchString = preg_replace(
                '#(https?:)?//(www\.)?youtube\.com#i',
                '$1//$2youtube-nocookie.com',
                $searchString
            );
        }

        return $searchString;
    }
}
