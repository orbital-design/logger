<?php
/**
 * Logger\WP_Emoji\Component class
 *
 * @since 1.0.0
 *
 * @package Logger
 */

namespace Logger\WP_Emoji;

use Logger\Component_Interface;
use function add_action;
use function remove_action;
use function add_filter;
use function remove_filter;

/**
 * Class for removing WP Emoji
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
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'wp_emoji';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        // Tidy wp_head.
        add_action( 'init', [ $this, 'disableWordPressEmoji' ] );
    }

    /**
     * Disable Emoji throughout core
     */
    public function disableWordPressEmoji()
    {
        // all actions related to emojis.
        remove_action( 'admin_print_styles', 'print_emoji_styles' );
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

        // filter to remove TinyMCE emojis.
        add_filter( 'tiny_mce_plugins', [ $this, 'disableEmojiTinymce' ] );
    }

    /**
     * Disable Emoji in Tiny MCE
     *
     * @param array $plugins Plugin Object.
     */
    public function disableEmojiTinymce( $plugins )
    {
        if ( is_array( $plugins ) ) {
            return array_diff( $plugins, array( 'wpemoji' ) );
        } else {
            return array();
        }
    }

}
